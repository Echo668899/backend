<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * Up主
 * @package App\Models
 * @property int _id 用户ID
 * @property string nickname 昵称
 * @property string headico 头像
 * @property string categories 类型 女优:jp_av 厂牌:brand
 * @property int is_hot 热门 0否 1是
 * @property string cup 胸围 ABCD
 * @property int birthday 出生日期
 * @property int sort 排序
 * @property int first_letter 首字母
 * @property int post_fee_rate 帖子分成
 * @property int post_upload_num 每日文章最大可发布数
 * @property int post_total 帖子数量
 * @property int post_click_total 帖子点击数量
 * @property int post_favorite_total 帖子收藏数量
 * @property int movie_fee_rate 视频分成
 * @property int movie_money_limit 视频价格限制
 * @property int movie_upload_num 每日视频最大可发布数
 * @property int movie_total 视频数量
 * @property int movie_click_total 视频点击数量
 * @property int movie_favorite_total 视频收藏数量
 * @property int movie_hot 单个视频最大热度
 * @property int fans_total 粉丝量
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserUpModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_up';
}
