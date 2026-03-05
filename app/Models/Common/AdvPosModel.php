<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 广告位置
 * @package App\Models
 * @property string _id 编号
 * @property string name 广告位名称
 * @property string code 广告位标识
 * @property int is_disabled 是否禁用0 1禁用
 * @property int width 宽
 * @property int height 高
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AdvPosModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'adv_pos';
}
