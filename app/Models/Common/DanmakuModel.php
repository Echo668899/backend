<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 弹幕表
 * @package App\Models
 * @property int _id 彈幕编号
 * @property string object_id 资源编号
 * @property string sub_id 子资源编号(如章节ID、视频集数ID、音频集数ID等)
 * @property string object_type 资源类型
 * @property int user_id 用户id
 * @property int pos 弹幕位置,movie为播放进度/s,comics和novel为该章节的阅读进度
 * @property int size 字体大小
 * @property int color RGB色
 * @property int pool 弹幕池
 * @property string content 内容
 * @property int status 状态 正常1 待审核0
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class DanmakuModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'danmaku';
}
