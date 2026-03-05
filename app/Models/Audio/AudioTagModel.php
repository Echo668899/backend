<?php

declare(strict_types=1);

namespace App\Models\Audio;

use App\Core\Mongodb\MongoModel;

/**
 * 有声标签管理
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string attribute 属性 新番 原作 人物
 * @property int is_hot 是否热门 1是 0否
 * @property int count 资源数量
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AudioTagModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'audio_tag';
}
