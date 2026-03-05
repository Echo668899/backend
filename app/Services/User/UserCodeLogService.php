<?php

namespace App\Services\User;

use App\Core\Services\BaseService;
use App\Models\User\UserCodeLogModel;

/**
 *  兑换码日志
 * @package App\Services
 */
class UserCodeLogService extends BaseService
{
    /**
     * 兑换记录
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function getLog($userId, $page = 1, $pageSize = 12)
    {
        $query = ['user_id' => $userId];
        $count = UserCodeLogModel::count($query);
        $rows  = UserCodeLogModel::find($query, [], ['_id' => -1], ($page - 1) * $pageSize, $pageSize);
        foreach ($rows as &$row) {
            $row = [
                'id'    => strval($row['_id']),
                'code'  => strval($row['code']),
                'name'  => strval($row['name']),
                'tips'  => $row['type'] == 'group' ? strval("会员:{$row['add_num']}天") : "金币:{$row['add_num']}个",
                'label' => date('Y-m-d H:i:s', $row['created_at']),
            ];
            unset($row);
        }
        return [
            'data'         => $rows,
            'total'        => strval($count),
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }
}
