<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Models\Common\AppLogModel;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

/**
 * 日活
 */
class AppLogService extends BaseService
{
    /**
     * 记录每日活跃
     * @param  int  $userId
     * @return void
     */
    public static function do(int $userId)
    {
        $idValue = date('ymd') . '_' . $userId;
        $keyName = 'app_log:' . $idValue;
        $redis   = redis();
        if ($redis->exists($keyName) == false) {
            $userInfo = UserService::getInfoFromCache($userId);
            if (empty($userInfo)) {
                return;
            }
            AppLogModel::findAndModify(
                ['_id' => $idValue],
                [
                    '$set' => [
                        'date'          => date('Y-m-d'),
                        'user_id'       => $userId,
                        'ip'            => CommonUtil::getClientIp(),
                        'month'         => date('Y-m'),
                        'channel_name'  => strval($userInfo['channel_name']),
                        'device_type'   => $userInfo['device_type'],
                        'register_date' => $userInfo['register_date'],
                        'jet_lag'       => UserService::regDiff($userInfo),
                        'updated_at'    => time()
                    ],
                    '$setOnInsert' => [
                        '_id'        => $idValue,
                        'created_at' => time(),
                    ]
                ],
                [],
                true,
                true
            );
            $endTime = CommonUtil::getTodayEndTime() - time() + 180;
            $redis->set($keyName, 1, $endTime);
        }
    }
}
