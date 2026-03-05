<?php

declare(strict_types=1);

namespace App\Models\Ai;

use App\Core\Mongodb\MongoModel;

/**
 * ai模板
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string description 描述
 * @property string type 类型 绘画 换脸 换装 文生图片 图生视频 文字转语音
 * @property array tags 标签id
 * @property int money 金币
 * @property int adult 是否成人 1是0否
 * @property string img 封面图
 * @property object config 配置值
 * @property int sort 排序
 * @property int is_hot 热门
 * @property int is_disabled 是否显示
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AiTplModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'ai_tpl';
}
