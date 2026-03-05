<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 队列任务
 * @package App\Models
 * @property int _id 编号
 * @property string queue 队列名称
 * @property string server_name 机器名称
 * @property string job 任务
 * @property string exception 异常
 * @property int failed_at 异常时间
 * @property int plan_at 计划执行时间
 * @property int status 状态 0等待 1执行中 2执行成功 -1执行失败
 * @property int success_at 成功时间
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class JobModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'job';
}
