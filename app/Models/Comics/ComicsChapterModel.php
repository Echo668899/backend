<?php

declare(strict_types=1);

namespace App\Models\Comics;

use App\Core\Mongodb\MongoModel;

/**
 * 漫画章节
 * @package App\Models
 * @property string _id 编号
 * @property string name 名称
 * @property string img 封面
 * @property string content 内容
 * @property int sort 排序
 * @property string comics_id 漫画编号
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ComicsChapterModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'comics_chapter';
}
