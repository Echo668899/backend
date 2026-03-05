<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 评论表
 * @package App\Models
 * @property string _id 评论编号
 * @property string object_id 资源编号
 * @property string parent_id 上级评论id
 * @property string comment_id 第一级评论id
 * @property int from_uid 发表评论的用户id
 * @property int to_uid 目标用户id，回复评论用
 * @property string object_type 评论的数据的类型 评论:comment 漫画:comics 小说:novel 有声:audio 视频:movie 帖子:post
 * @property string content 内容
 * @property string comment_type 评论类型 文字:text 图片:image
 * @property string ip IP
 * @property int love 点赞个数
 * @property int child_num 第一级评论含有的回复数量，只统计第一级
 * @property int status 状态 正常1 待审核0
 * @property string time 时间 用于弹幕
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class CommentModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'comment';
}
