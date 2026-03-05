<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 广告
 * @package App\Models
 * @property string _id 编号
 * @property string name 广告名称
 * @property string description 广告位描述
 * @property string position_code 广告位标识
 * @property string type 广告类型 text文本 video视频 image图片
 * @property string right 权利 (全部:all 普通用户:normal 会员:vip)
 * @property string channel_code 渠道code
 * @property string content 广告内容
 * @property int start_time 开始时间
 * @property int end_time 结束
 * @property int show_time 展示时长
 * @property int sort 排序
 * @property int click 点击次数
 * @property string link 广告链接
 * @property int is_disabled 是否禁用0 1禁用
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AdvModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'adv';
}
