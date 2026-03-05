<?php

namespace App\Services\Report;

use App\Core\Services\BaseService;

/**
 * 渠道统计
 */
class ReportChannelLogService extends BaseService
{
    /**
     * 记录独立ip数
     * @param       $channelName //渠道码
     * @param       $ip
     * @param       $date
     * @return void
     */
    public static function doIP($channelName, $ip, $date = null)
    {
        $date = $date ?? date('Y-m-d');
        // 所有
        $keyName = 'report_channel_ip:_all:' . $date;
        redis()->pfAdd($keyName, [$ip]);
        if (redis()->ttl($keyName) === -1) {
            redis()->expire($keyName, 86400 * 31);
        }

        // 某个渠道
        if (!empty($channelName) && $channelName != '_all') {
            $keyName = "report_channel_ip:{$channelName}:" . $date;
            redis()->pfAdd($keyName, [$ip]);
            if (redis()->ttl($keyName) === -1) {
                redis()->expire($keyName, 86400 * 31);
            }
        }
    }

    /**
     * 记录独立ip数量
     * copy的doIP代码
     * @param       $parenId //一级用户id
     * @param       $ip
     * @param       $date
     * @return void
     */
    public static function doUserIP($parenId, $ip, $date = null)
    {
        $date = $date ?? date('Y-m-d');
        // 所有
        $keyName = 'report_user_channel_ip:_all:' . $date;
        redis()->pfAdd($keyName, [$ip]);
        if (redis()->ttl($keyName) === -1) {
            redis()->expire($keyName, 86400 * 31);
        }

        // 某个渠道
        if (!empty($parenId) && $parenId != '_all') {
            $keyName = "report_user_channel_ip:{$parenId}:" . $date;
            redis()->pfAdd($keyName, [$ip]);
            if (redis()->ttl($keyName) === -1) {
                redis()->expire($keyName, 86400 * 31);
            }
        }
    }

    /**
     * 获取独立ip计数
     * @param      $channelName //渠道码
     * @param      $date
     * @return int
     */
    public static function getIPCount($channelName, $date)
    {
        $keyName = "report_channel_ip:{$channelName}:" . $date;
        return intval(redis()->pfcount($keyName));
    }

    /**
     * 获取独立ip计数
     * copy的getIPCount代码
     * @param      $parenId //一级用户id
     * @param      $date
     * @return int
     */
    public static function getUserIPCount($parenId, $date)
    {
        $keyName = "report_user_channel_ip:{$parenId}:" . $date;
        return intval(redis()->pfcount($keyName));
    }

    /**
     * 记录独立用户数
     * @param       $channelName //渠道码
     * @param       $userId
     * @param       $date
     * @return void
     */
    public static function doUV($channelName, $userId, $date = null)
    {
        $date = $date ?? date('Y-m-d');
        // 所有
        $keyName = 'report_channel_uv:_all:' . $date;
        redis()->pfAdd($keyName, [$userId]);
        if (redis()->ttl($keyName) === -1) {
            redis()->expire($keyName, 86400 * 31);
        }

        // 某个渠道
        if (!empty($channelName) && $channelName != '_all') {
            $keyName = "report_channel_uv:{$channelName}:" . $date;
            redis()->pfAdd($keyName, [$userId]);
            if (redis()->ttl($keyName) === -1) {
                redis()->expire($keyName, 86400 * 31);
            }
        }
    }

    /**
     * 记录独立用户数
     * copy的doUV代码
     * @param       $parenId //一级用户id
     * @param       $userId
     * @param       $date
     * @return void
     */
    public static function doUserUV($parenId, $userId, $date = null)
    {
        $date = $date ?? date('Y-m-d');
        // 所有
        $keyName = 'report_user_channel_uv:_all:' . $date;
        redis()->pfAdd($keyName, [$userId]);
        if (redis()->ttl($keyName) === -1) {
            redis()->expire($keyName, 86400 * 31);
        }

        // 某个渠道
        if (!empty($parenId) && $parenId != '_all') {
            $keyName = "report_user_channel_uv:{$parenId}:" . $date;
            redis()->pfAdd($keyName, [$userId]);
            if (redis()->ttl($keyName) === -1) {
                redis()->expire($keyName, 86400 * 31);
            }
        }
    }

    /**
     * 获取记录独立用户数
     * @param      $channelName //渠道码
     * @param      $date
     * @return int
     */
    public static function getUVCount($channelName, $date)
    {
        $keyName = "report_channel_uv:{$channelName}:" . $date;
        return intval(redis()->pfcount($keyName));
    }

    /**
     * 获取记录独立用户数
     * copy的getUVCount代码
     * @param      $parenId //一级用户id
     * @param      $date
     * @return int
     */
    public static function getUserUVCount($parenId, $date)
    {
        $keyName = "report_user_channel_uv:{$parenId}:" . $date;
        return intval(redis()->pfcount($keyName));
    }

    /**
     * 记录页面数
     * @param       $channelName //渠道码
     * @param       $date
     * @return void
     */
    public static function doPV($channelName, $date = null)
    {
        $date = $date ?? date('Y-m-d');
        // 所有
        $keyName = 'report_channel_pv:_all:' . $date;
        redis()->incr($keyName);
        if (redis()->ttl($keyName) === -1) {
            redis()->expire($keyName, 86400 * 31);
        }

        // 某个渠道
        if (!empty($channelName) && $channelName != '_all') {
            $keyName = "report_channel_pv:{$channelName}:" . $date;
            redis()->incr($keyName);
            if (redis()->ttl($keyName) === -1) {
                redis()->expire($keyName, 86400 * 31);
            }
        }
    }

    /**
     * 记录页面数
     * copy的doPV代码
     * @param       $parenId //一级用户id
     * @param       $date
     * @return void
     */
    public static function doUserPV($parenId, $date = null)
    {
        $date = $date ?? date('Y-m-d');
        // 所有
        $keyName = 'report_user_channel_pv:_all:' . $date;
        redis()->incr($keyName);
        if (redis()->ttl($keyName) === -1) {
            redis()->expire($keyName, 86400 * 31);
        }

        // 某个渠道
        if (!empty($parenId) && $parenId != '_all') {
            $keyName = "report_user_channel_pv:{$parenId}:" . $date;
            redis()->incr($keyName);
            if (redis()->ttl($keyName) === -1) {
                redis()->expire($keyName, 86400 * 31);
            }
        }
    }

    /**
     * 获取页面计数
     * @param      $channelName //渠道码
     * @param      $date
     * @return int
     */
    public static function getPVCount($channelName, $date)
    {
        $keyName = "report_channel_pv:{$channelName}:" . $date;
        return intval(redis()->get($keyName) ?? 0);
    }

    /**
     * 获取页面计数
     * copy的getPVCount代码
     * @param      $parenId //一级用户id
     * @param      $date
     * @return int
     */
    public static function getUserPVCount($parenId, $date)
    {
        $keyName = "report_user_channel_pv:{$parenId}:" . $date;
        return intval(redis()->get($keyName) ?? 0);
    }

    /**
     * 记录浏览数,movie comics novel post audio....
     * @param       $channelName //渠道码
     * @param       $date
     * @return void
     */
    public static function doView($channelName, $date = null)
    {
        $date = $date ?? date('Y-m-d');

        // 所有
        $keyName = 'report_channel_view:_all:' . $date;
        redis()->incr($keyName);
        redis()->expire($keyName, 86400 * 31, 'NX');
        if (redis()->ttl($keyName) === -1) {
            redis()->expire($keyName, 86400 * 31);
        }

        // 某个渠道
        if (!empty($channelName) && $channelName != '_all') {
            $keyName = "report_channel_view:{$channelName}:" . $date;
            redis()->incr($keyName);
            redis()->expire($keyName, 86400 * 31, 'NX');
            if (redis()->ttl($keyName) === -1) {
                redis()->expire($keyName, 86400 * 31);
            }
        }
    }

    /**
     * 记录浏览数,movie comics novel post audio....
     * copy的doView代码
     * @param       $parentId //一级用户id
     * @param       $date
     * @return void
     */
    public static function doUserView($parentId, $date = null)
    {
        $date = $date ?? date('Y-m-d');

        // 所有
        $keyName = 'report_user_channel_view:_all:' . $date;
        redis()->incr($keyName);
        redis()->expire($keyName, 86400 * 31, 'NX');
        if (redis()->ttl($keyName) === -1) {
            redis()->expire($keyName, 86400 * 31);
        }

        // 某个渠道
        if (!empty($parentId) && $parentId != '_all') {
            $keyName = "report_user_channel_view:{$parentId}:" . $date;
            redis()->incr($keyName);
            redis()->expire($keyName, 86400 * 31, 'NX');
            if (redis()->ttl($keyName) === -1) {
                redis()->expire($keyName, 86400 * 31);
            }
        }
    }

    /**
     * 获取浏览数计数
     * @param      $channelName //渠道码
     * @param      $date
     * @return int
     */
    public static function getViewCount($channelName, $date)
    {
        $keyName = "report_channel_view:{$channelName}:" . $date;
        return intval(redis()->get($keyName) ?? 0);
    }

    /**
     * 获取浏览数计数
     * copy的getViewCount代码
     * @param      $parentId //一级用户id
     * @param      $date
     * @return int
     */
    public static function getUserViewCount($parentId, $date)
    {
        $keyName = "report_user_channel_view:{$parentId}:" . $date;
        return intval(redis()->get($keyName) ?? 0);
    }
}
