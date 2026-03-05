<?php

declare(strict_types=1);

namespace App\Models\Movie;

use App\Core\Mongodb\MongoModel;

/**
 * 视频菜单
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string code 唯一标识,首页用home
 * @property string style 样式
 * @property int sort 排序
 * @property string position 分区
 * @property string filter 搜索条件
 * @property string seo_title seo-标题
 * @property string seo_keywords seo-关键字
 * @property string seo_description seo-描述
 * @property int is_disabled 是否禁用
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class MovieNavModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'movie_nav';
}
