<?php

namespace App\Services\Novel;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Novel\NovelBuyPayload;
use App\Jobs\Event\Payload\Novel\NovelSearchKeywordPayload;
use App\Models\Common\CommentModel;
use App\Models\Novel\NovelChapterModel;
use App\Models\Novel\NovelModel;
use App\Models\Novel\NovelTagModel;
use App\Models\User\UserModel;
use App\Services\Common\AdvService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\ElasticService;
use App\Services\Common\JobService;
use App\Services\Report\ReportNovelLogService;
use App\Services\User\AccountService;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;
use Phalcon\Manager\MediaLSJService;
use Phalcon\Manager\MediaService;
use Phalcon\Manager\MediaTangXinService;

/**
 * 有声
 */
class NovelService extends BaseService
{
    /**
     * @param $novelId
     * @return bool
     */
    public static function has(string $id)
    {
        return NovelModel::count(['_id' => $id]) > 0;
    }

    /**
     * 从缓存中获取信息
     * @param $id
     * @return array|mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getInfoCache($id)
    {
        $keyName = "novel_detail_{$id}";
        $result = cache()->get($keyName);
        if (is_null($result)) {
            $result = ElasticService::get($id, 'novel', 'novel');
            cache()->set($keyName, $result, 300);
        }
        if (empty($result) || $result['status'] != 1) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '小说不存在或已下架!');
        }
        return $result;
    }

    /**
     * 同步到es
     * @param $id
     * @return bool
     */
    public static function asyncEs($id)
    {
        $id = strval($id);
        $row = NovelModel::findByID($id);
        if (empty($row)) {
            return false;
        }
        if ($row['status'] != 1) {
            ElasticService::delete("novel", "novel", $id);
            return true;
        }

        $row['tags'] = NovelTagService::getByIds($row['tags']);

        $row['kouling'] = CommonUtil::getKouling($id, 'C-');
        $row['id'] = $row['_id'];
        $row['last_update'] = intval($row['last_update']);
        $row['show_at'] = intval($row['show_at']);
        $row['update_date_value'] = empty($row['update_date']) ? 0 : CommonValues::getComicsWeek($row['update_date']) * 1;
        //es分词 汉字支持比较差 如果实在需要使用 需要配置字段为关键字类型
        //$row['cat_code'] = CommonValues::getComicsCategoriesCode($row['cat_id']);

        $commentOk = CommentModel::count(['object_id' => $id, 'object_type' => 'novel', 'status' => 1]);//已通过审核
        $commentNo = CommentModel::count(['object_id' => $id, 'object_type' => 'novel', 'status' => 0]);//未通过审核

        CommonService::setRedisCounter("novel_click_{$id}", $row['real_click']);
        CommonService::setRedisCounter("novel_favorite_{$id}", $row['real_favorite']);
        CommonService::setRedisCounter("novel_love_{$id}", $row['real_love']);
        CommonService::setRedisCounter("novel_comment_ok_{$id}", $commentOk);
        CommonService::setRedisCounter("novel_comment_no_{$id}", $commentNo);

        NovelModel::updateById([
            'async_at' => time(),
            'click_total' => $row['real_click'] + $row['click'],
            'love_total' => $row['real_love'] + $row['love'],
            'favorite_total' => $row['real_favorite'] + $row['favorite'],
            'comment' => intval($commentOk + $commentNo)
        ], $id);
        unset($row['_id']);
        return ElasticService::save($row['id'], $row, 'novel', 'novel');
    }

    /**
     * @param $id
     * @return true
     * @throws \Phalcon\Storage\Exception
     */
    public static function delete(string $id)
    {
        NovelModel::deleteById($id);
        NovelChapterModel::delete(['novel_id' => $id]);
        ElasticService::delete('novel', 'novel', $id);
        self::delCache($id);
        return true;
    }

    /**
     * 删除缓存
     * @param $id
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public static function delCache(string $id)
    {
        cache()->delete("novel_detail_{$id}");
    }

    /**
     * 搜索
     * @param array $filter
     * @return array
     */
    public static function doSearch(array $filter = [],$userId=null)
    {
        $page       = $filter['page'] ?: 1;
        $pageSize   = $filter['page_size'] ?: 16;
        $keywords   = strval($filter['keywords']);
        $payType    = strval($filter['pay_type']);
        $catId      = strval($filter['cat_id']);
        $tagId      = strval($filter['tag_id']);
        $isEnd      = strval($filter['is_end']);
        $icon       = strval($filter['icon']);
        $ids        = strval($filter['ids']);
        $notIds     = strval($filter['not_ids']);
        $updateDate = strval($filter['update_date']);
        $updateStatus = strval($filter['update_status']);
        $adCode     = strval($filter['ad_code']);
        $order      = $filter['order'] ?: '';
        $kouling    = $filter['kouling'];
        $language   = $filter['language'];

        $from = ($page - 1) * $pageSize;
        $source = array();
        $query = array(
            'from' => $from,
            'size' => $pageSize,
            'min_score' => 1.0,
            '_source' => $source,
            'query' => [
                'bool' => [
                    'must' => []
                ]
            ]
        );
        if (empty($filter['is_all'])) {
            $query['query']['bool']['must'][] = array(
                'term' => array('status' => 1)
            );
        }
        switch ($order) {
            case "click":
                $query['sort'] = [
                    ['click_total' => ['order' => 'desc']],
                ];
                break;
            case "click7":
                $ids = ReportNovelLogService::getIds('click','week',$page,$pageSize);
                $ids = join(',',$ids);
                break;
            case "click30":
                $ids = ReportNovelLogService::getIds('click','month',$page,$pageSize);
                $ids = join(',',$ids);
                break;
            case 'love':
                $query['sort'] = [
                    ['love_total' => ['order' => 'desc']],
                ];
                break;
            case "love7":
                $ids = ReportNovelLogService::getIds('love','week',$page,$pageSize);
                $ids = join(',',$ids);
                break;
            case "love30":
                $ids = ReportNovelLogService::getIds('love','month',$page,$pageSize);
                $ids = join(',',$ids);
                break;
            case 'favorite':
                $query['sort'] = [
                    ['favorite_total' => ['order' => 'desc']],
                ];
                break;
            case "favorite7":
                $ids = ReportNovelLogService::getIds('favorite','week',$page,$pageSize);
                $ids = join(',',$ids);
                break;
            case "favorite30":
                $ids = ReportNovelLogService::getIds('favorite','month',$page,$pageSize);
                $ids = join(',',$ids);
                break;
            case 'buy':
                $query['sort'] = [
                    ['buy' => ['order' => 'desc']],
                ];
                break;
            case "sort":
                $query['sort'] = [
                    ['sort' => ['order' => 'desc']],
                ];
                break;
            case 'new':
                $query['sort'] = [
                    ['show_at' => ['order' => 'desc']],
                ];
                break;
            case "rand":
                $query['sort'] = [
                    [
                        '_script' => [
                            "script" => 'Math.random()',
                            "type" => "number",
                            "order" => "asc"
                        ]
                    ],
                ];
                break;
            case "update_date":
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

        //关键字
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
                        ///类似like
                        'wildcard' => [
                            'name.wild' => [
                                'value' => "*{$keywords}*",
                                'case_insensitive' => true,
                                'boost' => 5  // 高权重
                            ]
                        ]
                    ];
                }
                // 第二优先级：分词匹配 name（支持中文分词）
                $should[] = [
                    'multi_match' => [
                        'query' => $keywords,
                        "fields" => ["name"],
                        'boost' => 3  // 中等权重
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
            $query['min_score'] = 2.0;
            array_unshift($query['sort'], ['_score' => ['order' => 'desc']]);//优先按评分
//            $query['track_scores'] = true;///调试用,如果添加了自定义sort _source将会返回null,所以可以手动开启
            if(mb_strlen($keywords)<15){
                NovelKeywordsService::do($keywords);
            }
        }
        if (!empty($catId)) {
            $query['query']['bool']['must'][]=['term' => ['cat_id' => $catId]];
            unset($catId);
        }
        if (!empty($tagId)) {
            $query['query']['bool']['must'][]=['terms' => ['tags.id' => explode(',', $tagId)]];
            unset($tagId);
        }
        if (!empty($payType)) {
            $query['query']['bool']['must'][]=['term' => ['pay_type' => $payType]];
            unset($payType);
        }
        if (!empty($isEnd)) {
            $query['query']['bool']['must'][]=['term' => ['update_status' => $isEnd == 'y' ? 1 : 0]];
            unset($isHot);
        }
        if (!empty($icon)) {
            $query['query']['bool']['must'][]=['term' => ['icon' => $icon]];
            unset($icon);
        }
        if (!empty($updateDate)) {
            ///数字1-7
            $query['query']['bool']['must'][]=['term' => ['update_date_value' => $updateDate]];
            unset($updateDate);
        }
        if (!empty($updateStatus)) {
            $query['query']['bool']['must'][]=['term' => ['update_status' => $updateStatus=='y'?1:0]];
            unset($updateStatus);
        }
        if (!empty($kouling)) {
            $query['query']['bool']['must'][] = ['multi_match' => ['query' => $kouling, "type" => "phrase", 'fields' => ['kouling']]];
            unset($kouling);
        }
        if (!empty($ids)) {
            $query['query']['bool']['must'][]=['terms' => ['id' => explode(',', $ids)]];
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
            $query['query']['bool']['must_not'][] = array(
                'ids' => array('values' => $notIds)
            );
            unset($notIds);
        }

        $items = array();
        $result = ElasticService::search($query, 'novel', 'novel');

        //获取计数器
        $redisCounterKeys = [];
        foreach ($result['hits']['hits'] as $item) {
            $id = $item['_source']['id'];
            $redisCounterKeys[] = "novel_click_".$id;
            $redisCounterKeys[] = "novel_love_".$id;
            $redisCounterKeys[] = "novel_favorite_".$id;
            $redisCounterKeys[] = "novel_comment_ok_".$id;;
//            $redisCounterKeys[] = "movie_comment_no_".$id;;
        }
        $counterMap = CommonService::getRedisCounters($redisCounterKeys);

        foreach ($result['hits']['hits'] as $item) {
            $item = $item['_source'];
            $item = [
                'id' => strval($item['id']),
                'name' => value(function ()use($item,$language){
                    $name = $item['name' . $language] ?? $item['name'];
                    return strval($name);
                }),
                'alias_name' => strval($item['alias_name']),
                'author' => strval($item['author']),
                'sub_name' => value(function () use ($item) {
                    if ($item['update_status'] == 1) {
                        return '共' . $item['chapter_count'] . '章';
                    } else {
                        return '更新' . $item['chapter_count'] . '章';
                    }
                }),
                'type' => 'novel',
                'img' => CommonService::getCdnUrl($item['img_x']),
                'description' => value(function ()use($item,$language){
                    $name = $item['description' . $language] ?? $item['description'];
                    return strval($name);
                }),
                'pay_type' => strval($item['pay_type']),
                'score' => strval($item['score']),
                'money' => strval($item['money']),
                'category' => $item['cat_id'] ?? '',
                'click' => value(function () use ($item,$counterMap) {
                    $keyName = 'novel_click_' . $item['id'];
                    $real = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['click'] + $real)));
                }),
                'love' => value(function () use ($item,$counterMap) {
                    $keyName = 'novel_love_' . $item['id'];
                    $real = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['love'] + $real)));
                }),
                'favorite' => value(function () use ($item,$counterMap) {
                    $keyName = 'novel_favorite_' . $item['id'];
                    $real = $counterMap[$keyName] ?? 0;
                    return strval(CommonUtil::formatNum(intval($item['favorite'] + $real)));
                }),
//                 //列表用不到评论数量
//                'comment' => value(function () use ($item,$counterMap) {
//                    $ok = $counterMap['novel_comment_ok_' . $item['id']] ?? 0;
////                    $no = $counterMap['novel_comment_no_' . $item['id']] ?? 0;
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
                'update_date' => strval($item['update_date']),
                'show_at' => !empty($item['show_at']) ? CommonUtil::ucTimeAgo(intval($item['show_at'])) : '',
                'is_adult' => $item['is_adult'] == 1 ? 'y' : 'n',
                'tags' => value(function () use ($item) {
                    if (empty($item['tags'])) {
                        return array();
                    }
                    $tags = array();
                    $index = 0;
                    foreach ($item['tags'] as $tag) {
                        if ($index > 3) {
                            break;
                        }
                        $tags[] = [
                            'id' => strval($tag['id']),
                            'name' => strval($tag['name']),
                        ];
                        $index++;
                    }
                    return $tags;
                }),

                'kouling' => strval($item['kouling']),
                'link' => '',
            ];
            $items[] = $item;
        }
        //排行榜结果集重排
        if(in_array($order,['click7','click30','love7','love30','favorite7','favorite30'])&&!empty($ids)) {
            $idArr = explode(',', $ids);
            $items = CommonUtil::arraySort($items, 'id', $idArr);
        }
        //埋点
        if(!empty($userId)&&!empty($keywords)&&mb_strlen($keywords)<15){
            JobService::create(new EventBusJob(new NovelSearchKeywordPayload($userId,$keywords,$result['hits']['total']['value']??0)));
        }

        if (!empty($adCode)) {
            $items = AdvService::insertAdsToListByPage($items, $adCode, 5, 4, $page,$pageSize,true);
            foreach ($items as $key => $item) {//组装广告数据
                if ($item['_ad']) {
                    $items[$key] = self::getAdItem($item);
                }
            }
            unset($adCode);
        }

        $items = array_values($items);
        $result = [
            'data' => $items,
            'total' => $result['hits']['total']['value'] ? strval($result['hits']['total']['value']) : '0',
            'current_page' => strval($page),
            'page_size' => strval($pageSize),
        ];
        $result['last_page'] = strval(ceil($result['total'] / $pageSize));
        return $result;

    }


    /**
     * 广告
     * @param $item
     * @return array
     */
    private static function getAdItem($item)
    {
        $row = [
            'id' => strval($item['id']),
            'name' => strval($item['name']),
            'alias_name' => '',
            'author' => '广告',
            'sub_name' => '',
            'type' => 'ad',
            'img' => CommonService::getCdnUrl($item['content']),
            'description' => '',
            'pay_type' => 'free',
            'score' => '',
            'money' => '0',
            'category' => '',
            'click' => strval(CommonUtil::formatNum(rand(10000, 100000))),
            'love' => strval(CommonUtil::formatNum(rand(500, 10000))),
            'favorite' => strval(CommonUtil::formatNum(rand(1000, 10000))),
            'comment' => '0',
            'icon' => '',
            'update_status' => '',
            'update_date' => '',
            'show_at' => '',
            'is_adult' => '',
            'tags' => [],

            'kouling' => '',
            'link' => strval($item['link']),
        ];
        return $row;
    }

    /**
     * @param string $source
     * @param string $category
     * @return true
     */
    public static function asyncMrsByCat(string $category, $source = null)
    {
        if (!in_array($category, ['18R', 'normal'])) {
            throw new \Exception('仅支持 18R normal');
        }

        if ($source == 'xiaozu') {
            $mediaUrl = ConfigService::getConfig('xiaozu_media_api');
            $mediaKey = ConfigService::getConfig('xiaozu_media_key');
            $isPublic = false;
            $mediaService = new MediaService($mediaUrl, $mediaKey);
        }else if ($source == 'tangxin') {
            $mediaUrl = ConfigService::getConfig('tangxin_media_api');
            $mediaKey = ConfigService::getConfig('tangxin_media_key');
            $isPublic = false;
            $mediaService = new MediaTangXinService($mediaUrl, $mediaKey);
        } else {
            $mediaUrl = ConfigService::getConfig('media_api');
            $mediaAppid = ConfigService::getConfig('media_appid');
            $mediaKey = ConfigService::getConfig('media_key');
            $isPublic = false;
            $mediaService = new MediaLSJService($mediaUrl, $mediaKey,$mediaAppid);
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
     * @param $mrsData
     * @param $isPublic
     * @return bool
     */
    public static function saveMrsData($mrsData, $isPublic = false)
    {
        if (empty($mrsData['id']) || empty($mrsData['name']) || empty($mrsData['img_x']) || empty($mrsData['chapter'])) {
            return false;
        }
        if ($mrsData['category'] == 'audio') {
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
                    $tagModel = NovelTagModel::findFirst(['_id' => $tag['id']]);
                    if (empty($tagModel)) {
                        $tags[] = NovelTagModel::insert([
                            '_id' => $tag['id'] * 1,
                            'name' => $tag['name'],
                            'is_hot' => 0,
                            'attribute' => $tag['group'] ? $tag['group'] : ''
                        ]);
                    } else {
                        if (empty($tagModel['attribute']) && !empty($tag['group'])) {
                            NovelTagModel::save(array(
                                '_id' => $tagModel['_id'],
                                'attribute' => $tag['group']
                            ));
                        }
                        $tags[] = $tagModel['_id'];
                    }
                }
            }
        }
        $mid = $mrsData['id'];
        $novelModel = NovelModel::findFirst(['_id' => $mid]);
        if (empty($novelModel)) {
            $novelSaveData = [
                '_id' => $mid,
                'name' => $mrsData['name'],
                'alias_name' => $mrsData['alias_name'],
                'author' => $mrsData['author'],
                'cat_id' => $mrsData['category'],
                'tags' => $tags,
                'img_x' => $mrsData['img_x'],
                'img_y' => $mrsData['img_y'],
                'click' => rand(800000, 1100000),
                'real_click' => 0,
                'love' => rand(3000, 5000),
                'real_love' => 0,
                'favorite' => rand(3000, 5000),
                'real_favorite' => 0,
                'favorite_rate' => 0,
                'click_total' => 0,
                'love_total' => 0,
                'favorite_total' => 0,


                'comment' => 0,
                'buy' => 0,
                'money' => 0,
                'pay_type' => 'vip',
                'score' => rand(92, 96),
                'free_chapter' => '',
                'description' => $mrsData['description'] ?: '',
                'chapter_count' => count($mrsData['chapter']),
                'sort' => 0,
                'is_adult' => intval($mrsData['is_adult']),
                'status' => 0,
                'update_status' => intval($mrsData['update_status']),
                'update_date' => strval($mrsData['update_date']),//更新日期,1 2 3 4 星期几的意思
                'last_update' => intval($mrsData['last_update']),
                'show_at' => mt_rand(time() - 24 * 3600, time()),
                'created_at' => intval($mrsData['created_at']),
                'updated_at' => intval($mrsData['updated_at']),
            ];
            NovelModel::insert($novelSaveData);
        } else {
            $novelSaveData = array(
                'name' => $mrsData['name'],
                'alias_name' => $mrsData['alias_name'],
                'author' => $mrsData['author'],
                'img_x' => $mrsData['img_x'],
                'img_y' => $mrsData['img_y'],
                'is_adult' => intval($mrsData['is_adult']),
                'update_status' => intval($mrsData['update_status']),
                'update_date' => $mrsData['update_date'],
                'last_update' => intval($mrsData['last_update']),
                'chapter_count' => count($mrsData['chapter'])
            );
            NovelModel::update($novelSaveData, array('_id' => $mid));
        }
        foreach ($mrsData['chapter'] as $index => $chapter) {
            $chapterRow = NovelChapterModel::findByID($chapter['id']);
            if (empty($chapterRow)) {
                NovelChapterModel::insert([
                    '_id' => $chapter['id'],
                    'novel_id' => $mid,
                    'name' => $chapter['name'],
                    'img' => $chapter['img'],
                    'sort' => $index + 1,
                    'content' => $chapter['content']
                ]);
            } else {
                NovelChapterModel::updateById([
                    'novel_id' => $mid,
                    'name' => $chapter['name'],
                    'img' => $chapter['img'],
                    'sort' => $index + 1,
                    'content' => $chapter['content']
                ], $chapter['id']);
            }
        }
        return true;
    }

    /**
     * @param string $source
     * @param array $mids
     * @return true
     */
    public static function asyncMrsByIds(array $mids, $source = null)
    {
        if ($source == 'xiaozu') {
            $mediaUrl = ConfigService::getConfig('xiaozu_media_api');
            $mediaKey = ConfigService::getConfig('xiaozu_media_key');
            $isPublic = false;
            $mediaService = new MediaService($mediaUrl, $mediaKey);
        }else if ($source == 'tangxin') {
            $mediaUrl = ConfigService::getConfig('tangxin_media_api');
            $mediaKey = ConfigService::getConfig('tangxin_media_key');
            $isPublic = false;
            $mediaService = new MediaTangXinService($mediaUrl, $mediaKey);
        } else {
            $mediaUrl = ConfigService::getConfig('media_api');
            $mediaAppid = ConfigService::getConfig('media_appid');
            $mediaKey = ConfigService::getConfig('media_key');
            $isPublic = false;
            $mediaService = new MediaLSJService($mediaUrl, $mediaKey,$mediaAppid);
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
     * 购买小说
     * @param $userId
     * @param $novelId
     * @param $chapterId
     * @return true
     * @throws BusinessException
     */
    public static function doBuy($userId,$novelId,$chapterId='')
    {
        $novelId = strval($novelId);
        $chapterId = strval($chapterId);
        $chapterId = '';//一般不用单章解锁
        if(empty($novelId)){
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请选择要购买的视频!');
        }
        $hasBuy = UserBuyLogService::has($userId,$novelId,'novel',$chapterId);
        if($hasBuy){
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '您已经购买过了!');
        }
        $audioRow = NovelModel::findByID($novelId);
        if(empty($audioRow)||$audioRow['status']!=1){
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '小说已下架!');
        }
        $money = $audioRow['money'];
        if($money <1){
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '此小说无需购买!');
        }

        $userRow = UserModel::findByID(intval($userId));
        UserService::checkDisabled($userRow);
        if($audioRow['position']=='dark'){
            $isDarkVip = UserService::isVip($userRow,true);
            if($isDarkVip){
                //获取VIP折扣
                $discountRate=$userRow['group_dark_rate'];
                $money=!empty($discountRate)?round($money*$discountRate/100,0):$money;
            }
        }else{
            $isVip = UserService::isVip($userRow,false);
            if($isVip){
                //获取VIP折扣
                $discountRate=$userRow['group_rate'];
                $money=!empty($discountRate)?round($money*$discountRate/100,0):$money;
            }
        }
        $money=$money<0?0:$money;
        if($userRow['balance'] < $money){
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '可用余额不足!');
        }
        $orderSn=CommonUtil::createOrderNo('NOVEL');
        if($money>0) {
            AccountService::reduceBalance($userRow,$orderSn,$money,8,'balance',"购买有声消耗:{$money},小说ID:{$novelId} ".($chapterId?"章节:{$chapterId}":""));
        }
        UserBuyLogService::do($orderSn,$userRow,$novelId,'novel',$audioRow['img_x'],$money,$audioRow['money'],$audioRow['position'],$chapterId);
        self::handler('buy',$novelId,$money);
        JobService::create(new EventBusJob(new NovelBuyPayload($userId,$novelId,$orderSn,$money,$userRow['balance'],$userRow['balance']-$money)));
        return true;
    }

    /**
     * @param $action
     * @param $novelId
     * @param $money
     * @return void
     */
    public static function handler($action, $novelId, $money = null)
    {
        switch ($action) {
            case 'click':
                CommonService::updateRedisCounter("novel_click_{$novelId}", 1);
                NovelModel::updateRaw(array('$inc' => array('real_click' => 1)), array('_id' => $novelId));
                ReportNovelLogService::inc($novelId, 'click', 1);
                break;
            case 'buy':
                NovelModel::updateRaw(array('$inc' => array('buy' => 1)), array('_id' => $novelId));
                ReportNovelLogService::inc($novelId, 'buy_num', 1);
                ReportNovelLogService::inc($novelId, 'buy_total', intval($money));
                break;
            case 'favorite':
                CommonService::updateRedisCounter("novel_favorite_{$novelId}", 1);
                NovelModel::updateRaw(array('$inc' => array('real_favorite' => 1)), array('_id' => $novelId));
                ReportNovelLogService::inc($novelId, 'favorite', 1);
                break;
            case 'unFavorite':
                CommonService::updateRedisCounter("novel_favorite_{$novelId}", -1);
                NovelModel::updateRaw(array('$inc' => array('real_favorite' => -1)), array('_id' => $novelId));
                ReportNovelLogService::inc($novelId, 'favorite', -1);
                break;
            case 'love':
                CommonService::updateRedisCounter("novel_love_{$novelId}", 1);
                NovelModel::updateRaw(array('$inc' => array('real_love' => 1)), array('_id' => $novelId));
                ReportNovelLogService::inc($novelId, 'love', 1);
                break;
            case 'unLove':
                CommonService::updateRedisCounter("novel_love_{$novelId}", -1);
                NovelModel::updateRaw(array('$inc' => array('real_love' => -1)), array('_id' => $novelId));
                ReportNovelLogService::inc($novelId, 'love', -1);
                break;
        }
    }
}
