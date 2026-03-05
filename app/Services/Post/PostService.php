<?php

namespace App\Services\Post;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Post\PostBuyPayload;
use App\Jobs\Event\Payload\Post\PostSearchKeywordPayload;
use App\Models\Common\CommentModel;
use App\Models\Post\PostModel;
use App\Models\Post\PostTagModel;
use App\Models\User\UserModel;
use App\Services\Common\AdvService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\ElasticService;
use App\Services\Common\JobService;
use App\Services\Report\ReportPostLogService;
use App\Services\User\AccountService;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserService;
use App\Services\User\UserUpService;
use App\Utils\CommonUtil;
use Phalcon\Manager\MediaLSJService;
use Phalcon\Manager\MediaService;
use Phalcon\Manager\MediaTangXinService;

class PostService extends BaseService
{
    /**
     * @param       $id
     * @return bool
     */
    public static function has(string $id)
    {
        return PostModel::count(['_id' => $id]) > 0;
    }

    /**
     * @param                             $id
     * @return true
     * @throws \Phalcon\Storage\Exception
     */
    public static function delete(string $id)
    {
        PostModel::deleteById($id);
        ElasticService::delete('post', 'post', $id);
        self::delCache($id);
        return true;
    }

    /**
     * 删除缓存
     * @param                             $id
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public static function delCache(string $id)
    {
        cache()->delete("post_detail_{$id}");
    }

    /**
     * 从缓存中获取信息
     * @param                             $id
     * @return array|mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getInfoCache($id)
    {
        $keyName = "post_detail_{$id}";
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $result = ElasticService::get($id, 'post', 'post');
            cache()->set($keyName, $result, 300);
        }
        if (empty($result) || $result['status'] != 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '帖子不存在或已下架!');
        }
        return $result;
    }

    /**
     * 同步至es
     * @param       $id
     * @return bool
     */
    public static function asyncEs($id)
    {
        $id  = strval($id);
        $row = PostModel::findByID($id);
        if (empty($row)) {
            return false;
        }

        $row['id']      = $row['_id'];
        $row['tags']    = PostTagService::getByIds($row['tags']);
        $row['kouling'] = CommonUtil::getKouling(strval($id), 'M-');
        $row['show_at'] = intval($row['show_at']);
        $row['user_id'] = value(function () use ($row) {
            $userRow = UserModel::findByID($row['user_id'], '_id', ['_id', 'nickname', 'username', 'headico']);
            return [
                'id'       => intval($userRow['_id']),
                'nickname' => strval($userRow['nickname'] ?: '已注销'),
                'username' => strval($userRow['username']),
                'headico'  => strval($userRow['headico']),
                'is_vip'   => strval($userRow && UserService::isVip($userRow) ? 'y' : 'n'),
                'is_up'    => strval($userRow && UserUpService::has($userRow['_id']) ? 'y' : 'n'),
            ];
        });

        $commentOk = CommentModel::count(['object_id' => $id, 'object_type' => 'post', 'status' => 1]);// 已通过审核
        $commentNo = CommentModel::count(['object_id' => $id, 'object_type' => 'post', 'status' => 0]);// 未通过审核
        CommonService::setRedisCounter("post_click_{$id}", $row['real_click']);
        CommonService::setRedisCounter("post_favorite_{$id}", $row['real_favorite']);
        CommonService::setRedisCounter("post_love_{$id}", $row['real_love']);
        CommonService::setRedisCounter("post_comment_ok_{$id}", $commentOk);
        CommonService::setRedisCounter("post_comment_no_{$id}", $commentNo);

        PostModel::updateById([
            'async_at'       => time(),
            'click_total'    => $row['real_click'] + $row['click'],
            'love_total'     => $row['real_love'] + $row['love'],
            'favorite_total' => $row['real_favorite'] + $row['favorite'],
            'comment'        => intval($commentOk + $commentNo)
        ], $id);

        unset($row['_id']);
        return ElasticService::save($id, $row, 'post', 'post');
    }

    /**
     * 搜索
     * @param  array $filter
     * @param        $userId
     * @return array
     */
    public static function doSearch(array $filter = [], $userId = null)
    {
        $page      = $filter['page'] ?: 1;
        $pageSize  = $filter['page_size'] ?: 16;
        $keywords  = $filter['keywords'];
        $tagId     = $filter['tag_id'];
        $globalTop = $filter['global_top'];
        $homeTop   = $filter['home_top'];
        $ids       = $filter['ids'];
        $notIds    = $filter['not_ids'];
        $homeId    = $filter['home_id'];
        $homeIds   = $filter['home_ids'];
        $payType   = $filter['pay_type'];
        $position  = $filter['position'];
        $order     = $filter['order'] ?: 'sort';
        $status    = $filter['status'] ?: '';
        $adCode    = $filter['ad_code'];
        $type      = $filter['type'];
        $kouling   = $filter['kouling'];
        $language  = $filter['language'];

        $from = ($page - 1) * $pageSize;

        $source = [];
        $query  = [
            'from'      => $from,
            'size'      => $pageSize,
            'min_score' => 1.0,
            '_source'   => $source,
            'query'     => []
        ];
        if (is_null($userId) || $userId != $homeId) {
            $query['query']['bool']['must'][] = [
                'term' => ['status' => 1]
            ];
            $query['query']['bool']['must'][] = [
                'term' => ['permission' => 'public']
            ];
        } else {
            if ($status !== '') {
                $query['query']['bool']['must'][] = [
                    'term' => ['status' => intval($status)]
                ];
            }
        }
        switch ($order) {
            case 'click':
                $query['sort'] = [
                    ['click_total' => ['order' => 'desc']],
                ];
                break;
            case 'click7':
                $ids = ReportPostLogService::getIds('click', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'click30':
                $ids = ReportPostLogService::getIds('click', 'month', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'love':
                $query['sort'] = [
                    ['love_total' => ['order' => 'desc']],
                ];
                break;
            case 'love7':
                $ids = ReportPostLogService::getIds('love', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'love30':
                $ids = ReportPostLogService::getIds('love', 'month', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'favorite':
                $query['sort'] = [
                    ['favorite_total' => ['order' => 'desc']],
                ];
                break;
            case 'favorite7':
                $ids = ReportPostLogService::getIds('favorite', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'favorite30':
                $ids = ReportPostLogService::getIds('favorite', 'month', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'hot':
                $query['sort'] = [
                    ['hot_rate' => ['order' => 'desc']],
                ];
                break;
            case 'buy':
                $query['sort'] = [
                    ['buy' => ['order' => 'desc']],
                ];
                break;
            case 'sort':
                $query['sort'] = [
                    ['sort' => ['order' => 'desc']],
                ];
                break;
            case 'new':
                $query['sort'] = [
                    ['show_at' => ['order' => 'desc']],
                ];
                break;
            case 'rand':
                $query['sort'] = [
                    [
                        '_script' => [
                            'script' => 'Math.random()',
                            'type'   => 'number',
                            'order'  => 'asc'
                        ]
                    ],
                ];
                break;
            default:
                $query['sort'] = [
                    ['sort' => ['order' => 'desc']],
                    ['show_at' => ['order' => 'desc']],
                ];
                break;
        }

        // 关键字
        if ($keywords) {
            $query['query']['bool']['should'] = value(function () use ($keywords) {
                $should = [];
                if (mb_strlen($keywords, 'UTF-8') >= 2) {
                    // 第一优先级：match_phrase（精确匹配）
                    $should[] = [
                        //                        'match_phrase' => [
                        //                            'content' => [
                        //                                'query' => $keywords,
                        //                                'boost' => 5  // 高权重
                        //                            ]
                        //                        ],
                        // /类似like
                        'wildcard' => [
                            'content.wild' => [
                                'value'            => "*{$keywords}*",
                                'case_insensitive' => true,
                                'boost'            => 5  // 高权重
                            ]
                        ]
                    ];
                }
                // 第二优先级：分词匹配 name（支持中文分词）
                $should[] = [
                    'multi_match' => [
                        'query'  => $keywords,
                        'fields' => ['title', 'content'],
                        'boost'  => 3  // 中等权重
                    ]
                ];
                // 第三优先级：标签精确匹配
                if (mb_strlen($keywords, 'UTF-8') >= 2) {
                    $should[] = [
                        'term' => [
                            'tags.name.keyword' => [
                                'value' => $keywords,
                                'boost' => 1.5  // 低权重
                            ]
                        ]
                    ];
                }
                return $should;
            });
            $query['query']['bool']['minimum_should_match'] = 1;  // 至少匹配一个should条件
            $query['min_score']                             = 2.0;
            array_unshift($query['sort'], ['_score' => ['order' => 'desc']]);// 优先按评分
            //            $query['track_scores'] = true;///调试用,如果添加了自定义sort _source将会返回null,所以可以手动开启
            if (mb_strlen($keywords) < 15) {
                PostKeywordsService::do($keywords);
            }
        }

        if (!empty($tagId)) {
            $query['query']['bool']['must'][] = ['terms' => ['tags.id' => explode(',', $tagId)]];
            unset($tagId);
        }
        if (!empty($globalTop)) {
            $query['query']['bool']['must'][] = ['term' => ['global_top' => $globalTop]];
            unset($globalTop);
        }
        if (!empty($homeTop)) {
            $query['query']['bool']['must'][] = ['term' => ['home_top' => $homeTop]];
            unset($homeTop);
        }
        if (!empty($ids)) {
            $query['query']['bool']['must'][] = ['terms' => ['id' => explode(',', $ids)]];
        }
        if (!empty($notIds)) {
            $query['query']['bool']['must_not'][] = ['ids' => ['values' => explode(',', $notIds)]];
            unset($notIds);
        }
        if (!empty($homeId)) {
            $query['query']['bool']['must'][] = ['term' => ['user_id.id' => $homeId]];
            unset($homeId);
        }
        if (!empty($homeIds)) {
            $query['query']['bool']['must'][] = ['terms' => ['user_id.id' => explode(',', $homeIds)]];
            unset($homeIds);
        }
        if (!empty($payType)) {
            $query['query']['bool']['must'][] = ['term' => ['pay_type' => $payType]];
            unset($payType);
        }
        if (!empty($type)) {
            if ($type === 'image') {
                // 有图片
                $query['query']['bool']['must'][] = ['exists' => ['field' => 'images.url']];
            } elseif ($type === 'video') {
                // 有视频
                $query['query']['bool']['must'][] = ['exists' => ['field' => 'videos.url']];
            }
            unset($type);
        }
        if (!empty($position)) {
            $query['query']['bool']['must'][] = ['term' => ['position' => $position]];
            unset($position);
        }

        if (!empty($kouling)) {
            $query['query']['bool']['must'][] = ['multi_match' => ['query' => $kouling, 'type' => 'phrase', 'fields' => ['kouling']]];
            unset($kouling);
        }
        $items  = [];
        $result = ElasticService::search($query, 'post', 'post');
        // 获取计数器
        $redisCounterKeys = [];
        foreach ($result['hits']['hits'] as $item) {
            $id                 = $item['_source']['id'];
            $redisCounterKeys[] = 'post_click_' . $id;
            $redisCounterKeys[] = 'post_love_' . $id;
            $redisCounterKeys[] = 'post_favorite_' . $id;
            $redisCounterKeys[] = 'post_comment_ok_' . $id;
            //            $redisCounterKeys[] = "audio_comment_no_".$id;;
        }
        $counterMap = CommonService::getRedisCounters($redisCounterKeys);

        foreach ($result['hits']['hits'] as $item) {
            $item = $item['_source'];
            $item = [
                'id'    => strval($item['id']),
                'title' => value(function () use ($item, $language) {
                    $name = $item['title' . $language] ?? $item['title'];
                    return strval($name);
                }),
                'content' => value(function () use ($item, $language) {
                    $name = $item['content' . $language] ?? $item['content'];
                    return strval($name);
                }),
                'type'    => 'post',
                'user_id' => value(function () use ($item) {
                    $user = $item['user_id'];
                    if (empty($user)) {
                        return [];
                    }
                    return [
                        'id'       => strval($user['id']),
                        'nickname' => strval($user['nickname']),
                        'username' => strval($user['username']),
                        'headico'  => CommonService::getCdnUrl($user['headico']),
                    ];
                }),
                'tags' => value(function () use ($item) {
                    if (empty($item['tags'])) {
                        return [];
                    }
                    $tags = [];
                    foreach ($item['tags'] as $index => $tag) {
                        if ($index >= 3) {
                            break;
                        }
                        $tags[] = [
                            'id'   => strval($tag['id']),
                            'name' => strval($tag['name']),
                        ];
                    }
                    return $tags;
                }),
                'images' => value(function () use ($item) {
                    $rows = [];
                    foreach ($item['images'] as $index => $image) {
                        $rows[] = [
                            'url' => CommonService::getCdnUrl($image['url']),
                        ];
                    }
                    return $rows;
                }),
                'videos' => value(function () use ($item) {
                    $rows = [];
                    foreach ($item['videos'] as $index => $video) {
                        $rows[] = [
                            'img' => CommonService::getCdnUrl($video['img'], 'image'),
                            'url' => '', // 列表不展示
                            //                            'url'=>M3u8Service::encode($video['url'],'tencent')
                        ];
                    }
                    return $rows;
                }),
                'has_images' => count($item['images']) ? 'y' : 'n',
                'has_videos' => count($item['videos']) ? 'y' : 'n',
                'love'       => value(function () use ($item, $counterMap) {
                    $keyName = 'post_love_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['love'] + $real)));
                }),
                'click' => value(function () use ($item, $counterMap) {
                    $keyName = 'post_click_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['click'] + $real)));
                }),
                'favorite' => value(function () use ($item, $counterMap) {
                    $keyName = 'post_favorite_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['favorite'] + $real)));
                }),
                //                 //列表用不到评论数量
                //                'comment' => value(function () use ($item,$counterMap) {
                //                    $ok = $counterMap["post_comment_ok_{$item['id']}"] ?? 0;
                // //                    $no = $counterMap["post_comment_no_{$item['id']}"] ?? 0;
                //                    return strval(CommonUtil::formatNum(intval($ok)));
                //                }),
                'money'        => strval($item['money']),
                'pay_type'     => strval($item['pay_type']),
                'pos_info'     => strval($item['pos_info']),
                'created_at'   => strval(CommonUtil::ucTimeAgo($item['created_at'])),
                'last_comment' => $item['last_comment'] ? strval(CommonUtil::ucTimeAgo($item['last_comment'])) : '',

                'status'      => strval($item['status']),
                'status_text' => strval(CommonValues::getPostStatus($item['status'])),

                'kouling' => strval($item['kouling']),
                'ad_link' => '',
            ];
            $items[] = $item;
        }

        // 排行榜结果集重排
        if (in_array($order, ['click7', 'click30', 'love7', 'love30', 'favorite7', 'favorite30']) && !empty($ids)) {
            $idArr = explode(',', $ids);
            $items = CommonUtil::arraySort($items, 'id', $idArr);
        }
        // 埋点
        if (!empty($userId) && !empty($keywords) && mb_strlen($keywords) < 15) {
            JobService::create(new EventBusJob(new PostSearchKeywordPayload($userId, $keywords, $result['hits']['total']['value'] ?? 0)));
        }

        if (!empty($adCode)) {
            $items = AdvService::insertAdsToList($items, $adCode, 4, 5, true);
            foreach ($items as $key => $item) {// 组装广告数据
                if ($item['_ad']) {
                    $items[$key] = self::getAdItem($item);
                }
            }
            unset($adCode);
        }

        $items  = array_values($items);
        $result = [
            'data'         => $items,
            'total'        => $result['hits']['total']['value'] ? strval($result['hits']['total']['value']) : '0',
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
        ];
        $result['last_page'] = strval(ceil($result['total'] / $pageSize));
        return $result;
    }

    /**
     * @param  string $source
     * @param  string $category GC AV
     * @return true
     */
    public static function asyncMrsByCat(string $category, $source = null)
    {
        if ($source == 'xiaozu') {
            $mediaUrl     = ConfigService::getConfig('xiaozu_media_api');
            $mediaKey     = ConfigService::getConfig('xiaozu_media_key');
            $isPublic     = false;
            $mediaService = new MediaService($mediaUrl, $mediaKey);
        } elseif ($source == 'tangxin') {
            $mediaUrl     = ConfigService::getConfig('tangxin_media_api');
            $mediaKey     = ConfigService::getConfig('tangxin_media_key');
            $isPublic     = false;
            $mediaService = new MediaTangXinService($mediaUrl, $mediaKey);
        } else {
            $mediaUrl     = ConfigService::getConfig('media_api');
            $mediaAppid   = ConfigService::getConfig('media_appid');
            $mediaKey     = ConfigService::getConfig('media_key');
            $isPublic     = false;
            $mediaService = new MediaLSJService($mediaUrl, $mediaKey, $mediaAppid);
        }

        if (empty($mediaUrl)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '请配置媒资库接口地址');
        }
        if (empty($mediaKey)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '请配置媒资库接口key');
        }

        $saveMrsData = function ($rows) use ($isPublic) {
            foreach ($rows as $item) {
                self::saveMrsData($item, $isPublic);
            }
        };
        $mediaService->asyncPostByCategory($category, $saveMrsData);
        return true;
    }

    /**
     * 保存媒资库数据
     * @param  array               $mrsData
     * @param                      $isPublic
     * @return bool|int|mixed|null
     */
    public static function saveMrsData(array $mrsData, $isPublic = false)
    {
        if (empty($mrsData['id']) || empty($mrsData['title']) || empty($mrsData['img_x'])) {
            return null;
        }

        $tags = [];
        /***私有库同步标签  非私有不同步**/
        if ($isPublic === false) {
            if ($mrsData['tags']) {
                foreach ($mrsData['tags'] as $tag) {
                    if (empty($tag)) {
                        continue;
                    }
                    $tagModel = PostTagModel::findByID(intval($tag['id']));
                    if (empty($tagModel)) {
                        $tags[] = PostTagModel::insert([
                            '_id'       => $tag['id'] * 1,
                            'name'      => $tag['name'],
                            'is_hot'    => 0,
                            'attribute' => $tag['group'] ?: '',
                        ]);
                    } else {
                        if (empty($tagModel['attribute']) && !empty($tag['group'])) {
                            PostTagModel::save([
                                '_id'       => $tagModel['_id'],
                                'attribute' => $tag['group']
                            ]);
                        }
                        $tags[] = $tagModel['_id'];
                    }
                }
            }
        }
        $postModel = PostModel::findFirst(['_id' => $mrsData['id']]);
        if (empty($postModel)) {
            // $positions = array_keys( CommonValues::getPostPosition());
            $postSaveData = [
                '_id'     => $mrsData['id'],
                'user_id' => value(function () use ($mrsData) {
                    $user   = $mrsData['up_user'];
                    $userId = intval($user['id']);
                    // /生成用户和up
                    $userRow = UserModel::findByID($userId);
                    if (empty($userRow)) {
                        $userRow = UserService::register('username', uniqid(), uniqid('phone_'), 'android', '1.0', '', 'system', '', $user['nickname'], $user['headico'], $userId);
                        //                        并不是发帖就一定是up主
                        //                        UserUpService::do($userRow['_id'],$userRow['nickname'],$userRow['headico'],'post',true);
                    }
                    return $userId;
                }),
                'source'         => $mrsData['source'],
                'title'          => $mrsData['title'],
                'content'        => $mrsData['content'],
                'tags'           => $tags,
                'at_users'       => [],
                'images'         => $mrsData['images'],
                'videos'         => $mrsData['videos'],
                'files'          => $mrsData['files'],
                'click'          => $mrsData['click'],
                'real_click'     => 0,
                'love'           => $mrsData['love'],
                'real_love'      => 0,
                'favorite'       => $mrsData['favorite'],
                'real_favorite'  => 0,
                'favorite_rate'  => 0,
                'hot_rate'       => 0,
                'click_total'    => 0,
                'love_total'     => 0,
                'favorite_total' => 0,

                'comment'      => 0,
                'last_comment' => 0,

                'permission' => 'public',
                'money'      => $mrsData['money'],
                'pay_type'   => $mrsData['pay_type'],
                'position'   => $mrsData['position'],

                'global_top' => 0,
                'home_top'   => 0,
                'ip'         => '',
                'pos_info'   => '',
                'deny_msg'   => '',
                'sort'       => 0,
                'status'     => 0, // 默认未上架
                'show_at'    => mt_rand(time() - 24 * 3600, time()),

                'created_at' => $mrsData['created_at'],
                'updated_at' => $mrsData['updated_at'],
            ];
            $postId = PostModel::insert($postSaveData);
        } else {
            //            $postSaveData = array(
            //                '_id' => $postModel['_id'],
            //                'title' => $mrsData['title'],
            //                'content' => $mrsData['content'],
            //            );
            $postId = $postModel['_id'];
        }
        return $postId;
    }

    /**
     * @param  string $source
     * @param  array  $mids
     * @return true
     */
    public static function asyncMrsByIds(array $mids, $source = null)
    {
        if ($source == 'xiaozu') {
            $mediaUrl     = ConfigService::getConfig('xiaozu_media_api');
            $mediaKey     = ConfigService::getConfig('xiaozu_media_key');
            $isPublic     = false;
            $mediaService = new MediaService($mediaUrl, $mediaKey);
        } elseif ($source == 'tangxin') {
            $mediaUrl     = ConfigService::getConfig('tangxin_media_api');
            $mediaKey     = ConfigService::getConfig('tangxin_media_key');
            $isPublic     = false;
            $mediaService = new MediaTangXinService($mediaUrl, $mediaKey);
        } else {
            $mediaUrl     = ConfigService::getConfig('media_api');
            $mediaAppid   = ConfigService::getConfig('media_appid');
            $mediaKey     = ConfigService::getConfig('media_key');
            $isPublic     = false;
            $mediaService = new MediaLSJService($mediaUrl, $mediaKey, $mediaAppid);
        }

        if (empty($mediaUrl)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '请配置媒资库接口地址');
        }
        if (empty($mediaKey)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '请配置媒资库接口key');
        }

        $saveMrsData = function ($rows) use ($isPublic) {
            foreach ($rows as $item) {
                self::saveMrsData($item, $isPublic);
            }
        };
        $mediaService->asyncPostByIds($mids, $saveMrsData);
        return true;
    }

    /**
     * 购买帖子
     * @param                    $userId
     * @param                    $postId
     * @return true
     * @throws BusinessException
     */
    public static function doBuy($userId, $postId)
    {
        $postId = strval($postId);
        if (empty($postId)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请选择要解锁的帖子!');
        }
        $hasBuy = UserBuyLogService::has($userId, $postId, 'post');
        if ($hasBuy) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '您已经购买过了!');
        }
        $postRow = PostModel::findByID($postId);
        if (empty($postRow) || $postRow['status'] != 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '帖子已下架!');
        }
        $money = $postRow['money'];
        if ($money < 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '此帖子无需购买!');
        }

        $userRow = UserModel::findByID(intval($userId));
        UserService::checkDisabled($userRow);

        $homeRow = [];
        if (!empty($movieRow['user_id'])) {
            $homeRow = UserModel::findByID(intval($movieRow['user_id']));
        }

        if ($postRow['position'] == 'dark') {
            $isDarkVip = UserService::isVip($userRow, true);
            if ($isDarkVip) {
                // 获取VIP折扣
                $discountRate = $userRow['group_dark_rate'];
                $money        = !empty($discountRate) ? round($money * $discountRate / 100, 0) : $money;
            }
        } else {
            $isVip = UserService::isVip($userRow, false);
            if ($isVip) {
                // 获取VIP折扣
                $discountRate = $userRow['group_rate'];
                $money        = !empty($discountRate) ? round($money * $discountRate / 100, 0) : $money;
            }
        }
        $money = $money < 0 ? 0 : $money;
        if ($userRow['balance'] < $money) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '可用余额不足!');
        }
        $orderSn = CommonUtil::createOrderNo('POST');
        if ($money > 0) {
            AccountService::reduceBalance($userRow, $orderSn, $money, 8, 'balance', "购买视频消耗:{$money},帖子Id:{$postId} ");

            if (!empty($homeRow)) {
                // 创作者计算分成比例,>0才分成
                $homeInfo  = UserService::getInfoFromCache($homeRow['_id']);
                $saleRate  = intval($homeInfo['creator']['post_fee_rate']);
                $saleMoney = round($money * $saleRate / 100, 2);
                if ($saleRate > 0 && $saleRate <= 100 && $saleMoney > 0) {
                    AccountService::addBalance(
                        $homeRow,
                        $orderSn,
                        $saleMoney,
                        10,
                        'balance_income',
                        "帖子收入:{$saleMoney},({$postRow['title']})"
                    );
                }
            }
        }
        UserBuyLogService::do($orderSn, $userRow, $postId, 'post', $postRow['images'][0]['url'], $money, $postRow['money'], $postRow['position'], );

        self::handler('buy', $postId, $money);
        JobService::create(new EventBusJob(new PostBuyPayload($userId, $postId, $orderSn, $money, $userRow['balance'], $userRow['balance'] - $money)));
        return true;
    }

    /**
     * @param       $action
     * @param       $postId
     * @param       $money
     * @return void
     */
    public static function handler($action, $postId, $money = null)
    {
        switch ($action) {
            case 'click':
                CommonService::updateRedisCounter("post_click_{$postId}", 1);
                PostModel::updateRaw(['$inc' => ['real_click' => 1]], ['_id' => $postId]);
                ReportPostLogService::inc($postId, 'click', 1);
                break;
            case 'buy':
                PostModel::updateRaw(['$inc' => ['buy' => 1]], ['_id' => $postId]);
                ReportPostLogService::inc($postId, 'buy_num', 1);
                ReportPostLogService::inc($postId, 'buy_total', intval($money));
                break;
            case 'favorite':
                CommonService::updateRedisCounter("post_favorite_{$postId}", 1);
                PostModel::updateRaw(['$inc' => ['real_favorite' => 1]], ['_id' => $postId]);
                ReportPostLogService::inc($postId, 'favorite', 1);
                break;
            case 'unFavorite':
                CommonService::updateRedisCounter("post_favorite_{$postId}", -1);
                PostModel::updateRaw(['$inc' => ['real_favorite' => -1]], ['_id' => $postId]);
                ReportPostLogService::inc($postId, 'favorite', -1);
                break;
            case 'love':
                CommonService::updateRedisCounter("post_love_{$postId}", 1);
                PostModel::updateRaw(['$inc' => ['real_love' => 1]], ['_id' => $postId]);
                ReportPostLogService::inc($postId, 'love', 1);
                break;
            case 'unLove':
                CommonService::updateRedisCounter("post_love_{$postId}", -1);
                PostModel::updateRaw(['$inc' => ['real_love' => -1]], ['_id' => $postId]);
                ReportPostLogService::inc($postId, 'love', -1);
                break;
        }
    }

    /**
     * 替换富文本中的图片为cdn
     * @param                        $content
     * @return array|string|string[]
     */
    public static function parseContentImgCdn($content)
    {
        $content = htmlspecialchars_decode(stripslashes($content));     // html实体转标签
        preg_match_all("/<img .*?src=[\'|\"](.*?(?:))[\'|\"].*?[\/]?>/", $content, $out, PREG_PATTERN_ORDER);      // 正则匹配img标签的src属性，返回二维数组

        if (!empty($out)) {
            $rows = $out[1] ?? [];

            foreach ($rows as $src) {
                $item = parse_url($src);
                $path = $item['path'];
                if (strpos($src, 'http') !== false) {
                    continue;
                }
                $path = str_replace('$media', '', $path);
                // /标签替换
                $content = str_replace($src, "\" data-src=\"{$src}", $content);
                $content = str_replace($src, CommonService::getCdnUrl($path), $content);
            }
        }
        return $content;
    }

    /**
     * 替换富文本中的图片为cdn
     * @param                        $content
     * @return array|string|string[]
     */
    public static function parseLsjContentImgCdn($content)
    {
        // 处理回车换行
        $content = str_replace(["\r\n", "\n"], '<br/>', $content);

        if (strpos($content, '[[img') !== false) {
            preg_match_all('/\[\[img:\/\/(.*)\]\]/iUs', $content, $out);
            if (!empty($out)) {
                $rows = $out[0] ?? [];
                foreach ($rows as $index => $src) {
                    $cdnUrl = CommonService::getCdnUrl($out[1][$index]);
                    // /标签替换
                    $content = str_replace($src, "<img  data-src=\"$cdnUrl\"/>", $content);
                }
            }
        }

        return $content;
    }

    /**
     * 穿插广告
     * @param        $item
     * @return array
     */
    private static function getAdItem($item)
    {
        $row = [
            'id'         => strval($item['id']),
            'title'      => strval($item['name']),
            'content'    => '',
            'type'       => 'ad',
            'user_id'    => [],
            'tags'       => [],
            'images'     => [],
            'videos'     => [],
            'has_images' => 'y',
            'has_videos' => 'n',

            'click'        => strval(CommonUtil::formatNum(rand(10000, 100000))),
            'love'         => strval(CommonUtil::formatNum(rand(500, 10000))),
            'favorite'     => strval(CommonUtil::formatNum(rand(1000, 10000))),
            'comment'      => '0',
            'money'        => '',
            'pay_type'     => '',
            'pos_info'     => '',
            'created_at'   => '',
            'last_comment' => '',

            'ad_link' => strval($item['link']),
        ];
        return $row;
    }
}
