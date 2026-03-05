<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\User\UserCodeLogModel;
use App\Models\User\UserCodeModel;
use App\Models\User\UserModel;
use App\Utils\CommonUtil;

/**
 *  兑换码
 * @package App\Services
 */
class UserCodeService extends BaseService
{
    /**
     * 保存数据
     * @param                 $data
     * @return bool|int|mixed
     */
    public static function save($data)
    {
        if ($data['_id']) {
            return UserCodeModel::updateById($data, $data['_id']);
        }
        $data['used_num'] = 0;
        $data['code_key'] = substr(CommonUtil::getId(), 8, 16);
        if (empty($data['expired_at'])) {
            $data['expired_at'] = strtotime('+30 days');
        } else {
            $data['expired_at'] = strtotime($data['expired_at']);
        }
        $num = empty($data['num']) ? 1 : $data['num'] * 1;
        for ($index = 0; $index < $num; $index++) {
            while (true) {
                $code    = self::createCode();
                $codeRow = UserCodeModel::count(['code' => $code]);
                if (empty($codeRow)) {
                    $data['code'] = $code;
                    UserCodeModel::insert($data);
                    break;
                }
            }
        }

        return true;
    }

    /**
     * 使用兑换码
     * @param                    $userId
     * @param                    $code
     * @return bool
     * @throws BusinessException
     */
    public static function doCode($userId, $code)
    {
        $code   = strtoupper(strval($code));
        $userId = intval($userId);
        if (empty($code) || strlen($code) < 5) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '兑换码错误!');
        }
        $user = UserModel::findByID($userId);
        UserService::checkDisabled($user);

        $userCode = UserCodeModel::findFirst(['code' => $code]);
        if (empty($userCode)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '兑换码不存在!');
        }
        if ($userCode['status'] || $userCode['used_num'] >= $userCode['can_use_num']) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '该兑换码已使用!');
        }
        $now = time();
        if ($userCode['expired_at'] < $now) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '兑换码已经过期!');
        }
        $userCodeLog = UserCodeLogModel::count(['code_key' => $userCode['code_key'], 'user_id' => $userId]);
        if ($userCodeLog) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '同一组兑换码只能用一次!');
        }
        //        没啥用
        //        $userCodeLog = $this->userCodeLogService->findFirst(array('user_id' => $userId), array(), array('created_at' => -1));
        //        if ($userCodeLog) {
        //            $codeEndTime = $userCodeLog['created_at'] + 86400 * $userCodeLog['add_num'];
        //            if ($now < $codeEndTime) {
        //                throw new BusinessException(StatusCode::DATA_ERROR, '兑换的vip未过期之前，不能重复兑换!');
        //            }
        //        }

        try {
            $usedNum = intval($userCode['used_num']) + 1;
            $update  = [
                '$inc' => ['used_num' => 1]
            ];
            if ($usedNum == $userCode['can_use_num']) {
                $update['$set'] = [
                    'status' => 1
                ];
            }
            $result1 = UserCodeModel::updateRaw($update, ['_id' => $userCode['_id'], 'can_use_num' => ['$gte' => $usedNum]]);
            $data    = [
                'name'      => $userCode['name'],
                'type'      => $userCode['type'],
                'code'      => $userCode['code'],
                'code_id'   => $userCode['_id'] * 1,
                'user_id'   => $user['_id'],
                'object_id' => $userCode['object_id'] * 1,
                'username'  => $user['username'],
                'code_key'  => $userCode['code_key'],
                'add_num'   => $userCode['add_num'] * 1
            ];
            if (empty($result1)) {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, '兑换错误,请稍后再试!');
            }
            $result2 = UserCodeLogModel::insert($data);
            if ($userCode['type'] == 'point') {// 兑换金币
                $productInfo = UserProductService::getInfo($userCode['object_id']);
                if (empty($productInfo)) {
                    throw new BusinessException(StatusCode::PARAMETER_ERROR, '金币套餐不存在!');
                }
                $result3 = AccountService::addBalance($user, CommonUtil::createOrderNo('AR'), $userCode['add_num'], 1, 'balance', '使用金币兑换码', $userCode['code']);
            } else {
                $result3 = UserService::doChangeGroup($user, $userCode['add_num'], $userCode['object_id']);
            }
            if ($result2 && $result3) {
                UserService::setInfoToCache($user['_id']);
                return true;
            }
        } catch (\Exception $exception) {
        }
        throw new BusinessException(StatusCode::PARAMETER_ERROR, '兑换错误,请稍后再试!');
    }

    /**
     * @return string
     *                生成兑换码
     */
    protected static function createCode()
    {
        $string       = 'QAZWSXEDCRFVTGBYHNUMJKLP123456789';
        $len          = strlen($string);
        $returnString = '';
        for ($i = 1; $i <= 8; $i++) {
            $rand = mt_rand(0, $len - 1);
            $returnString .= $string[$rand];
        }
        return $returnString;
    }
}
