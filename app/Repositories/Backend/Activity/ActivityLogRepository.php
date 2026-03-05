<?php

namespace App\Repositories\Backend\Activity;

use App\Constants\CommonValues;
use App\Core\Repositories\BaseRepository;
use App\Models\Activity\ActivityLotteryLogModel;

/**
 * 活动-抽奖-记录
 */
class ActivityLogRepository extends BaseRepository
{
    /**
     * @param        $request
     * @return array
     */
    public static function getLotteryLog($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['user_id']) {
            $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            $query['user_id']  = $filter['user_id'];
        }
        if ($request['username']) {
            $filter['username'] = self::getRequest($request, 'username');
            $query['username']  = $filter['username'];
        }
        if ($request['activity_id']) {
            $filter['activity_id'] = self::getRequest($request, 'activity_id');
            $query['activity_id']  = $filter['activity_id'];
        }
        if ($request['order_sn']) {
            $filter['order_sn'] = self::getRequest($request, 'order_sn');
            $query['order_sn']  = $filter['order_sn'];
        }
        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = ActivityLotteryLogModel::count($query);
        $items  = ActivityLotteryLogModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $items[$index]       = $item;
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
