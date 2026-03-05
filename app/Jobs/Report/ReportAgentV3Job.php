<?php

namespace App\Jobs\Report;

use App\Jobs\BaseJob;
use App\Models\Common\AppLogModel;
use App\Models\Report\ReportChannelLogModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Services\Common\ChannelService;
use App\Services\Common\ConfigService;
use App\Utils\LogUtil;
use Phalcon\Manager\AgentV3Service;

/**
 * 上报代理系统v3
 * 渠道相关数据
 */
class ReportAgentV3Job extends BaseJob
{
    public $startAt;

    public function __construct($startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @param       $_id
     * @return void
     */
    public function handler($_id)
    {
        $channels = ChannelService::getAll();
        self::syncChannelDayLog(date('Y-m-d'));
        $this->order($channels);
        $this->point($channels);
        $this->user($channels);
        $this->day($channels);

        // /0点的时候还需要同步昨日统计数据
        if (date('H') == '00' && date('i') < 30) {
            self::syncChannelDayLog(date('Y-m-d', strtotime('-1day')));
        }
    }

    /**
     * 上报订单
     * @param $channels
     */
    public function order($channels)
    {
        $query     = ['pay_at' => ['$gte' => $this->startAt], 'status' => 1, 'channel_name' => ['$in' => $channels]];
        $count     = UserOrderModel::count($query);
        $pageSize  = 300;// /文档要求最大300
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $items  = UserOrderModel::find($query, [], ['_id' => 1], $skip, $pageSize);
            $result = self::getClient()->pushOrder($items);
            LogUtil::info(sprintf(__CLASS__ . ' order   %s/%s =>%s ', $page, $totalPage, $result ? 'ok' : 'error'));
        }
    }

    /**
     * 上报订单
     * @param $channels
     */
    public function point($channels)
    {
        $query     = ['pay_at' => ['$gte' => $this->startAt], 'status' => 1, 'channel_name' => ['$in' => $channels]];
        $count     = UserRechargeModel::count($query);
        $pageSize  = 300;// /文档要求最大300
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $items  = UserRechargeModel::find($query, [], ['_id' => 1], $skip, $pageSize);
            $result = self::getClient()->pushRecharge($items);
            LogUtil::info(sprintf(__CLASS__ . ' order   %s/%s =>%s ', $page, $totalPage, $result ? 'ok' : 'error'));
        }
    }

    /**
     * 上报用户
     * @param $channels
     */
    public function user($channels)
    {
        $query     = ['register_at' => ['$gte' => $this->startAt], 'channel_name' => ['$in' => $channels]];
        $count     = UserModel::count($query, 'register_at');
        $pageSize  = 300;// /文档要求最大300
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $items  = UserModel::find($query, [], ['register_at' => 1], $skip, $pageSize, 'register_at');
            $result = self::getClient()->pushUser($items);
            LogUtil::info(sprintf(__CLASS__ . ' user   %s/%s =>%s ', $page, $totalPage, $result ? 'ok' : 'error'));
        }
    }

    /**
     * 上报日活
     * @param       $channels
     * @return void
     */
    public function day($channels)
    {
        $query     = ['created_at' => ['$gte' => $this->startAt], 'channel_name' => ['$in' => $channels]];
        $count     = AppLogModel::count($query, 'date');
        $pageSize  = 300;// /文档要求最大300
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $items  = AppLogModel::find($query, [], ['_id' => 1], $skip, $pageSize, 'date');
            $result = self::getClient()->pushDay($items);
            LogUtil::info(sprintf(__CLASS__ . ' day   %s/%s =>%s ', $page, $totalPage, $result ? 'ok' : 'error'));
        }
    }

    /**
     * 上报管理员日志
     * @param       $action
     * @param       $username
     * @param       $clientIp
     * @return void
     */
    public static function doAdminLog($action, $username, $clientIp = '')
    {
        try {
            self::getClient()->doActionLog($action, $username, $clientIp);
        } catch (\Exception $e) {
        }
    }

    /**
     * 同步渠道统计
     * @param       $date
     * @param       $channelCode
     * @return void
     */
    public static function syncChannelDayLog($date, $channelCode = '')
    {
        try {
            $rows = self::getClient()->pullChannelDayLog($date, $channelCode);
            foreach ($rows as $row) {
                $channel = $row['channel'];
                // /TODO 复用report_channel_log表,需要和ReportServerJob中的渠道统计一致
                $idValue = md5($channel . '_' . $date);
                $update  = [
                    '$set' => [
                        'agent_v3' => value(function () use ($row) {
                            unset($row['app_id'],$row['channel'],$row['date']);
                            return $row;
                        }),
                        'updated_at' => time()
                    ],
                    '$setOnInsert' => [
                        '_id'        => $idValue,
                        'code'       => $channel,
                        'date'       => $date,
                        'created_at' => time(),
                    ]
                ];
                ReportChannelLogModel::findAndModify(
                    ['_id' => $idValue],
                    $update,
                    [],
                    true
                );
                LogUtil::info(sprintf(__CLASS__ . ' Async Channel Day:%s 渠道:%s', $date, $channel));
            }
        } catch (\Exception $e) {
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    /**
     * @return AgentV3Service
     */
    private static function getClient()
    {
        $agentUrl = ConfigService::getConfig('channel_system_url');
        $agentId  = ConfigService::getConfig('channel_system_app_id');
        $agentKey = ConfigService::getConfig('channel_system_app_key');
        return new AgentV3Service($agentUrl, $agentId, $agentKey);
    }
}
