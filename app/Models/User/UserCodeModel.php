<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户兑换码
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string code_key 兑换码key
 * @property int status 状态 0未使用 1已使用 -1作废
 * @property int object_id 资源id 用户组或金币
 * @property int type 兑换码类型 用户组:group 金币:point
 * @property string code 兑换码
 * @property int can_use_num 使用次数
 * @property int used_num 已使用次数
 * @property int add_num 增加数量 天数或金币数
 * @property int expired_at 有效期 过期无效
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserCodeModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_code';
}
