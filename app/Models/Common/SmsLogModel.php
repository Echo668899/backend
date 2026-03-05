<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 短信日志
 * @package App\Models
 * @property string _id 编号
 * @property string phone 手机号码
 * @property string content 短信内容
 * @property string error_info 错误内容
 * @property string ip ip
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class SmsLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'sms_log';
}
