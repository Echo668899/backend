<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户组
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string description 描述
 * @property string tips 促销提示
 * @property int is_disabled 状态
 * @property int sort 排序
 * @property string img 图片
 * @property string icon 图标
 * @property string group 分组 普通:normal 暗网:dark
 * @property int rate 购片折扣
 * @property int coupon_num 折扣券张数
 * @property float price 价格
 * @property float old_price 原价
 * @property int day_num 可用天数
 * @property int gift_num 赠送金币
 * @property int download_num 下载次数
 * @property string day_tips 天数提示
 * @property string price_tips 价格提示
 * @property array right 权益数组
 * @property string activity_id 绑定的促销活动id
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserGroupModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_group';
}
