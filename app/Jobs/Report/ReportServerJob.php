<?php

namespace App\Jobs\Report;

use App\Constants\CommonValues;
use App\Jobs\BaseJob;
use App\Models\Common\AppLogModel;
use App\Models\Report\ReportChannelLogModel;
use App\Models\Report\ReportServerLogModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Services\Common\ChannelService;
use App\Services\Report\ReportAdvAppLogService;
use App\Services\Report\ReportAdvLogService;
use App\Services\Report\ReportChannelLogService;
use App\Services\User\UserActiveService;
use App\Utils\LogUtil;

/**
 * 系统统计
 */
class ReportServerJob extends BaseJob
{
    public function handler($_id)
    {
        $this->doCounter(date('Y-m-d'));
        $this->doChannelDau(date('Y-m-d'));

        // /0点的时候还需要统计昨日数据
        if (date('H') == '00' && date('i') < 30) {
            $this->doCounter(date('Y-m-d', strtotime('-1day')));
            $this->doChannelDau(date('Y-m-d', strtotime('-1day')));
        }
    }

    /**
     * 统计日活等
     * @param  string $date
     * @return void
     */
    public function doCounter(string $date)
    {
        $userTotal = 0;
        // 设备用户数
        foreach (CommonValues::getDeviceTypes() as $deviceType => $_v) {
            LogUtil::info(__CLASS__ . " 统计设备:{$deviceType} {$date}日注册");
            $type  = 'device_type_' . $deviceType;
            $count = UserModel::count([
                'register_at' => ['$lte' => strtotime($date . ' 23:59:59')],
                'device_type' => $deviceType,
            ], 'register_at');
            $idValue = md5($date . '_' . $type);
            ReportServerLogModel::findAndModify(
                ['_id' => $idValue],
                [
                    '$set' => [
                        'type'       => $type,
                        'value'      => intval($count),
                        'date'       => $date,
                        'updated_at' => time()
                    ],
                    '$setOnInsert' => [
                        'created_at' => time(),
                    ]
                ],
                [],
                true,
                true
            );

            $userTotal += $count;
        }

        // 用户总数
        $type    = 'user_total';
        $idValue = md5($date . '_' . $type);
        ReportServerLogModel::findAndModify(
            ['_id' => $idValue],
            [
                '$set' => [
                    'type'       => $type,
                    'value'      => intval($userTotal),
                    'date'       => $date,
                    'updated_at' => time()
                ],
                '$setOnInsert' => [
                    'created_at' => time(),
                ]
            ],
            [],
            true,
            true
        );

        // 今日注册
        $userRegTotal = UserModel::count(['register_date' => $date], 'register_date');
        $type         = 'user_reg';
        $idValue      = md5($date . '_' . $type);
        ReportServerLogModel::findAndModify(
            ['_id' => $idValue],
            [
                '$set' => [
                    'type'       => $type,
                    'value'      => intval($userRegTotal),
                    'date'       => $date,
                    'updated_at' => time()
                ],
                '$setOnInsert' => [
                    'created_at' => time(),
                ]
            ],
            [],
            true,
            true
        );

        // 今日日活
        $userDauTotal = AppLogModel::count(['date' => $date]);
        $ip           = ReportChannelLogService::getIPCount('_all', $date);
        $pv           = ReportChannelLogService::getPVCount('_all', $date);
        $uv           = ReportChannelLogService::getUVCount('_all', $date);
        $type         = 'app_day';
        $idValue      = md5($date . '_' . $type);
        ReportServerLogModel::findAndModify(
            ['_id' => $idValue],
            [
                '$set' => [
                    'type'  => $type,
                    'value' => [
                        'dau' => intval($userDauTotal),
                        'ip'  => intval($ip),
                        'pv'  => intval($pv),
                        'uv'  => intval($uv),
                    ],
                    'date'       => $date,
                    'updated_at' => time()
                ],
                '$setOnInsert' => [
                    'created_at' => time(),
                ]
            ],
            [],
            true,
            true
        );

        // 今日充值
        $order    = $this->doCounterOrder(['created_at' => ['$gte' => strtotime($date), '$lte' => strtotime($date . ' 23:59:59')]]);
        $recharge = $this->doCounterRecharge(['created_at' => ['$gte' => strtotime($date), '$lte' => strtotime($date . ' 23:59:59')]]);
        $type     = 'money';
        $idValue  = md5($date . '_' . $type);
        ReportServerLogModel::findAndModify(
            ['_id' => $idValue],
            [
                '$set' => [
                    'type'  => $type,
                    'value' => [
                        'total_order'    => ($order['total_order'] + $recharge['total_order']),
                        'success_order'  => ($order['success_order'] + $recharge['success_order']),
                        'success_amount' => ($order['success_amount'] + $recharge['success_amount']),
                    ],
                    'date'       => $date,
                    'updated_at' => time()
                ],
                '$setOnInsert' => [
                    'created_at' => time(),
                ]
            ],
            [],
            true,
            true
        );
    }

    /**
     * 渠道日活统计
     * @param  string $date 基准日期
     * @return void
     */
    public function doChannelDau(string $date)
    {
        // 获取所有非空渠道
        $channels = ChannelService::getAll();

        // 插入_all渠道,为汇总
        array_unshift($channels, '_all');

        foreach ($channels as $channel) {
            LogUtil::info(__CLASS__ . " 统计渠道:{$channel} {$date}日数据");

            // 注册用户数
            $userAndroidReg = value(function () use ($channel, $date) {
                $query = ['channel_name' => $channel, 'register_date' => $date, 'device_type' => 'android'];
                if ($channel === '_all') {
                    unset($query['channel_name']);
                }
                return UserModel::count($query, 'register_date');
            });
            $userIosReg = value(function () use ($channel, $date) {
                $query = ['channel_name' => $channel, 'register_date' => $date, 'device_type' => 'ios'];
                if ($channel === '_all') {
                    unset($query['channel_name']);
                }
                return UserModel::count($query, 'register_date');
            });
            $userWebReg = value(function () use ($channel, $date) {
                $query = ['channel_name' => $channel, 'register_date' => $date, 'device_type' => 'web'];
                if ($channel === '_all') {
                    unset($query['channel_name']);
                }
                return UserModel::count($query, 'register_date');
            });
            $reg = $userAndroidReg + $userIosReg + $userWebReg;

            // 计算该渠道的会员充值
            $order = value(function () use ($channel, $date) {
                $query = ['channel_name' => $channel, 'created_at' => ['$gte' => strtotime($date), '$lte' => strtotime($date . ' 23:59:59')]];
                if ($channel === '_all') {
                    unset($query['channel_name']);
                }
                return $this->doCounterOrder($query);
            });
            // 计算该渠道的金币充值
            $recharge = value(function () use ($channel, $date) {
                $query = ['channel_name' => $channel, 'created_at' => ['$gte' => strtotime($date), '$lte' => strtotime($date . ' 23:59:59')]];
                if ($channel === '_all') {
                    unset($query['channel_name']);
                }
                return $this->doCounterRecharge($query);
            });

            // 计算该渠道今日累计日活
            $dauAll = AppLogModel::count(['channel_name' => $channel, 'date' => $date], 'date');

            // 生成唯一ID
            $idValue = md5($channel . '_' . $date);
            ReportChannelLogModel::findAndModify(
                ['_id' => $idValue],
                [
                    '$set' => [
                        'reg'         => $reg,
                        'reg_android' => $userAndroidReg,
                        'reg_ios'     => $userIosReg,
                        'reg_web'     => $userWebReg,

                        'ip'      => ReportChannelLogService::getIPCount($channel, $date),
                        'uv'      => ReportChannelLogService::getUVCount($channel, $date),
                        'pv'      => ReportChannelLogService::getPVCount($channel, $date),
                        'view'    => ReportChannelLogService::getViewCount($channel, $date),
                        'adv'     => ReportAdvLogService::getFieldCount($date, 'click', $channel),
                        'adv_app' => ReportAdvAppLogService::getFieldCount($date, 'click', $channel),

                        // 平均在线时长=渠道每日总在线时长/渠道每日总新增
                        'daot' => value(function () use ($reg, $channel, $date) {
                            $time = UserActiveService::getOnlineTime($channel, $date);
                            if ($reg > 0) {
                                return round($time / $reg, 2);
                            }
                            return 0;
                        }),
                        'daot_android' => value(function () use ($userAndroidReg, $channel, $date) {
                            $time = UserActiveService::getOnlineTime($channel, $date, 'android');
                            if ($userAndroidReg > 0) {
                                return round($time / $userAndroidReg, 2);
                            }
                            return 0;
                        }),
                        'daot_ios' => value(function () use ($userIosReg, $channel, $date) {
                            $time = UserActiveService::getOnlineTime($channel, $date, 'ios');
                            if ($userIosReg > 0) {
                                return round($time / $userIosReg, 2);
                            }
                            return 0;
                        }),
                        'daot_web' => value(function () use ($userWebReg, $channel, $date) {
                            $time = UserActiveService::getOnlineTime($channel, $date, 'web');
                            if ($userWebReg > 0) {
                                return round($time / $userWebReg, 2);
                            }
                            return 0;
                        }),

                        'dau_all' => $dauAll,

                        'order'    => $order,
                        'recharge' => $recharge,

                        'updated_at' => time()
                    ],
                    '$setOnInsert' => [
                        '_id'          => $idValue,
                        'channel_name' => $channel,
                        'date'         => $date,
                        'dau_0'        => 0,
                        'dau_1'        => 0,
                        'dau_3'        => 0,
                        'dau_5'        => 0,
                        'dau_7'        => 0,
                        'dau_15'       => 0,
                        'created_at'   => strtotime($date),
                    ]
                ],
                [],
                true,
                true
            );

            /**
             * 次日=「基准日的前 1 天注册」在「基准日」活跃
             * 3日=「基准日的前 3 天注册」在「基准日」活跃
             */
            // 0为今天
            $days = [0, 1, 3, 7, 15];
            foreach ($days as $day) {
                $regDate = date('Y-m-d', strtotime($date) - 3600 * 24 * $day);
                LogUtil::info(__CLASS__ . " 统计渠道:{$channel} {$regDate}日注册 {$day}日后($date)的日活");

                $query = [
                    'register_date' => $regDate, // 注册日期 = 基准日往前 N 天
                    'date'          => $date,             // 活跃日期 = 基准日
                    'channel_name'  => $channel,
                ];
                if ($channel === '_all') {
                    unset($query['channel_name']);
                }

                $count = AppLogModel::count($query, 'date');

                ReportChannelLogModel::findAndModify(
                    ['_id' => md5($channel . '_' . $regDate)],
                    [
                        '$set' => [
                            "dau_{$day}" => $count
                        ]
                    ],
                    [],
                    false,
                    true
                );
            }
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    /**
     * 订单统计
     * @param        $query
     * @return array
     */
    private function doCounterOrder($query)
    {
        $result = UserOrderModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    // 订单总数
                    'total_order' => ['$sum' => 1],
                    // 成功订单数
                    'success_order' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$status', 1]],
                                1,
                                0]
                        ]
                    ],
                    // 成功金额
                    'success_amount' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$status', 1]],
                                '$real_price',
                                0
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        return [
            'total_order'    => intval($result['total_order'] ?? 0),
            'success_order'  => intval($result['success_order'] ?? 0),
            'success_amount' => round($result['success_amount'] ?? 0, 2),
        ];
    }

    /**
     * 金币统计
     * @param             $query
     * @return array|null
     */
    private function doCounterRecharge($query)
    {
        $result = UserRechargeModel::aggregate([
            [
                '$match' => $query
            ],
            [
                '$group' => [
                    '_id' => null,
                    // 订单总数
                    'total_order' => ['$sum' => 1],
                    // 成功订单数
                    'success_order' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$status', 1]],
                                1,
                                0]
                        ]
                    ],
                    // 成功金额
                    'success_amount' => [
                        '$sum' => [
                            '$cond' => [
                                ['$eq' => ['$status', 1]],
                                '$real_amount',
                                0
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        return [
            'total_order'    => intval($result['total_order'] ?? 0),
            'success_order'  => intval($result['success_order'] ?? 0),
            'success_amount' => round($result['success_amount'] ?? 0, 2),
        ];
    }
}
