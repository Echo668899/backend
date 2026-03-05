<?php

namespace App\Jobs\Report;

use App\Jobs\BaseJob;
use App\Models\Common\AppLogModel;
use App\Models\Report\ReportHourLogModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Utils\LogUtil;

/**
 * 强大的小时统计
 * Class ReportHourJob
 * @package App\Jobs\Report
 */
class ReportHourJob extends BaseJob
{
    public $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function handler($uniqid)
    {
        $startTime = strtotime($this->date);
        $endTime   = strtotime(date('Y-m-d 23:59:59', $startTime));
        // 统计24小时
        $pid = $this->count($startTime, $endTime, 0);
        for ($i = 0; $i < 4 * 24; $i++) {
            $startAt = $i * 15 * 60 + $startTime;
            $endAt   = $startAt + 15 * 60;
            LogUtil::info(__CLASS__ . ' ' . date('Y-m-d H:i:s', $startAt) . '至' . date('Y-m-d H:i:s', $endAt));
            $this->count($startAt, $endAt, $pid);
        }
    }

    public function count($startAt, $endAt, $pid)
    {
        $date = date('Y-m-d', $startAt);

        $androidUser = UserModel::count(['register_at' => ['$gte' => $startAt, '$lte' => $endAt], 'device_type' => 'android'], 'register_at');
        $iosUser     = UserModel::count(['register_at' => ['$gte' => $startAt, '$lte' => $endAt], 'device_type' => 'ios'], 'register_at');
        $webUser     = UserModel::count(['register_at' => ['$gte' => $startAt, '$lte' => $endAt], 'device_type' => 'web'], 'register_at');
        $totalUser   = $androidUser + $iosUser + $webUser;

        $androidDay = AppLogModel::count(['created_at' => ['$gte' => $startAt, '$lte' => $endAt], 'device_type' => 'android'], 'created_at');
        $iosDay     = AppLogModel::count(['created_at' => ['$gte' => $startAt, '$lte' => $endAt], 'device_type' => 'ios'], 'created_at');
        $webDay     = AppLogModel::count(['created_at' => ['$gte' => $startAt, '$lte' => $endAt], 'device_type' => 'web'], 'created_at');
        $totalDay   = $androidDay + $iosDay + $webDay;

        $vipTotal      = UserOrderModel::count(['created_at' => ['$gte' => $startAt, '$lte' => $endAt]]);
        $rechargeTotal = UserRechargeModel::count(['created_at' => ['$gte' => $startAt, '$lte' => $endAt]]);

        $succVip = UserOrderModel::aggregate([
            ['$match' => ['created_at' => ['$gte' => $startAt, '$lte' => $endAt], 'status' => 1]],
            ['$group' => ['_id' => null, 'total_money' => ['$sum' => '$real_price'], 'count_num' => ['$sum' => 1]]]
        ]);
        $succVipTotal = $succVip ? $succVip['count_num'] : 0;
        $succVipMoney = $succVip ? $succVip['total_money'] : 0;

        $succRecharge = UserRechargeModel::aggregate([
            ['$match' => ['created_at' => ['$gte' => $startAt, '$lte' => $endAt], 'status' => 1]],
            ['$group' => ['_id' => null, 'total_money' => ['$sum' => '$real_amount'], 'count_num' => ['$sum' => 1]]]
        ]);
        $succRechargeTotal = $succRecharge ? $succRecharge['count_num'] : 0;
        $succRechargeMoney = $succRecharge ? $succRecharge['total_money'] : 0;

        $orderTotal = $vipTotal + $rechargeTotal;
        $succTotal  = $succVipTotal + $succRechargeTotal;
        $succMoney  = $succVipMoney + $succRechargeMoney;

        // 客单价=总金额/成功订单数
        $tav = $succTotal > 0 ? round($succMoney / $succTotal, 2) : 0;
        // 付费率=成功订单数/注册用户数
        $apr = $totalUser > 0 ? round($succTotal / $totalUser * 100, 2) : 0;
        // 用户平均收入
        $arpu = $totalUser > 0 ? round($succMoney / $totalUser, 2) : 0;
        // 支付成功率=成功订单数/总订单数
        $payr = $orderTotal > 0 ? round($succTotal / $orderTotal * 100, 2) : 0;

        $data = [
            '_id'           => md5($startAt . '_' . $endAt),
            'dau'           => $totalDay,
            'dau_android'   => $androidDay,
            'dau_ios'       => $iosDay,
            'dau_web'       => $webDay,
            'reg'           => $totalUser,
            'reg_android'   => $androidUser,
            'reg_ios'       => $iosUser,
            'reg_web'       => $webUser,
            'order'         => $orderTotal,
            'order_success' => $succTotal,
            'order_money'   => $succMoney,
            'tav'           => $tav,
            'apr'           => $apr,
            'payr'          => $payr,
            'arpu'          => $arpu,
            'month'         => date('Y-m', $startAt),
            'date'          => $date,
            'date_limit'    => date('H:i:s', $startAt) . '-' . date('H:i:s', $endAt),
            'pid'           => strval($pid),
            'created_at'    => time(),
            'updated_at'    => time(),
        ];
        ReportHourLogModel::findAndModify(['_id' => $data['_id']], $data, [], true);
        return $data['_id'];
    }

    public function success($uniqid)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
