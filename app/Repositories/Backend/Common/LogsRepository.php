<?php

namespace App\Repositories\Backend\Common;

use App\Core\Repositories\BaseRepository;
use App\Models\Admin\AdminLogModel;
use App\Models\Common\SmsLogModel;

class LogsRepository extends BaseRepository
{
    /**
     * 获取日志列表
     * @param        $request
     * @return array
     */
    public static function getAdminList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);

        $query  = [];
        $filter = [];

        if ($request['admin_id']) {
            $filter['admin_id'] = self::getRequest($request, 'admin_id', 'int');
            $query['admin_id']  = $filter['admin_id'];
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
        $count  = AdminLogModel::count($query);
        $items  = AdminLogModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $items[$index]      = $item;
        }

        return [
            'filter'   => $filter,
            'items'    => empty($items) ? [] : array_values($items),
            'count'    => $count,
            'page'     => $page,
            'pageSize' => $pageSize
        ];
    }

    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getSmsList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);

        $query  = [];
        $filter = [];

        if ($request['phone']) {
            $filter['phone'] = self::getRequest($request, 'phone');
            $query['phone']  = $filter['phone'];
        }
        if ($request['ip']) {
            $filter['ip'] = self::getRequest($request, 'ip');
            $query['ip']  = $filter['ip'];
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
        $count  = count($query);
        $items  = SmsLogModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $items[$index]      = $item;
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
