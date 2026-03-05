<?php

namespace App\Services\Activity\Handler;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Models\Activity\ActivityLotteryLogModel;
use App\Models\User\UserModel;
use App\Services\Activity\ActivityLotteryChanceService;
use App\Services\User\AccountService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

/**
 * 抽奖
 */
class LotteryHandler extends BaseHandler
{
    /**
     * @return string
     */
    public function code(): string
    {
        return 'lottery';
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return '抽奖';
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return '抽奖，包含虚拟奖励、实物奖励、概率控制';
    }

    /**
     * @return array[]
     */
    public function schema(): array
    {
        return [
            // 1. 奖品配置（奖项、概率、展示名称）
            'prizes' => [
                'type'        => 'list',
                'label'       => '奖品列表',
                'item_schema' => [
                    'name'    => ['type' => 'text', 'label' => '奖品名称'],
                    'rate'    => ['type' => 'number', 'label' => '中奖概率'],
                    'image'   => ['type' => 'image', 'label' => '奖品图片'],
                    'options' => [
                        'vip'    => '会员',
                        'point'  => '金币',
                        'credit' => '积分',
                        'entity' => '实物',
                        'none'   => '无奖品',
                    ],
                ],
            ],

            // 2. 每日抽奖次数
            'max_times_per_day' => [
                'type'    => 'number',
                'label'   => '每人每日最大抽奖次数 （0 = 不限制）',
                'default' => 0,
            ],

            // 3. 参与方式（单选）
            'mode' => [
                'type'    => 'select',
                'label'   => '参与方式',
                'options' => [
                    [
                        'code'        => 'chance',
                        'name'        => '消耗抽奖机会参与',
                        'description' => '每次参与需要消耗的机会（0 = 不消耗）',
                    ],
                    [
                        'code'        => 'point',
                        'name'        => '消耗金币参与',
                        'description' => '每次参与需要消耗的金币（0 = 不消耗）',
                    ],
                    [
                        'code'        => 'credit',
                        'name'        => '消耗积分参与',
                        'description' => '每次参与需要消耗的积分（0 = 不消耗）',
                    ],
                ],
                'default' => 'chance',
            ],
        ];
    }

    /**
     * 执行抽奖逻辑
     */
    public function execute(int $userId, array $activityRow)
    {
        $userRow = UserModel::findByID($userId);
        $orderSn = CommonUtil::createOrderNo('ACT');
        // 校验次数限制
        $this->checkTimesLimit($userId, $activityRow);

        // 扣除积分/金币
        $this->costUserResource($userId, $activityRow, $orderSn);

        // 执行抽奖
        $prize = $this->doLottery($activityRow);

        // 记录日志
        ActivityLotteryLogModel::insert([
            'user_id'     => intval($userId),
            'username'    => strval($userRow['username']),
            'activity_id' => strval($activityRow['_id']),
            'order_sn'    => $orderSn,
            'prize_name'  => strval($prize['name']),
            'prize_type'  => strval($prize['type']),
            'prize_image' => strval($prize['image']),
            'prize_rate'  => strval($prize['rate']),
            'cost_model'  => strval($activityRow['tpl_config']['mode'] ?? ''),
            'cost_value'  => intval($activityRow['tpl_config']['mode_value'] ?? 0),
        ]);
        // 发放虚拟奖励
        if ($prize['type'] == 'vip') {
            UserService::doChangeGroup($userRow, max($prize['num'], 0), 1);
        } elseif ($prize['type'] == 'point') {
            AccountService::addBalance($userRow, $orderSn, $prize['num'], 12, 'balance');
        }
        return $prize;
    }

    /**
     * 校验次数
     * @param  int        $userId
     * @param  array      $activityRow
     * @return void
     * @throws \Exception
     */
    private function checkTimesLimit(int $userId, array $activityRow)
    {
        $todayStart = strtotime('today');

        $config = $activityRow['tpl_config'];
        $max    = $config['max_times_per_day'] ?? 1;
        $max    = $max < 0 ? 0 : $max;

        if ($max) {
            // 查询今日次数
            $count = ActivityLotteryLogModel::count([
                'user_id'     => $userId,
                'activity_id' => strval($activityRow['_id']),
                'created_at'  => ['$gte' => $todayStart, '$lt' => $todayStart + 86400]
            ]);
            if ($count >= $max) {
                throw new \Exception('今日抽奖次数已用完');
            }
        }
    }

    /**
     * @param  int         $userId
     * @param  array       $activityRow
     * @param  mixed       $orderSn
     * @return string|true
     */
    private function costUserResource(int $userId, array $activityRow, $orderSn)
    {
        $mode  = $activityRow['tpl_config']['mode'] ?? '';
        $value = $activityRow['tpl_config']['mode_value'] ?? 0;

        if ($mode == 'chance') {
            // 扣次数
            if (ActivityLotteryChanceService::getNum($userId, $activityRow['_id']) <= 0) {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, '次数不足，无法抽奖');
            }
            ActivityLotteryChanceService::inc($userId, $activityRow['_id'], -1);
        } elseif ($mode == 'point') {
            // 扣金币
            $userRow = UserModel::findByID($userId);
            if ($userRow['balance'] < $value) {
                throw new BusinessException(StatusCode::PARAMETER_ERROR, '金币不足，无法抽奖');
            }
            AccountService::reduceBalance($userRow, $orderSn, $value, 8, 'balance');
        } elseif ($mode == 'credit') {
            // 扣积分 //TODO,暂未实现积分字段
            //            $userRow = UserModel::findByID($userId);
            //            if($userRow['credit']<$value){
            //                throw new BusinessException(StatusCode::PARAMETER_ERROR,'积分不足，无法抽奖');
            //            }
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '积分不足，无法抽奖');
        }
    }

    /**
     * 执行抽奖
     * @param  array          $activityRow
     * @return mixed|string[]
     */
    private function doLottery(array $activityRow)
    {
        $prizes = $activityRow['tpl_config']['prizes'] ?? [];
        $total  = 0;
        foreach ($prizes as $prize) {
            $total += $prize['rate'];
        }
        $rand    = mt_rand(1, $total);
        $current = 0;

        foreach ($prizes as $prize) {
            $current += $prize['rate'];
            if ($rand <= $current) {
                return $prize;
            }
        }

        // 理论上不会走到这里
        return [
            'name' => '无奖品',
            'num'  => '1',
            'rate' => '0',
            'type' => 'none'
        ];
    }
}
