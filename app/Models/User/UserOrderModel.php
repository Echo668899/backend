<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户会员订单
 * @package App\Models
 * @property int _id 编号
 * @property string order_sn 订单编号
 * @property int user_id 用户编号
 * @property string device_type 设备类型
 * @property string username 用户名
 * @property string channel_name 渠道名称
 * @property int register_at 注册日期
 * @property int group_id 用户组
 * @property string group_name 用户组名称
 * @property int status 支付状态 0未支付 1已支付 -1支付失败
 * @property int day_num 天数
 * @property int gift_num 赠送金币
 * @property int download_num 下载次数
 * @property int discount_coupon 折扣券张数
 * @property int group_rate 折扣率
 * @property int price 价格
 * @property float real_price 真实价格
 * @property string pay_id 支付编号
 * @property string pay_name 支付名称
 * @property int pay_at 支付时间
 * @property float pay_rate 费率
 * @property string trade_sn 交易单号
 * @property string register_ip 注册ip
 * @property string created_ip 购买ip
 * @property string register_date 注册时间
 * @property int jet_lag 从注册之日到该条订单距离多少天 0当日注册当日购买 1当日注册次日购买等等
 * @property string pay_date 支付日期
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserOrderModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_order';
}
