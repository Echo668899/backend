<?php

declare(strict_types=1);

namespace App\Models\Movie;

use App\Core\Mongodb\MongoModel;

/**
 * 视频专题
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string img 图片
 * @property string bg_img 背景图片
 * @property string position 位置 自己填
 * @property string description 描述
 * @property int sort 排序
 * @property string filter 检索条件
 * @property int is_disabled 是否显示
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class MovieSpecialModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'movie_special';
}
