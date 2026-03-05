<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 支付日志
 * @package App\Models
 * @property int _id 编号
 * @property string unique_id 唯一标识
 * @property string type 类型
 * @property string order_id 订单编号
 * @property int status 订单状态
 * @property string trade_no 交易单号
 * @property float money 金额
 * @property int pat_at 支付时间
 * @property float pay_rate 支付比例
 * @property string error_msg 错误信息
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class PaymentLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'payment_log';
}
