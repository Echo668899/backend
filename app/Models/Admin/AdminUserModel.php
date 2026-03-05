<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\Mongodb\MongoModel;

/**
 * 管理用户
 * @package App\Models
 * @property int _id 编号
 * @property int role_id 角色编号
 * @property string real_name 真实姓名
 * @property string email Email
 * @property string username 用户名
 * @property string password 密码
 * @property string slat 密码盐
 * @property string google_code 谷歌验证码
 * @property string login_at 登陆时间
 * @property string login_ip 登陆ip
 * @property int login_num 登陆次数
 * @property int is_disabled 是否禁用
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AdminUserModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'admin_user';
}
