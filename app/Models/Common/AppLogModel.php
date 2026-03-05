<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * APP日志
 * @package App\Models
 * @property string _id 编号
 * @property int user_id 用户编号
 * @property string date 日期
 * @property string month 日期
 * @property string channel_name 渠道
 * @property string device_type 设备类型
 * @property string register_date 注册日期
 * @property int jet_lag 从注册之日到该条数据距离多少天 0当日 1次日等等
 * @property string ip ip
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AppLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'app_log';
}
