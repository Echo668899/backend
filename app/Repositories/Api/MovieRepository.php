<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Movie\MovieBlockModel;
use App\Models\Movie\MovieModel;
use App\Models\Movie\MovieNavModel;
use App\Models\Movie\MovieTagModel;
use App\Services\Activity\ActivityService;
use App\Services\Common\AdvService;
use App\Services\Common\ApiService;
use App\Services\Common\CommonService;
use App\Services\Common\IpService;
use App\Services\Common\M3u8Service;
use App\Services\Movie\MovieBlockService;
use App\Services\Movie\MovieCategoryService;
use App\Services\Movie\MovieDisLoveService;
use App\Services\Movie\MovieDownloadService;
use App\Services\Movie\MovieFavoriteService;
use App\Services\Movie\MovieHistoryService;
use App\Services\Movie\MovieLoveService;
use App\Services\Movie\MovieService;
use App\Services\Movie\MovieTagService;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserGroupService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

class MovieRepository extends BaseRepository
{

    /**
     * nav下模块,常规模块,带items
     * @param $navId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function navBlock($navId,$page=1)
    {
        $navId = intval($navId);
        $navRow = MovieNavModel::findByID($navId);
        if (empty($navRow)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '板块不存在');
        }
        $result = [];
        if($page==1){

            //轮播广告
            $adv=AdvService::getAll('app_banner');
            if($adv){
                $result[] = [
                    'id' => '-1',
                    'name' => '广告',
                    'style' => '-1',
                    'filter'=>[],
                    'route'=>'',
                    'route_name'=>'',
                    'items' => $adv
                ];
            }
        }
        $blocks = MovieBlockService::get(intval($navId), $page, 6);
        $ad = AdvService::getAll('app_block_list');
        foreach ($blocks as $block) {
            if($block['style']>=40&&$block['style']<=49){
                foreach ($block['filter'] as &$item) {
                    $item['filter']['position']=$navRow['position'];
                    unset($item);
                }
            }else{
                $block['filter']['position']=$navRow['position'];
            }
            $result[] = [
                'id' => strval($block['id']),
                'name' => strval($block['name']),
                'style' => strval($block['style']),
                'filter' => value(function ()use($block){
                    if($block['style']>=40&&$block['style']<=49){
                        return [];
                    }else{
                        return $block['filter'];
                    }
                }),
                'route'=>strval($block['route']),
                'route_name'=>strval($block['route_name']?:'更多'),
                'items' => self::getBlockItems($block)
            ];
            if (count($ad)) {
                //一个模块一个广告
                $result[] = [
                    "id" => "-1",
                    "name" => "ad",
                    "style" => "-1",
                    'filter' => [],
                    'route'=>'',
                    'route_name'=>'',
                    "items" => [array_shift($ad)]
                ];
            }

        }
        return $result;
    }

    /**
     * nav下模块,列表模块,带filter
     * @param $navId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function navFilter($navId)
    {
        $navId = intval($navId);
        $navRow = MovieNavModel::findByID($navId);
        if (empty($navRow)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '板块不存在');
        }
        $result=[
            'banner'=>[
                'style' => '-2',
                'items' => AdvService::getAll('app_banner')
            ],
            'style' =>value(function ()use($navRow){
                //具体显示样式,根据业务自行选择
                if($navRow['style']=='video_2'){
                    return strval('4');
                }
                if($navRow['style']=='video_3'){
                    return strval('4');
                }
                return strval('30');
            }),
            'filters'=>value(function ()use($navRow){
                $filters = json_decode($navRow['filter'],true);
                //普通列表
                if($navRow['style']=='video_2'){
                    $filters['position']=$navRow['position'];
                    $filters['ad_code']='app_data_list';
                    return $filters;
                }
                //带tab的列表
                if($navRow['style']=='video_3'){
                    $result=[];
                    foreach ($filters as $filter) {
                        $result[] = [
                            'name' => strval($filter['name']),
                            'style' => value(function ()use($filter){
                                if($filter['style']<10){
                                    return strval(4);
                                }elseif ($filter['style']<20){
                                    return strval(12);
                                }
                                return strval(30);
                            }),
                            'filter' => value(function ()use($filter,$navRow){
                                if(empty($filter['filter']['ad_code'])){
                                    $filter['filter']['ad_code'] = 'app_data_list';
                                }
                                $filter['filter']['position']=$navRow['position'];
                                return $filter['filter'];
                            }),
                        ];
                    }
                    return $result;
                }
                return [];
            })
        ];
        return $result;
    }

    /**
     * 筛选页面
     * @return array
     */
    public static function filter()
    {
        /**
         * field提交到搜索接口的字段
         * select仅支持 单选:only 多选:multiple
         */
        $result = [
            //题材-分类
            'category'=>[
                'field'=>'cat_id',
                'select'=>'only',
                'items'=>value(function (){
                    $rows = [
                        [
                            'name'=>'全部',
                            'value'=>''
                        ]
                    ];
                    foreach (MovieCategoryService::getAll() as $category) {
                        $rows[]=[
                            'name'=>strval($category['name']),
                            'value'=>strval($category['id']),
                        ];
                    }
                    return $rows;
                })
            ],
            'tag'=>[
                'field'=>'tag_id',
                'select'=>'multiple',
                'items'=>value(function (){
                    $rows=[];
                    foreach (MovieTagService::getGroupAttrAll() as $groupName=>$items) {
                        $rows[]=[
                            'name'=>strval($groupName),
                            'items'=>value(function ()use($items){
                                $rows=[];
                                foreach ($items as $item) {
                                    $rows[]= [
                                        'name'=>strval($item['name']),
                                        'value'=>strval($item['id']),
                                    ];
                                }
                                return $rows;
                            }),
                        ];
                    }
                    return $rows;
                })
            ],


            //付费,连载
            'pay_type'=>[
                'field'=>'pay_type',
                'select'=>'only',
                'items'=>value(function (){
                    $rows =[
                        [
                            'name'=>'全部',
                            'value'=>''
                        ]
                    ];
                    foreach (CommonValues::getPayTypes() as $code=>$name) {
                        $rows[]=[
                            'name'=>strval($name),
                            'value'=>strval($code),
                        ];
                    }
                    return $rows;
                })
            ],
            'sort'=>[
                'field'=>'order',
                'select'=>'only',
                'items'=>value(function (){
                    $rows =[
                        [
                            'name'=>'人气推荐',
                            'value'=>'favorite'
                        ],
                        [
                            'name'=>'最近更新',
                            'value'=>'update_date'
                        ],
                        [
                            'name'=>'最新上架',
                            'value'=>'new'
                        ],
                        [
                            'name'=>'最多观看',
                            'value'=>'click'
                        ],
                        [
                            'name'=>'最多收藏',
                            'value'=>'favorite'
                        ],
                    ];
                    return $rows;
                })
            ],
        ];
        return $result;
    }

    /**
     * @param array $block
     * @return mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    private static function getBlockItems(array $block)
    {
        $language = ApiService::getLanguage();
        $keyName = "movie_block_{$block['id']}:{$language}";
        $result = cache()->get($keyName);
        if (is_null($result)) {
            //特殊样式
            if($block['style']>=40&&$block['style']<=49){
                $result=[];
                foreach ($block['filter'] as $item) {
                    $filter=$item['filter'];
                    $filter['page_size'] = $filter['page_size']?:$block['num'];
                    $result[]=[
                        'name'=>strval($item['name']),
                        'items'=>self::doSearch($filter)['data'],
                    ];
                }
            }else{
                $filter=$block['filter'];
                $filter['page_size'] = $filter['page_size']?:$block['num'];
                $result = self::doSearch($filter)['data'];
            }
            cache()->set($keyName, $result, 300);
        }
        return $result;
    }


    /**
     * 模块详情
     * @param $blockId
     * @return array
     * @throws BusinessException
     */
    public static function getBlockDetail($blockId)
    {
        $row = MovieBlockModel::findByID(intval($blockId));
        if (empty($row)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '未找到相关模块!');
        }
        $navRow = MovieNavModel::findByID(intval($row['nav_id']));

        $filter = json_decode($row['filter'], true);
        //特殊样式
        if($row['style']>=40&&$row['style']<=49){
            return [
                'id' => strval($row['_id']),
                'name' => strval($row['name']),
                'style' => strval($row['style']),
                'filters' => value(function ()use($filter,$navRow){
                    $rows=[];
                    foreach ($filter as $item) {
                        $item['page'] = 1;
                        $item['page_size'] = 6 * 4;
                        $item['filter'] = $navRow['position'];
                        $item['ad_code'] = 'app_data_list';
                        $rows[]=[
                            'name'=>$item['name'],
                            'filter'=>$item['filter'],
                        ];
                    }
                    return $rows;
                })
            ];
        }else{
            $filter['page'] = 1;
            $filter['page_size'] = 6 * 4;
            $filter['position'] = $navRow['position'];
            $filter['ad_code'] = 'app_data_list';
            return [
                'id' => strval($row['_id']),
                'name' => strval($row['name']),
                'style' => value(function ()use($row){
                    if($row['style']<10){
                        return strval(4);
                    }elseif ($row['style']<20){
                        return strval(12);
                    }
                    return strval(30);
                }),
                'filters' => [
                    ['name'=>'近期最佳', 'filter'=>value(function ()use($filter){
                        $filter['order'] = '';
                        return $filter;
                    })],
                    ['name'=>'最近更新', 'filter'=>value(function ()use($filter){
                        $filter['order'] = 'new';
                        return $filter;
                    })],
                    ['name'=>'最多观看', 'filter'=>value(function ()use($filter){
                        $filter['order'] = 'click';
                        return $filter;
                    })],
                    ['name'=>'最多收藏', 'filter'=>value(function ()use($filter){
                        $filter['order'] = 'favorite';
                        return $filter;
                    })],
                ]
            ];
        }
    }

    /**
     * 详情
     * @param $userId
     * @param $movieId
     * @param $linkId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function getDetail($userId, $movieId, $linkId = null)
    {

        $result = MovieService::getInfoCache($movieId);
        $userInfo = UserService::getInfoFromCache($userId);

        $links = $result['links'];
        $currentLink = [];

        $lastRead = [];
        if (!empty($userId) && empty($linkId)) {
            //上次观看
            $lastRead = MovieHistoryService::getLastRead($userId, $movieId);
        }

        foreach ($links as $index => $link) {
            $item = [
                'id' => strval($link['id']),
                'name' => "第" . ($index + 1) . "集",
                'current' => value(function () use ($link, $lastRead, $linkId, $index) {
                    ///优先选中的
                    if (!empty($linkId)) {
                        return $link['id'] == $linkId ? 'y' : 'n';
                    }
                    if (!empty($lastRead) && $lastRead['link_id'] == $link['id']) {
                        return 'y';
                    }
                    // 如果没有 lastRead，才默认选中第一集
                    if (empty($lastRead) && $index == 0) {
                        return 'y';
                    }
                    return 'n';
                }),
            ];
            if ($item['current'] == 'y') {
                $currentLink = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'current' => $item['current'],
                    'm3u8_url' => strval($link['m3u8_url']),
                    'duration' => strval($link['duration']),
                    'preview_m3u8_url' => strval($link['preview_m3u8_url']),
                ];
                $prevLink = $links[$index - 1] ?? [];
                $nextLink = $links[$index + 1] ?? [];
            }
            $links[$index] = $item;
        }


        $result = [
            'id' => strval($result['id']),
            'lid' => strval($currentLink['id']),
            'name' => strval($result['name']),
            'number' => strpos($result['number'], 'FH_') !== false ? '' : $result['number'],
            'description' => strval($result['description'] ?: '当前暂无简介'),

            'tags' => value(function () use ($result) {
                $rows = [];
                foreach ($result['tags'] as $item) {
                    $rows[] = [
                        'id' => strval($item['id']),
                        'name' => strval($item['name'])
                    ];
                }
                return $rows;
            }),
            'img' => CommonService::getCdnUrl($result['img_y'] ?: $result['img_x']),
            'preview_images' => value(function () use ($result) {
                $rows = [];
                foreach ($result['preview_images'] as $image) {
                    $rows[] = CommonService::getCdnUrl($image);
                }
                return $rows;
            }),
            'click' => value(function () use ($result) {
                $real = CommonService::getRedisCounter("movie_click_{$result['id']}");
                return strval((intval($result['click'] + $real)));
            }),
            'comment' => value(function () use ($result) {
                $real = CommonService::getRedisCounter("movie_comment_ok_{$result['id']}");
                return strval((intval($real)));
            }),
            'love' => value(function () use ($result) {
                $real = CommonService::getRedisCounter('movie_love_' . $result['id']);
                return strval($result['love'] + $real);
            }),
            'favorite' => value(function () use ($result) {
                $real = CommonService::getRedisCounter('movie_favorite_' . $result['id']);
                return strval($result['favorite'] + $real);
            }),
            'has_love' => MovieLoveService::has($userId, $movieId) ? 'y' : 'n',
            'has_favorite' => MovieFavoriteService::has($userId, $movieId) ? 'y' : 'n',


            'duration' => value(function ()use($currentLink){
                if($currentLink['duration']>0){
                    return CommonUtil::formatSecond($currentLink['duration'], true);
                }
                return '';
            }),
            'pay_type' => strval($result['pay_type']),
            'money'         => value(function ()use($userInfo,$result){
                if($result['money']<=0){return '';}
                //现价
                if($result['position']=='dark'){
                    if($userInfo['group_dark_rate']!=100){
                        return strval(round($result['money']*$userInfo['group_dark_rate']/100,0));
                    }
                }else{
                    if($userInfo['group_rate']!=100){
                        return strval(round($result['money']*$userInfo['group_rate']/100,0));
                    }
                }
                return strval($result['money']);
            }),
            //原价
            'old_money'     =>value(function ()use($userInfo,$result){
                if($result['money']<=0){
                    return '';
                }
                if($result['position']=='dark'){
                    if($userInfo['is_dark_vip']=='y'){
                        return '';
                    }
                }else{
                    if($userInfo['is_vip']=='y'){
                        return '';
                    }
                }
                //这里用最大会员折扣
                $group = UserGroupService::getMaxRateGroup();

                return strval(round($result['money']*$group['rate']/100,0));
            }),
            'layer_type'=>'limit',//默认都显示次数


            'recommend_items' => value(function () use ($result, $movieId) {
                return self::doSearch([
                    'page_size' => '24',
                    'not_ids' => $movieId,
                    'order' => 'rand',
                    'tag_id' => join(',', array_column($result['tags'], 'id'))
                ])['data'];
            }),

            //播放
            'play_ads' => value(function () {
                $rows = AdvService::getAll('movie_info_play');
                shuffle($rows);
                $result = [];
                foreach ($rows as $index => $row) {
                    if ($index < 3) {
                        $result[] = $row;
                    }
                }
                return $result;
            }),
            'play_ads_auto_jump' => 'y',
            'play_ads_time' => '5',
            'seek_time' => value(function () use ($lastRead) {
                if (!empty($lastRead)) {
                    return strval($lastRead['time']);
                }
                return '0';
            }),

            'show_at' => date('Y-m-d H:i', $result['show_at'] ? $result['show_at'] : $result['created_at']),
            'prev_id' => strval(isset($prevLink) ? $prevLink['id'] : ''),
            'next_id' => strval(isset($nextLink) ? $nextLink['id'] : ''),
            'links' => value(function () use ($links) {
                $chunks = array_chunk($links, 50);
                foreach ($chunks as $index => &$chunk) {
                    $start = $index + 1;
                    $end = $start * 50;
                    if ($end > count($chunk)) {
                        $end = count($links);
                    }
                    $chunk = [
                        'name' => $start . '-' . $end,
                        'items' => $chunk,
                    ];
                    unset($chunk);
                }
                return $chunks;
            }),
            'links_tips'=>value(function () use ($links,$currentLink) {
                if(count($links) > 1){
                    return    "全".count($links)."集"." 正在观看·{$currentLink['name']}" ;
                }
                return "";
            }),
            //我的信息
            'user' => [
                'id'        => strval($userInfo['id']),
                'username'  => strval($userInfo['username']),
                'nickname'  => strval($userInfo['nickname']),
                'headico'   => CommonService::getCdnUrl($userInfo['headico']),
                'is_vip'    => strval($result['position']=='dark'?$userInfo['is_dark_vip']:$userInfo['is_vip']),
                'is_up'     => strval($userInfo['is_up']),
                'balance'   => strval($userInfo['balance'] )
            ],
            //作者信息
            'actors'=>value(function () use ($result){
                $rows=[];
                foreach ($result['user_id'] as $homeInfo) {
                    $rows[]=[
                        'id'        => strval($homeInfo['id']),
                        'username'  => strval($homeInfo['username']),
                        'nickname'  => strval($homeInfo['nickname']),
                        'headico'   => CommonService::getCdnUrl($homeInfo['headico']),
                        'is_vip'    => strval(UserService::isVip($homeInfo)),
                        'is_up'     => strval($homeInfo['is_up']),
                        'balance'   => strval(''),//和上面user字段对齐
                    ];
                }
                return $rows;
            }),

            //活动
            'activity'=>value(function ()use($userInfo){
                //获取当前进行中的倒计时活动
                $activity = ActivityService::getCountdownOne($userInfo);
                if(empty($activity)){return null;}
                return [
                    'id'        => strval($activity['id']),
                    'name'      => strval($activity['name']),
                    'end_time'  => strval($activity['end_time']),
                    'is_show_time' => strval($activity['tpl_config']['is_show_time']),//为n前端不显示倒计时
                    'link'      => strval($activity['tpl_config']['link']),
                ];
            })
        ];
        $result['play_ads_time'] = value(function () use ($result) {
            $time = array_sum(array_column($result['play_ads'], 'time'));
            return strval($time);
        });


        $hasBuy = value(function () use ($result,$userInfo,$currentLink) {
            if(in_array($userInfo['id'],array_column($result['actors'],'id'))){
                return 'y';
            }
            if($result['position']!='dark'&&$userInfo['group_rate']=='0'){
                return 'y';
            }
            if($result['position']=='dark'&&$userInfo['group_dark_rate']=='0'){
                return 'y';
            }
            return UserBuyLogService::has($userInfo['id'], $result['id'],'movie',$currentLink['id'])?'y':'n';
        });


        //用户是否购买||用户权限
        if($hasBuy=='y'||$result['pay_type']=='free'){
            $result['layer_type']='';
        }else{
            if($result['pay_type']=='money'){
                $result['layer_type']='money';
                $currentLink['m3u8_url'] = $currentLink['preview_m3u8_url']?:$currentLink['m3u8_url'];
            }else{
                //是否会员
                $isVIP= $result['position']=='dark'?$userInfo['is_dark_vip']:$userInfo['is_vip'];
                if($isVIP=='y'){
                    $result['layer_type']= '';
                }else{
                    //普通用户使用次数看免费视频
                    $maxNum  = MovieHistoryService::getCanPlayNum();
                    $playNum =0;
                    if($maxNum>0){
                        //今日播放次數
                        $playNum = MovieHistoryService::getPlayNum($userId,$movieId,$currentLink['id']);
                    }
                    //当今日播放次数小于当前可播放次数
                    if ($playNum > $maxNum) {
                        $result['layer_type']= '';
                    }else{
                        $result['layer_type']='limit';
                        $currentLink['m3u8_url'] = $currentLink['preview_m3u8_url']?:$currentLink['m3u8_url'];
                    }
                }
            }
        }



        $isChina = IpService::isChina(CommonUtil::getClientIp());
        $result['play_links'] = [
            [
                'id' => $movieId,
                'lid' => $currentLink['id'],
                'code' => 'line1',
                'name' => '线路1',
//                'm3u8_url'=>'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'm3u8_url' => M3u8Service::encode($currentLink['m3u8_url'], $isChina?'tencent':'aws')
            ],
            [
                'id' => $movieId,
                'lid' => $currentLink['id'],
                'code' => 'line2',
                'name' => '线路2',
                'm3u8_url' => M3u8Service::encode($currentLink['m3u8_url'],  $isChina ? 'aws' : 'tencent')
            ],
            [
                'id' => $movieId,
                'lid' => $currentLink['id'],
                'code' => 'line3',
                'name' => '线路3',
                'm3u8_url' => M3u8Service::encode($currentLink['m3u8_url'], 'free')
            ],
        ];
        return $result;
    }

    /**
     * 视频详情带推荐列表
     * MovieShortSearchIdFilterView
     * @param array $filter
     * @param $userId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function similarSearch(array $filter = [],$userId=null)
    {
        $movieId = self::getRequest($filter, 'id', 'string');
        if(empty($movieId)){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,"参数错误");
        }
        $movieInfo = MovieService::getInfoCache($movieId);
        $filter['tag_id']= join(',',array_column($movieInfo['tags'],'id'));
        $filter['not_ids']=$movieId;
        $filter['position'] = $movieInfo['position'];
//        $filter['ad_code']='video_block_waterfall';

        return self::doSearch($filter,$userId);
    }

    /**
     *
     * @param $tagId
     * @return array
     * @throws BusinessException
     */
    public static function getTagDetail($tagId)
    {
        $row = MovieTagModel::findByID(intval($tagId));
        if (empty($row)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '未找到相关标签!');
        }

        $filter = [
            'tag_id' => $tagId,
            'page_size'=>'20',
        ];

        return [
            'id' => strval($row['_id']),
            'name' => strval($row['name']),
            'filters' => [
                ['name'=>'近期最佳', 'filter'=>value(function ()use($filter){
                    $filter['order'] = '';
                    return $filter;
                })],
                ['name'=>'最近更新', 'filter'=>value(function ()use($filter){
                    $filter['order'] = 'new';
                    return $filter;
                })],
                ['name'=>'最多观看', 'filter'=>value(function ()use($filter){
                    $filter['order'] = 'click';
                    return $filter;
                })],
                ['name'=>'最多收藏', 'filter'=>value(function ()use($filter){
                    $filter['order'] = 'favorite';
                    return $filter;
                })],
            ]
        ];
    }

    /**
     * 去点赞
     * @param $userId
     * @param $movieId
     * @return bool
     * @throws BusinessException
     */
    public static function doLove($userId, $movieId)
    {
        return MovieLoveService::do($userId, $movieId);
    }

    /**
     * 去点踩
     * @param $userId
     * @param $movieId
     * @return bool
     * @throws BusinessException
     */
    public static function doDisLove($userId,$movieId)
    {
        return MovieDisLoveService::do($userId, $movieId);
    }


    /**
     * 点赞列表
     * @param $userId
     * @param $page
     * @return array
     */
    public static function getLoveList($userId, $page = 1, $pageSize = 12,$cursor='')
    {
        $ids = MovieLoveService::getIds($userId, $page, $pageSize,$cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }
        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }

        return [
            'data' => $data,
            'total' => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size' => strval($ids['page_size']),
            'last_page' => strval($ids['last_page']),
            'cursor'    => strval($ids['cursor']),
        ];
    }

    /**
     * 去收藏
     * @param $userId
     * @param $movieId
     * @return bool
     * @throws \App\Exception\BusinessException
     */
    public static function doFavorite($userId, $movieId)
    {
        return MovieFavoriteService::do($userId, $movieId);
    }

    /**
     * 收藏的视频列表
     * @param $userId
     * @param $page
     * @return array
     */
    public static function getFavoriteList($userId, $page = 1, $pageSize = 12,$cursor='')
    {
        $ids = MovieFavoriteService::getIds($userId, $page, $pageSize,$cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }
        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }

        return [
            'data' => $data,
            'total' => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size' => strval($ids['page_size']),
            'last_page' => strval($ids['last_page']),
            'cursor'    => strval($ids['cursor']),
        ];
    }

    /**
     * 添加观看记录
     * @param $userId
     * @param $movieId
     * @param $linkId
     * @param $playTime
     * @param $viewTime
     * @param $code
     * @param $event
     * @return bool
     */
    public static function doHistory($userId, $movieId, $linkId, $playTime,$viewTime,$code,$event)
    {
        return MovieHistoryService::do($userId, $movieId, $linkId, $playTime,$viewTime,$code,$event);
    }

    /**
     * 删除历史记录
     * @param $userId
     * @param $movieIds
     * @return bool|mixed
     */
    public static function delHistory($userId, $movieIds)
    {
        return MovieHistoryService::delete($userId, $movieIds);
    }

    /**
     * 获取历史记录
     * @param $userId
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public static function getHistoryList($userId, $page = 1, $pageSize = 12,$cursor='')
    {
        $ids = MovieHistoryService::getIds($userId, $page, $pageSize,$cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }

        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }
        return [
            'data' => $data,
            'total' => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size' => strval($ids['page_size']),
            'last_page' => strval($ids['last_page']),
            'cursor'    => strval($ids['cursor']),
        ];
    }

    /**
     * 视频预览
     * @param string $id
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function getPlayUrl(string $id)
    {
        $result = MovieService::getInfoCache($id);
        $links = $result['links'];

        $isChina = IpService::isChina(CommonUtil::getClientIp());
        return [
            'play_url' => M3u8Service::encode($links[0]['preview_m3u8_url'], $isChina?'tencent':'aws', 'Api')
        ];
    }

    /**
     * 解锁视频
     * @param $userId
     * @param $movieId
     * @param $linkId
     * @return true
     * @throws BusinessException
     */
    public static function doBuy($userId,$movieId,$linkId)
    {
        return MovieService::doBuy($userId,$movieId,$linkId);
    }

    /**
     * 购买记录
     * @param $userId
     * @param $page
     * @param $pageSize
     * @param $cursor
     * @return array
     */
    public static function getBuyLogList($userId,$page=1,$pageSize=20,$cursor=null)
    {
        $ids = UserBuyLogService::getIds($userId,'movie',$page,$pageSize,$cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }

        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }
        return [
            'data' => $data,
            'total' => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size' => strval($ids['page_size']),
            'last_page' => strval($ids['last_page']),
            'cursor'    => strval($ids['cursor']),
        ];
    }

    /**
     * 搜索
     * @param array $filter
     * @return array
     */
    public static function doSearch(array $filter = [],$userId=null)
    {
        $query = [];
        $query['page'] = self::getRequest($filter, 'page', 'int', 1);
        $query['page_size'] = self::getRequest($filter, 'page_size', 'int', 12);
        $query['keywords'] = self::getRequest($filter, 'keywords', 'string', '');
        $query['x_filter'] = self::getRequest($filter, 'x_filter', 'string', '');
        $query['icon'] = self::getRequest($filter, 'icon', 'string', '');
        $query['position'] = self::getRequest($filter, 'position', 'string', '');
        $query['pay_type'] = self::getRequest($filter, 'pay_type', 'string', '');
        $query['cat_id'] = self::getRequest($filter, 'cat_id', 'string', '');
        $query['tag_id'] = self::getRequest($filter, 'tag_id', 'string', '');
        $query['home_id'] = self::getRequest($filter, 'home_id', 'string', '');
        $query['home_ids'] = self::getRequest($filter, 'home_ids', 'string', '');
        $query['canvas'] = self::getRequest($filter, 'canvas', 'string', '');
        $query['ids'] = self::getRequest($filter, 'ids', 'string', '');
        $query['not_ids'] = self::getRequest($filter, 'not_ids', 'string', '');
        $query['order'] = self::getRequest($filter, 'order', 'string', '');
        $query['status']    = self::getRequest($filter, 'status', 'string', '');

        $query['ad_code'] = self::getRequest($filter, 'ad_code', 'string', '');
        $query['language'] = self::getRequest($filter, 'language', 'string', ApiService::getLanguage());
        $query['duration'] = self::getRequest($filter, 'duration', 'string', '');
        return MovieService::doSearch($query,$userId);
    }

    /**
     * @param $userId
     * @param $movieId
     * @param $linkId
     * @return array
     * @throws BusinessException
     */
    public static function doDownload($userId,$movieId,$linkId=null)
    {
        return MovieDownloadService::do($userId,$movieId,$linkId);
    }

    /**
     * 发布信息
     * @param $userId
     * @return array
     */
    public static function getCreateInfo($userId)
    {
        $result=[
            'tags'=>value(function (){
                $result=[];
                $items = MovieTagModel::find([],[],[],0,1000);
                foreach ($items as $item) {
                    if($item['is_show_upload']==0){
                        continue;
                    }
                    $result[$item['attribute']][] = [
                        'id'    =>strval($item['_id']),
                        'name'  =>strval($item['name']),
                        'click' => strval(CommonUtil::formatNum($item['click']??0).'次播放'),
                        'favorite' => strval(CommonUtil::formatNum($item['favorite']??0).'次收藏'),
                        'follow'=>strval(CommonUtil::formatNum($item['follow']?:0).'人参与'),
                        //关注的用户
                        'follow_user'=>value(function ()use($item){
                            $num = max(4,$item['follow']??0);
                            $result = [];
                            for($n=1;$n<=$num;$n++){
                                //随机生成头像
                                $userRow = UserService::getDefaultUserRow(null);
                                $result[]= CommonService::getCdnUrl($userRow['headico']);
                            }
                            return $result;
                        }),
                    ];
                }
                return $result;
            }),
            'tags_limit'=>'2',
        ];
        return $result;
    }


    /**
     * 保存视频
     * @param $userId
     * @param $request
     * @return bool
     * @throws BusinessException
     */
    public static function doCreate($userId,$request)
    {
        $uploadId = self::getRequest($request,'upload_id','string');
        $tagIds = value(function ()use($request) {
            $tagIds = self::getRequest($request,'tags','string');
            $tagIds = explode(',',$tagIds);
            $result = [];
            foreach ($tagIds as $tagId) {
                if (empty($tagId)) {
                    continue;
                }
                $result[] = intval($tagId);
            }
            return $result;
        });
        $money = self::getRequest($request,'money','int',0);
        $width = self::getRequest($request,'width','int',0);
        $height = self::getRequest($request,'height','int',0);
        $name = self::getRequest($request,'name','string');
        $duration = self::getRequest($request,'duration','string');
        $img = self::getRequest($request,'img','string');
        $userInfo = UserService::getInfoFromCache($userId);

        if(!in_array('do_movie', UserService::getRights($userInfo))) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'您没用发布视频的权限!');
        }
        //判断今日发布次数
        $count=MovieModel::count(['user_id' => $userId,'created_at'=>['$gte'=>strtotime(date('Y-m-d')),'$lte'=>strtotime(date('Y-m-d 23:59:59'))]]);
        if($count>=$userInfo['creator']['movie_upload_num']){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'今日发布次数不足');
        }

        if(empty($uploadId)){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'请携带上传ID');
        }
        if(empty($width)||empty($height)){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'无法获取视频宽高');
        }
        if(empty($duration)){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'无法获取视频时长');
        }
        if(empty($tagIds)){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'请至少选择一个视频标签');
        }
        if(count($tagIds)>2){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'最多只能选择两个标签');
        }
        if(empty($name)){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'请输入视频标题');
        }
        // 检查金额不能超过上限
        if ($money > $userInfo['creator']['movie_money_limit']) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '钻石价格超过最大限制，请重新输入');
        }
        $mid = "upload_".$uploadId;
        if(MovieModel::count(['mid'=>$mid])>0){
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '该视频已存在');
        }

        $saveData=[
            'mid'           => $mid,
            'user_id'       => [intval($userId)],
            'categories'    => 0,
            'tags'          => $tagIds,
            'name'          => strval($name),
            'img_x'         => strval($img),
            'img_y'         => '',
            'number'        => uniqid('FH_'),


            'sort'          => 0,
            'favorite'      => rand(16800, 19000),
            'real_favorite' => 0,
            'love'          => rand(16800, 19000),
            'real_love'     => 0,
            'dislove'       => 0,
            'real_dislove'  => 0,
            'click'         => rand(15800, 19000),
            'real_click'    => 0,
            'hot_rate'      => 0,
            'favorite_rate' => 0,
            'click_total'   => 0,
            'love_total'    => 0,
            'favorite_total' => 0,
            'dislove_total' => 0,

            'score'         => rand(92, 96),
            'buy'           => 0,
            'comment'       => 0,
            'money'         => $money,
            'pay_type'      => CommonValues::getPayTypeByMoney($money),
            'width'         => 0,
            'height'        => 0,
            'position'      =>'normal',
            'canvas'        => '',
            'status'        => -2,
            'is_more_link'  => 0,
            'links'         =>[
                [
                    'id'        => strval($mid),
                    'name'      => strval('0'),
                    'duration'  => intval($duration),
                    'm3u8_url'  => '',
                    'preview_m3u8_url'  => '',
                ]
            ],
            'description'=>'',
            'preview_images'=>[],
            'update_status'=>1,
            'publisher' =>'',
            'issue_date'=>'',
            'icon'=>'',
            'show_at'=>0,
            'async_at'=>0,
        ];
        $result = MovieModel::insert($saveData,false);
        return boolval($result);
    }

    /**
     * 站点地图
     * @param $page
     * @param $pageSize
     * @return array
     */
    public static function sitemap($page = 1, $pageSize = 5000)
    {
        $where = ['status' => 1];
        $items = MovieModel::find($where, array('_id'), array('_id' => -1), ($page - 1) * $pageSize, $pageSize);
        return array_column($items, '_id');
    }
}
