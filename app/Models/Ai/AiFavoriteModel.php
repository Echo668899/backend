<?php

declare(strict_types=1);

namespace App\Models\Ai;

use App\Core\Mongodb\MongoModel;

/**
 * 收藏
 * @package App\Models
 * @property string _id 编号
 * @property int user_id 用户编号
 * @property int order_id 订单编号
 * @property int folder_id 收藏夹编号
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AiFavoriteModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'ai_favorite';
}
