<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户收藏,适用于收藏某个板块
 * @package App\Models
 * @property string _id 编号 {user_id}_{object_type}_{object_id}
 * @property int user_id 编号
 * @property string object_type 资源类型
 * @property string object_id 资源类型
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserFavoriteModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_favorite';
}
