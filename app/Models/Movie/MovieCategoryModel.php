<?php

declare(strict_types=1);

namespace App\Models\Movie;

use App\Core\Mongodb\MongoModel;

/**
 * 视频分类管理
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string position 位置
 * @property int is_hot 是否热门 1是 0否
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class MovieCategoryModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'movie_category';
}
