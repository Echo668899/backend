<?php

declare(strict_types=1);

namespace App\Models\Report;

use App\Core\Mongodb\MongoModel;

/**
 * 统计-帖子每日
 * @package App\Models
 * @property string _id 唯一编号{id}_{date}
 * @property int post_id 帖子id
 * @property string name 名称
 * @property string label 日期
 * @property string position 视频所属板块
 * @property string pay_type 购买类型
 * @property int click 观看
 * @property int love 点赞
 * @property int favorite 收藏
 * @property int buy_num 销售数量
 * @property int buy_total 销售金额
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ReportPostLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'report_post_log';
}
