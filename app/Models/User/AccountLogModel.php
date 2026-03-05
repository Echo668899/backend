<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 账号日志日志
 * @package App\Models
 * @property int _id 编号
 * @property string order_sn 编号
 * @property int user_id 用户编号
 * @property string username 用户名
 * @property string balance_field 余额字段  balance balance_freeze
 * @property string object_id 对应的事件编号
 * @property float change_value 操作数量
 * @property float old_value 操作前数量
 * @property float new_value 操作后数量
 * @property string remark 备注
 * @property string ext 扩展
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AccountLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'account_log';
}
