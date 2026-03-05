<?php

namespace App\Repositories\Api;

use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\User\UserLoginPayload;
use App\Jobs\Event\Payload\User\UserLogoutPayload;
use App\Jobs\Event\Payload\User\UserRegisterPayload;
use App\Models\User\UserModel;
use App\Services\Common\ApiService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\JobService;
use App\Services\Common\TokenService;
use App\Services\Im\ImService;
use App\Services\User\UserService;
use App\Utils\AesUtil;
use App\Utils\CommonUtil;

class LoginRepository extends BaseRepository
{
    /**
     * 账号登录
     * 设备号是辅助的,可以实现多端登录
     * @param                    $request
     * @return array
     * @throws BusinessException
     */
    public static function username($request)
    {
        $channelCode = value(function () use ($request) {
            $channelCode = self::getRequest($request, 'channel_code', 'string', CommonUtil::getChannelCode($request['clipboard_text']));
            // 过滤官网渠道码和特殊渠道码
            if (in_array($channelCode, ['null', 'default', 'system', '_all'])) {
                return '';
            }
            if (strpos($channelCode, 'oversea_') === 0 || strpos($channelCode, 'china_') === 0) {
                return '';
            }
            return $channelCode;
        });
        $shareCode = self::getRequest($request, 'share_code', 'string', CommonUtil::getShareCode($request['clipboard_text']));
        $username  = self::getRequest($request, 'username');
        $password  = self::getRequest($request, 'password');
        if (!preg_match('/^[a-zA-Z0-9]{6,12}$/', $username)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '账号只能输入字母和数字，长度必须在 6 到 12 位之间!');
        }
        if (!preg_match('/^[\x21-\x7E]{6,32}$/', $password)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '密码长度需在 6 到 32 位之间，且只能包含字母、数字或符号!');
        }
        $deviceType    = ApiService::getDeviceType();
        $deviceVersion = ApiService::getVersion();
        $userRow       = UserModel::findFirst(['account_type' => 'username', 'account' => $username]);
        if (empty($userRow)) {
            $userRow = UserService::register('username', $username, uniqid('phone_'), $deviceType, $deviceVersion, $shareCode, $channelCode, $password);
            JobService::create(new EventBusJob(new UserRegisterPayload($userRow['_id'], 'username', $deviceType, $deviceVersion, $channelCode, $shareCode, $userRow['register_at'])));
        } else {
            if (UserService::checkPassword($password, $userRow['password']) === false) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, '账号或密码错误!');
            }
            if ($userRow['is_disabled']) {
                $serviceEmail = ConfigService::getConfig('service_email');
                // 被禁用用户无法进入
                throw  new BusinessException(StatusCode::DATA_ERROR, '该用户账号已被系统禁用,请联系管理员解除!' . $serviceEmail);
            }
            JobService::create(new EventBusJob(new UserLoginPayload($userRow['_id'], 'username', $deviceType)));
        }
        return self::getLoginToken($userRow, $request, 'username');
    }

    /**
     * 设备号登录
     * 设备号是唯一的,无法实现多端登录
     * @param                    $request
     * @return array
     * @throws BusinessException
     */
    public static function device($request)
    {
        $channelCode = value(function () use ($request) {
            $channelCode = self::getRequest($request, 'channel_code', 'string', CommonUtil::getChannelCode($request['clipboard_text']));
            // 过滤官网渠道码和特殊渠道码
            if (in_array($channelCode, ['null', 'default', 'system', '_all'])) {
                return '';
            }
            if (strpos($channelCode, 'oversea_') === 0 || strpos($channelCode, 'china_') === 0) {
                return '';
            }
            return $channelCode;
        });
        $shareCode     = self::getRequest($request, 'share_code', 'string', CommonUtil::getShareCode($request['clipboard_text']));
        $deviceId      = ApiService::getDeviceId();
        $deviceType    = ApiService::getDeviceType();
        $deviceVersion = ApiService::getVersion();

        if ($deviceType == 'web' && strlen($deviceId) > 36) {
            $deviceIdInfo = AesUtil::decrypt($deviceId, 'd05418183a778ae7');
            $deviceIdInfo = json_decode($deviceIdInfo, true);
            if (empty($deviceIdInfo) || empty($deviceIdInfo['d'])) {
                throw  new BusinessException(StatusCode::DATA_ERROR, '请求不合法,请从官网下载最新!');
            }
            $deviceId    = $deviceIdInfo['d'];
            $channelCode = $deviceIdInfo['c'] ?: '';
            $shareCode   = $deviceIdInfo['s'] ?: '';
        }
        $userRow = UserModel::findFirst(['account_type' => 'device', 'account' => $deviceId]);
        if (empty($userRow)) {
            $userRow = UserService::register('device', $deviceId, uniqid('phone_'), $deviceType, $deviceVersion, $shareCode, $channelCode);
            JobService::create(new EventBusJob(new UserRegisterPayload($userRow['_id'], 'device', $deviceType, $deviceVersion, $channelCode, $shareCode, $userRow['register_at'])));
        } else {
            if ($userRow['is_disabled']) {
                $serviceEmail = ConfigService::getConfig('service_email');
                // 被禁用用户无法进入
                throw  new BusinessException(StatusCode::DATA_ERROR, '该用户账号已被系统禁用,请联系管理员解除!' . $serviceEmail);
            }

            JobService::create(new EventBusJob(new UserLoginPayload($userRow['_id'], 'device', $deviceType)));
        }
        $tokenInfo              = self::getLoginToken($userRow, $request, 'device');
        $tokenInfo['has_login'] = 'n';// 设备登录算作游客,未登录

        return $tokenInfo;
    }

    /**
     * 凭证登录
     * 设备号是辅助的,username和device都可以实现多端登录
     * @param                    $request
     * @return array
     * @throws BusinessException
     */
    public static function qrcode($request)
    {
        $code = self::getRequest($request, 'code');

        if (strpos($code, '==>') < 1) {
            throw new BusinessException(StatusCode::DATA_ERROR, '凭证内容错误!');
        }
        $code     = explode('==>', $code);
        $username = $code[0];
        if (empty($username)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '凭证内容错误!');
        }
        $userRow = UserModel::findFirst(['username' => $username]);
        if (empty($userRow)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '用户不存在!');
        }
        $text = UserService::getAccountSlat($username);
        if (empty($code[1]) || $code[1] != $text) {
            throw new BusinessException(StatusCode::DATA_ERROR, '凭证内容错误!');
        }
        UserService::checkDisabled($userRow);
        JobService::create(new EventBusJob(new UserLoginPayload($userRow['_id'], $userRow['account_type'], ApiService::getDeviceType())));
        return self::getLoginToken($userRow, $request, 'qrcode');
    }

    /**
     * @param       $tokenInfo
     * @return void
     */
    public static function logout($tokenInfo)
    {
        $token = explode('_', $tokenInfo['token']);
        if ($token[0] === 'D') {
            /* 单端登录,设备登录 */
            TokenService::del($tokenInfo['user_id'], 'device', 'redis');
        } else {
            /* 多端登录 */
            TokenService::hDel($tokenInfo['user_id'], ApiService::getDeviceId(), 'user');
        }
        JobService::create(new EventBusJob(new UserLogoutPayload($tokenInfo['user_id'])));
    }

    /**
     * @param  array $userRow
     * @param        $request
     * @param  mixed $action
     * @return array
     */
    private static function getLoginToken(array $userRow, $request, $action)
    {
        $deviceId      = ApiService::getDeviceId();
        $deviceType    = ApiService::getDeviceType();
        $deviceVersion = ApiService::getVersion();
        $deviceName    = self::getRequest($request, 'device_name');// 当前登录设备名称
        $deviceInfo    = self::getRequest($request, 'device_info');// 当前登录设备信息,其中isPhysicalDevice字段为false,表示虚拟机
        $loginIp       = CommonUtil::getClientIp();

        // /拦截系统用户
        if ($userRow['channel_name'] == 'system') {
            throw new BusinessException(StatusCode::DATA_ERROR, '禁止登录该账号!');
        }

        $update = [
            '$set' => [
                'login_at'       => time(),
                'login_ip'       => $loginIp,
                'login_date'     => date('Y-m-d'),
                'device_type'    => ApiService::getDeviceType(),
                'device_version' => ApiService::getVersion(),
                'login_device'   => [
                    $deviceId => [
                        'device_id'      => $deviceId,
                        'device_type'    => $deviceType,
                        'device_name'    => $deviceName,
                        'device_version' => $deviceVersion,
                        'device_info'    => $deviceInfo,
                        'ip'             => $loginIp,
                        'login_at'       => time(),
                    ]
                ]
            ],
            '$inc' => [
                'login_num' => 1,
            ]
        ];

        if (!empty($deviceInfo)) {
            $update['$set']['device_info'] = $deviceInfo;
        }

        UserModel::updateRaw($update, ['_id' => $userRow['_id']]);
        $expire = 3600 * 24 * 0.5;

        if ($action == 'device') {
            /* 单端登录 */
            $token = TokenService::set($userRow['_id'], $userRow['username'], 'device', $expire);
        } else {
            /* 多端登录 */
            $token = TokenService::hSet($userRow['_id'], $userRow['username'], ApiService::getDeviceId(), 'user', $expire);
        }

        $token['is_vip'] = UserService::isVip($userRow) ? 'y' : 'n';
        return [
            'user_id'       => strval($token['user_id']),
            'username'      => strval($userRow['username']),
            'nickname'      => strval($userRow['nickname']),
            'headico'       => strval(CommonService::getCdnUrl($userRow['headico'], 'image')),
            'token'         => strval($token['token']),
            'is_vip'        => strval($token['is_vip']),
            'websocket_key' => strval(ImService::getConfig()['key']), // websocket key
            'websocket'     => strval(ImService::getWsUrl($token['user_id'], ApiService::getDeviceId()))
        ];
    }
}
