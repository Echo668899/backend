<?php

declare(strict_types=1);

namespace App\Models\Movie;

use App\Core\Mongodb\MongoModel;

/**
 * 视频关键字
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property int is_hot 是否热门
 * @property string sort 排序
 * @property int num 次数
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class MovieKeywordsModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'movie_keywords';
}
