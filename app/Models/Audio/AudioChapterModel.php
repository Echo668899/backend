<?php

declare(strict_types=1);

namespace App\Models\Audio;

use App\Core\Mongodb\MongoModel;

/**
 * 有声章节
 * @package App\Models
 * @property string _id 编号
 * @property string name 名称
 * @property string img 封面
 * @property string preview_content 试听内容
 * @property string content 完整内容
 * @property int sort 排序
 * @property string audio_id 有声编号
 * @property int is_audio 是否是有声有声
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AudioChapterModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'audio_chapter';
}
