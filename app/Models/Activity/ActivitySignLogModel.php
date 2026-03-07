<?php

declare(strict_types=1);

namespace App\Models\Activity;

use App\Core\Mongodb\MongoModel;

/**
 * 活动-签到记录
 * @package App\Models
 * @property string _id 编号
 * @property string activity_id 活动编号
 * @property int user_id 用户id
 * @property string username 用户名
 * @property string order_sn 订单号
 * @property string prize_name 奖品名称
 * @property string prize_type 奖品类型
 * @property string prize_image 奖品图片
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ActivitySignLogModel extends MongoModel
{
    static $connection = "default";
    static $collection = "activity_sign_log";
}