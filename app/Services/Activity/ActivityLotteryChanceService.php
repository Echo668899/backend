<?php

namespace App\Services\Activity;

use App\Core\Services\BaseService;
use App\Models\Activity\ActivityLotteryChanceModel;

/**
 * 活动-抽奖-次数
 */
class ActivityLotteryChanceService extends BaseService
{
    public static function fmt($userId, $activityId)
    {
        return "{$activityId}_{$userId}";
    }

    /**
     * 获取指定活动的抽奖次数
     * @param            $userId
     * @param            $activityId
     * @return int|mixed
     */
    public static function getNum($userId, $activityId)
    {
        $row = ActivityLotteryChanceModel::findByID(self::fmt($userId, $activityId));
        return $row['value'] ?? 0;
    }

    /**
     * 增加|减少计数
     * @param       $userId
     * @param       $activityId
     * @param       $value
     * @return void
     */
    public static function inc($userId, $activityId, $value = 1)
    {
        ActivityLotteryChanceModel::findAndModify(
            ['_id' => self::fmt($userId, $activityId)],
            [
                '$inc' => [
                    'value' => $value,
                ],
                '$setOnInsert' => [
                    'activity_id' => strval($activityId),
                    'user_id'     => intval($userId),
                    'created_at'  => time(),
                    'updated_at'  => time(),
                ]
            ],
            [],
            true,
            true
        );
    }
}
