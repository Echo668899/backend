<?php

declare(strict_types=1);

namespace App\Models\Ai;

use App\Core\Mongodb\MongoModel;

/**
 * ai关键字
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property int is_hot 是否热门
 * @property string sort 排序
 * @property int num 次数
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AiKeywordsModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'ai_keywords';
}
