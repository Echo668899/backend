<?php

namespace App\Services\User;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\User\UserModel;
use App\Models\User\UserPlatformLogModel;

/**
 * 用户跨平台资金托管
 */
class UserPlatformLogService extends BaseService
{
    /**
     * @param         $platform
     * @param         $userId
     * @return string
     */
    public static function fmt($platform, $userId)
    {
        return md5($platform . '_' . $userId);
    }

    /**
     * @param                   $platform
     * @param                   $userId
     * @return array|mixed|null
     */
    public static function has($platform, $userId)
    {
        return UserPlatformLogModel::findByID(self::fmt($platform, $userId));
    }

    /**
     * 进入,写记录
     * @param       $platform
     * @param       $userId
     * @return true
     */
    public static function enter($platform, $userId)
    {
        $userId = intval($userId);
        $_id    = self::fmt($platform, $userId);

        $userRow = UserModel::findByID($userId);
        UserPlatformLogModel::findAndModify(
            ['_id' => $_id],
            [
                '$set' => [
                    'username'   => strval($userRow['username']),
                    'enter_time' => time(),
                    'error_num'  => 0,
                    'error_msg'  => '',
                    'updated_at' => time()
                ],
                '$inc' => [
                    'enter_amount' => doubleval($userRow['balance']),
                ],
                '$setOnInsert' => [
                    '_id'           => $_id,
                    'platform_code' => strval($platform),
                    'user_id'       => intval($userId),
                    'exit_amount'   => doubleval(0.0),
                    'status'        => 'locked',
                    'exit_time'     => null,
                    'created_at'    => time(),
                ]
            ],
            [],
            true,
            true
        );
        return true;
    }

    /**
     * 退出,删记录
     * @param                    $platform
     * @param                    $userId
     * @return true
     * @throws BusinessException
     */
    public static function exit($platform, $userId)
    {
        $_id    = self::fmt($platform, $userId);
        $hasRow = UserPlatformLogModel::findByID($_id);
        if (empty($hasRow)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '当前没有在游戏中');
        }
        // /直接删除记录
        UserPlatformLogModel::deleteById($_id);
        return true;
    }
}
