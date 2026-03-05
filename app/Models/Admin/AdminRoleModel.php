<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Mongodb\MongoModel;

/**
 * 管理角色
 * @package App\Models
 * @property int _id 编号
 * @property string name 角色名
 * @property string rights 权限
 * @property int is_disabled 是否禁用
 * @property string description 描述
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AdminRoleModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'admin_role';
}
