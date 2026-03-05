<?php

namespace App\Services\User;

use App\Core\Services\BaseService;
use App\Models\User\AccountLogModel;
use App\Models\User\UserModel;

/**
 * Class AccountService
 * @package App\Services
 */
class AccountService extends BaseService
{
    /**
     * 增加用户余额
     * @param         $userRow
     * @param         $orderSn
     * @param         $num
     * @param         $type
     * @param         $balanceField
     * @param  string $remark
     * @param  string $ext
     * @return bool
     */
    public static function addBalance($userRow, $orderSn, $num, $type, $balanceField, $remark = '', $ext = '')
    {
        if (!in_array($balanceField, ['balance', 'balance_freeze', 'balance_income', 'balance_income_freeze', 'balance_share', 'balance_share_freeze'])) {
            return false;
        }
        $num  = round($num, 2);
        $data = [
            'order_sn'      => $orderSn,
            'user_id'       => intval($userRow['_id']),
            'username'      => strval($userRow['username']),
            'balance_field' => strval($balanceField),
            'object_id'     => '',
            'change_value'  => $num,
            'old_value'     => round($userRow[$balanceField], 2),
            'new_value'     => round($userRow[$balanceField] + $num, 2),
            'type'          => intval($type), // 余额类型 getAccountLogsType
            'remark'        => $remark,
            'ext'           => $ext,
        ];
        $result1 = UserModel::updateRaw(['$inc' => [$balanceField => $num]], ['_id' => intval($userRow['_id'])]);
        if ($result1) {
            $result2 = AccountLogModel::insert($data);
            UserService::setInfoToCache(intval($userRow['_id']));
            return !empty($result2);
        }
        return false;
    }

    /**
     * 减少用户余额
     * @param         $userRow
     * @param         $orderSn
     * @param         $num
     * @param         $type
     * @param         $balanceField
     * @param  string $remark
     * @param  string $ext
     * @return bool
     */
    public static function reduceBalance($userRow, $orderSn, $num, $type, $balanceField, $remark = '', $ext = '')
    {
        if (!in_array($balanceField, ['balance', 'balance_freeze', 'balance_income', 'balance_income_freeze', 'balance_share', 'balance_share_freeze'])) {
            return false;
        }
        $num  = round($num, 2);
        $num  = $num > 0 ? $num * -1 : $num;
        $data = [
            'order_sn'      => $orderSn,
            'user_id'       => intval($userRow['_id']),
            'username'      => strval($userRow['username']),
            'balance_field' => strval($balanceField),
            'object_id'     => '',
            'change_value'  => $num,
            'old_value'     => round($userRow[$balanceField], 2),
            'new_value'     => round($userRow[$balanceField] + $num, 2),
            'type'          => intval($type), // 余额类型 getAccountLogsType
            'remark'        => $remark,
            'ext'           => $ext,
        ];
        $result1 = UserModel::updateRaw(['$inc' => [$balanceField => $num]], ['_id' => intval($userRow['_id'])]);
        if ($result1) {
            $result2 = AccountLogModel::insert($data);
            UserService::setInfoToCache(intval($userRow['_id']));
            return !empty($result2);
        }
        return false;
    }

    /**
     * 获取余额日志
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param  mixed $balanceField
     * @param        $cursor
     * @return array
     */
    public static function getLogs($userId, $balanceField = 'balance', $page = 1, $pageSize = 20, $cursor = '')
    {
        $userId = intval($userId);
        $where  = ['user_id' => $userId,
            'balance_field'  => $balanceField
        ];
        $count = AccountLogModel::count($where);

        if (!empty($cursor)) {
            $where['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = AccountLogModel::find($where, [], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = AccountLogModel::find($where, [], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }

        return [
            'data' => value(function () use ($rows) {
                foreach ($rows as &$row) {
                    $row = [
                        'id'           => strval($row['_id']),
                        'order_sn'     => strval($row['order_sn']),
                        'change_value' => strval($row['change_value']),
                        'old_value'    => strval($row['old_value'] * 1),
                        'new_value'    => strval($row['new_value'] * 1),
                        'label'        => date('Y-m-d H:i:s', $row['created_at']),
                        'remark'       => strval($row['remark'])
                    ];
                    unset($item);
                }
                return $rows;
            }),
            'total'        => strval($count),
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
            'last_page'    => strval(ceil($count / $pageSize)),
            'cursor'       => !empty($rows) ? strval($rows[count($rows) - 1]['updated_at']) : '',
        ];
    }
}
