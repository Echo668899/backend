<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * @package App\Models
 * @property int _id 编号
 * @property int user_id 邀请人
 * @property int share_id 被邀请人
 * @property string label 日期
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserShareLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_share_log';
}
