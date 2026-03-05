<?php

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Core\Repositories\BaseRepository;
use App\Models\User\UserBuyLogModel;

class UserBuyLogRepository extends BaseRepository
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
        $sort     = self::getRequest($request, 'sort', 'string', 'created_at');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['user_id']) {
            $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            $query['user_id']  = $filter['user_id'];
        }
        if ($request['username']) {
            $filter['username'] = self::getRequest($request, 'username', 'string');
            $query['username']  = $filter['username'];
        }

        if ($request['channel_name']) {
            $filter['channel_name'] = self::getRequest($request, 'channel_name', 'string');
            $query['channel_name']  = $filter['channel_name'];
        }
        if ($request['status'] != '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }

        if ($request['object_id']) {
            $filter['object_id'] = self::getRequest($request, 'object_id', 'string');
            $query['object_id']  = $filter['object_id'];
        }

        if ($request['object_type']) {
            $filter['object_type'] = self::getRequest($request, 'object_type');
            $query['object_type']  = $filter['object_type'];
        }
        if ($request['order_sn']) {
            $filter['order_sn'] = self::getRequest($request, 'order_sn');
            $query['order_sn']  = $filter['order_sn'];
        }
        if ($request['start_time']) {
            $filter['start_time']        = self::getRequest($request, 'start_time');
            $query['created_at']['$gte'] = intval(strtotime($filter['start_time']));
        }

        if ($request['end_time']) {
            $filter['end_time']          = self::getRequest($request, 'end_time');
            $query['created_at']['$lte'] = intval(strtotime($filter['end_time']));
        }
        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = UserBuyLogModel::count($query);
        $items  = UserBuyLogModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']   = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']   = date('Y-m-d H:i', $item['updated_at']);
            $item['register_at']  = date('Y-m-d H:i', $item['register_at']);
            $item['channel_name'] = $item['channel_name'] ?: '-';

            $item['object_money']     = format_num($item['object_money'], 2);
            $item['object_money_old'] = format_num($item['object_money_old'], 2);
            $item['object_type']      = CommonValues::getBuyType($item['object_type']);
            $items[$index]            = $item;
        }

        return [
            'filter'   => $filter,
            'items'    => empty($items) ? [] : array_values($items),
            'count'    => $count,
            'page'     => $page,
            'pageSize' => $pageSize
        ];
    }
}
