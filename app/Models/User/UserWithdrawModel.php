<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 提现记录
 * @package App\Models
 * @property string _id 编号
 * @property string order_sn 订单编号
 * @property int user_id 用户编号
 * @property string record_type 类型 balance balance_share balance_xxx
 * @property int status 操作状态,0处理中 1已提现 -1拒绝 2申请中 3预处理
 * @property float num 金币数量
 * @property float fee 费率
 * @property string method 提现方式bank alipay
 * @property string account 账号 支付是账号  银行卡就是卡号
 * @property string account_name 用户名称
 * @property string bank_name 银行名称
 * @property string admin_user 操作的管理员
 * @property string error_msg 拒绝原因
 * @property string channel_name 渠道
 * @property string reg_ip 注册ip
 * @property string ip ip
 * @property string address 地区
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserWithdrawModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_withdraw';
}
