<?php

declare(strict_types=1);

namespace App\Models\Movie;

use App\Core\Mongodb\MongoModel;

/**
 * 视频管理
 * @package App\Models
 * @property int _id 编号
 * @property string mid 媒资库id {媒资库_mid} 例如 xb_1024
 * @property array user_id 用户ID
 * @property int categories 分类id
 * @property array tags 标签id
 * @property string name 名称
 * @property string img_x 图片-横
 * @property string img_y 图片-竖
 * @property string number 番号(厂牌视频,可用于视频关联,默认生成编号)
 * @property int sort 排序
 * @property int favorite 虚拟收藏数
 * @property int real_favorite 真实收藏数
 * @property int click 虚拟点击数
 * @property int real_click 真实点击数
 * @property int love 虚拟点赞数
 * @property int real_love 真实点赞数
 * @property int dislove 虚拟点踩数 没啥用,为了保持结构,默认0
 * @property int real_dislove 真实点踩数
 * @property int favorite_rate 收藏率
 * @property int hot_rate 热度,详见Stats/StatsMovieHotJob
 * @property int click_total 点击总数
 * @property int love_total 点赞总数
 * @property int dislove_total 点踩总数
 * @property int favorite_total 收藏总数
 * @property int score 评分 0-100
 * @property int buy 购买次数
 * @property int comment 真实评论数
 * @property int money 金币
 * @property string pay_type 购买类型 money<0免费 money=0vip money>0金币
 * @property int width 宽度
 * @property int height 高度
 * @property string position 视频所属板块 动漫 视频
 * @property string canvas 视频画布 long横 short竖
 * @property int status 状态 -2处理中 -1已下架 0待上架  1已上架 2待审核 3未通过
 * @property int is_more_link 是否多集 0否 1是
 * @property array links 播放地址 {id,name,duration,preview_url,m3u8_url}
 * @property string description 描述
 * @property array preview_images 预览图片
 * @property int update_status 更新状态 1完 0更新中
 * @property string publisher 发行商
 * @property string issue_date 发行日期
 * @property string icon 角标
 * @property int show_at 上架时间
 * @property int async_at 同步时间
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class MovieModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'movie';
}
