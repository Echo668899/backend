<?php

declare(strict_types=1);

namespace App\Models\Post;

use App\Core\Mongodb\MongoModel;

/**
 * 社区帖子
 * @package App\Models
 * @property string _id 编号
 * @property int user_id 用户编号
 * @property string source 来源
 * @property string title 标题
 * @property string content 内容
 * @property array tags 标签id
 * @property array at_users 被@用户id
 * @property array images 图片
 * @property array videos 视频
 * @property array files 附件
 * @property string extra_type 扩展类型，如 vote audio 等
 * @property object extra_data 扩展数据 JSON 内容（根据 extra_type 不同结构不同）
 * @property int click 虚拟点击次数
 * @property int real_click 真实点击次数
 * @property int love 虚拟点赞次数
 * @property int real_love 真实点赞次数
 * @property int favorite 虚拟收藏次数
 * @property int real_favorite 真实收藏次数
 * @property int favorite_rate 收藏率
 * @property int hot_rate 热度,详见Stats/StatsPostHotJob
 * @property int click_total 点击总数
 * @property int love_total 点赞总数
 * @property int favorite_total 收藏总数
 * @property int comment 评论次数
 * @property int last_comment 最后评论
 * @property int permission 权限 public(公开) private(私密) subscribe(订阅)
 * @property int money 金币
 * @property string pay_type 购买类型 money<0免费 money=0vip money>0金币
 * @property string position 板块
 * @property int global_top 全局置顶
 * @property int home_top 主页置顶
 * @property string ip ip
 * @property string pos_info 位置信息
 * @property int sort 排序
 * @property int status 状态 -2处理失败 -1处理中 0待审核 1正常 2审核不通过
 * @property string deny_msg 审核未通过原因
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class PostModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'post';
}
