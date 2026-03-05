<?php

declare(strict_types=1);

namespace App\Models\Report;

use App\Core\Mongodb\MongoModel;

/**
 * 日志-广告点击
 * @package App\Models
 * @property int _id 编号
 * @property string adv_id 广告id
 * @property string name 广告名称
 * @property string label 日期
 * @property string channel_name 渠道码
 * @property int click 点击次数
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ReportAdvLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'report_adv_log';
}
