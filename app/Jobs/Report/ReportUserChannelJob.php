<?php

namespace App\Jobs\Report;

use App\Jobs\BaseJob;
use App\Models\Common\AppLogModel;
use App\Models\Report\ReportUserChannelLogModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Models\User\UserShareLogModel;
use App\Services\Report\ReportAdvAppLogService;
use App\Services\Report\ReportAdvLogService;
use App\Services\Report\ReportChannelLogService;
use App\Services\User\UserActiveService;
use App\Utils\LogUtil;

/**
 * 用户邀请统计
 */
class ReportUserChannelJob extends BaseJob
{
    public function handler($_id)
    {
        $this->doChannelDau(date('Y-m-d'));

        // /0点的时候还需要统计昨日数据
        if (date('H') == '00' && date('i') < 30) {
            $this->doChannelDau(date('Y-m-d', strtotime('-1day')));
        }
    }

    /**
     * 渠道日活统计
     * @param  string $date 基准日期
     * @return void
     */
    public function doChannelDau(string $date)
    {
        // 获取指定日期的邀请记录
        $shareLogs = [];
        $channels  = [];
        $page      = 1;
        $pageSize  = 1000;
        while (true) {
            $query = [
                'created_at' => ['$gte' => strtotime($date), '$lt' => strtotime($date . ' 23:59:59')]
            ];
            $rows = UserShareLogModel::find($query, ['user_id', 'share_id'], [], ($page - 1) * $pageSize, $pageSize);
            if (empty($rows)) {
                break;
            }
            foreach ($rows as $row) {
                $shareLogs[strval($row['user_id'])][] = $row['share_id'];
                // 提取出渠道(此处为username)
                $channels[] = strval($row['user_id']);
            }
            $page++;
        }

        // 插入_all渠道,为汇总
        array_unshift($channels, '_all');

        foreach ($channels as $channel) {
            LogUtil::info(__CLASS__ . " 统计用户分享:{$channel} {$date}日数据");
            if ($channel == '_all') {
                $childIds = value(function () use ($shareLogs) {
                    $ids = [];
                    foreach ($shareLogs as $userId => $childIds) {
                        foreach ($childIds as $childId) {
                            $ids[] = $childId;
                        }
                    }
                    return $ids;
                });
            } else {
                $childIds = $shareLogs[$channel];
            }

            // 注册用户数
            $userAndroidReg = value(function () use ($channel, $date, $childIds) {
                $query = ['_id' => ['$in' => $childIds], 'register_date' => $date, 'device_type' => 'android'];
                return UserModel::count($query);
            });
            $userIosReg = value(function () use ($channel, $date, $childIds) {
                $query = ['_id' => ['$in' => $childIds], 'register_date' => $date, 'device_type' => 'ios'];
                return UserModel::count($query);
            });
            $userWebReg = value(function () use ($channel, $date, $childIds) {
                $query = ['_id' => ['$in' => $childIds], 'register_date' => $date, 'device_type' => 'web'];
                return UserModel::count($query);
            });
            $reg = $userAndroidReg + $userIosReg + $userWebReg;

            // 计算该渠道的会员充值
            $order = value(function () use ($channel, $date, $childIds) {
                $query = ['user_id' => ['$in' => $childIds], 'created_at' => ['$gte' => strtotime($date), '$lte' => strtotime($date . ' 23:59:59')]];
                return $this->doCounterOrder($query);
            });
            // 计算该渠道的金币充值
            $recharge = value(function () use ($channel, $date, $childIds) {
                $query = ['user_id' => ['$in' => $childIds],  'created_at' => ['$gte' => strtotime($date), '$lte' => strtotime($date . ' 23:59:59')]];
                return $this->doCounterRecharge($query);
            });

            // 计算该渠道今日累计日活
            $dauAll = AppLogModel::count(['user_id' => ['$in' => $childIds], 'date' => $date]);

            // 生成唯一ID
            $idValue = md5($channel . '_' . $date);
            ReportUserChannelLogModel::findAndModify(
                ['_id' => $idValue],
                [
                    '$set' => [
                        'reg'         => $reg,
                        'reg_android' => $userAndroidReg,
                        'reg_ios'     => $userIosReg,
                        'reg_web'     => $userWebReg,

                        'ip'      => ReportChannelLogService::getUserIPCount($channel, $date),
                        'uv'      => ReportChannelLogService::getUserUVCount($channel, $date),
                        'pv'      => ReportChannelLogService::getUserPVCount($channel, $date),
                        'view'    => ReportChannelLogService::getUserViewCount($channel, $date),
                        'adv'     => ReportAdvLogService::getFieldUserCount($date, 'click', $channel),
                        'adv_app' => ReportAdvAppLogService::getFieldUserCount($date, 'click', $channel),

                        // 平均在线时长=渠道每日总在线时长/渠道每日总新增
                        'daot' => value(function () use ($reg, $channel, $date) {
                            $time = UserActiveService::getOnlineTimeUser($channel, $date);
                            if ($reg > 0) {
                                return round($time / $reg, 2);
                            }
                            return 0;
                        }),
                        'daot_android' => value(function () use ($userAndroidReg, $channel, $date) {
                            $time = UserActiveService::getOnlineTimeUser($channel, $date, 'android');
                            if ($userAndroidReg > 0) {
                                return round($time / $userAndroidReg, 2);
                            }
                            return 0;
                        }),
                        'daot_ios' => value(function () use ($userIosReg, $channel, $date) {
                            $time = UserActiveService::getOnlineTimeUser($channel, $date, 'ios');
                            if ($userIosReg > 0) {
                                return round($time / $userIosReg, 2);
                            }
                            return 0;
                        }),
                        'daot_web' => value(function () use ($userWebReg, $channel, $date) {
                            $time = UserActiveService::getOnlineTimeUser($channel, $date, 'web');
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
                LogUtil::info(__CLASS__ . " 统计用户分享:{$channel} {$regDate}日注册 {$day}日后($date)的日活");

                $query = [
                    'register_date' => $regDate, // 注册日期 = 基准日往前 N 天
                    'date'          => $date,             // 活跃日期 = 基准日
                    /**用户邀请不可能一天超过总注册的 1/50 ,所以用$in暂时没有问题,只有统计_all渠道的时候才会出现很多$childIds**/
                    'user_id' => ['$in' => $childIds],
                ];

                $count = AppLogModel::count($query);

                ReportUserChannelLogModel::findAndModify(
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
