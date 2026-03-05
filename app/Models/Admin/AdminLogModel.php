<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Mongodb\MongoModel;

/**
 * 管理员日志
 * @package App\Models
 * @property int _id 编号
 * @property string admin_id 管理员编号
 * @property string admin_name 管理员名称
 * @property string content 内容
 * @property string ip ip
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AdminLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'admin_log';
}
