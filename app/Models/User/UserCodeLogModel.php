<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户兑换码日志
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string code 兑换码
 * @property string code_key 兑换码key
 * @property int code_id 兑换码编号
 * @property int object_id 资源id 用户组或金币
 * @property int type 兑换码类型 用户组:group 金币:point
 * @property int user_id 使用人编号
 * @property string username 用户名
 * @property int add_num 增加数量 天数或金币数
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserCodeLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_code_log';
}
