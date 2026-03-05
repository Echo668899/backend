<?php

declare(strict_types=1);

namespace App\Models\Ai;

use App\Core\Mongodb\MongoModel;

/**
 * ai标签管理
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string type 类型  绘画 换脸 换装 文生图片 图生视频 文字转语音
 * @property int is_hot 是否热门 1是 0否
 * @property int count 资源数量
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AiTagModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'ai_tag';
}
