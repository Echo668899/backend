<?php

namespace App\Services\Common;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Center\CenterDataJob;
use App\Services\Report\ReportChannelLogService;
use App\Services\User\UserService;
use App\Utils\AesDynamicUtil;
use App\Utils\CommonUtil;
use Phalcon\Manager\Center\CenterDataService;

class ApiService extends BaseService
{
    public const WEB_KEY         = 'x3t8rvtaescfe38s';
    public const IOS_KEY         = '402baa485bf32c35';
    public const ANDROID_KEY     = 'cf83c7058b9d535c';
    public const DEBUG_KEY       = '2DCiXDoNmuQrJjxktdLPy5cXcVhoC5pw';
    public const HEADER_KEYWORDS = 'Dart';

    private static $version;
    private static $deviceType;
    private static $token;
    private static $deviceId;
    private static $time;
    private static $isDebug = false;
    private static $encodeKey;

    private static $tokenInfo = [];
    /**
     * 日活统计范围
     * @var string[]
     */
    private static $dayReports = [
        'comics/detail', 'comics/chapter',
        'movie/detail',
        'novel/detail', 'novel/chapter',
        'audio/detail', 'audio/chapter',
        'post/detail',
        'user/info', 'user/vip', 'user/recharge',
    ];

    /**
     * PV统计范围
     * @var string[]
     */
    private static $pvReports = [
        'comics/detail', 'comics/chapter', 'comics/navBlock', 'comics/navFilter', 'comics/blockDetail', 'comics/tagDetail',
        'movie/detail', 'movie/navBlock', 'movie/navFilter', 'comics/blockDetail', 'comics/tagDetail',
        'novel/detail', 'novel/chapter', 'novel/navBlock', 'novel/navFilter', 'novel/blockDetail', 'novel/tagDetail',
        'audio/detail', 'audio/chapter', 'audio/navBlock', 'audio/navFilter', 'audio/blockDetail', 'audio/tagDetail',
        'post/detail', 'post/navBlock', 'post/navFilter', 'post/blockDetail', 'post/tagDetail',
        'user/info', 'user/vip', 'user/recharge',
    ];

    /**
     * 加密数据
     * @param              $data
     * @return bool|string
     */
    public static function encryptData($data)
    {
        if (self::$isDebug) {
            return $data;
        }
        $data = json_encode($data);
        //        if (self::$deviceType == 'web') {
        // //            return AesUtil::encryptBase64($data, self::$encodeKey);
        //            return AesDynamicUtil::encryptBase64($data, self::getRequestId(), self::$encodeKey);
        //        } else {
        // //            return AesUtil::encryptRaw($data, self::$encodeKey);
        //            return AesDynamicUtil::encryptRaw($data, self::getRequestId(), self::$encodeKey);
        //        }
        return AesDynamicUtil::encryptRaw($data, self::getRequestId(), self::$encodeKey);
    }

    public static function handler()
    {
        $url              = $_REQUEST['_url'];
        self::$version    = self::getVersion();
        self::$deviceType = self::getDeviceType();
        self::$time       = self::getTime();
        $debugKey         = self::getDebugKey();

        if (empty(self::$version) || empty(self::$deviceType) || empty(self::$time)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '数据封装错误!');
        }
        if (!in_array(self::$deviceType, ['ios', 'android', 'web'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '设备信息错误!');
        }
        if (self::$deviceType == 'web') {
            self::$encodeKey = self::WEB_KEY;
        } elseif (self::$deviceType == 'ios') {
            self::$encodeKey = self::IOS_KEY;
        } else {
            self::$encodeKey = self::ANDROID_KEY;
        }
        if (!empty($debugKey) && $debugKey == self::DEBUG_KEY) {
            self::$isDebug = true;
        }
        self::$time = strtotime(self::$time);
        self::checkRequestSafe();
        $_REQUEST['_url'] = $url;
        self::addAppLogs();

        self::addCenterData();
    }

    public static function checkRequestSafe()
    {
        $deviceType = self::getDeviceType();
        if (self::$isDebug) {
            self::$token    = request()->getHeader('token');
            self::$deviceId = request()->getHeader('deviceId');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new BusinessException(StatusCode::DATA_ERROR, '安全性错误R!');
        }
        if ($deviceType != 'web') {
            if (empty($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], ApiService::HEADER_KEYWORDS) === false) {
                throw new BusinessException(StatusCode::DATA_ERROR, '安全性错误A!');
            }
        }
        /*if ((time() - $this->time) > 300) {
            throw new BusinessException(StatusCode::DATA_ERROR, '安全性错误T!');
        }*/
        $data = file_get_contents('php://input');
        //        if ($deviceType == 'web') {
        // //            $data = AesUtil::decryptBase64($data, self::$encodeKey);
        //            $data = AesDynamicUtil::decryptBase64($data, self::getRequestId(), self::$encodeKey);
        //        } else {
        // //            $data = AesUtil::decryptRaw($data, self::$encodeKey);
        //            $data = AesDynamicUtil::decryptRaw($data, self::getRequestId(), self::$encodeKey);
        //        }
        $data = AesDynamicUtil::decryptRaw($data, self::getRequestId(), self::$encodeKey);
        if (empty($data)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '安全性错误D!');
        }
        $data = json_decode($data, true);
        if (empty($data['deviceId'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '安全性错误DD!');
        }
        self::$deviceId = trim($data['deviceId']);
        if (!empty($data['token'])) {
            self::$token = $data['token'];
        }
        $_REQUEST = empty($data['data']) ? [] : $data['data'];
    }

    /**
     * 增加app记录
     */
    public static function addAppLogs()
    {
        $controllerName = dispatcher()->getControllerName();
        $actionName     = dispatcher()->getActionName();
        $urlKey         = $controllerName . '/' . $actionName;

        $token = self::getToken();
        if (empty($token) || empty($token['user_id'])) {
            return;
        }

        $userId   = intval($token['user_id']);
        $userInfo = UserService::getInfoFromCache($userId);

        /**记录日活**/
        if (in_array($urlKey, self::$dayReports)) {
            AppLogService::do($userId);
        }

        /**记录IP,每日独立ip数**/
        ReportChannelLogService::doIP($userInfo['channel_name'], CommonUtil::getClientIp());// 渠道侧
        ReportChannelLogService::doUserIP($userInfo['parent_id'], CommonUtil::getClientIp());// 用户侧

        /**记录UV,每日独立用户数**/
        ReportChannelLogService::doUV($userInfo['channel_name'], $userId);// 渠道侧
        ReportChannelLogService::doUserUV($userInfo['parent_id'], $userId);// 用户侧

        /**记录PV,每日页面访问次数**/
        if (in_array($urlKey, self::$pvReports)) {
            ReportChannelLogService::doPV($userInfo['channel_name']);// 渠道侧
            ReportChannelLogService::doUserPV($userInfo['parent_id']);// 用户侧
        }
    }

    /**
     * 数据中心
     * @return void
     */
    public static function addCenterData()
    {
        $tokenInfo = self::getToken();
        $userInfo  = $tokenInfo ? UserService::getInfoFromCache($tokenInfo['user_id']) : [];
        $configs   = CenterDataJob::getCenterConfig('data');

        # 数据中心初始化
        CenterDataService::setRedis(redis());
        CenterDataService::setSessionId(self::getSessionId());
        CenterDataService::setDeviceType(self::getDeviceType());
        CenterDataService::setDeviceId(self::getDeviceId());
        CenterDataService::setClientIp(CommonUtil::getClientIp());
        CenterDataService::setAppid($configs['appid']);
        CenterDataService::setUserId($tokenInfo['user_id'] ?? '');
        CenterDataService::setUserAgent(CommonUtil::getUserAgent());
        CenterDataService::setChannelCode($userInfo['channel_name'] ?? '');
    }

    /**
     * 获取token
     * @return mixed
     */
    public static function getToken()
    {
        if (!empty(self::$tokenInfo)) {
            return self::$tokenInfo;
        }
        $token = self::$token;
        if (empty($token)) {
            return null;
        }
        $token = explode('_', $token);
        if (count($token) != 3) {
            return null;
        }
        if ($token[0] === 'D') {
            /* 单端登录,设备登录 */
            $tokenInfo = TokenService::get($token[2], 'device');
        } else {
            /* 多端登录,仅适用username和qrcode登录,device由于设备号唯一,所以无法支持;如果deviceId变了,而token没变,则为攻击 */
            $tokenInfo = TokenService::hGet($token[2], self::$deviceId, 'user');
        }

        if (empty($tokenInfo) || $tokenInfo['user_id'] != $token[2] || $tokenInfo['token'] != ($token[0] . '_' . $token[1])) {
            return null;
        }
        if ($tokenInfo['expired_at'] <= time()) {
            return null;
        }

        $tokenInfo['user_id'] = intval($tokenInfo['user_id']);
        self::$tokenInfo      = $tokenInfo;
        return self::$tokenInfo;
    }

    public static function getRequestId()
    {
        return request()->getHeader('requestId');
    }

    public static function getSessionId()
    {
        return request()->getHeader('sessionId');
    }

    public static function getVersion()
    {
        return request()->getHeader('version');
    }

    public static function getDeviceType()
    {
        return request()->getHeader('deviceType');
    }

    public static function getTime()
    {
        return request()->getHeader('time');
    }

    public static function getDebugKey()
    {
        return request()->getHeader('debugKey');
    }

    public static function getLanguage()
    {
        return request()->getHeader('language');
    }

    public static function getDeviceId()
    {
        return self::$deviceId;
    }

    public static function isDebug()
    {
        return self::$isDebug;
    }
}
