<?php

declare(strict_types=1);

namespace App\Models\Post;

use App\Core\Mongodb\MongoModel;

/**
 * 帖子标签管理
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string description 介绍
 * @property string attribute 属性 新番 原作 人物
 * @property int is_hot 是否热门 1是 0否
 * @property int is_show_upload 是否显示在上传 1是 0否
 * @property int count 资源数量
 * @property int click 点击数量
 * @property int love 点赞数量
 * @property int favorite 收藏数量
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class PostTagModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'post_tag';
}
