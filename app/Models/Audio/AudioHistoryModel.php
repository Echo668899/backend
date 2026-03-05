<?php

declare(strict_types=1);

namespace App\Models\Audio;

use App\Core\Mongodb\MongoModel;

/**
 * 有声历史记录管理
 * @package App\Models
 * @property int _id 编号
 * @property int user_id 用户id
 * @property string audio_id 资源编号
 * @property string chapter_id 章节编号
 * @property string label 日期
 * @property int status 状态 1正常 0删除
 * @property int time 观看时间
 * @property string code 观看线路
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AudioHistoryModel extends MongoModel
{
    public static $connection = 'history';
    public static $collection = 'audio_history';
}
