<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 收款单表
 * @package App\Models
 * @property int _id 编号
 * @property string order_sn 订单编号
 * @property string trade_sn 支付编号
 * @property int user_id 用户编号
 * @property string device_type 设备类型
 * @property float price 金额
 * @property float real_price 实际金额
 * @property string record_type 类型vip point金币 game游戏
 * @property int object_id 产品编号
 * @property int pay_id 支付方式编号
 * @property string pay_name 支付方式
 * @property int pay_at 支付时间
 * @property float pay_rate 支付手续费
 * @property string pay_date 支付日期
 * @property string channel_name 渠道名称
 * @property int register_at 注册时间
 * @property int order_at 订单时间
 * @property string register_date 注册日期
 * @property int jet_lag 从注册之日到该条订单距离多少天 0当日注册当日购买 1当日注册次日购买等等
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class CollectionsModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'collections';
}
