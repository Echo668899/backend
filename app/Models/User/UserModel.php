<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户
 * @package App\Models
 * @property int _id 编号
 * @property string nickname 昵称
 * @property string username 用户名
 * @property string country 国家 英文
 * @property string lang 语言
 * @property string area 国际电话区号
 * @property string phone 手机号码
 * @property string account_type 账户类型 deviceId email google
 * @property string account 唯一账号 统一小写格式化
 * @property string password 账户密码
 * @property string device_type 设备类型
 * @property float device_version 设备版本号
 * @property float balance 充值余额
 * @property float balance_freeze 冻结余额
 * @property float balance_income 收入余额
 * @property float balance_income_freeze 冻结收入余额
 * @property float balance_share 邀请余额,邀请任务送现金
 * @property float balance_share_freeze 冻结邀请余额
 * @property int group_id 用户组
 * @property int group_rate 用户折扣
 * @property string group_name 用户组名称
 * @property int group_start_time 组开始时间
 * @property int group_end_time 组结束时间
 * @property int group_icon 组图标
 * @property array right 用户权益
 * @property int group_dark_id 用户组
 * @property int group_dark_rate 用户折扣
 * @property string group_dark_name 用户组名称
 * @property int group_dark_start_time 组开始时间
 * @property int group_dark_end_time 组结束时间
 * @property string headico 头像
 * @property string headbg 背景图
 * @property string sign 个性签名
 * @property string sex 性别 unknown man woman
 * @property string age 年龄
 * @property string height 身高
 * @property string weight 体重 kg
 * @property int fans 粉丝数量
 * @property int follow 关注数量
 * @property int love 获赞数量
 * @property int share 分享次数
 * @property string tag 用户标签
 * @property string channel_name 渠道
 * @property string parent_name 推荐人
 * @property int parent_id 推荐人编号
 * @property int transfer_id 资金转移ID
 * @property int withdraw_fee 提现费率
 * @property object withdraw_info 提现信息(历史)
 * @property int first_pay 第一次充值时间
 * @property int last_pay 最后一次充值时间
 * @property int pay_total 累计充值金额
 * @property int register_at 注册时间
 * @property string register_date 注册日
 * @property string register_ip 注册ip
 * @property int login_num 登录次数
 * @property int login_at 最后登录时间
 * @property string login_date 最后登录日
 * @property string login_ip 最近登录ip
 * @property array login_device 登录设备
 * @property int is_disabled 是否禁用 1是 0否
 * @property string error_msg 禁用原因
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user';
}
