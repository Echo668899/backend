<?php

declare(strict_types=1);

namespace App\Models\Ai;

use App\Core\Mongodb\MongoModel;

/**
 * ai模块
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string sub_name 子名称
 * @property int nav_id 模块所属菜单id
 * @property string icon 图标
 * @property int style 风格 样式1:1x1 样式2:1xN(横向滚动) 样式3:2x2 样式4:3x3
 * @property int sort 排序
 * @property string filter 检索条件
 * @property int num 展示数量
 * @property int is_disabled 是否显示
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AiBlockModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'ai_block';
}
