<?php

declare(strict_types=1);

namespace App\Models\Report;

use App\Core\Mongodb\MongoModel;

/**
 * 统计-用户渠道(邀请的人)日活
 * @package App\Models
 * @property string _id 唯一编号{channel_name}_{date}的md5值
 * @property string channel_name 邀请人id 用户id
 * @property string date 日期
 * @property int reg 注册人数
 * @property int reg_android 注册人数
 * @property int reg_ios 注册人数
 * @property int reg_web 注册人数
 * @property int ip 独立ip数
 * @property int uv 独立用户数
 * @property int pv 页面访问次数
 * @property int view 内容访问次数
 * @property int adv 广告点击
 * @property int adv_app 广告-应用点击
 * @property int daot DAOT 日均使用时长,单位/秒 新增用户总时长/新增用户数量
 * @property int daot_android DAOT 日均使用时长,单位/秒 新增用户总时长/新增用户数量
 * @property int daot_ios DAOT 日均使用时长,单位/秒 新增用户总时长/新增用户数量
 * @property int daot_web DAOT 日均使用时长,单位/秒 新增用户总时长/新增用户数量
 * @property int dau_all 今日总日活,涵历史新增的用户,只要在今天活跃,就算总的
 * @property int dau_0 今日活跃总数(留存)
 * @property int dau_1 次日活跃总数(留存)
 * @property int dau_3 3日活跃总数(留存)
 * @property int dau_5 5日活跃总数(留存)
 * @property int dau_7 7日活跃总数(留存)
 * @property int dau_15 15日活跃总数(留存)
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ReportUserChannelLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'report_user_channel_log';
}
