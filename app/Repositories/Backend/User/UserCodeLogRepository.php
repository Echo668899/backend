<?php

declare(strict_types=1);

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Core\Repositories\BaseRepository;
use App\Models\User\UserCodeLogModel;
use App\Services\User\UserGroupService;
use App\Services\User\UserProductService;

/**
 * 兑换码日志
 * @package App\Repositories\Backend
 */
class UserCodeLogRepository extends BaseRepository
{
    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['type']) {
            $filter['type'] = self::getRequest($request, 'type', 'string');
            $query['type']  = $filter['type'];
        }
        if ($request['user_id']) {
            $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            $query['user_id']  = $filter['user_id'];
        }
        if ($request['group_id']) {
            $filter['group_id'] = self::getRequest($request, 'group_id', 'int');
            $query['group_id']  = $filter['group_id'];
        }
        if ($request['username']) {
            $filter['username'] = self::getRequest($request, 'username');
            $query['username']  = ['$regex' => $filter['username'], '$options' => 'i'];
        }
        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }
        if ($request['code'] != null) {
            $filter['code'] = self::getRequest($request, 'code');
            $query['code']  = $filter['code'];
        }

        $userGroups    = UserGroupService::getAll();
        $productGroups = UserProductService::getAll();
        $skip          = ($page - 1) * $pageSize;
        $fields        = [];
        $count         = UserCodeLogModel::count($query);
        $items         = UserCodeLogModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['user_group']  = $userGroups[$item['group_id']]['name'];
            $item['type']        = CommonValues::getUserCodeType($item['type']);
            $item['object_name'] = $item['type'] == 'point' ? $productGroups[$item['object_id']]['name'] : $userGroups[$item['object_id']]['name'];
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
