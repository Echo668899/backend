<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户金币充值订单
 * @package App\Models
 * @property int _id 编号
 * @property string order_sn 订单编号
 * @property string trade_sn 支付编号
 * @property int user_id 用户编号
 * @property string device_type 设备类型
 * @property string username 用户名
 * @property string record_type 类型 point金币
 * @property int status 状态0处理中 1成功 -1失败
 * @property float amount 金额
 * @property float real_amount 实际金额
 * @property int product_id 产品编号
 * @property int give 赠送数量
 * @property int vip 赠送VIP天数
 * @property int num 数量
 * @property float fee 费率
 * @property string pay_id 支付方式编号
 * @property string pay_name 支付方式
 * @property int pay_at 支付时间
 * @property float pay_rate 支付费率
 * @property string pay_date 支付日期
 * @property string channel_name 渠道
 * @property int register_at 注册时间
 * @property string register_date 注册时间
 * @property int jet_lag 从注册之日到该条订单距离多少天 0当日注册当日购买 1当日注册次日购买等等
 * @property string register_ip 注册ip
 * @property string created_ip ip
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserRechargeModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_recharge';
}
