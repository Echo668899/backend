<?php

namespace App\Services\Admin;

use App\Core\Services\BaseService;
use App\Services\Common\TokenService;
use App\Utils\CommonUtil;

class AdminUserService extends BaseService
{
    /**
     * 密码加密
     * @param  string $password
     * @param  string $slat
     * @return string
     */
    public static function makePassword(string $password, string $slat): string
    {
        return md5(md5($password) . 'This is password' . md5($slat));
    }

    public static function getUserId()
    {
        $token = self::getToken();
        return $token['user_id'] ?: null;
    }

    /**
     * @return mixed|null
     */
    public static function getToken()
    {
        $ip = CommonUtil::getClientIp();
        //        /********ip必须在白名单 可以用tools添加**************/
        //        $ips = ConfigService::getConfig('whitelist_ip');
        //        if(empty($ips)){
        //            return null;
        //        }
        //        if(strpos($ips,$ip)===false){
        //            return null;
        //        }
        //        /********ip必须在白名单**************/
        $tokenStr = cookies()->get('token')->getValue();
        $uid      = cookies()->get('uid')->getValue();
        if (empty($tokenStr) || empty($uid)) {
            return null;
        }
        $token = TokenService::get($uid, 'admin', 'redis');
        if (empty($token) || $token['user_id'] != $uid || $token['token'] != $tokenStr) {
            return null;
        }
        if ($ip != $token['ip']) {
            return null;
        }
        return $token;
    }
}
