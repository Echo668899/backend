<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Utils\CommonUtil;

class TokenService extends BaseService
{
    /**
     * @param        $userId
     * @param        $username
     * @param        $namespace
     * @param        $expired
     * @param        $ext
     * @param        $driver
     * @return array
     */
    public static function set($userId, $username, $namespace = 'user', $expired = 7200, $ext = null, $driver = 'redis')
    {
        $token = [
            'token'      => 'D_' . md5(strval(uniqid(microtime(true)) . mt_rand(1000, 9999))),
            'user_id'    => strval($userId),
            'username'   => $username,
            'expired_at' => strval(time() + $expired),
            'ip'         => CommonUtil::getClientIp(),
            'ext'        => $ext
        ];
        if ($driver == 'session') {
            $session = session();
            $session->set(self::fmt($userId, $namespace), json_encode($token, JSON_UNESCAPED_UNICODE));
        } else {
            redis()->set(self::fmt($userId, $namespace), json_encode($token, JSON_UNESCAPED_UNICODE), $expired);
        }
        return $token;
    }

    /**
     * @param             $userId
     * @param  string     $namespace
     * @param  string     $driver
     * @return mixed|null
     */
    public static function get($userId, $namespace = 'user', $driver = 'redis')
    {
        if ($driver == 'session') {
            $token = session()->get(self::fmt($userId, $namespace));
        } else {
            $token = redis()->get(self::fmt($userId, $namespace));
        }
        return $token ? json_decode($token, true) : null;
    }

    /**
     * @param           $userId
     * @param  string   $namespace
     * @param  string   $driver
     * @return int|void
     */
    public static function del($userId, $namespace = 'user', $driver = 'redis')
    {
        if ($driver == 'session') {
            session()->remove(self::fmt($userId, $namespace));
            return true;
        }
        return redis()->del(self::fmt($userId, $namespace));
    }

    /**
     * @param           $userId
     * @param  string   $namespace
     * @return bool|int
     */
    public static function has($userId, $namespace = 'user')
    {
        return redis()->exists(self::fmt($userId, $namespace));
    }

    /**
     * 多端登录,适用一个账号多个设备
     * @param        $userId
     * @param        $username
     * @param        $deviceId
     * @param        $namespace
     * @param        $expired
     * @param        $ext
     * @return array
     */
    public static function hSet($userId, $username, $deviceId, $namespace = 'user', $expired = 3600 * 24 * 30, $ext = null)
    {
        $token = [
            'token'      => 'U_' . md5(strval(uniqid(microtime(true)) . mt_rand(1000, 9999))),
            'user_id'    => strval($userId),
            'username'   => $username,
            'expired_at' => strval(time() + $expired),
            'ip'         => CommonUtil::getClientIp(),
            'ext'        => $ext
        ];
        redis()->hSet(self::fmt($userId, $namespace), $deviceId, json_encode($token, JSON_UNESCAPED_UNICODE));
        return $token;
    }

    /**
     * 多端登录-获取指定端
     * @param             $userId
     * @param             $deviceId
     * @param             $namespace
     * @return mixed|null
     */
    public static function hGet($userId, $deviceId, $namespace = 'user')
    {
        $token = redis()->hGet(self::fmt($userId, $namespace), $deviceId);
        return $token ? json_decode($token, true) : null;
    }

    /**
     * 多端登录-删除指定端
     * @param                   $userId
     * @param                   $deviceId
     * @param                   $namespace
     * @return false|int|\Redis
     */
    public static function hDel($userId, $deviceId, $namespace = 'user')
    {
        return redis()->hDel(self::fmt($userId, $namespace), $deviceId);
    }

    /**
     * 多端登录-延期
     * @param                        $userId
     * @param                        $deviceId
     * @param                        $namespace
     * @param                        $expired
     * @return false|float|int|mixed 返回最新的时间
     */
    public static function hExpire($userId, $deviceId, $namespace = 'user', $expired = 3600 * 24 * 30)
    {
        $key   = self::fmt($userId, $namespace);
        $token = redis()->hGet($key, $deviceId);
        if (!$token) {
            return false;
        }

        $data = json_decode($token, true);
        if (!$data || !isset($data['expired_at'])) {
            return false;
        }

        // 延长有效时间
        $data['expired_at'] = time() + $expired;

        // 写回 redis
        redis()->hSet($key, $deviceId, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $data['expired_at'];
    }

    /**
     * @param         $userId
     * @param         $namespace
     * @return string
     */
    private static function fmt($userId, $namespace)
    {
        return 'token_' . $namespace . ':' . $userId;
    }
}
