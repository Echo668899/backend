<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Mongodb\MongoModel;

/**
 * 系统资源
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string parent_id 上级id
 * @property int sort 排序
 * @property string key 唯一标识符
 * @property string class_name 样式名
 * @property string link 链接
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AuthorityModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'authority';
}
