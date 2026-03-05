<?php

namespace App\Services\User;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\User\UserModel;
use App\Models\User\UserWithdrawModel;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\IpService;
use App\Utils\CommonUtil;

/**
 * Class WithdrawService
 * @package App\Services
 */
class UserWithdrawService extends BaseService
{
    /**
     * 去提现
     * @param                    $userId
     * @param                    $method
     * @param                    $bankName
     * @param                    $accountName
     * @param                    $account
     * @param                    $num
     * @param                    $balanceField
     * @return true
     * @throws BusinessException
     */
    public static function doWithdraw($userId, $method, $bankName, $accountName, $account, $num, $balanceField)
    {
        $userId  = intval($userId);
        $userRow = UserModel::findByID($userId);
        UserService::checkDisabled($userRow);
        if (!in_array($balanceField, ['balance_income', 'balance_share'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '提现类型错误,仅收入和邀请余额可提现!');
        }
        if ($userRow[$balanceField] < $num) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '可用提现金额不足!');
        }

        $minNum = ConfigService::getConfig('withdraw_min');
        $rate   = $userRow['withdraw_fee'] ?: ConfigService::getConfig('withdraw_fee');
        if ($minNum > $num) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, sprintf('最低提现数量不能低于%s', $minNum));
        }

        if ($method == 'alipay') {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '支付宝维护中,请选择其它方式!');
        }
        if ($method == 'bank' && empty($bankName)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '银行名称不能为空!');
        }
        if ($method == 'usdt' && (empty($account) || empty($accountName))) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '钱包类型和地址不能为空!');
        }

        $actionKey = 'do_withdraw_' . $userId;
        if (!CommonService::checkActionLimit($actionKey, 30, 1)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '操作过于频繁!');
        }

        $ip   = CommonUtil::getClientIp();
        $data = [
            'order_sn'     => CommonUtil::createOrderNo(ConfigService::getConfig('order_prefix') . 'W'),
            'user_id'      => intval($userId),
            'username'     => strval($userRow['username']),
            'channel_name' => strval($userRow['channel_name']),
            'record_type'  => strval($balanceField),
            'status'       => 0,
            'method'       => strval($method),
            'num'          => doubleval($num),
            'fee'          => doubleval($num * $rate * 0.01),
            'account'      => strval($account),
            'account_name' => strval($accountName),
            'bank_name'    => strval($bankName),
            'ip'           => strval($ip),
            'reg_ip'       => strval($userRow['register_ip']),
            'address'      => value(function () use ($ip) {
                $ipInfo   = IpService::parse($ip);
                $country  = empty($ipInfo['country']) ? 'unknown' : $ipInfo['country'];
                $province = empty($ipInfo['province']) ? 'unknown' : $ipInfo['province'];
                $city     = empty($ipInfo['city']) ? 'unknown' : $ipInfo['city'];
                return $country . '-' . $province . '-' . $city;
            }),
        ];
        try {
            $result1 = UserWithdrawModel::insert($data);
            if ($balanceField == 'balance_income') {
                $result2 = AccountService::reduceBalance($userRow, $data['order_sn'], $data['num'], 2, 'balance_income', '余额提现');
                $result3 = AccountService::addBalance($userRow, $data['order_sn'], $data['num'], 2, 'balance_income_freeze', '余额提现');
            } elseif ($balanceField == 'balance_share') {
                $result2 = AccountService::reduceBalance($userRow, $data['order_sn'], $data['num'], 2, 'balance_share', '分享提现');
                $result3 = AccountService::addBalance($userRow, $data['order_sn'], $data['num'], 2, 'balance_share_freeze', '余额提现');
            }
            if ($result1 && $result2 && $result3) {
                return true;
            }
        } catch (\Exception $exception) {
        }
        throw  new BusinessException(StatusCode::PARAMETER_ERROR, '提现错误,请稍后再试!');
    }

    /**
     * 审核提现
     * @param                    $id
     * @param                    $status   1成功 -1失败
     * @param  string            $errorMsg
     * @return bool
     * @throws BusinessException
     */
    public static function reviewWithdraw($id, $status, $errorMsg = '')
    {
        $id       = intval($id);
        $withdraw = UserWithdrawModel::findByID($id);
        if (empty($withdraw) || $withdraw['status'] != 0) {
            throw new BusinessException(StatusCode::DATA_ERROR, '订单不存在或者状态异常!');
        }
        $userRow = UserModel::findByID($withdraw['user_id']);
        UserService::checkDisabled($userRow);

        if ($status == 1) {
            self::agree($withdraw, $userRow);
        } elseif ($status == -1) {
            self::refuse($withdraw, $userRow, $errorMsg);
        } else {
            throw new BusinessException(StatusCode::DATA_ERROR, '不能解析的提现信息!');
        }
        return true;
    }

    /**
     * 同意
     * @param       $withdraw
     * @param       $userRow
     * @return bool
     */
    public static function agree($withdraw, $userRow)
    {
        UserWithdrawModel::update(['status' => 1], ['_id' => $withdraw['_id']]);
        if ($withdraw['record_type'] == 'balance') {
            AccountService::reduceBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 2, 'balance_freeze', '提现审核通过,冻结金额扣除');// /减去冻结余额
        } elseif ($withdraw['record_type'] == 'balance_income') {
            AccountService::reduceBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 2, 'balance_income_freeze', '提现审核通过,冻结金额扣除');// /减去冻结余额
        } elseif ($withdraw['record_type'] == 'balance_share') {
            AccountService::reduceBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 2, 'balance_share_freeze', '提现审核通过,冻结金额扣除');// /减去冻结余额
        }
        return true;
    }

    /**
     * 拒绝
     * @param        $withdraw
     * @param        $userInfo
     * @param  mixed $userRow
     * @param  mixed $errorMsg
     * @return bool
     */
    public static function refuse($withdraw, $userRow, $errorMsg = '')
    {
        UserWithdrawModel::update(['status' => -1, 'error_msg' => $errorMsg], ['_id' => $withdraw['_id']]);
        if ($withdraw['record_type'] == 'balance') {
            AccountService::addBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 7, 'balance', '提现拒绝,金额解冻');// /加上冻结余额
            AccountService::reduceBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 7, 'balance_freeze', '提现拒绝');// /减去冻结余额
        } elseif ($withdraw['record_type'] == 'balance_income') {
            AccountService::addBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 7, 'balance_income', '提现拒绝,金额解冻');// /加上冻结余额
            AccountService::reduceBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 7, 'balance_income_freeze', '提现拒绝');// /减去冻结余额
        } elseif ($withdraw['record_type'] == 'balance_share') {
            AccountService::addBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 7, 'balance_share', '提现拒绝,金额解冻');// /加上冻结余额
            AccountService::reduceBalance($userRow, $withdraw['order_sn'], $withdraw['num'], 7, 'balance_share_freeze', '提现拒绝');// /减去冻结余额
        }
        return true;
    }

    /**
     * 提现记录
     * @param        $userId
     * @param        $balanceField
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function getLogs($userId, $balanceField, $page = 1, $pageSize = 20)
    {
        $userId = intval($userId);
        $where  = ['user_id' => $userId, 'record_type' => $balanceField];
        $count  = UserWithdrawModel::count($where);
        $rows   = UserWithdrawModel::find($where, [], [], ($page - 1) * $pageSize, $pageSize);
        foreach ($rows as &$row) {
            $row = [
                'id'           => strval($row['_id']),
                'order_sn'     => strval($row['order_sn']),
                'num'          => strval($row['num']),
                'fee'          => strval($row['fee']),
                'method'       => strval($row['method']),
                'account_name' => strval($row['account_name']),
                'account'      => strval($row['account']),
                'bank_name'    => strval($row['bank_name']),
                'status'       => strval($row['status']),
                'status_text'  => CommonValues::getWithdrawStatus($row['status']),
                'created_at'   => date('Y-m-d H:i:s', $row['created_at']),
                'updated_at'   => date('Y-m-d H:i:s', $row['updated_at']),
            ];
            unset($item);
        }
        return  [
            'data'         => $rows,
            'total'        => strval($count),
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }
}
