<?php

declare(strict_types=1);

namespace App\Models\Activity;

use App\Core\Mongodb\MongoModel;

/**
 * 活动
 * @package App\Models
 * @property string _id 编号
 * @property string name 名称
 * @property string tpl_id 模板id
 * @property string tpl_config 模板配置
 * @property string img_x 封面
 * @property string description 活动描述
 * @property int start_time 开始时间
 * @property int end_time 结束时间
 * @property int sort 排序
 * @property string right 权限
 * @property int is_disabled 是否显示
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ActivityModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'activity';
}
