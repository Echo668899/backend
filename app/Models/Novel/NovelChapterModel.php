<?php

declare(strict_types=1);

namespace App\Models\Novel;

use App\Core\Mongodb\MongoModel;

/**
 * 小说章节
 * @package App\Models
 * @property string _id 编号
 * @property string name 名称
 * @property string img 封面
 * @property string content 内容
 * @property int sort 排序
 * @property string novel_id 小说编号
 * @property int is_audio 是否是有声小说
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class NovelChapterModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'novel_chapter';
}
