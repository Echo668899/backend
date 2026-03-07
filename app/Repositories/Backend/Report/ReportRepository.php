<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Report;


use App\Core\Repositories\BaseRepository;
use App\Models\Report\ReportAdvAppLogModel;
use App\Models\Report\ReportAdvLogModel;
use App\Models\Report\ReportAudioLogModel;
use App\Models\Report\ReportChannelLogModel;
use App\Models\Report\ReportComicsLogModel;
use App\Models\Report\ReportHourLogModel;
use App\Models\Report\ReportMovieLogModel;
use App\Models\Report\ReportNovelLogModel;
use App\Models\Report\ReportPostLogModel;
use App\Models\Report\ReportServerLogModel;
use App\Models\Report\ReportUserChannelLogModel;
use App\Services\User\UserActiveService;
use App\Utils\CommonUtil;

/**
 * report
 * @package App\Repositories\Backend
 */
class ReportRepository extends BaseRepository
{
    /**
     *系统统计数据
     */
    public static function getReportData()
    {
        //日活
        $result['app_day'] = self::getAppDayItems();

        //充值
        $result['money'] = self::getOrderItems('money', 15);

        //注册
        $appRegData = ReportServerLogModel::findFirst(['type' => 'user_reg'], [], ['date' => -1]);
        $appRegData['value'] = (format_num($appRegData['value'], 2));
        $result['user_reg'] = $appRegData;


        //设备类型 IOS
        $appIosData = ReportServerLogModel::findFirst(['type' => 'device_type_ios'],[],['date' => -1]);
        $appIosData['value'] = (format_num($appIosData['value'], 2));
        $result['device_type_ios'] = $appIosData;

        //设备类型 android
        $appAndroidData = ReportServerLogModel::findFirst(['type' => 'device_type_android'],[],['date' => -1]);
        $appAndroidData['value'] = (format_num($appAndroidData['value'], 2));
        $result['device_type_android'] = $appAndroidData;

        //设备类型 web
        $appWebData = ReportServerLogModel::findFirst(['type' => 'device_type_web'],[],['date' => -1]);
        $appWebData['value'] = (format_num($appWebData['value'], 2));
        $result['device_type_web'] = $appWebData;


        //用户总数 user_total
        $userTotalData = ReportServerLogModel::findFirst(['type' => 'user_total'], [],['date' => -1]);
        $userTotalData['value'] = (format_num($userTotalData['value'], 2));
        $result['user_total'] = $userTotalData;


        //留存等
        $result['dau_log'] = value(function (){
            $query = ['channel_name' => '_all'];
            $items = ReportChannelLogModel::find($query, [], ['date' => -1], 0, 15);
            foreach ($items as &$item) {
                $item['reg'] = (format_num($item['reg'], 2));
                $item['ip'] = (format_num($item['ip'], 2));
                $item['uv'] = (format_num($item['uv'], 2));
                $item['pv'] = (format_num($item['pv'], 2));

                foreach ([0, 1, 3, 5, 7, 15] as $day) {
                    $item["dau_{$day}"] = format_num($item["dau_{$day}"], 2);
                }
                unset($item);
            }
            return $items;
        });

        //当前在线
        $result['user_active'] = UserActiveService::getTotalCount();


        //网站统计数据
        $result['web_log'] = self::getWebLog(15);


        return $result;
    }

    /**
     * @return array
     */
    protected static function getAppDayItems()
    {
        $query = ['type' => 'app_day'];
        $data = array();
        $items = ReportServerLogModel::find($query, [], ['date' => -1], 0, 15);
        foreach ($items as $item) {
            $data[] = [
                'date'=>$item['date'],
                'dau'=>format_num($item['value']['dau'], 2),
                'ip'=>format_num($item['value']['ip'], 2),
                'pv'=>format_num($item['value']['pv'], 2),
                'uv'=>format_num($item['value']['uv'], 2),
            ];
        }
        return $data;
    }

    /**
     * @param $type
     * @param $pageSize
     * @return array
     */
    protected static function getOrderItems($type, $pageSize)
    {
        $query = ['type' => $type];
        $data = array();
        $items = ReportServerLogModel::find($query, [], ['date' => -1], 0, $pageSize);
        foreach ($items as $item) {
            $data[] = [
                'date'=>$item['date'],
                'total_order'=>format_num($item['value']['total_order'], 2),//订单总数
                'success_order'=>format_num($item['value']['success_order'], 2),//成功订单数
                'success_amount'=>format_num($item['value']['success_amount'], 2),//成功金额
                'rate'=>round($item['value']['success_order']>0 ? $item['value']['success_order']/$item['value']['total_order'] : 0, 2),//支付成功率
            ];
        }
        return $data;
    }

    /**
     * 获取代理系统V3的,web统计
     * @param $pageSize
     * @return array
     */
    protected static function getWebLog($pageSize)
    {
        $types = [
            'ip'=>'IP',
            'pv'=>'PV',
            'uv'=>'UV',
            'click_android'=>'安卓点击',
            'click_ios'=>'IOS点击',
            'total_user_reg'=>'总新增',
            'total_android_user_reg'=>'安卓新增',
            'total_ios_user_reg'=>'IOS新增',
            'total_order_money'=>'累计充值',
            'today_order_money'=>'今日充值',
            'urr_1'=>'次日留存',
            'urr_3'=>'3日留存',
            'urr_7'=>'7日留存',
        ];
        $result = [
            'date'=>[],
            'types'=>$types
        ];
        $query = ['code' => '_all'];
        $webLogData = ReportChannelLogModel::find($query, [], ['date' => -1], 0, $pageSize);
        $webLogData = array_reverse($webLogData);
        foreach ($webLogData as $item) {
            $result['date'][] = $item['date'];
            foreach($item['agent_v3'] as $key=>$value){
                $result[$key][] = format_num($value, 2);
            }
        }
        return $result;
    }

    /**
     * 小时报表
     * @param $request
     * @return array
     */
    public static function getHour($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 10);
        $sort = self::getRequest($request, 'sort', 'string', 'date');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();


        if ($request['month']) {
            $filter['month'] = self::getRequest($request, 'month');
            $query['month'] = $filter['month'];
        }
        $query['pid'] = self::getRequest($request, 'pid', 'string', '0');

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportHourLogModel::count($query);
        $items = ReportHourLogModel::find($query, $fields, array(($query['pid'] == 0 ? $sort : 'date_limit') => ($query['pid'] == 0 ? $order : 1)), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['dau'] = format_num($item['dau'], 2);
            $item['dau_android'] = format_num($item['dau_android'], 2);
            $item['dau_ios'] = format_num($item['dau_ios'], 2);
            $item['dau_web'] = format_num($item['dau_web'], 2);
            $item['reg'] = format_num($item['reg'], 2);
            $item['reg_android'] = format_num($item['reg_android'], 2);
            $item['reg_ios'] = format_num($item['reg_ios'], 2);
            $item['reg_web'] = format_num($item['reg_web'], 2);
            $item['order'] = format_num($item['order'], 2);
            $item['order_success'] = format_num($item['order_success'], 2);
            $item['order_money'] = format_num($item['order_money'], 2);
            $item['tav'] = format_num($item['tav'], 2);
            $item['apr'] = strval($item['apr'] . '%');
            $item['payr'] = strval($item['payr'] . '%');
            $item['arpu'] = strval(format_num($item['arpu'], 2));
            $item['haveChild'] = ReportHourLogModel::count(['pid' => $item['_id']]) > 0 ? true : false;
            $item['date'] = $item['pid'] == '0' ? $item['date'] : $item['date_limit'];
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $items[$index] = $item;
        }
        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'haveChild' => $query['pid'] != '0' ? true : false
        );
    }

    /**
     * 渠道统计
     * @param $request
     * @return array
     */
    public static function getChannel($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['channel_name']) {
            $filter['channel_name'] = self::getRequest($request, 'channel_name');
            $query['channel_name'] = $filter['channel_name'];
        }
        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time');
            $query['date']['$gte'] = $filter['start_time'];
        }
        if ($request['end_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time');
            $query['date']['$lte'] = $filter['end_time'];
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportChannelLogModel::count($query);
        $items = ReportChannelLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('y-m-d H:i', $item['updated_at']);
            $item['reg'] = format_num($item['reg'], 2);
            $item['reg_android'] = format_num($item['reg_android'], 2);
            $item['reg_ios'] = format_num($item['reg_ios'], 2);
            $item['reg_web'] = format_num($item['reg_web'], 2);
            $item['daot'] = CommonUtil::formatSecond($item['daot'],true);
            $item['daot_android'] = CommonUtil::formatSecond($item['daot_android'],true);
            $item['daot_ios'] = CommonUtil::formatSecond($item['daot_ios'],true);
            $item['daot_web'] = CommonUtil::formatSecond($item['daot_web'],true);

            $item['adv'] = format_num($item['adv'], 2);
            $item['adv_app'] = format_num($item['adv_app'], 2);

            $item['ip'] = format_num($item['ip'], 2);
            $item['uv'] = format_num($item['uv'], 2);
            $item['pv'] = format_num($item['pv'], 2);


            $item['amount']=format_num($item['order']['success_amount']+$item['recharge']['success_amount'],2);
            $item['order_amount']=format_num($item['order']['success_amount'],2);
            $item['recharge_amount']=format_num($item['recharge']['success_amount'],2);

            unset($item['order'],$item['recharge']);
            foreach ([0, 1, 3, 5, 7, 15] as $day) {
                $item["dau_{$day}"] = format_num($item["dau_{$day}"], 2);
            }
            $items[$index] = $item;
        }

        $countInfo = ReportChannelLogModel::aggregate([
            [
                '$match' => $query
            ],

            [
                '$group' => [
                    '_id' => null,

                    // group 阶段用扁平字段求和
                    'order_success_amount'   => ['$sum' => ['$ifNull' => ['$order.success_amount', 0]]],
                    'recharge_success_amount' => ['$sum' => ['$ifNull' => ['$recharge.success_amount', 0]]],


                    'reg' => ['$sum' => '$reg'],
                    'reg_android' => ['$sum' => '$reg_android'],
                    'reg_web' => ['$sum' => '$reg_web'],
                    'reg_ios' => ['$sum' => '$reg_ios'],

                    'ip' => ['$sum' => '$ip'],
                    'uv' => ['$sum' => '$uv'],
                    'pv' => ['$sum' => '$pv'],
                    'adv' => ['$sum' => '$adv'],
                    'adv_app' => ['$sum' => '$adv_app'],
                    'dau_0' => ['$sum' => '$dau_0'],
                    'dau_1' => ['$sum' => '$dau_1'],
                    'dau_3' => ['$sum' => '$dau_3'],
                    'dau_5' => ['$sum' => '$dau_5'],
                    'dau_7' => ['$sum' => '$dau_7'],
                    'dau_15' => ['$sum' => '$dau_15'],
                ]
            ],
        ]);

        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,

            'totalRow' => [
                'channel_name' => '合计',
                'date' => '-',
                'amount'=>format_num($countInfo['order_success_amount']+$countInfo['recharge_success_amount'],2),
                'order_amount'=>format_num($countInfo['order_success_amount'],2),
                'recharge_amount'=>format_num($countInfo['recharge_success_amount'],2),


                'reg' => format_num($countInfo['reg'],2),
                'reg_android' => format_num($countInfo['reg_android'],2),
                'reg_web' => format_num($countInfo['reg_web'],2),
                'reg_ios' => format_num($countInfo['reg_ios'],2),

                'daot' => '-',


                'ip' => format_num($countInfo['ip'],2),
                'uv' => format_num($countInfo['uv'],2),
                'pv' => format_num($countInfo['pv'],2),
                'adv' => format_num($countInfo['adv'],2),
                'adv_app' => format_num($countInfo['adv_app'],2),
                'dau_0' => format_num($countInfo['dau_0'],2),
                'dau_1' => format_num($countInfo['dau_1'],2),
                'dau_3' => format_num($countInfo['dau_3'],2),
                'dau_5' => format_num($countInfo['dau_5'],2),
                'dau_7' => format_num($countInfo['dau_7'],2),
                'dau_15' => format_num($countInfo['dau_15'],2),
                'updated_at' => '-',
            ]
        );
    }

    /**
     * 用户渠道
     * @param $request
     * @return array
     */
    public static function getUserChannel($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['channel_name']) {
            $filter['channel_name'] = self::getRequest($request, 'channel_name');
            $query['channel_name'] = $filter['channel_name'];
        }
        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time');
            $query['date']['$gte'] = $filter['start_time'];
        }
        if ($request['end_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time');
            $query['date']['$lte'] = $filter['end_time'];
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportUserChannelLogModel::count($query);
        $items = ReportUserChannelLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('y-m-d H:i', $item['updated_at']);
            $item['reg'] = format_num($item['reg'], 2);
            $item['reg_android'] = format_num($item['reg_android'], 2);
            $item['reg_ios'] = format_num($item['reg_ios'], 2);
            $item['reg_web'] = format_num($item['reg_web'], 2);
            $item['daot'] = CommonUtil::formatSecond($item['daot'],true);
            $item['daot_android'] = CommonUtil::formatSecond($item['daot_android'],true);
            $item['daot_ios'] = CommonUtil::formatSecond($item['daot_ios'],true);
            $item['daot_web'] = CommonUtil::formatSecond($item['daot_web'],true);

            $item['adv'] = format_num($item['adv'], 2);
            $item['adv_app'] = format_num($item['adv_app'], 2);

            $item['ip'] = format_num($item['ip'], 2);
            $item['uv'] = format_num($item['uv'], 2);
            $item['pv'] = format_num($item['pv'], 2);


            $item['amount']=format_num($item['order']['success_amount']+$item['recharge']['success_amount'],2);
            $item['order_amount']=format_num($item['order']['success_amount'],2);
            $item['recharge_amount']=format_num($item['recharge']['success_amount'],2);

            unset($item['order'],$item['recharge']);
            foreach ([0, 1, 3, 5, 7, 15] as $day) {
                $item["dau_{$day}"] = format_num($item["dau_{$day}"], 2);
            }
            $items[$index] = $item;
        }

        $countInfo = ReportUserChannelLogModel::aggregate([
            [
                '$match' => $query
            ],

            [
                '$group' => [
                    '_id' => null,

                    // group 阶段用扁平字段求和
                    'order_success_amount'   => ['$sum' => ['$ifNull' => ['$order.success_amount', 0]]],
                    'recharge_success_amount' => ['$sum' => ['$ifNull' => ['$recharge.success_amount', 0]]],


                    'reg' => ['$sum' => '$reg'],
                    'reg_android' => ['$sum' => '$reg_android'],
                    'reg_web' => ['$sum' => '$reg_web'],
                    'reg_ios' => ['$sum' => '$reg_ios'],

                    'ip' => ['$sum' => '$ip'],
                    'uv' => ['$sum' => '$uv'],
                    'pv' => ['$sum' => '$pv'],
                    'adv' => ['$sum' => '$adv'],
                    'adv_app' => ['$sum' => '$adv_app'],
                    'dau_0' => ['$sum' => '$dau_0'],
                    'dau_1' => ['$sum' => '$dau_1'],
                    'dau_3' => ['$sum' => '$dau_3'],
                    'dau_5' => ['$sum' => '$dau_5'],
                    'dau_7' => ['$sum' => '$dau_7'],
                    'dau_15' => ['$sum' => '$dau_15'],
                ]
            ],
        ]);

        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,

            'totalRow' => [
                'channel_name' => '合计',
                'date' => '-',
                'amount'=>format_num($countInfo['order_success_amount']+$countInfo['recharge_success_amount'],2),
                'order_amount'=>format_num($countInfo['order_success_amount'],2),
                'recharge_amount'=>format_num($countInfo['recharge_success_amount'],2),


                'reg' => format_num($countInfo['reg'],2),
                'reg_android' => format_num($countInfo['reg_android'],2),
                'reg_web' => format_num($countInfo['reg_web'],2),
                'reg_ios' => format_num($countInfo['reg_ios'],2),

                'daot' => '-',


                'ip' => format_num($countInfo['ip'],2),
                'uv' => format_num($countInfo['uv'],2),
                'pv' => format_num($countInfo['pv'],2),
                'adv' => format_num($countInfo['adv'],2),
                'adv_app' => format_num($countInfo['adv_app'],2),
                'dau_0' => format_num($countInfo['dau_0'],2),
                'dau_1' => format_num($countInfo['dau_1'],2),
                'dau_3' => format_num($countInfo['dau_3'],2),
                'dau_5' => format_num($countInfo['dau_5'],2),
                'dau_7' => format_num($countInfo['dau_7'],2),
                'dau_15' => format_num($countInfo['dau_15'],2),
                'updated_at' => '-',
            ]
        );
    }

    /**
     * 视频
     * @param $request
     * @return array
     */
    public static function getMovie($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['movie_id']) {
            $filter['movie_id'] = self::getRequest($request, 'movie_id', 'string');
            $query['movie_id'] = $filter['movie_id'];
        }

        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time', 'string');
            $query['label'] = ['$gte' => $filter['start_time']];
        }
        if ($request['start_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time', 'string');
            $query['label'] = ['$lte' => $filter['end_time']];
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportMovieLogModel::count($query);
        $items = ReportMovieLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['click'] = format_num($item['click'], 2);
            $item['love'] = format_num($item['love'], 2);
            $item['favorite'] = format_num($item['favorite'], 2);
            $item['buy_num'] = format_num($item['buy_num'], 2);
            $item['buy_total'] = format_num($item['buy_total'], 2);
            $item['download'] = format_num($item['download'], 2);
            $items[$index] = $item;
        }
        $countInfo = ReportMovieLogModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    'click' => ['$sum' => '$click'],
                    'buy_total' => ['$sum' => '$buy_total'],
                    'buy_num' => ['$sum' => '$buy_num'],
                    'favorite' => ['$sum' => '$favorite'],
                    'download' => ['$sum' => '$download'],
                ]
            ]
        ]);
        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRow' => [
                '_id' => '合计',
                'movie_id' => '-',
                'name' => '-',
                'label' => '-',
                'click' => format_num($countInfo['click'], 2),
                'buy_total' => format_num($countInfo['buy_total'], 2),
                'buy_num' => format_num($countInfo['buy_num'], 2),
                'favorite' => format_num($countInfo['favorite'], 2),
                'download' => format_num($countInfo['download'], 2),
                'updated_at' => '-',
                'created_at' => '-',
            ]
        );
    }

    /**
     * 漫画
     * @param $request
     * @return array
     */
    public static function getComics($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['comics_id']) {
            $filter['comics_id'] = self::getRequest($request, 'comics_id', 'string');
            $query['comics_id'] = $filter['comics_id'];
        }
        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time', 'string');
            $query['label'] = ['$gte' => $filter['start_time']];
        }
        if ($request['start_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time', 'string');
            $query['label'] = ['$lte' => $filter['end_time']];
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportComicsLogModel::count($query);
        $items = ReportComicsLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['click'] = format_num($item['click'], 2);
            $item['love'] = format_num($item['love'], 2);
            $item['favorite'] = format_num($item['favorite'], 2);
            $items[$index] = $item;
        }
        $countInfo = ReportComicsLogModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    'click' => ['$sum' => '$click'],
                    'love' => ['$sum' => '$love'],
                    'favorite' => ['$sum' => '$favorite'],
                ]
            ]
        ]);
        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRow' => [
                '_id' => '合计',
                'comics_id' => '-',
                'name' => '-',
                'label' => '-',
                'click' => format_num($countInfo['click'], 2),
                'love' => format_num($countInfo['love'], 2),
                'favorite' => format_num($countInfo['favorite'], 2),
                'updated_at' => '-',
                'created_at' => '-',
            ]
        );
    }

    /**
     * 有声
     * @param $request
     * @return array
     */
    public static function getAudio($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['audio_id']) {
            $filter['audio_id'] = self::getRequest($request, 'audio_id', 'string');
            $query['audio_id'] = $filter['audio_id'];
        }
        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time', 'string');
            $query['label'] = ['$gte' => $filter['start_time']];
        }
        if ($request['start_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time', 'string');
            $query['label'] = ['$lte' => $filter['end_time']];
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportAudioLogModel::count($query);
        $items = ReportAudioLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['click'] = format_num($item['click'], 2);
            $item['love'] = format_num($item['love'], 2);
            $item['favorite'] = format_num($item['favorite'], 2);
            $items[$index] = $item;
        }
        $countInfo = ReportAudioLogModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    'click' => ['$sum' => '$click'],
                    'love' => ['$sum' => '$love'],
                    'favorite' => ['$sum' => '$favorite'],
                ]
            ]
        ]);
        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRow' => [
                '_id' => '合计',
                'audio_id' => '-',
                'name' => '-',
                'label' => '-',
                'click' => format_num($countInfo['click'], 2),
                'love' => format_num($countInfo['love'], 2),
                'favorite' => format_num($countInfo['favorite'], 2),
                'updated_at' => '-',
                'created_at' => '-',
            ]
        );
    }

    /**
     * 小说
     * @param $request
     * @return array
     */
    public static function getNovel($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['comics_id']) {
            $filter['comics_id'] = self::getRequest($request, 'comics_id', 'string');
            $query['comics_id'] = $filter['comics_id'];
        }
        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time', 'string');
            $query['label'] = ['$gte' => $filter['start_time']];
        }
        if ($request['start_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time', 'string');
            $query['label'] = ['$lte' => $filter['end_time']];
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportNovelLogModel::count($query);
        $items = ReportNovelLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['click'] = format_num($item['click'], 2);
            $item['love'] = format_num($item['love'], 2);
            $item['favorite'] = format_num($item['favorite'], 2);
            $items[$index] = $item;
        }
        $countInfo = ReportNovelLogModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    'click' => ['$sum' => '$click'],
                    'love' => ['$sum' => '$love'],
                    'favorite' => ['$sum' => '$favorite'],
                ]
            ]
        ]);
        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRow' => [
                '_id' => '合计',
                'novel_id' => '-',
                'name' => '-',
                'label' => '-',
                'click' => format_num($countInfo['click'], 2),
                'love' => format_num($countInfo['love'], 2),
                'favorite' => format_num($countInfo['favorite'], 2),
                'updated_at' => '-',
                'created_at' => '-',
            ]
        );
    }

    /**
     * 帖子
     * @param $request
     * @return array
     */
    public static function getPost($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['post_id']) {
            $filter['post_id'] = self::getRequest($request, 'post_id', 'string');
            $query['post_id'] = $filter['post_id'];
        }
        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time', 'string');
            $query['label'] = ['$gte' => $filter['start_time']];
        }
        if ($request['start_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time', 'string');
            $query['label'] = ['$lte' => $filter['end_time']];
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportPostLogModel::count($query);
        $items = ReportPostLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['click'] = format_num($item['click'], 2);
            $item['love'] = format_num($item['love'], 2);
            $item['favorite'] = format_num($item['favorite'], 2);
            $items[$index] = $item;
        }
        $countInfo = ReportPostLogModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    'click' => ['$sum' => '$click'],
                    'love' => ['$sum' => '$love'],
                    'favorite' => ['$sum' => '$favorite'],
                ]
            ]
        ]);
        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRow' => [
                '_id' => '合计',
                'post_id' => '-',
                'name' => '-',
                'label' => '-',
                'click' => format_num($countInfo['click'], 2),
                'love' => format_num($countInfo['love'], 2),
                'favorite' => format_num($countInfo['favorite'], 2),
                'updated_at' => '-',
                'created_at' => '-',
            ]
        );
    }

    /**
     * 广告
     * @param $request
     * @return array
     */
    public static function getAdv($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['adv_id']) {
            $filter['adv_id'] = self::getRequest($request, 'adv_id', 'string');
            $query['adv_id'] = $filter['adv_id'];
        }

        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time');
            $query['created_at']['$gte'] = strtotime($filter['start_time']);
        }
        if ($request['end_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time');
            $query['created_at']['$lte'] = strtotime($filter['end_time'] . " 23:59:59");
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportAdvLogModel::count($query);
        $items = ReportAdvLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['click'] = format_num($item['click'], 2);
            $items[$index] = $item;
        }
        $countInfo = ReportAdvLogModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    'click' => ['$sum' => '$click'],
                ]
            ]
        ]);
        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRow' => [
                '_id' => '合计',
                'adv_id' => '-',
                'name' => '-',
                'label' => '-',
                'click' => format_num($countInfo['click'], 2),
                'updated_at' => '-',
                'created_at' => '-',
            ]
        );
    }

    /**
     * @param $request
     * @return array
     */
    public static function getAdvApp($request = [])
    {
        $page = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort = self::getRequest($request, 'sort', 'string', 'user_reg');
        $order = self::getRequest($request, 'order', 'int', -1);

        $query = array();
        $filter = array();

        if ($request['adv_id']) {
            $filter['adv_id'] = self::getRequest($request, 'adv_id', 'string');
            $query['adv_id'] = $filter['adv_id'];
        }

        if ($request['start_time']) {
            $filter['start_time'] = self::getRequest($request, 'start_time');
            $query['created_at']['$gte'] = strtotime($filter['start_time']);
        }
        if ($request['end_time']) {
            $filter['end_time'] = self::getRequest($request, 'end_time');
            $query['created_at']['$lte'] = strtotime($filter['end_time'] . " 23:59:59");
        }

        $skip = ($page - 1) * $pageSize;
        $fields = array();
        $count = ReportAdvAppLogModel::count($query);
        $items = ReportAdvAppLogModel::find($query, $fields, array($sort => $order), $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['click'] = format_num($item['click'], 2);
            $items[$index] = $item;
        }
        $countInfo = ReportAdvAppLogModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    'click' => ['$sum' => '$click'],
                ]
            ]
        ]);
        return array(
            'filter' => $filter,
            'items' => empty($items) ? array() : array_values($items),
            'count' => $count,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalRow' => [
                '_id' => '合计',
                'adv_id' => '-',
                'name' => '-',
                'label' => '-',
                'click' => format_num($countInfo['click'], 2),
                'updated_at' => '-',
                'created_at' => '-',
            ]
        );
    }


}
