<?php

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Core\Repositories\BaseRepository;
use App\Models\Common\CollectionsModel;

class CollectionsRepository extends BaseRepository
{
    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 30);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['user_id']) {
            $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            $query['user_id']  = $filter['user_id'];
        }

        if ($request['channel_name']) {
            $filter['channel_name'] = self::getRequest($request, 'channel_name', 'string');
            $query['channel_name']  = $filter['channel_name'];
        }

        if ($request['device_type']) {
            $filter['device_type'] = self::getRequest($request, 'device_type', 'string');
            $query['device_type']  = $filter['device_type'];
        }
        if ($request['pay_id']) {
            $filter['pay_id'] = self::getRequest($request, 'pay_id', 'int');
            $query['pay_id']  = $filter['pay_id'];
        }

        if ($request['pay_name']) {
            $filter['pay_name'] = self::getRequest($request, 'pay_name');
            $query['pay_name']  = ['$regex' => $filter['pay_name'], '$options' => 'i'];
        }

        if ($request['record_type']) {
            $filter['record_type'] = self::getRequest($request, 'record_type');
            $query['record_type']  = $filter['record_type'];
        }
        if ($request['device_type']) {
            $filter['device_type'] = self::getRequest($request, 'device_type');
            $query['device_type']  = $filter['device_type'];
        }
        if ($request['order_sn']) {
            $filter['order_sn'] = self::getRequest($request, 'order_sn');
            $query['order_sn']  = $filter['order_sn'];
        }
        if ($request['trade_sn']) {
            $filter['trade_sn'] = self::getRequest($request, 'trade_sn');
            $query['trade_sn']  = $filter['trade_sn'];
        }

        if ($request['start_time']) {
            $filter['start_time']    = self::getRequest($request, 'start_time');
            $query['pay_at']['$gte'] = strtotime($filter['start_time']);
        }

        if ($request['end_time']) {
            $filter['end_time']      = self::getRequest($request, 'end_time');
            $query['pay_at']['$lte'] = strtotime($filter['end_time']);
        }
        if ($request['reg_start_time']) {
            $filter['reg_start_time']     = self::getRequest($request, 'reg_start_time');
            $query['register_at']['$gte'] = strtotime($filter['reg_start_time']);
        }
        if ($request['reg_end_time']) {
            $filter['reg_end_time']       = self::getRequest($request, 'reg_end_time');
            $query['register_at']['$lte'] = strtotime($filter['reg_end_time']);
        }
        $skip   = ($page - 1) * $pageSize;
        $fields = [];

        $count = CollectionsModel::count($query);
        $items = CollectionsModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['register_at'] = date('Y-m-d H:i', $item['register_at']);

            $item['pay_at']       = $item['pay_at'] ? date('m-d H:i:s', $item['pay_at']) : '-';
            $item['pay_date']     = $item['pay_date'] ?: '-';
            $item['pay_name']     = $item['pay_name'] ?: '-';
            $item['channel_name'] = $item['channel_name'] ?: '-';

            $item['record_type'] = CommonValues::getAccountRecordType($item['record_type']);
            $item['price']       = format_num($item['price'], 2);
            $item['real_price']  = format_num($item['real_price'], 2);
            $items[$index]       = $item;
        }
        $moneyCount = CollectionsModel::aggregate([['$match' => $query], ['$group' => ['_id' => null, 'order_money' => ['$sum' => '$real_price'], ]]]);
        return [
            'filter'   => $filter,
            'items'    => empty($items) ? [] : array_values($items),
            'count'    => $count,
            'page'     => $page,
            'pageSize' => $pageSize,
            'totalRow' => [
                '_id'          => '合计',
                'channel_name' => "总数: {$count}",
                'real_price'   => '合计: ' . format_num($moneyCount ? $moneyCount['order_money'] : 0, 2),
            ]
        ];
    }
}
