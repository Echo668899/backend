<?php

declare(strict_types=1);

namespace App\Models\Ai;

use App\Core\Mongodb\MongoModel;

/**
 * AI订单
 * @package App\Models
 * @property int _id 编号
 * @property string order_sn 订单编号
 * @property string order_type 订单类型
 * @property int user_id 用户编号
 * @property int tpl_id 模板编号
 * @property string device_type 设备类型
 * @property string username 用户名
 * @property string channel_name 渠道名称
 * @property array extra 订单需求
 * @property array out_data 返回处理结果
 * @property int status 处理状态 -2错误(不退款,技术检查)  -1处理失败(退款)  0待处理 1处理中 2处理成功
 * @property float amount 金额
 * @property float real_amount 实际金额
 * @property string task_id 任务 id，ai接口返回的任务id
 * @property int is_delete 是否删除
 * @property string error_msg 错误信息
 * @property string label 日期
 * @property string register_ip 注册ip
 * @property string created_ip 购买ip
 * @property int register_at 注册日期
 * @property string register_date 注册时间
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AiOrderModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'ai_order';
}
