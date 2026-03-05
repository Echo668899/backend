<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户粉丝,关注
 * @package App\Models
 * @property int _id 编号
 * @property int user_id 编号
 * @property int home_id 编号
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserFansModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_fans';
}
