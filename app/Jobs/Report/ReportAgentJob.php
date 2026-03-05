<?php

namespace App\Jobs\Report;

use App\Jobs\BaseJob;
use App\Models\Report\ReportChannelLogModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Services\Common\ChannelService;
use App\Services\Common\ConfigService;
use App\Utils\LogUtil;
use Phalcon\Manager\AgentService;

/**
 * 上报
 * Class ReportAgentJob
 * @package App\Jobs\Report
 */
class ReportAgentJob extends BaseJob
{
    public $startAt;
    public $agentService;

    public function __construct($startAt)
    {
        $this->startAt      = $startAt;
        $agentUrl           = ConfigService::getConfig('agent_url');
        $agentId            = ConfigService::getConfig('agent_appid');
        $agentKey           = ConfigService::getConfig('agent_appkey');
        $this->agentService = new AgentService($agentUrl, $agentId, $agentKey);
    }

    public function handler($uniqid)
    {
        $channels = ChannelService::getAll();
        $this->order($channels);
        $this->point($channels);
        $this->user($channels);
        if (date('H') == '00' && date('i') < 30) {
            $this->day($channels, 2);
        } else {
            $this->day($channels);
        }
    }

    /**
     * @param $channels
     */
    public function order($channels)
    {
        $query     = ['pay_at' => ['$gte' => $this->startAt], 'status' => 1, 'channel_name' => ['$in' => $channels]];
        $count     = UserOrderModel::count($query);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $items  = UserOrderModel::find($query, [], ['_id' => 1], $skip, $pageSize);
            $result = $this->agentService->order($items);
            LogUtil::info(sprintf(__CLASS__ . ' user_order   %s/%s =>%s ', $page, $totalPage, $result ? 'ok' : 'error'));
        }
    }

    /**
     * @param $channels
     */
    public function point($channels)
    {
        $query     = ['pay_at' => ['$gte' => $this->startAt], 'status' => 1, 'channel_name' => ['$in' => $channels]];
        $count     = UserRechargeModel::count($query);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $items  = UserRechargeModel::find($query, [], ['_id' => 1], $skip, $pageSize);
            $result = $this->agentService->recharge($items);
            LogUtil::info(sprintf(__CLASS__ . ' user_recharge   %s/%s =>%s ', $page, $totalPage, $result ? 'ok' : 'error'));
        }
    }

    /**
     * @param $channels
     */
    public function user($channels)
    {
        $query     = ['register_at' => ['$gte' => $this->startAt], 'channel_name' => ['$in' => $channels]];
        $count     = UserModel::count($query, 'register_at');
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $items  = UserModel::find($query, [], ['register_at' => 1], $skip, $pageSize, 'register_at');
            $result = $this->agentService->user($items);
            LogUtil::info(sprintf(__CLASS__ . ' user   %s/%s =>%s ', $page, $totalPage, $result ? 'ok' : 'error'));
        }
    }

    /**
     * 日活
     * @param        $channels
     * @param  mixed $maxDay
     * @return void
     */
    public function day($channels, $maxDay = 1)
    {
        for ($i = 0; $i < $maxDay; $i++) {
            $date  = date('Y-m-d', strtotime("-{$i}day"));
            $query = ['date' => strval($date), 'code' => ['$in' => $channels]];

            $count     = ReportChannelLogModel::count($query, 'date');
            $pageSize  = 1000;
            $totalPage = ceil($count / $pageSize);
            for ($page = 1; $page <= $totalPage; $page++) {
                $skip   = ($page - 1) * $pageSize;
                $items  = ReportChannelLogModel::find($query, [], [], $skip, $pageSize, 'date');
                $result = $this->agentService->day($items);
                LogUtil::info(sprintf(__CLASS__ . ' day   %s/%s =>%s ', $page, $totalPage, $result ? 'ok' : 'error'));
            }
        }
    }

    public function success($uniqid)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
