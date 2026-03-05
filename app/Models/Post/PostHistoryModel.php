<?php

declare(strict_types=1);

namespace App\Models\Post;

use App\Core\Mongodb\MongoModel;

/**
 * 帖子历史记录管理
 * @package App\Models
 * @property int _id 编号
 * @property int user_id 用户id
 * @property int post_id 资源编号
 * @property string label 日期
 * @property int status 状态 1正常 0删除
 * @property int time 观看时间
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class PostHistoryModel extends MongoModel
{
    public static $connection = 'history';
    public static $collection = 'post_history';
}
