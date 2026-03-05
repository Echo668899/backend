<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 渠道包
 * @package App\Models
 * @property string _id 编号
 * @property string code 渠道标识
 * @property string name 渠道名
 * @property string dual_link 下载链接-双端
 * @property string android_link 下载链接-安卓
 * @property string ios_link 下载链接-IOS
 * @property string line_code 线路编号
 * @property int is_auto 是否自动下载
 * @property int is_disabled 是否禁用
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ChannelApkModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'channel_apk';
}
