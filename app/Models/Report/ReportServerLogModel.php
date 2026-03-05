<?php

declare(strict_types=1);

namespace App\Models\Report;

use App\Core\Mongodb\MongoModel;

/**
 * app统计数据
 * @package App\Models
 * @property string _id 编号
 * @property string type 类型
 * @property int value 值
 * @property string date 日期
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ReportServerLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'report_server_log';
}
