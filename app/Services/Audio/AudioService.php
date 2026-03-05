<?php

namespace App\Services\Audio;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Audio\AudioBuyPayload;
use App\Jobs\Event\Payload\Audio\AudioSearchKeywordPayload;
use App\Models\Audio\AudioChapterModel;
use App\Models\Audio\AudioModel;
use App\Models\Audio\AudioTagModel;
use App\Models\Common\CommentModel;
use App\Models\User\UserModel;
use App\Services\Common\AdvService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\ElasticService;
use App\Services\Common\JobService;
use App\Services\Report\ReportAudioLogService;
use App\Services\User\AccountService;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;
use Phalcon\Manager\MediaLSJService;
use Phalcon\Manager\MediaService;
use Phalcon\Manager\MediaTangXinService;

class AudioService extends BaseService
{
    /**
     * @param  string $id
     * @return bool
     */
    public static function has(string $id)
    {
        return AudioModel::count(['_id' => $id]) > 0;
    }

    /**
     * 从缓存中获取信息
     * @param                             $id
     * @return array|mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getInfoCache($id)
    {
        $keyName = "audio_detail_{$id}";
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $result = ElasticService::get($id, 'audio', 'audio');
            cache()->set($keyName, $result, 300);
        }
        if (empty($result) || $result['status'] != 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '小说不存在或已下架!');
        }
        return $result;
    }

    /**
     * 同步到es
     * @param       $id
     * @return bool
     */
    public static function asyncEs($id)
    {
        $id  = strval($id);
        $row = AudioModel::findByID($id);
        if (empty($row)) {
            return false;
        }
        if ($row['status'] != 1) {
            ElasticService::delete('audio', 'audio', $id);
            return true;
        }

        $row['tags'] = AudioTagService::getByIds($row['tags']);

        $row['kouling']           = CommonUtil::getKouling($id, 'C-');
        $row['id']                = $row['_id'];
        $row['last_update']       = intval($row['last_update']);
        $row['show_at']           = intval($row['show_at']);
        $row['update_date_value'] = empty($row['update_date']) ? 0 : CommonValues::getComicsWeek($row['update_date']) * 1;
        // es分词 汉字支持比较差 如果实在需要使用 需要配置字段为关键字类型
        // $row['cat_code'] = CommonValues::getComicsCategoriesCode($row['cat_id']);

        $commentOk = CommentModel::count(['object_id' => $id, 'object_type' => 'audio', 'status' => 1]);// 已通过审核
        $commentNo = CommentModel::count(['object_id' => $id, 'object_type' => 'audio', 'status' => 0]);// 未通过审核

        CommonService::setRedisCounter("audio_click_{$id}", $row['real_click']);
        CommonService::setRedisCounter("audio_favorite_{$id}", $row['real_favorite']);
        CommonService::setRedisCounter("audio_love_{$id}", $row['real_love']);
        CommonService::setRedisCounter("audio_comment_ok_{$id}", $commentOk);
        CommonService::setRedisCounter("audio_comment_no_{$id}", $commentNo);

        AudioModel::updateById([
            'async_at'       => time(),
            'click_total'    => $row['real_click'] + $row['click'],
            'love_total'     => $row['real_love'] + $row['love'],
            'favorite_total' => $row['real_favorite'] + $row['favorite'],
            'comment'        => intval($commentOk + $commentNo)
        ], $id);
        unset($row['_id']);
        return ElasticService::save($row['id'], $row, 'audio', 'audio');
    }

    /**
     * @param                             $id
     * @return true
     * @throws \Phalcon\Storage\Exception
     */
    public static function delete(string $id)
    {
        AudioModel::deleteById($id);
        AudioChapterModel::delete(['audio_id' => $id]);
        ElasticService::delete('audio', 'audio', $id);
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
        cache()->delete("audio_detail_{$id}");
    }

    /**
     * 搜索
     * @param  array      $filter
     * @param  null|mixed $userId
     * @return array
     */
    public static function doSearch(array $filter = [], $userId = null)
    {
        $page         = $filter['page'] ?: 1;
        $pageSize     = $filter['page_size'] ?: 16;
        $keywords     = strval($filter['keywords']);
        $payType      = strval($filter['pay_type']);
        $catId        = strval($filter['cat_id']);
        $tagId        = strval($filter['tag_id']);
        $isEnd        = strval($filter['is_end']);
        $icon         = strval($filter['icon']);
        $ids          = strval($filter['ids']);
        $notIds       = strval($filter['not_ids']);
        $updateDate   = strval($filter['update_date']);
        $updateStatus = strval($filter['update_status']);
        $adCode       = strval($filter['ad_code']);
        $order        = $filter['order'] ?: '';
        $kouling      = $filter['kouling'];
        $language     = $filter['language'];
        $from         = ($page - 1) * $pageSize;
        $source       = [];
        $query        = [
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
        if (empty($filter['is_all'])) {
            $query['query']['bool']['must'][] = [
                'term' => ['status' => 1]
            ];
        }
        switch ($order) {
            case 'click':
                $query['sort'] = [
                    ['click_total' => ['order' => 'desc']],
                ];
                break;
            case 'click7':
                $ids = ReportAudioLogService::getIds('click', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'click30':
                $ids = ReportAudioLogService::getIds('click', 'month', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'love':
                $query['sort'] = [
                    ['love_total' => ['order' => 'desc']],
                ];
                break;
            case 'love7':
                $ids = ReportAudioLogService::getIds('love', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'love30':
                $ids = ReportAudioLogService::getIds('love', 'month', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'favorite':
                $query['sort'] = [
                    ['favorite_total' => ['order' => 'desc']],
                ];
                break;
            case 'favorite7':
                $ids = ReportAudioLogService::getIds('favorite', 'week', $page, $pageSize);
                $ids = join(',', $ids);
                break;
            case 'favorite30':
                $ids = ReportAudioLogService::getIds('favorite', 'month', $page, $pageSize);
                $ids = join(',', $ids);
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
            case 'update_date':
                $query['sort'] = [
                    ['last_update' => ['order' => 'desc']],
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
                        'fields' => ['name'],
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
                AudioKeywordsService::do($keywords);
            }
        }

        if (!empty($catId)) {
            $query['query']['bool']['must'][] = ['term' => ['cat_id' => $catId]];
            unset($catId);
        }
        if (!empty($tagId)) {
            $query['query']['bool']['must'][] = ['terms' => ['tags.id' => explode(',', $tagId)]];
            unset($tagId);
        }
        if (!empty($payType)) {
            $query['query']['bool']['must'][] = ['term' => ['pay_type' => $payType]];
            unset($payType);
        }
        if (!empty($isEnd)) {
            $query['query']['bool']['must'][] = ['term' => ['update_status' => $isEnd == 'y' ? 1 : 0]];
            unset($isHot);
        }
        if (!empty($icon)) {
            $query['query']['bool']['must'][] = ['term' => ['icon' => $icon]];
            unset($icon);
        }
        if (!empty($updateDate)) {
            // /数字1-7
            $query['query']['bool']['must'][] = ['term' => ['update_date_value' => $updateDate]];
            unset($updateDate);
        }
        if (!empty($updateStatus)) {
            $query['query']['bool']['must'][] = ['term' => ['update_status' => $updateStatus == 'y' ? 1 : 0]];
            unset($updateStatus);
        }
        if (!empty($kouling)) {
            $query['query']['bool']['must'][] = ['multi_match' => ['query' => $kouling, 'type' => 'phrase', 'fields' => ['kouling']]];
            unset($kouling);
        }
        if (!empty($ids)) {
            $query['query']['bool']['must'][] = ['terms' => ['id' => explode(',', $ids)]];
        }
        if (!empty($notIds)) {
            $notIds = explode(',', $notIds);
            foreach ($notIds as $key => $notId) {
                if ($notId) {
                    $notIds[$key] = strval($notId);
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
        $result = ElasticService::search($query, 'audio', 'audio');

        // 获取计数器
        $redisCounterKeys = [];
        foreach ($result['hits']['hits'] as $item) {
            $id                 = $item['_source']['id'];
            $redisCounterKeys[] = 'audio_click_' . $id;
            $redisCounterKeys[] = 'audio_love_' . $id;
            $redisCounterKeys[] = 'audio_favorite_' . $id;
            $redisCounterKeys[] = 'audio_comment_ok_' . $id;
            //            $redisCounterKeys[] = "audio_comment_no_".$id;;
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
                'alias_name' => strval($item['alias_name']),
                'author'     => strval($item['author']),
                'sub_name'   => value(function () use ($item) {
                    if ($item['update_status'] == 1) {
                        return '共' . $item['chapter_count'] . '章';
                    }
                    return '更新' . $item['chapter_count'] . '章';
                }),
                'type'        => 'audio',
                'img'         => CommonService::getCdnUrl($item['img_x']),
                'description' => value(function () use ($item, $language) {
                    $name = $item['description' . $language] ?? $item['description'];
                    return strval($name);
                }),
                'pay_type' => strval($item['pay_type']),
                'score'    => strval($item['score']),
                'money'    => strval($item['money']),
                'category' => $item['cat_id'] ?? '',
                'click'    => value(function () use ($item, $counterMap) {
                    $keyName = 'audio_click_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['click'] + $real)));
                }),
                'love' => value(function () use ($item, $counterMap) {
                    $keyName = 'audio_love_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['love'] + $real)));
                }),
                'favorite' => value(function () use ($item, $counterMap) {
                    $keyName = 'audio_favorite_' . $item['id'];
                    $real    = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['favorite'] + $real)));
                }),
                //                 //列表用不到评论数量
                //                'comment' => value(function () use ($item,$counterMap) {
                //                    $ok = $counterMap["audio_comment_ok_{$item['id']}"] ?? 0;
                // //                    $no = $counterMap["audio_comment_no_{$item['id']}"] ?? 0;
                //                    return strval(CommonUtil::formatNum(intval($ok)));
                //                }),
                'icon' => value(function () use ($item) {
                    if (!empty($item['icon'])) {
                        return $item['icon'];
                    }
                    if ($item['pay_type'] == 'free') {
                        return 'free';
                    } elseif (
                        $item['show_at'] + 86400 * 3 > time()) {
                        return 'new';
                    }
                    return '';
                }),
                'update_status' => strval($item['update_status']),
                'update_date'   => strval($item['update_date']),
                'show_at'       => !empty($item['show_at']) ? CommonUtil::ucTimeAgo(intval($item['show_at'])) : '',
                'is_adult'      => $item['is_adult'] == 1 ? 'y' : 'n',
                'tags'          => value(function () use ($item) {
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
            JobService::create(new EventBusJob(new AudioSearchKeywordPayload($userId, $keywords, $result['hits']['total']['value'] ?? 0)));
        }

        if (!empty($adCode)) {
            $items = AdvService::insertAdsToList($items, $adCode, 0, 5, true);
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
     * @param  string $category
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

        $mediaService->asyncNovelByCategory($category, $saveMrsData);
        return true;
    }

    /**
     * 保存媒资库数据
     * @param       $mrsData
     * @param       $isPublic
     * @return bool
     */
    public static function saveMrsData($mrsData, $isPublic = false)
    {
        if (empty($mrsData['id']) || empty($mrsData['name']) || empty($mrsData['img_x']) || empty($mrsData['chapter'])) {
            return false;
        }
        // 只存储有声
        if ($mrsData['category'] != 'audio') {
            return false;
        }
        $tags = [];
        /***私有库同步标签  非私有不同步**/
        if (!$isPublic) {
            if ($mrsData['tags']) {
                foreach ($mrsData['tags'] as $tag) {
                    if (empty($tag)) {
                        continue;
                    }
                    $tagModel = AudioTagModel::findFirst(['_id' => $tag['id']]);
                    if (empty($tagModel)) {
                        $tags[] = AudioTagModel::insert([
                            '_id'       => $tag['id'] * 1,
                            'name'      => $tag['name'],
                            'is_hot'    => 0,
                            'attribute' => $tag['group'] ? $tag['group'] : ''
                        ]);
                    } else {
                        if (empty($tagModel['attribute']) && !empty($tag['group'])) {
                            AudioTagModel::save([
                                '_id'       => $tagModel['_id'],
                                'attribute' => $tag['group']
                            ]);
                        }
                        $tags[] = $tagModel['_id'];
                    }
                }
            }
        }
        $mid        = $mrsData['id'];
        $audioModel = AudioModel::findFirst(['_id' => $mid]);
        if (empty($audioModel)) {
            $audioSaveData = [
                '_id'            => $mid,
                'name'           => $mrsData['name'],
                'alias_name'     => $mrsData['alias_name'],
                'author'         => $mrsData['author'],
                'cat_id'         => $mrsData['category'],
                'tags'           => $tags,
                'img_x'          => $mrsData['img_x'],
                'img_y'          => $mrsData['img_y'],
                'click'          => rand(800000, 1100000),
                'real_click'     => 0,
                'love'           => rand(3000, 5000),
                'real_love'      => 0,
                'favorite'       => rand(3000, 5000),
                'real_favorite'  => 0,
                'favorite_rate'  => 0,
                'click_total'    => 0,
                'love_total'     => 0,
                'favorite_total' => 0,

                'comment'       => 0,
                'buy'           => 0,
                'money'         => 0,
                'pay_type'      => 'vip',
                'score'         => rand(92, 96),
                'free_chapter'  => '',
                'description'   => $mrsData['description'] ?: '',
                'chapter_count' => count($mrsData['chapter']),
                'sort'          => 0,
                'is_adult'      => intval($mrsData['is_adult']),
                'status'        => 0,
                'update_status' => intval($mrsData['update_status']),
                'update_date'   => strval($mrsData['update_date']), // 更新日期,1 2 3 4 星期几的意思
                'last_update'   => intval($mrsData['last_update']),
                'show_at'       => mt_rand(time() - 24 * 3600, time()),
                'created_at'    => intval($mrsData['created_at']),
                'updated_at'    => intval($mrsData['updated_at']),
            ];
            AudioModel::insert($audioSaveData);
        } else {
            $audioSaveData = [
                'name'          => $mrsData['name'],
                'alias_name'    => $mrsData['alias_name'],
                'author'        => $mrsData['author'],
                'img_x'         => $mrsData['img_x'],
                'img_y'         => $mrsData['img_y'],
                'is_adult'      => intval($mrsData['is_adult']),
                'update_status' => intval($mrsData['update_status']),
                'update_date'   => $mrsData['update_date'],
                'last_update'   => intval($mrsData['last_update']),
                'chapter_count' => count($mrsData['chapter'])
            ];
            AudioModel::update($audioSaveData, ['_id' => $mid]);
        }
        foreach ($mrsData['chapter'] as $index => $chapter) {
            $chapterRow = AudioChapterModel::findByID($chapter['id']);
            if (empty($chapterRow)) {
                AudioChapterModel::insert([
                    '_id'      => $chapter['id'],
                    'audio_id' => $mid,
                    'name'     => $chapter['name'],
                    'img'      => $chapter['img'],
                    'sort'     => $index + 1,
                    'content'  => $chapter['content']
                ]);
            } else {
                AudioChapterModel::updateById([
                    'audio_id' => $mid,
                    'name'     => $chapter['name'],
                    'img'      => $chapter['img'],
                    'sort'     => $index + 1,
                    'content'  => $chapter['content']
                ], $chapter['id']);
            }
        }
        return true;
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

        $mediaService->asyncNovelByIds($mids, $saveMrsData);
        return true;
    }

    /**
     * 购买有声
     * @param                    $userId
     * @param                    $audioId
     * @param                    $chapterId
     * @return true
     * @throws BusinessException
     */
    public static function doBuy($userId, $audioId, $chapterId)
    {
        $audioId   = strval($audioId);
        $chapterId = strval($chapterId);
        $chapterId = '';// 一般不用单章解锁
        if (empty($audioId)/* ||empty($chapterId) */) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请选择要购买的有声!');
        }
        $hasBuy = UserBuyLogService::has($userId, $audioId, 'audio', $chapterId);
        if ($hasBuy) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '您已经购买过了!');
        }
        $audioRow = AudioModel::findByID($audioId);
        if (empty($audioRow) || $audioRow['status'] != 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '有声已下架!');
        }
        $money = $audioRow['money'];
        if ($money < 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '此有声无需购买!');
        }

        $userRow = UserModel::findByID(intval($userId));
        UserService::checkDisabled($userRow);
        if ($audioRow['position'] == 'dark') {
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
        $orderSn = CommonUtil::createOrderNo('AUDIO');
        if ($money > 0) {
            AccountService::reduceBalance($userRow, $orderSn, $money, 8, 'balance', "购买有声消耗:{$money},有声ID:{$audioId} " . ($chapterId ? "章节:{$chapterId}" : ''));
        }
        UserBuyLogService::do($orderSn, $userRow, $audioId, 'audio', $audioRow['img_x'], $money, $audioRow['money'], $audioRow['position'], $chapterId);
        self::handler('buy', $audioId, $money);
        JobService::create(new EventBusJob(new AudioBuyPayload($userId, $audioId, $orderSn, $money, $userRow['balance'], $userRow['balance'] - $money)));
        return true;
    }

    /**
     * @param       $action
     * @param       $audioId
     * @param       $money
     * @return void
     */
    public static function handler($action, $audioId, $money = null)
    {
        switch ($action) {
            case 'click':
                CommonService::updateRedisCounter("audio_click_{$audioId}", 1);
                AudioModel::updateRaw(['$inc' => ['real_click' => 1]], ['_id' => $audioId]);
                ReportAudioLogService::inc($audioId, 'click', 1);
                break;
            case 'buy':
                AudioModel::updateRaw(['$inc' => ['buy' => 1]], ['_id' => $audioId]);
                ReportAudioLogService::inc($audioId, 'buy_num', 1);
                ReportAudioLogService::inc($audioId, 'buy_total', intval($money));
                break;
            case 'favorite':
                CommonService::updateRedisCounter("audio_favorite_{$audioId}", 1);
                AudioModel::updateRaw(['$inc' => ['real_favorite' => 1]], ['_id' => $audioId]);
                ReportAudioLogService::inc($audioId, 'favorite', 1);
                break;
            case 'unFavorite':
                CommonService::updateRedisCounter("audio_favorite_{$audioId}", -1);
                AudioModel::updateRaw(['$inc' => ['real_favorite' => -1]], ['_id' => $audioId]);
                ReportAudioLogService::inc($audioId, 'favorite', -1);
                break;
            case 'love':
                CommonService::updateRedisCounter("audio_love_{$audioId}", 1);
                AudioModel::updateRaw(['$inc' => ['real_love' => 1]], ['_id' => $audioId]);
                ReportAudioLogService::inc($audioId, 'love', 1);
                break;
            case 'unLove':
                CommonService::updateRedisCounter("audio_love_{$audioId}", -1);
                AudioModel::updateRaw(['$inc' => ['real_love' => -1]], ['_id' => $audioId]);
                ReportAudioLogService::inc($audioId, 'love', -1);
                break;
        }
    }

    /**
     * 广告
     * @param        $item
     * @return array
     */
    private static function getAdItem($item)
    {
        $row = [
            'id'            => strval($item['id']),
            'name'          => strval($item['name']),
            'alias_name'    => '',
            'author'        => '广告',
            'sub_name'      => '',
            'type'          => 'ad',
            'img'           => CommonService::getCdnUrl($item['content']),
            'description'   => '',
            'pay_type'      => 'free',
            'score'         => '',
            'money'         => '0',
            'category'      => '',
            'click'         => strval(CommonUtil::formatNum(rand(10000, 100000))),
            'love'          => strval(CommonUtil::formatNum(rand(500, 10000))),
            'favorite'      => strval(CommonUtil::formatNum(rand(1000, 10000))),
            'comment'       => '0',
            'icon'          => '',
            'update_status' => '',
            'update_date'   => '',
            'show_at'       => '',
            'is_adult'      => '',
            'tags'          => [],

            'kouling' => '',
            'link'    => strval($item['link']),
        ];
        return $row;
    }
}
