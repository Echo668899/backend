<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 文章分类
 * @package App\Models
 * @property string _id 编号
 * @property string code 唯一标识
 * @property string name 名称
 * @property string img 图片
 * @property string language 语言
 * @property int sort 排序
 * @property int parent_id 上级编号
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ArticleCategoryModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'article_category';
}
