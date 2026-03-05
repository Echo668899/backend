<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户购买记录(视频,漫画)
 * @package App\Models
 * @property int _id 编号
 * @property string order_sn 订单编号
 * @property int user_id 用户id
 * @property int username 用户名
 * @property int channel_name 渠道名称
 * @property int object_id 资源id
 * @property string object_img 资源图片
 * @property string object_type 资源类型 视频:movie 游戏:game
 * @property int object_money 资源金额
 * @property int object_money_old 资源金额-原价
 * @property array ext_ids 扩展id列表,主要用于movie的links
 * @property int register_at 注册日期
 * @property string label 购买日期
 * @property string end_time 有效期
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserBuyLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_buy_log';
}
