<?php

declare(strict_types=1);

namespace App\Models\Comics;

use App\Core\Mongodb\MongoModel;

/**
 * 漫画资源
 * @package App\Models
 * @property string _id 编号
 * @property string name 名称
 * @property string alias_name 名称
 * @property string cat_id 分类
 * @property array tags 标签
 * @property string img_x 封面图-默认
 * @property string img_y 封面图-竖
 * @property int click 虚拟点击数
 * @property int real_click 真实点击数
 * @property int love 虚拟点赞
 * @property int real_love 真实点赞
 * @property int favorite 虚拟收藏数
 * @property int real_favorite 真实收藏数
 * @property int favorite_rate 收藏率
 * @property int click_total 点击总数
 * @property int love_total 点赞总数
 * @property int favorite_total 收藏总数
 * @property int comment 真实评论数
 * @property int buy 购买次数
 * @property int money 金币
 * @property string pay_type 购买类型 money<0免费 money=0vip money>0金币
 * @property int score 评分
 * @property string free_chapter 免费章节
 * @property string description 描述
 * @property int chapter_count 总章节
 * @property int sort 排序
 * @property int is_adult 是否成人 0否 1是
 * @property int status 状态 -1已下架 0待上架  1已上架
 * @property int update_status 更新状态 0更新中 1已完结
 * @property string update_date 更新时间 周一 周二 ...
 * @property string last_update 最后更新时间
 * @property string icon 角标
 * @property int show_at 创建时间
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ComicsModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'comics';
}
