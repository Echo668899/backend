<?php

declare(strict_types=1);

namespace App\Models\Activity;

use App\Core\Mongodb\MongoModel;

/**
 * 活动-抽奖机会
 * @package App\Models
 * @property string _id 编号 {activity_id}_{user_id}_{次数类型}
 * @property string activity_id 活动编号
 * @property int user_id 用户id
 * @property int value 剩余次数
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ActivityLotteryChanceModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'activity_lottery_chance';
}
