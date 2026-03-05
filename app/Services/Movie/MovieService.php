<?php

namespace App\Services\Movie;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Movie\MovieBuyPayload;
use App\Jobs\Event\Payload\Movie\MovieSearchKeywordPayload;
use App\Models\Common\CommentModel;
use App\Models\Movie\MovieCategoryModel;
use App\Models\Movie\MovieModel;
use App\Models\Movie\MovieTagModel;
use App\Models\User\UserModel;
use App\Models\User\UserUpModel;
use App\Services\Common\AdvService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\ElasticService;
use App\Services\Common\JobService;
use App\Services\Report\ReportMovieLogService;
use App\Services\User\AccountService;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserService;
use App\Services\User\UserUpService;
use App\Utils\CommonUtil;
use Phalcon\Manager\MediaLSJService;
use Phalcon\Manager\MediaService;
use Phalcon\Manager\MediaTangXinService;

class MovieService extends BaseService
{
    /**
     * @param       $id
     * @return bool
     */
    public static function has(string $id)
    {
        return MovieModel::count(['_id' => $id]) > 0;
    }

    /**
     * @param                             $id
     * @return true
     * @throws \Phalcon\Storage\Exception
     */
    public static function delete(string $id)
    {
        MovieModel::deleteById($id);
        ElasticService::delete('movie', 'movie', $id);
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
        cache()->delete("movie_detail_{$id}");
    }

    /**
     * 从缓存中获取信息
     * @param                             $id
     * @return array|mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getInfoCache($id)
    {
        $keyName = "movie_detail_{$id}";
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $result = ElasticService::get($id, 'movie', 'movie');
            cache()->set($keyName, $result, 300);
        }
        if (empty($result) || $result['status'] != 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '视频不存在或已下架!');
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
        $row = MovieModel::findByID($id);
        if (empty($row)) {
            return false;
        }

        $row['id']              = $row['_id'];
        $row['tags']            = MovieTagService::getByIds($row['tags']);
        $row['categories']      = MovieCategoryService::getById($row['categories']);
        $row['kouling']         = CommonUtil::getKouling(strval($id), 'M-');
        $row['show_at']         = intval($row['show_at']);
        $row['issue_date']      = strval($row['issue_date'] ?: date('2010-01-01'));
        $row['issue_date_time'] = strtotime($row['issue_date']);
        $row['duration']        = $row['links'][0]['duration'];
        $row['user_id']         = value(function () use ($row) {
            foreach ($row['user_id'] as $userId) {
                $userRow = UserModel::findByID($userId, '_id', ['_id', 'nickname', 'username', 'headico']);
                $rows[]  = [
                    'id'       => intval($userId),
                    'nickname' => strval($userRow['nickname'] ?: '已注销'),
                    'username' => strval($userRow['username']),
                    'headico'  => strval($userRow['headico']),
                    'is_vip'   => strval($userRow && UserService::isVip($userRow) ? 'y' : 'n'),
                    'is_up'    => strval($userRow && UserUpService::has($userRow['_id']) ? 'y' : 'n'),
                ];
            }
            return $rows;
        });

        $commentOk = CommentModel::count(['object_id' => $id, 'object_type' => 'movie', 'status' => 1]);// 已通过审核
        $commentNo = CommentModel::count(['object_id' => $id, 'object_type' => 'movie', 'status' => 0]);// 未通过审核
        CommonService::setRedisCounter("movie_click_{$id}", $row['real_click'] ?? 0);
        CommonService::setRedisCounter("movie_favorite_{$id}", $row['real_favorite'] ?? 0);
        CommonService::setRedisCounter("movie_love_{$id}", $row['real_love'] ?? 0);
        CommonService::setRedisCounter("movie_dislove_{$id}", $row['real_dislove'] ?? 0);
        CommonService::setRedisCounter("movie_comment_ok_{$id}", $commentOk);
        CommonService::setRedisCounter("movie_comment_no_{$id}", $commentNo);

        MovieModel::updateById([
            'async_at'       => time(),
            'click_total'    => $row['real_click'] + $row['click'],
            'love_total'     => $row['real_love'] + $row['love'],
            'dislove_total'  => $row['real_dislove'] + $row['dislove'],
            'favorite_total' => $row['real_favorite'] + $row['favorite'],
            'comment'        => intval($commentOk + $commentNo)
        ], $id);

        unset($row['_id']);
        return ElasticService::save($id, $row, 'movie', 'movie');
    }

    /**
     * 搜索
     * @param  array      $filter
     * @param  null|mixed $userId
     * @return array
     */
    public static function doSearch(array $filter = [], $userId = null)
    {
        $page     = $filter['page'] ?: 1;
        $pageSize = $filter['page_size'] ?: 16;
        $keywords = $filter['keywords'];
        $number   = $filter['number'];
        $payType  = $filter['pay_type'];
        $position = $filter['position'];
        $catId    = $filter['cat_id'];
        $tagId    = $filter['tag_id'];
        $canvas   = $filter['canvas'];
        $thumb    = $filter['thumb'];
        $ids      = $filter['ids'];
        $notIds   = $filter['not_ids'];
        $xFilter  = $filter['x_filter'];
        $adCode   = $filter['ad_code'];
        $order    = $filter['order'] ?: '';
        $status   = $filter['status'] ?: '';
        $homeId   = $filter['home_id'];
        $homeIds  = $filter['home_ids'];
        $duration = $filter['duration'];// 格式gte300或let300
        $kouling  = $filter['kouling'];
        $language = $filter['language'];

        $from   = ($page - 1) * $pageSize;
        $source = [];
        $query  = [
            'from'      => $from,
            'size'      => $pageSize,
            'min_score' => 1.0,
            '_source'   => $source,
            'query'     => [
                'bool' => [
                    'must' => []
                ]
            ]
        ];
        if (is_null($userId) || $userId != $homeId) {
            $query['query']['bool']['must'][] = [
                'term' => ['status' => 1]
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
                $ids = ReportMovieLogService::getIds('click', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'click30':
                $ids = ReportMovieLogService::getIds('click', 'month', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'love':
                $query['sort'] = [
                    ['love_total' => ['order' => 'desc']],
                ];
                break;
            case 'love7':
                $ids = ReportMovieLogService::getIds('love', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'love30':
                $ids = ReportMovieLogService::getIds('love', 'month', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'favorite':
                $query['sort'] = [
                    ['favorite_total' => ['order' => 'desc']],
                ];
                break;
            case 'favorite7':
                $ids = ReportMovieLogService::getIds('favorite', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'favorite30':
                $ids = ReportMovieLogService::getIds('favorite', 'month', $page, $pageSize);
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
                        //                            'name' => [
                        //                                'query' => $keywords,
                        //                                'boost' => 5  // 高权重
                        //                            ]
                        //                        ],
                        // /类似like
                        'wildcard' => [
                            'name.wild' => [
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
                        'fields' => ['name', 'number'],
                        'boost'  => 3  // 中等权重
                    ]
                ];
                // 第三优先级：标签精确匹配
                if (mb_strlen($keywords, 'UTF-8') >= 2) {
                    $should[] = [
                        'term' => [
                            'user_id.nickname' => [
                                'value' => $keywords,
                                'boost' => 4  // 中高权重
                            ]
                        ]
                    ];
                    $should[] = [
                        'term' => [
                            'tags.name.keyword' => [
                                'value' => $keywords,
                                'boost' => 1  // 低权重
                            ]
                        ]
                    ];
                    $should[] = [
                        'term' => [
                            'categories.name.keyword' => [
                                'value' => $keywords,
                                'boost' => 1 // 低权重
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
                MovieKeywordsService::do($keywords);
            }
        }
        if (!empty($catId)) {
            $query['query']['bool']['must'][] = ['terms' => ['categories.id' => explode(',', $catId)]];
            unset($catId);
        }
        if (!empty($tagId)) {
            $query['query']['bool']['must'][] = ['terms' => ['tags.id' => explode(',', $tagId)]];
            unset($tagId);
        }
        if (!empty($homeId)) {
            $query['query']['bool']['must'][] = ['term' => ['user_id.id' => $homeId]];
            unset($homeId);
        }
        if (!empty($xFilter)) {
            $query['query']['bool']['must'][] = ['term' => ['x_filter' => $xFilter]];
            unset($xFilter);
        }
        if (!empty($homeIds)) {
            $query['query']['bool']['must'][] = ['terms' => ['user_id.id' => explode(',', $homeIds)]];
            unset($homeIds);
        }
        if (!empty($payType)) {
            $query['query']['bool']['must'][] = ['term' => ['pay_type' => $payType]];
            unset($payType);
        }
        if (!empty($canvas)) {
            $query['query']['bool']['must'][] = ['term' => ['canvas' => $canvas]];
            unset($canvas);
        }
        if (!empty($position) && $position != 'all') {
            $query['query']['bool']['must'][] = ['terms' => ['position' => ['all', $position]]];
            unset($position);
        }
        if (!empty($kouling)) {
            $query['query']['bool']['must'][] = ['multi_match' => ['query' => $kouling, 'type' => 'phrase', 'fields' => ['kouling']]];
            unset($kouling);
        }
        if (!empty($number)) {
            $query['query']['bool']['must'][] = ['term' => ['number' => strtolower($number)]];
            unset($number);
        }
        if (!empty($duration)) {
            if (preg_match('/^(gte|lte|gt|lt)(\d+)$/', $duration, $m)) {
                $op                               = $m[1];   // gte
                $val                              = $m[2];   // 300
                $query['query']['bool']['must'][] = ['range' => ['duration' => [$op => intval($val)]]];
            }
            unset($duration);
        }
        if (!empty($ids)) {
            $query['query']['bool']['must'][] = ['terms' => ['id' => explode(',', $ids)]];
        }
        if (!empty($notIds)) {
            $notIds = explode(',', $notIds);
            foreach ($notIds as $key => $notId) {
                if ($notId) {
                    $notIds[$key] = intval($notId);
                } else {
                    unset($notIds[$key]);
                }
            }
            $query['query']['bool']['must_not'][] = [
                'ids' => ['values' => $notIds]
            ];
            unset($notIds);
        }
        $items  = [];
        $result = ElasticService::search($query, 'movie', 'movie');

        // 获取计数器
        $redisCounterKeys = [];
        foreach ($result['hits']['hits'] as $item) {
            $id                 = $item['_source']['id'];
            $redisCounterKeys[] = 'movie_click_' . $id;
            $redisCounterKeys[] = 'movie_love_' . $id;
            $redisCounterKeys[] = 'movie_favorite_' . $id;
            $redisCounterKeys[] = 'movie_comment_ok_' . $id;
            //            $redisCounterKeys[] = "movie_comment_no_".$id;;
        }

        $counterMap = CommonService::getRedisCounters($redisCounterKeys);

        foreach ($result['hits']['hits'] as $item) {
            $item = $item['_source'];
            $item = [
                'id'   => strval($item['id']),
                'name' => value(function () use ($item, $language) {
                    $name = $item['name' . $language] ?? $item['name'];
                    return strval($name);
                }),
                'user_id' => value(function () use ($item) {
                    $rows = [];
                    foreach ($item['user_id'] as $user) {
                        $rows[] = [
                            'id'       => strval($user['id']),
                            'nickname' => strval($user['nickname']),
                            'username' => strval($user['username']),
                            'headico'  => CommonService::getCdnUrl($user['headico']),
                        ];
                    }
                    return $rows;
                }),
                'type' => 'video',
                //                //列表用不到描述
                //                'description' => strval($item['description']),

                'img'      => CommonService::getCdnUrl((($canvas == 'short' || $thumb == 'short') && !empty($item['img_y'])) ? $item['img_y'] : $item['img_x']),
                'pay_type' => strval($item['pay_type']),
                'money'    => strval($item['money']),
                'category' => $item['categories']['name'] ?? '',
                'click'    => value(function () use ($item, $counterMap) {
                    $keyName = 'movie_click_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['click'] + $real)));
                }),
                'love' => value(function () use ($item, $counterMap) {
                    $keyName = 'movie_love_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['love'] + $real)));
                }),
                'favorite' => value(function () use ($item, $counterMap) {
                    $keyName = 'movie_favorite_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['favorite'] + $real)));
                }),
                //                 //列表用不到评论数量
                //                'comment' => value(function () use ($item,$counterMap) {
                //                    $ok = $counterMap['movie_comment_ok_' . $item['id']] ?? 0;
                // //                    $no = $counterMap['movie_comment_no_' . $item['id']] ?? 0;
                //                    return strval(CommonUtil::formatNum(intval($ok)));
                //                }),
                // 小图标
                'icon' => value(function () use ($item) {
                    if (!empty($item['icon'])) {
                        return $item['icon'];
                    }
                    if ($item['pay_type'] == 'money') {
                        return 'money';
                    }
                    return '';
                }),
                'duration' => value(function () use ($item) {
                    $duration = $item['links'][0]['duration'] ?? '0';
                    if ($duration > 0) {
                        return strval(CommonUtil::formatSecond($duration));
                    }
                    $count = count($item['links']);
                    return $item['update_status'] == 1 ? "全{$count}集" : "更新至{$count}集";
                }),
                'width'  => strval($item['width']),
                'height' => strval($item['height']),
                'canvas' => strval($item['canvas']),

                'img_width'  => strval($item['img_width']),
                'img_height' => strval($item['img_height']),

                'time_label' => CommonUtil::showTimeDiff($item['show_at']),
                'show_at'    => !empty($item['show_at']) ? CommonUtil::ucTimeAgo(intval($item['show_at'])) : '',
                'tags'       => value(function () use ($item) {
                    if (empty($item['tags'])) {
                        return [];
                    }
                    $tags  = [];
                    $index = 0;
                    foreach ($item['tags'] as $tag) {
                        if ($index > 3) {
                            break;
                        }
                        $tags[] = [
                            'id'   => strval($tag['id']),
                            'name' => strval($tag['name']),
                        ];
                        $index++;
                    }
                    return $tags;
                }),

                'kouling' => strval($item['kouling']),
                'link'    => '',
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
            JobService::create(new EventBusJob(new MovieSearchKeywordPayload($userId, $keywords, $result['hits']['total']['value'] ?? 0)));
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
        /*
         *
            'normal' => '视频',
            'dark' => '暗网',
            'cartoon'=>'动漫',
            'short' => '短剧',
            'douyin'=>'抖音',
            'bl'=>'蓝颜',
            'movie' => '影视',
        */
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
        $mediaService->asyncMovieByCategory($category, $saveMrsData);
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
        if (empty($mrsData['id']) || empty($mrsData['name']) || empty($mrsData['img_x'])) {
            return null;
        }
        if (empty($mrsData['links'])) {
            return null;
        }

        $tags  = [];
        $catId = '';
        /***私有库同步标签  非私有不同步**/
        if ($isPublic === false) {
            if ($mrsData['category']['id']) {
                $category = MovieCategoryModel::findFirst(['code' => $mrsData['category']['id']]);
                if (empty($category)) {
                    $catId = MovieCategoryModel::save([
                        'code'     => $mrsData['category']['id'],
                        'name'     => $mrsData['category']['name'],
                        'position' => 'all',
                        'is_hot'   => 0
                    ]);
                } else {
                    $catId = $category['_id'];
                }
            }
            if ($mrsData['tags']) {
                foreach ($mrsData['tags'] as $tag) {
                    if (empty($tag)) {
                        continue;
                    }
                    $tagModel = MovieTagModel::findByID(intval($tag['id']));
                    if (empty($tagModel)) {
                        $tags[] = MovieTagModel::insert([
                            '_id'       => $tag['id'] * 1,
                            'name'      => $tag['name'],
                            'is_hot'    => 0,
                            'attribute' => $tag['group'] ?: '',
                        ]);
                    } else {
                        if (empty($tagModel['attribute']) && !empty($tag['group'])) {
                            MovieTagModel::save([
                                '_id'       => $tagModel['_id'],
                                'attribute' => $tag['group']
                            ]);
                        }
                        $tags[] = $tagModel['_id'];
                    }
                }
            }
        }
        $movieModel = MovieModel::findFirst(['mid' => $mrsData['mid']]);
        if (empty($movieModel)) {
            // $positions = array_keys( CommonValues::getMoviePosition());
            $movieSaveData = [
                '_id' => $mrsData['id'],
                'mid' => $mrsData['mid'],
                // /查询演员 av项目才需要
                'user_id' => value(function () use ($mrsData) {
                    return [];
                    $actors = $mrsData['source_actor'];
                    $result = [];
                    foreach ($actors as $actor) {
                        // /生成用户和up
                        $userRow = UserUpModel::findByID($actor['id']);
                        if (empty($userRow)) {
                            $userRow = UserService::register('username', uniqid(), uniqid('phone_'), 'android', '1.0', '', 'system', '', $actor['name'], '', $actor['id']);
                            UserUpService::do($userRow['_id'], $userRow['nickname'], $userRow['headico'], 'jp_av', true);
                        }
                        $result[] = $userRow['_id'];
                    }
                    return $result;
                }),
                'categories'     => $catId ?? null,
                'tags'           => $tags,
                'name'           => $mrsData['name'],
                'number'         => $mrsData['number'] ?: uniqid('FH_'),
                'img_x'          => $mrsData['img_x'],
                'img_y'          => $mrsData['img_y'],
                'sort'           => 0,
                'favorite'       => rand(3000, 5000),
                'real_favorite'  => 0,
                'love'           => rand(3000, 5000),
                'real_love'      => 0,
                'dislove'        => 0,
                'real_dislove'   => 0,
                'click'          => rand(800000, 1100000),
                'real_click'     => 0,
                'favorite_rate'  => 0,
                'hot_rate'       => 0,
                'click_total'    => 0,
                'love_total'     => 0,
                'favorite_total' => 0,
                'dislove_total'  => 0,

                'score'          => rand(92, 96),
                'buy'            => 0,
                'comment'        => 0,
                'money'          => 0,
                'pay_type'       => 'vip',
                'width'          => $mrsData['width'] * 1,
                'height'         => $mrsData['height'] * 1,
                'position'       => $mrsData['position'],
                'canvas'         => $mrsData['canvas'],
                'status'         => 0, // 默认未上架
                'is_more_link'   => $mrsData['is_more_link'] * 1,
                'description'    => $mrsData['description'] ?: '',
                'show_at'        => mt_rand(time() - 24 * 3600 * 10, time()),
                'preview_images' => $mrsData['preview_images'] ?: [],
                'update_status'  => $mrsData['update_status'] * 1,
                'publisher'      => strval($mrsData['publisher']),
                'issue_date'     => strval($mrsData['issue_date']),
                'icon'           => '',
                'language'       => strval($mrsData['language']),
                'director'       => '',
                'links'          => $mrsData['links']
            ];
            $movieId = MovieModel::insert($movieSaveData);
        } else {
            $movieSaveData = [
                '_id'            => $movieModel['_id'],
                'name'           => $mrsData['name'],
                'img_x'          => $mrsData['img_x'],
                'img_y'          => $mrsData['img_y'],
                'width'          => $mrsData['width'] * 1,
                'height'         => $mrsData['height'] * 1,
                'is_more_link'   => $mrsData['is_more_link'] * 1,
                'update_status'  => $mrsData['update_status'] * 1,
                'publisher'      => strval($mrsData['publisher']),
                'issue_date'     => strval($mrsData['issue_date']),
                'preview_images' => $mrsData['preview_images'] ?: [],
                'links'          => $mrsData['links']
            ];
            $movieId = MovieModel::save($movieSaveData);
        }
        return $movieId;
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
        $mediaService->asyncMovieByIds($mids, $saveMrsData);
        return true;
    }

    /**
     * 购买视频
     * @param                    $userId
     * @param                    $movieId
     * @param                    $linkId
     * @return true
     * @throws BusinessException
     */
    public static function doBuy($userId, $movieId, $linkId)
    {
        $movieId = strval($movieId);
        $linkId  = strval($linkId);
        if (empty($movieId) || empty($linkId)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请选择要购买的视频!');
        }
        // 此处可以判断下视频分类,如果是连续剧等正常视频,可以不要linkId
        $hasBuy = UserBuyLogService::has($userId, $movieId, 'movie', $linkId);
        if ($hasBuy) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '您已经购买过了!');
        }
        $movieRow = MovieModel::findByID($movieId);
        if (empty($movieRow) || $movieRow['status'] != 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '视频已下架!');
        }
        $money = $movieRow['money'];
        if ($money < 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '此视频无需购买!');
        }

        $userRow = UserModel::findByID(intval($userId));
        UserService::checkDisabled($userRow);

        $homeRow = [];
        if (!empty($movieRow['user_id'])) {
            $homeRow = UserModel::findByID(intval($movieRow['user_id'][0]));
        }
        if ($movieRow['position'] == 'dark') {
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
        $orderSn = CommonUtil::createOrderNo('VIDEO');
        if ($money > 0) {
            AccountService::reduceBalance($userRow, $orderSn, $money, 8, 'balance', "购买视频消耗:{$money},视频ID:{$movieId} " . ($linkId ? "集数:{$linkId}" : ''));

            if (!empty($homeRow)) {
                // 创作者计算分成比例
                $homeInfo  = UserService::getInfoFromCache($homeRow['_id']);
                $saleRate  = intval($homeInfo['creator']['movie_fee_rate']);
                $saleMoney = round($money * $saleRate / 100, 2);
                if ($saleRate > 0 && $saleRate <= 100 && $saleMoney > 0) {
                    AccountService::addBalance(
                        $homeRow,
                        $orderSn,
                        $saleMoney,
                        10,
                        'balance_income',
                        "销售视频收益:{$saleMoney},({$movieRow['name']})"
                    );
                }
            }
        }
        UserBuyLogService::do($orderSn, $userRow, $movieId, 'movie', $movieRow['img_x'], $money, $movieRow['money'], $movieRow['position'], $linkId);
        self::handler('buy', $movieId, $money);
        JobService::create(new EventBusJob(new MovieBuyPayload($userId, $movieId, $orderSn, $money, $userRow['balance'], $userRow['balance'] - $money)));
        return true;
    }

    /**
     * @param       $action
     * @param       $movieId
     * @param       $money
     * @return void
     */
    public static function handler($action, $movieId, $money = null)
    {
        switch ($action) {
            case 'click':
                CommonService::updateRedisCounter("movie_click_{$movieId}", 1);
                MovieModel::updateRaw(['$inc' => ['real_click' => 1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'click', 1);
                break;
            case 'buy':
                MovieModel::updateRaw(['$inc' => ['buy' => 1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'buy_num', 1);
                ReportMovieLogService::inc($movieId, 'buy_total', intval($money));
                break;
            case 'favorite':
                CommonService::updateRedisCounter("movie_favorite_{$movieId}", 1);
                MovieModel::updateRaw(['$inc' => ['real_favorite' => 1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'favorite', 1);
                break;
            case 'unFavorite':
                CommonService::updateRedisCounter("movie_favorite_{$movieId}", -1);
                MovieModel::updateRaw(['$inc' => ['real_favorite' => -1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'favorite', -1);
                break;
            case 'love':
                CommonService::updateRedisCounter("movie_love_{$movieId}", 1);
                MovieModel::updateRaw(['$inc' => ['real_love' => 1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'love', 1);
                break;
            case 'unLove':
                CommonService::updateRedisCounter("movie_love_{$movieId}", -1);
                MovieModel::updateRaw(['$inc' => ['real_love' => -1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'love', -1);
                break;
            case 'disLove':
                CommonService::updateRedisCounter("movie_dislove_{$movieId}", 1);
                MovieModel::updateRaw(['$inc' => ['real_dislove' => 1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'dislove', 1);
                break;
            case 'unDisLove':
                CommonService::updateRedisCounter("movie_dislove_{$movieId}", -1);
                MovieModel::updateRaw(['$inc' => ['real_dislove' => -1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'dislove', -1);
                break;
            case 'download':
                MovieModel::updateRaw(['$inc' => ['download' => 1]], ['_id' => $movieId]);
                ReportMovieLogService::inc($movieId, 'download', 1);
                break;
        }
    }

    /**
     * 广告
     * @param        $ad
     * @param  mixed $item
     * @return array
     */
    private static function getAdItem($item)
    {
        $row = [
            'id'       => strval($item['id']),
            'name'     => strval($item['name']),
            'user_id'  => [],
            'type'     => 'ad',
            'img'      => CommonService::getCdnUrl($item['content']),
            'pay_type' => 'free',
            'money'    => '0',
            'category' => '',
            'click'    => strval(CommonUtil::formatNum(rand(10000, 100000))),
            'love'     => strval(CommonUtil::formatNum(rand(500, 10000))),
            'favorite' => strval(CommonUtil::formatNum(rand(1000, 10000))),
            //            'comment' => '0',
            'icon'       => '',
            'duration'   => '',
            'width'      => '',
            'height'     => '',
            'canvas'     => '',
            'time_label' => '',
            'tags'       => [],

            'link' => strval($item['link']),
        ];
        return $row;
    }
}
