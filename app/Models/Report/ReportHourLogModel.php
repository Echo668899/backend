<?php

declare(strict_types=1);

namespace App\Models\Report;

use App\Core\Mongodb\MongoModel;

/**
 * app统计数据-小时
 * @package App\Models
 * @property string _id 编号
 * @property int dau 总日活
 * @property int dau_android 安卓日活
 * @property int dau_ios IOS日活
 * @property int dau_web WEB日活
 * @property int reg 注册
 * @property int reg_android 安卓注册
 * @property int reg_ios IOS注册
 * @property int reg_web WEB注册
 * @property int order 订单
 * @property int order_success 成功订单数
 * @property int order_money 订单金额
 * @property int tav 客单价
 * @property int apr 日付费率
 * @property int payr 支付成功率
 * @property int arpu 用户平均收入
 * @property string month 年月
 * @property string date 日期
 * @property string date_limit 时间范围
 * @property string pid 上级id
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ReportHourLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'report_hour_log';
}
