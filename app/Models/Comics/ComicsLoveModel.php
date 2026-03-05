<?php

declare(strict_types=1);

namespace App\Models\Comics;

use App\Core\Mongodb\MongoModel;

/**
 * 点赞
 * @package App\Models
 * @property string _id 编号
 * @property int user_id 用户编号
 * @property int comics_id 漫画编号
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ComicsLoveModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'comics_love';
}
