<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 域名管理
 * @package App\Models
 * @property string _id 编号
 * @property string type 域名类型（主域名/网站域名）
 * @property string domain 域名
 * @property string tracking_code 统计脚本
 * @property array check 检查节点状态
 * @property int is_disabled 是否禁用
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class DomainModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'domain';
}
