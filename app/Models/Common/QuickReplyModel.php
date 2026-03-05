<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 快捷回复
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string content 内容
 * @property int sort 排序
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class QuickReplyModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'quick_reply';
}
