<?php

namespace App\Services\User;

use App\Core\Services\BaseService;

/**
 * 用户活跃
 * Class UserActiveService
 * @package App\Services
 */
class UserActiveService extends BaseService
{
    public const KEY = 'user_active';

    /**
     * 客户端心跳-活跃
     * 实现当前在线人数,xx人在看
     * 客户端定时心跳,30s一次
     * @param         $userId
     * @param  string $route  客户端路由
     * @return true
     */
    public static function do($userId, $route)
    {
        $redis   = redis();
        $nowTime = time();

        /* 加入全局在线集合 */
        $key      = self::KEY . ':user';
        $prevTime = $redis->zScore($key, $userId);

        // 多窗口重复心跳（30 秒内多次调用）则直接跳过
        if ($prevTime > 0 && $nowTime - $prevTime < 30) {
            return true;
        }

        $redis->zAdd($key, time(), $userId);
        $redis->zRemRangeByScore($key, 0, $nowTime - 60);// 释放过期记录,每个请求都清理,所以几乎没性能开销

        /* 加入路由集合-统计某个路由的在线人数 */
        $key = self::KEY . ":route:{$route}";
        $redis->zAdd($key, $nowTime, $userId);
        $redis->zRemRangeByScore($key, 0, $nowTime - 60);// 释放过期记录,每个请求都清理,所以几乎没性能开销
        if ($redis->ttl($key) === -1) {
            $redis->expire($key, 86400 * 1);
        }

        /*
         * $prevTime > 0 首次不计算
         * 之前来过，并且在 3 分钟内再次心跳
         */
        if ($prevTime > 0 && $nowTime - $prevTime <= 180) {
            $userInfo = UserService::getInfoFromCache($userId);
            $date     = date('Y-m-d');

            //            //所有-没意义
            //            $key = self::KEY . ":time:{$date}";
            //            $redis->hIncrBy($key, '_all', 30);//总的所有用户
            //            if ($redis->ttl($key) === -1) {
            //                $redis->expire($key, 86400 * 31);
            //            }

            // 渠道侧
            if (!empty($userInfo['channel_name']) && $userInfo['channel_name'] != '_all') {
                //                //渠道总的所有用户-没意义
                //                $redis->hIncrBy($key, $userInfo['channel_name'], 30);

                // 渠道新的用户-有意义
                // 因为渠道平均在线时长=新增用户的在线时长/新增用户数
                // 用于判断渠道当天的质量
                if (UserService::regDiff($userInfo) == 0) {
                    $key = self::KEY . ":time:{$date}";
                    $redis->hIncrBy($key, $userInfo['channel_name'], 30);
                    if ($redis->ttl($key) === -1) {
                        $redis->expire($key, 86400 * 31);
                    }

                    // 不同设备类型
                    $key = self::KEY . ":time_{$userInfo['device_type']}:{$date}";
                    $redis->hIncrBy($key, $userInfo['channel_name'], 30);
                    if ($redis->ttl($key) === -1) {
                        $redis->expire($key, 86400 * 31);
                    }
                }
            }

            // 用户侧
            if (!empty($userInfo['parent_id']) && $userInfo['parent_id'] != '_all') {
                // 因为渠道平均在线时长=新增用户的在线时长/新增用户数
                // 用于判断用户邀请当天的质量
                if (UserService::regDiff($userInfo) == 0) {
                    $key = self::KEY . ":time_user:{$date}";
                    $redis->hIncrBy($key, $userInfo['channel_name'], 30);
                    if ($redis->ttl($key) === -1) {
                        $redis->expire($key, 86400 * 31);
                    }

                    // 不同设备类型
                    $key = self::KEY . ":time_user_{$userInfo['device_type']}:{$date}";
                    $redis->hIncrBy($key, $userInfo['channel_name'], 30);
                    if ($redis->ttl($key) === -1) {
                        $redis->expire($key, 86400 * 31);
                    }
                }
            }
        }
    }

    /**
     * 判断用户是否在线
     * @param mixed $userId
     */
    public static function has($userId)
    {
        $key      = self::KEY . ':user';
        $prevTime = redis()->zScore($key, strval($userId));
        if ($prevTime > 0 && time() - $prevTime < 60) {
            return true;
        }
        return false;
    }

    /**
     * 批量判断用户是否在线
     * @param        $userIds
     * @return array
     */
    public static function hasIds($userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        $key    = self::KEY . ':user';
        $scores = redis()->zMScore($key, ...$userIds);
        $result = [];
        $now    = time();
        foreach ($userIds as $i => $id) {
            $score       = $scores[$i];
            $result[$id] = ($score > 0 && $now - $score < 60);
        }
        return $result;
    }

    /**
     * 获取当前在线总人数
     */
    public static function getTotalCount()
    {
        $redis     = redis();
        $globalKey = self::KEY . ':user';
        $threshold = time() - 60; // 最近一分钟视为在线
        return (string) $redis->zCount($globalKey, $threshold, '+inf');
    }

    /**
     * 获取某个路由的在线人数
     * @param mixed $route
     */
    public static function getRouteCount($route)
    {
        $redis     = redis();
        $zkey      = self::KEY . ":route:{$route}";
        $threshold = time() - 60; // 最近一分钟视为在线
        return (string) $redis->zCount($zkey, $threshold, '+inf');
    }

    /**
     * 获取所有在线时长//渠道侧
     * @param      $channel
     * @param      $date
     * @param      $deviceType
     * @return int
     */
    public static function getOnlineTime($channel, $date, $deviceType = '')
    {
        $redis = redis();
        $total = (int) ($redis->hGet(self::KEY . ':time' . ($deviceType ? "_{$deviceType}" : '') . ":{$date}", $channel) ?: 0);
        return $total;
    }

    /**
     * 获取所有在线时长//用户侧
     * @param      $channel
     * @param      $date
     * @param      $deviceType
     * @return int
     */
    public static function getOnlineTimeUser($channel, $date, $deviceType = '')
    {
        $redis = redis();
        $total = (int) ($redis->hGet(self::KEY . ':time_user' . ($deviceType ? "_{$deviceType}" : '') . ":{$date}", $channel) ?: 0);
        return $total;
    }
}
