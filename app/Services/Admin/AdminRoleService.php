<?php

namespace App\Services\Admin;

use App\Core\Services\BaseService;
use App\Models\Admin\AdminRoleModel;

class AdminRoleService extends BaseService
{
    public static function getRoles()
    {
        $items     = AdminRoleModel::find([], [], ['sort' => 1], 0, 100);
        $result    = [];
        $result[0] = [
            '_id'         => 0,
            'name'        => '超级管理员',
            'rights'      => '',
            'is_disabled' => 0
        ];
        foreach ($items as $item) {
            $result[$item['_id']] = [
                '_id'         => $item['_id'],
                'name'        => $item['name'],
                'rights'      => $item['rights'],
                'is_disabled' => $item['is_disabled']
            ];
        }
        return $result;
    }
}
