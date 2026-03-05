<?php

namespace App\Jobs\Center;

use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Utils\LogUtil;
use Phalcon\Manager\Center\CenterDataService;

/**
 * 数据中心
 */
class CenterDataJob extends CenterBaseJob
{
    public $action;

    /**
     * @param $action
     */
    public function __construct($action)
    {
        $this->action = $action;
    }

    public function handler($_id)
    {
        switch ($this->action) {
            case 'report':
                $this->report(time() - 3600 * 2);
                break;
            case 'queue':
                $this->queue();
                break;
        }
    }

    /**
     * 上报
     * @param  mixed $startAt
     * @return void
     */
    public function report($startAt)
    {
        $configs = self::getCenterConfig('data');

        // / 用户数据
        $userAction = function ($startAt, $configs) {
            $where = ['register_at' => ['$gte' => $startAt]];

            $count     = UserModel::count($where);
            $pageSize  = 1000;
            $totalPage = ceil($count / $pageSize);
            $result    = [];// /结构同埋点
            for ($page = 1; $page <= $totalPage; $page++) {
                $skip = ($page - 1) * $pageSize;

                $items = UserModel::find($where, ['_id', 'register_ip', 'account_type', 'account', 'device_type', 'channel_name', 'register_at'], ['register_at' => -1], $skip, $pageSize);
                foreach ($items as $item) {
                    $result[] = value(function () use ($item) {
                        CenterDataService::setSessionId();
                        CenterDataService::setClientIp($item['register_ip']);
                        CenterDataService::setDeviceType($item['device_type']);
                        CenterDataService::setUserId($item['_id']);
                        //                    CenterDataService::setUserAgent($_SERVER['HTTP_USER_AGENT']);
                        CenterDataService::setDeviceId($item['account']);
                        CenterDataService::setChannelCode($item['channel_name'] ?? '');
                        //                    CenterDataService::setDataCenterPushStatus($configs['status']);

                        return CenterDataService::getReportData('user_register', [
                            'type'        => strval($item['account_type'] == 'device' ? 'deviceid' : $item['account_type']),
                            'trace_id'    => strval(CenterDataService::uuidV4()),
                            'create_time' => intval($item['register_at']),
                        ]);
                    });
                }

                LogUtil::info(sprintf(__CLASS__ . ' user %s/%s', $page, $totalPage));
            }

            $result = array_chunk($result, 100);
            foreach ($result as $chunk) {
                CenterDataService::doReportCenter("{$configs['push_url']}/api/eventTracking/batchReport.json", $chunk);
            }
        };
        // / 订单数据-vip
        $vipAction = function ($startAt, $configs) {
            $where = ['created_at' => ['$gte' => $startAt]];

            $count     = UserOrderModel::count($where);
            $pageSize  = 1000;
            $totalPage = ceil($count / $pageSize);
            $result    = [];// /结构同埋点
            for ($page = 1; $page <= $totalPage; $page++) {
                $skip = ($page - 1) * $pageSize;

                $items   = UserOrderModel::find($where, [], ['created_at' => -1], $skip, $pageSize);
                $userIds = array_column($items, 'user_id');
                $users   = [];
                if (!empty($userIds)) {
                    $users = UserModel::find(['_id' => ['$in' => $userIds]], ['_id', 'register_ip', 'device_type', 'account', 'channel_name', 'register_at'], [], 0, count($userIds));
                    $users = array_column($users, null, '_id');
                }
                foreach ($items as $item) {
                    if ($item['pay_id'] == -1) {
                        continue;
                    }
                    $result[] = value(function () use ($item, $users, $configs) {
                        $userRow = $users[$item['user_id']];
                        CenterDataService::setSessionId();
                        CenterDataService::setClientIp($item['created_ip']);
                        CenterDataService::setDeviceType($userRow['device_type']);
                        CenterDataService::setUserId($userRow['_id']);
                        //                    CenterDataService::setUserAgent($_SERVER['HTTP_USER_AGENT']);
                        CenterDataService::setDeviceId($userRow['account']);
                        CenterDataService::setChannelCode($userRow['channel_name'] ?? '');
                        //                    CenterDataService::setDataCenterPushStatus($configs['status']);

                        if ($item['status'] == 1) {
                            // doVipOrderPay
                            return CenterDataService::getReportData('order_paid', [
                                'order_id'            => strval($configs['appid'] . '_' . $item['_id']),
                                'order_type'          => 'vip_subscription',
                                'product_id'          => strval($item['group_id']),
                                'amount'              => intval($item['real_price'] * 100),
                                'currency'            => 'CNY',
                                'coin_quantity'       => 0,
                                'vip_expiration_time' => time() + $item['day_num'] * 86400,
                                'pay_type'            => $item['pay_name'],
                                'pay_channel'         => '',
                                'transaction_id'      => strval($item['trade_sn']),
                                'create_time'         => intval($item['pay_at']),
                            ]);
                        }
                        // doVipOrder
                        return CenterDataService::getReportData('order_created', [
                            'order_id'          => strval($configs['appid'] . '_' . $item['_id']),
                            'order_type'        => 'vip_subscription',
                            'product_id'        => strval($item['group_id']),
                            'product_name'      => strval($item['group_name']),
                            'amount'            => intval($item['price'] * 100),
                            'currency'          => 'CNY',
                            'coin_quantity'     => 0,
                            'vip_duration_type' => strval($item['group_id']),
                            'vip_duration_name' => strval($item['group_name']),
                            'source_page_key'   => strval('vip'),
                            'source_page_name'  => strval('个人中心'),
                            'create_time'       => intval($item['created_at']),
                        ]);
                    });
                }
                LogUtil::info(sprintf(__CLASS__ . ' user_order %s/%s', $page, $totalPage));
            }

            $result = array_chunk($result, 100);
            foreach ($result as $chunk) {
                CenterDataService::doReportCenter("{$configs['push_url']}/api/eventTracking/batchReport.json", $chunk);
            }
        };
        // / 订单数据-金币
        $rechargeAction = function ($startAt, $configs) {
            $where = ['created_at' => ['$gte' => $startAt]];

            $count     = UserRechargeModel::count($where);
            $pageSize  = 1000;
            $totalPage = ceil($count / $pageSize);
            $result    = [];// /结构同埋点
            for ($page = 1; $page <= $totalPage; $page++) {
                $skip = ($page - 1) * $pageSize;

                $items   = UserRechargeModel::find($where, [], ['created_at' => -1], $skip, $pageSize);
                $userIds = array_column($items, 'user_id');
                $users   = [];
                if (!empty($userIds)) {
                    $users = UserModel::find(['_id' => ['$in' => $userIds]], ['_id', 'register_ip', 'device_type', 'account', 'channel_name', 'register_at'], [], 0, count($userIds));
                    $users = array_column($users, null, '_id');
                }

                foreach ($items as $item) {
                    if ($item['pay_id'] == -1) {
                        continue;
                    }
                    $result[] = value(function () use ($item, $users, $configs) {
                        $userRow = $users[$item['user_id']];
                        CenterDataService::setSessionId();
                        CenterDataService::setClientIp($item['created_ip']);
                        CenterDataService::setDeviceType($userRow['device_type']);
                        CenterDataService::setUserId($userRow['_id']);
                        //                    CenterDataService::setUserAgent($_SERVER['HTTP_USER_AGENT']);
                        CenterDataService::setDeviceId($userRow['account']);
                        CenterDataService::setChannelCode($userRow['channel_name'] ?? '');
                        //                    CenterDataService::setDataCenterPushStatus($configs['status']);

                        if ($item['status'] == 1) {
                            // /doRechargeOrderPay
                            return CenterDataService::getReportData('order_paid', [
                                'order_id'            => strval($configs['appid'] . '_' . $item['_id']),
                                'order_type'          => 'coin_purchase',
                                'product_id'          => strval($item['product_id']),
                                'amount'              => intval($item['real_amount'] * 100),
                                'currency'            => 'CNY',
                                'coin_quantity'       => intval($item['num']),
                                'vip_expiration_time' => 0,
                                'pay_type'            => $item['pay_name'],
                                'pay_channel'         => '',
                                'transaction_id'      => strval($item['trade_sn']),
                                'create_time'         => intval($item['pay_at']),
                            ]);
                        }
                        // /doRechargeOrder
                        return CenterDataService::getReportData('order_created', [
                            'order_id'          => strval($configs['appid'] . '_' . $item['_id']),
                            'order_type'        => 'coin_purchase',
                            'product_id'        => strval($item['product_id']),
                            'product_name'      => strval($item['num'] . '金币'),
                            'amount'            => intval($item['amount'] * 100),
                            'currency'          => 'CNY',
                            'coin_quantity'     => intval($item['num']),
                            'vip_duration_type' => strval($item['product_id']),
                            'vip_duration_name' => strval($item['num'] . '金币'),
                            'source_page_key'   => strval('recharge'),
                            'source_page_name'  => strval('个人中心'),
                            'create_time'       => intval($item['created_at']),
                        ]);
                    });
                }
                LogUtil::info(sprintf(__CLASS__ . ' user_recharge %s/%s', $page, $totalPage));
            }

            $result = array_chunk($result, 100);
            foreach ($result as $chunk) {
                CenterDataService::doReportCenter("{$configs['push_url']}/api/eventTracking/batchReport.json", $chunk);
            }
        };

        $userAction($startAt, $configs);
        $vipAction($startAt, $configs);
        $rechargeAction($startAt, $configs);
    }

    /**
     * 队列消费
     * @return void
     */
    public function queue()
    {
        $configs = self::getCenterConfig('data');
        CenterDataService::setRedis(redis());
        CenterDataService::onQueue(function ($rows) use ($configs) {
            if (!empty($configs['push_url'])) {
                CenterDataService::doReportCenter("{$configs['push_url']}/api/eventTracking/batchReport.json", $rows);
            }
        });
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
