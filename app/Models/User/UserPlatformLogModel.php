<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户跨平台资金托管
 * @package App\Models
 * @property string _id 编号
 * @property string platform_code 第三方平台标识 如lsj_aigirl lsj_aitools
 * @property int user_id 用户id
 * @property string username 用户名
 * @property float enter_amount 转入(冻结)金额
 * @property float exit_amount 转出(结算)金额,//未使用,因为退出直接删除当前数据
 * @property string status 状态,已转入第三方:locked 已正常退出并结算:settled 结算错误:error
 * @property int enter_time 进入时间
 * @property int exit_time 退出时间//未使用,因为退出直接删除当前数据
 * @property int error_num 下分错误次数
 * @property string error_msg 下分错误信息
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserPlatformLogModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_platform_log';
}
