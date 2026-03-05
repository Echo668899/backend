<?php

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Core\Repositories\BaseRepository;
use App\Models\User\AccountLogModel;

/**
 * Class AccountLogRepository
 * @package App\Repositories\Backend
 */
class AccountRepository extends BaseRepository
{
    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 10);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);

        $query  = [];
        $filter = [];

        if ($request['user_id']) {
            $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            $query['user_id']  = $filter['user_id'];
        }
        if ($request['order_sn']) {
            $filter['order_sn'] = self::getRequest($request, 'order_sn');
            $query['order_sn']  = $filter['order_sn'];
        }
        if ($request['type']) {
            $filter['type'] = self::getRequest($request, 'type', 'int');
            $query['type']  = $filter['type'];
        }
        if ($request['balance_field']) {
            $filter['balance_field'] = self::getRequest($request, 'balance_field');
            $query['balance_field']  = $filter['balance_field'];
        }
        if ($request['start_time']) {
            $filter['start_time']        = self::getRequest($request, 'start_time');
            $query['created_at']['$gte'] = strtotime($filter['start_time']);
        }

        if ($request['end_time']) {
            $filter['end_time']          = self::getRequest($request, 'end_time');
            $query['created_at']['$lte'] = strtotime($filter['end_time']);
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];

        $count = AccountLogModel::count($query);
        $items = AccountLogModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['type']          = CommonValues::getAccountLogsType($item['type']);
            $item['created_at']    = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']    = date('Y-m-d H:i', $item['updated_at']);
            $item['change_value']  = ($item['change_value'] > 0 ? '+' : '') . $item['change_value'];
            $item['balance_field'] = $item['balance_field'] . ' | ' . CommonValues::getBalanceField($item['balance_field']);
            $items[$index]         = $item;
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
