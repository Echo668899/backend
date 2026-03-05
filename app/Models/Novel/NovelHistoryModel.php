<?php

declare(strict_types=1);

namespace App\Models\Novel;

use App\Core\Mongodb\MongoModel;

/**
 * 小说历史记录管理
 * @package App\Models
 * @property int _id 编号
 * @property int user_id 用户id
 * @property string novel_id 资源编号
 * @property string chapter_id 章节编号
 * @property string chapter_name 章节名称
 * @property string label 日期
 * @property string index 客户端进度
 * @property int status 状态 1正常 0删除
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class NovelHistoryModel extends MongoModel
{
    public static $connection = 'history';
    public static $collection = 'novel_history';
}
