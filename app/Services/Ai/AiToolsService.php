<?php

namespace App\Services\Ai;

use App\Constants\StatusCode;
use App\Core\Mongodb\DB;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Common\AiToolsJob;
use App\Models\User\UserModel;
use App\Models\User\UserPlatformLogModel;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\JobService;
use App\Services\User\AccountService;
use App\Services\User\UserPlatformLogService;
use App\Services\User\UserService;
use Exception;
use Phalcon\Manager\MediaLSJAiToolsService;

/**
 * Ai工具
 */
class AiToolsService extends BaseService
{
    /**
     * 固定写死
     * @var string
     */
    public static $platform = 'lsj_aitools';

    /**
     * TODO 注意项目金币比例,必须为正数,项目金币比例为1:1,则填1, 金币比例为1:10则填10
     * @var int
     */
    public static $balanceRate = 1;

    /**
     * @return MediaLSJAiToolsService
     */
    public static function getClient()
    {
        $mediaUrl   = ConfigService::getConfig('media_api');
        $mediaAppid = ConfigService::getConfig('media_appid');
        $mediaKey   = ConfigService::getConfig('media_key');
        return new MediaLSJAiToolsService($mediaUrl, $mediaKey, $mediaAppid, env()->path('app.name'));
    }

    /**
     * 进入
     * @param                    $userId
     * @param  mixed             $theme
     * @return array|string[]
     * @throws BusinessException
     */
    public static function enter($userId, $theme = 'dark')
    {
        $userId  = intval($userId);
        $userRow = UserModel::findByID($userId);
        UserService::checkDisabled($userRow);
        $balance = $userRow['balance'];

        self::tryExit($userId);

        /**========由于调用外部接口,所以采用分段事务以免阻塞=========*/

        // 标记进入
        DB::connect()->startTransaction();
        UserPlatformLogService::enter(self::$platform, $userId);
        // 普通划转冻结
        if ($userRow['balance'] > 0) {
            UserModel::updateRaw([
                '$inc' => [
                    'balance'        => $balance * -1,
                    'balance_freeze' => $balance * 1
                ]
            ], ['_id' => $userId]);
        }
        DB::connect()->commitTransaction();

        try {
            $result = self::getClient()->auth(
                $userRow['_id'],
                $balance / self::$balanceRate,
                $userRow['nickname'],
                CommonService::getCdnUrl($userRow['headico']),
                $theme
            );
            if (empty($result['auth_url'])) {
                throw new Exception('连接失败,请稍后再尝试!');
            }
            UserService::setInfoToCache($userId);
            return [
                'auth_url' => $result['auth_url'],
            ];
        } catch (Exception $e) {
            DB::connect()->startTransaction();
            // 标记退出
            UserPlatformLogService::exit(self::$platform, $userId);

            // 冻结划转普通
            if ($userRow['balance'] > 0) {
                UserModel::updateRaw([
                    '$inc' => [
                        'balance'        => $balance * 1,
                        'balance_freeze' => $balance * -1
                    ]
                ], ['_id' => $userId]);
            }
            DB::connect()->commitTransaction();

            throw new BusinessException(StatusCode::DATA_ERROR, $e->getMessage());
        }
    }

    /**
     * 退出
     * @param                    $userId
     * @return bool
     * @throws BusinessException
     */
    public static function exit($userId)
    {
        $logRow = UserPlatformLogService::has(self::$platform, $userId);
        if (empty($logRow)) {
            return false;
        }
        if ($logRow['enter_amount'] > 0) {
            try {
                $result = self::getClient()->bringOutAssets(
                    $userId
                );
                if (empty($result)) {
                    throw new Exception('连接失败,请稍后再尝试!');
                }
            } catch (Exception $e) {
                $updateData = [
                    '$set' => [
                        'error_msg' => $e->getMessage(),
                    ],
                    '$inc' => [
                        'error_num' => 1,
                    ]
                ];
                if ($logRow['error_num'] >= 3) {
                    $updateData['$set']['status'] = 'error';
                }
                UserPlatformLogModel::updateRaw($updateData, ['_id' => $logRow['_id']]);
                throw new BusinessException(StatusCode::DATA_ERROR, $e->getMessage());
            }
            /**
             * 第一次带出,对方成功,但是网络超时,无响应
             * 第二次再带出,对方返回错误,同时返回上一次带出的钱
             */
            if (isset($result['old_balance'])) {
                $balance = round($result['old_balance']);
            } else {
                $balance = round($result['balance']);
            }
        } else {
            $balance = 0;
        }
        $balance = $balance < 0 ? $balance * -1 : $balance;

        $userRow = UserModel::findByID($userId);

        // 标记退出
        UserPlatformLogService::exit(self::$platform, $userId);
        // 资金划转
        $orderSn = uniqid('AITOOLS');
        if ($balance > 0) {
            AccountService::addBalance($userRow, $orderSn, $balance * self::$balanceRate * 1, 4, 'balance', 'AI工具使用结算,剩余返还');
        }
        if ($logRow['enter_amount'] > 0) {
            AccountService::reduceBalance($userRow, $orderSn, $logRow['enter_amount'], 4, 'balance_freeze', 'AI工具使用结算,冻结释放');
        }
        return true;
    }

    /**
     * 尝试退出
     * @param                    $userId
     * @return void
     * @throws BusinessException
     */
    public static function tryExit($userId)
    {
        /* 禁止重复带入,顺序一定是规范的流程 带入->带出->带入->... */
        $logRow = UserPlatformLogService::has(self::$platform, $userId);
        if (!empty($logRow)) {
            $job      = new AiToolsJob($userId);
            $job->_id = 'AITOOLS' . $logRow['_id'];
            JobService::create($job, 'default', kProdMode ? 'mongodb' : 'sync');
            throw new BusinessException(StatusCode::DATA_ERROR, '当前已在AI工具中,请等待结算后重试!');
        }
    }
}
