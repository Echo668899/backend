<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 文章
 * @package App\Models
 * @property string _id 编号
 * @property string title 标题
 * @property string category_code 分类
 * @property string content 内容
 * @property string img 图片
 * @property string seo_keywords Seo关键字
 * @property string seo_description Seo描述
 * @property string url url链接
 * @property int is_recommend 是否推荐
 * @property int sort 排序
 * @property int click 点击
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ArticleModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'article';
}
