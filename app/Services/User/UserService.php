<?php

namespace App\Services\User;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\User\UserShareJob;
use App\Models\Post\PostModel;
use App\Models\User\UserGroupModel;
use App\Models\User\UserModel;
use App\Models\User\UserUpModel;
use App\Services\Common\ChannelService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\ElasticService;
use App\Services\Common\JobService;
use App\Services\Common\TokenService;
use App\Utils\CommonUtil;
use App\Utils\GameNameUtil;
use Phalcon\Storage\Exception;

class UserService extends BaseService
{
    public const  ENCODE_STR = 'YQA4KUT9XI71B85MGNOSDFJ2RHEV6LPCWZ3';

    /**
     * 注册
     * @param                    $accountType
     * @param                    $account
     * @param                    $phone
     * @param                    $deviceType
     * @param                    $deviceVersion
     * @param                    $channelName
     * @param  mixed             $shareCode
     * @param  mixed             $password
     * @param  mixed             $nickname
     * @param  mixed             $headico
     * @param  null|mixed        $id
     * @return array
     * @throws BusinessException
     */
    public static function register($accountType, $account, $phone, $deviceType, $deviceVersion, $shareCode, $channelName = '', $password = '', $nickname = '', $headico = '', $id = null)
    {
        if (!in_array($accountType, ['device', 'email', 'username'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '不支持该类型注册!');
        }

        // /获取自增id
        $userId                  = $id ?: UserModel::getInsertId();
        $userRow                 = self::getDefaultUserRow($channelName);
        $userRow['_id']          = $userId;
        $userRow['username']     = self::encodeUserId($userId);
        $userRow['nickname']     = !empty($nickname) ? $nickname : $userRow['nickname'];
        $userRow['account_type'] = $accountType;
        $userRow['account']      = $account;
        $userRow['password']     = self::makePassword($password);
        $userRow['phone']        = $phone;
        $userRow['headico']      = $headico ?: $userRow['headico'];

        $userRow['device_type']    = $deviceType;
        $userRow['device_version'] = $deviceVersion;
        $userRow['channel_name']   = strval($channelName);

        if (!UserModel::insert($userRow)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '生成用户繁忙,请稍后再试!');
        }
        if ($channelName && $channelName != 'system') {
            ChannelService::bindChannel($channelName);
        }
        try {
            if (!empty($shareCode)) {
                self::doBindParent($userId, $shareCode);
            }
        } catch (\Exception $e) {
        }
        return $userRow;
    }

    /**
     * @param        $channelName
     * @return array
     */
    public static function getDefaultUserRow($channelName)
    {
        if ($channelName != 'system') {
            $ip       = CommonUtil::getClientIp();
            $firstPay = 0;
        } else {
            $ip       = '127.0.0.1';
            $firstPay = 1;
        }
        return [
            'nickname' => GameNameUtil::getNickname(),
            'username' => '',
            'country'  => 'unknown',
            'lang'     => 'unknown',
            'area'     => 'unknown',
            'phone'    => '',

            'account_type'          => '',
            'account'               => '',
            'password'              => '',
            'device_type'           => '',
            'device_version'        => '',
            'balance'               => 0,
            'balance_freeze'        => 0,
            'balance_income'        => 0,
            'balance_income_freeze' => 0,
            'balance_share'         => 0,
            'balance_share_freeze'  => 0,

            'group_id'         => 0,
            'group_rate'       => 100,
            'group_name'       => '',
            'group_start_time' => 0,
            'group_end_time'   => 0,

            'group_dark_id'         => 0,
            'group_dark_rate'       => 100,
            'group_dark_name'       => '',
            'group_dark_start_time' => 0,
            'group_dark_end_time'   => 0,

            'group_icon'  => '',
            'group_right' => [],

            'headico' => value(function () {
                $headicoGroup = env()->path('app.headico') ?: 'common';
                $mediaDir     = ConfigService::getConfig('media_dir');
                return sprintf('%s/common_file/headico/%s/%s.jpg', $mediaDir, $headicoGroup, mt_rand(1, 150));
            }),
            'headbg'        => '',
            'sign'          => '',
            'sex'           => 'unknown',
            'age'           => '',
            'height'        => '',
            'weight'        => '',
            'fans'          => 0,
            'follow'        => 0,
            'love'          => 0,
            'share'         => 0,
            'tag'           => '',
            'channel_name'  => '',
            'parent_name'   => '',
            'parent_id'     => 0,
            'transfer_id'   => 0,
            'withdraw_fee'  => 0,
            'withdraw_info' => null,
            'first_pay'     => $firstPay,
            'last_pay'      => 0,
            'pay_total'     => 0,
            'register_at'   => time(),
            'register_date' => date('Y-m-d'),
            'register_ip'   => $ip,
            'exp'           => 0,
            'login_num'     => 0,
            'login_at'      => 0,
            'login_date'    => '',
            'login_ip'      => '',
            'is_disabled'   => 0,
            'error_msg'     => '',
        ];
    }

    /**
     * 通过userId 生成邀请码
     * @param         $userId
     * @return string
     */
    public static function encodeUserId($userId)
    {
        $sLength = strlen(self::ENCODE_STR);
        $num     = $userId;
        $code    = '';
        while ($num > 0) {
            $mod  = $num % $sLength;
            $num  = ($num - $mod) / $sLength;
            $code = self::ENCODE_STR[$mod] . $code;
        }
        if (empty($code[3])) {
            $code = str_pad($code, 4, '0', STR_PAD_LEFT);
        }
        // 发现重复用户，从ID 91696789 开始添加前缀A
        return 'A' . $code;
    }

    /**
     * 创建密码
     * @param         $password
     * @return string
     */
    public static function makePassword($password)
    {
        if (empty($password)) {
            return '';
        }
        // /直接返回明文
        return $password;
        $password = env()->path('app.name') . '_' . $password;
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 通过邀请码计算用户id
     * @param            $code
     * @return float|int
     */
    public static function decodeUserId($code)
    {
        $sLength = strlen(self::ENCODE_STR);
        if (strrpos($code, '0') !== false) {
            $code = substr($code, strrpos($code, '0') + 1);
        }
        $len  = strlen($code);
        $code = strrev($code);
        $num  = 0;
        for ($i = 0; $i < $len; $i++) {
            $num += strpos(self::ENCODE_STR, $code[$i]) * pow($sLength, $i);
        }
        return $num;
    }

    /**
     * 修改用户组
     * @param                    $user
     * @param                    $dayNum
     * @param                    $groupId
     * @return bool
     * @throws BusinessException
     */
    public static function doChangeGroup($user, $dayNum, $groupId)
    {
        $groupId = intval($groupId);
        if (is_numeric($user)) {
            $user = UserModel::findByID(intval($user));
        }
        if (empty($user)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '用户不存在!');
        }
        $groupInfo = UserGroupModel::findByID($groupId);
        if (empty($groupInfo)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '用户套餐不存在!');
        }
        if ($groupInfo['group'] == 'dark') {
            return self::doChangeDarkGroup($user, $dayNum, $groupId);
        }
        if ($user['group_end_time'] < time()) {
            $user['group_id'] = 0;
        }
        $updated = [
            'updated_at' => time(),
            'group_rate' => $user['group_rate'] ?: 100
        ];
        if (empty($user['group_id'])) {
            $updated['group_id']   = $groupId;
            $updated['group_name'] = $groupInfo['name'];
            $updated['group_icon'] = strval($groupInfo['group_icon']);
            $updated['group_rate'] = intval($groupInfo['rate']);
            $updated['right']      = $groupInfo['right'];
        } else {
            $updated['group_id']   = $groupId;
            $updated['group_name'] = $groupInfo['name'];
            $updated['group_icon'] = strval($groupInfo['group_icon']);
            $updated['group_rate'] = min(intval($user['group_rate']), intval($groupInfo['rate']));
            $updated['right']      = array_merge($user['right'], $groupInfo['right']['logic']);
        }

        if ($user['group_end_time'] < time()) {
            $user['group_end_time'] = time();
        }
        if (empty($user['group_start_time'])) {
            $updated['group_start_time'] = time();
        }

        $updated['group_end_time'] = intval($dayNum * 24 * 3600 + $user['group_end_time']);
        UserModel::updateRaw(['$set' => $updated], ['_id' => intval($user['_id'])]);
        return true;
    }

    /**
     * 修改暗网等级
     * @param                    $user
     * @param                    $dayNum
     * @param                    $groupId
     * @return true
     * @throws BusinessException
     */
    public static function doChangeDarkGroup($user, $dayNum, $groupId)
    {
        $groupId = intval($groupId);
        if (is_numeric($user)) {
            $user = UserModel::findByID(intval($user));
        }
        if (empty($user)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '用户不存在!');
        }
        $groupInfo = UserGroupModel::findByID($groupId);
        if (empty($groupInfo)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '用户套餐不存在!');
        }
        if ($user['group_dark_end_time'] < time()) {
            $user['group_dark_id'] = 0;
        }
        $updated = [
            'updated_at'      => time(),
            'group_dark_rate' => $user['group_dark_rate'] ?: 100
        ];

        if (empty($user['group_id'])) {
            $updated['group_dark_id']   = $groupId;
            $updated['group_dark_name'] = $groupInfo['name'];
            $updated['group_dark_rate'] = intval($groupInfo['rate']);
            $updated['right']           = $groupInfo['right'];
        } else {
            $updated['group_dark_id']   = $groupId;
            $updated['group_dark_name'] = $groupInfo['name'];
            $updated['group_dark_rate'] = min(intval($user['group_dark_rate']), intval($groupInfo['rate']));
            $updated['right']           = array_merge($user['right'], $groupInfo['right']['logic']);
        }
        if ($user['group_dark_end_time'] < time()) {
            $user['group_dark_end_time'] = time();
        }
        if (empty($user['group_dark_start_time'])) {
            $updated['group_dark_start_time'] = time();
        }

        $updated['group_dark_end_time'] = intval($dayNum * 24 * 3600 + $user['group_dark_end_time']);
        UserModel::updateRaw(['$set' => $updated], ['_id' => intval($user['_id'])]);
        return true;
    }

    /**
     * 绑定上级
     * @param                    $user
     * @param                    $code
     * @return bool
     * @throws BusinessException
     */
    public static function doBindParent($user, $code)
    {
        if (empty($user) || empty($code)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '参数错误,请检查!');
        }
        $parentUser = UserModel::findFirst(['username' => $code]);

        if (empty($parentUser) || $parentUser['is_disabled']) {
            throw new BusinessException(StatusCode::DATA_ERROR, '未找到邀请人!');
        }

        if (is_numeric($user)) {
            $user = UserModel::findByID($user);
        }
        self::checkDisabled($user);
        if ($user['parent_id']) {
            throw new BusinessException(StatusCode::DATA_ERROR, '当前用户已经绑定了!');
        }
        if ($parentUser['_id'] == $user['_id']) {
            throw new BusinessException(StatusCode::DATA_ERROR, '禁止绑定自己!');
        }
        UserModel::updateRaw([
            '$set' => [
                'parent_id'   => $parentUser['_id'],
                'parent_name' => $parentUser['username'],
                'updated_at'  => time()
            ],
        ], ['_id' => $user['_id']]);
        UserModel::updateRaw([
            '$inc' => [
                'share' => 1,
            ],
        ], ['_id' => $parentUser['_id']]);
        JobService::create(new UserShareJob($user['_id'], $parentUser['_id']));
        return true;
    }

    /**
     * 用户vip权限
     * @param  array         $user
     * @return array|array[]
     */
    public static function getRights(array $user)
    {
        if (!self::isVip($user)) {
            return [];
        }
        return array_values($user['right'] ?? []);
    }

    /**
     * 判断是否vip
     * @param        $userRow
     * @param  mixed $isDark
     * @return bool
     */
    public static function isVip($userRow, $isDark = false)
    {
        if (empty($userRow)) {
            return false;
        }
        if ($isDark) {
            if (empty($userRow['group_dark_id']) || $userRow['group_dark_end_time'] < time()) {
                return false;
            }
        } else {
            if (empty($userRow['group_id']) || $userRow['group_end_time'] < time()) {
                return false;
            }
        }

        return true;
    }

    /**
     * 计算从注册之日到今天有多少天
     * @param                 $userRow
     * @return int|mixed|null
     */
    public static function regDiff($userRow)
    {
        if (empty($userRow)) {
            return null;
        }
        if ($userRow['register_date'] == date('Y-m-d')) {
            return 0;
        }
        $date1 = date_create(date('Y-m-d'));
        $date2 = date_create($userRow['register_date']);

        $interval = date_diff($date1, $date2);
        return $interval->days;
    }

    /**
     * 新用户倒计时
     * @param                 $userRow
     * @param  mixed          $day
     * @return float|int|null
     */
    public static function getNewUserTime($userRow, $day = 1)
    {
        if (empty($userRow)) {
            return null;
        }

        if (strpos($userRow['register_at'], '-') > 0) {
            $userRow['register_at'] = strtotime($userRow['register_at']);
        }
        $time = $day * 86400;
        if ((time() - $userRow['register_at']) < $time) {
            return ($userRow['register_at'] + $time) - time();
        }
        return 0;
    }

    /**
     * @param                    $userId
     * @param                    $field
     * @param                    $value
     * @return true
     * @throws BusinessException
     * @throws Exception
     */
    public static function doSimpleUpdate($userId, $field, $value)
    {
        $userId = intval($userId);
        $user   = self::getInfoFromCache($userId);

        self::checkDisabled($user);
        $fields = ['nickname', 'headico', 'headbg', 'sign', 'sex', 'email', 'fans_club_link'];
        if (!in_array($field, $fields)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '不支持该类型!');
        }
        if ($user[$field] == $value) {
            return true;
        }
        $updated = [];
        switch ($field) {
            case 'nickname':
                if (!in_array('do_nickname', self::getRights($user))) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '您没有权限修改昵称!');
                }
                if (mb_strlen($value, 'utf8') > 8) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '昵称不能超过8个字!');
                }
                // 验证关键字
                if (CommonUtil::checkKeywords($value) == false) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '昵称禁止填写广告!');
                }
                $updated = ['nickname' => $value];
                break;
            case 'sign':
                if (!in_array('di_sign', self::getRights($user))) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '您没有权限修改个人签名!');
                }
                //                throw new BusinessException(StatusCode::DATA_ERROR, '为避免广告,请联系在线客服修改个人签名!');
                //                if ($this->checkMerchant($userId)) {
                //                    throw new BusinessException(StatusCode::DATA_ERROR, '认证商家无法自定义签名!');
                //                }
                if (mb_strlen($value, 'utf-8') > 100) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '签名不能超过100个字符!');
                }

                $updated = ['sign' => strval($value)];
                break;
            case 'headico':
                if (!in_array('do_headico', self::getRights($user))) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '您没有权限自定义头像!');
                }
                //                if ($this->userUpService->has($userId)==false) {
                //                    throw new BusinessException(StatusCode::DATA_ERROR, 'UP主才可自定义头像!');
                //                }
                $updated = ['headico' => $value];
                break;
            case 'headbg':
                if (!in_array('do_headbg', self::getRights($user))) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '您没有权限自定义背景图!');
                }
                $updated = ['headbg' => $value];
                break;
            case 'sex':
                if (!in_array($value, ['unknown', 'man', 'woman'])) {
                    throw new BusinessException(StatusCode::DATA_ERROR, '没有更多的性别了!');
                }
                $updated = ['sex' => $value];
                break;
            case 'email':
                // TODO 验证邮箱格式,是否常用邮箱 qq 163 gmail icloud 等等等
                $updated = ['email' => $value];
                break;
        }
        UserModel::updateById($updated, $userId);
        self::setInfoToCache($userId);
        return true;
    }

    /**
     * 从缓存中取出用户信息
     * @param                $userId
     * @return iterable|void
     */
    public static function getInfoFromCache($userId)
    {
        $keyName = 'user_info_' . $userId;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $result = self::setInfoToCache($userId);
        }
        return $result;
    }

    /**
     * 设置用户信息到缓存
     * @param             $user
     * @return array|null
     */
    public static function setInfoToCache($user)
    {
        if ($user === 'interact') {
            return self::setSystemUserToCache($user);
        }
        if ($user === 'system') {
            return self::setSystemUserToCache($user);
        }
        if ($user === 'service') {
            return self::setSystemUserToCache($user);
        }
        if (is_numeric($user)) {
            $user = UserModel::findByID(intval($user));
        }
        // 没找到用户为已注销
        if (empty($user)) {
            return self::setSystemUserToCache(0);
        }
        $isVip     = self::isVip($user);
        $isDarkVip = self::isVip($user, true);

        $upRow  = UserUpModel::findByID($user['_id']);
        $result = [
            'id'           => $user['_id'],
            'username'     => $user['username'],
            'nickname'     => $user['nickname'],
            'country'      => $user['country'],
            'lang'         => $user['lang'],
            'area'         => $user['area'],
            'phone'        => CommonUtil::filterPhone($user['phone']),
            'account_type' => $user['account_type'],
            'account'      => $user['account'],
            'password'     => $user['password'],
            'device_type'  => $user['device_type'],

            'balance'               => $user['balance'] * 1,
            'balance_freeze'        => $user['balance_freeze'] * 1,
            'balance_income'        => $user['balance_income'] * 1,
            'balance_income_freeze' => $user['balance_income_freeze'] * 1,
            'balance_share'         => $user['balance_share'] * 1,
            'balance_share_freeze'  => $user['balance_share_freeze'] * 1,

            'group_id'         => $isVip ? $user['group_id'] * 1 : 0,
            'group_rate'       => $isVip ? $user['group_rate'] * 1 : 100,
            'group_name'       => $isVip ? strval($user['group_name']) : '',
            'group_start_time' => $isVip ? $user['group_start_time'] * 1 : 0,
            'group_end_time'   => $isVip ? $user['group_end_time'] * 1 : 0,

            'group_dark_id'         => $isDarkVip ? $user['group_dark_id'] * 1 : 0,
            'group_dark_rate'       => $isDarkVip ? $user['group_dark_rate'] * 1 : 100,
            'group_dark_name'       => $isDarkVip ? strval($user['group_dark_name']) : '',
            'group_dark_start_time' => $isDarkVip ? $user['group_dark_start_time'] * 1 : 0,
            'group_dark_end_time'   => $isDarkVip ? $user['group_dark_end_time'] * 1 : 0,

            'group_icon' => $isVip ? strval($user['group_icon']) : '',
            'right'      => $isVip ? ($user['right'] ?? []) : [],

            'headico' => $user['headico'],
            'headbg'  => $user['headbg'],
            'sign'    => $user['is_disabled'] ? '用户已注销' : $user['sign'],
            'sex'     => $user['sex'],
            'age'     => $user['age'],
            'height'  => $user['height'],
            'weight'  => $user['weight'],
            'fans'    => $user['fans'] * 1,
            'follow'  => $user['follow'] * 1,
            'love'    => $user['love'] * 1,
            'share'   => $user['share'] * 1,

            'channel_name' => strval($user['channel_name']),
            'parent_id'    => strval($user['parent_id']),
            'is_vip'       => $isVip ? 'y' : 'n',
            'is_dark_vip'  => $isDarkVip ? 'y' : 'n',
            'is_up'        => $upRow ? 'y' : 'n',
            'is_official'  => 'n',

            'category' => value(function () use ($upRow) {
                if ($upRow['categories'] == 'original') {
                    return '原创号';
                }
                if ($upRow['categories'] == 'media') {
                    return '传媒号';
                }
                return '个人号';
            }),
            'first_pay'     => $user['first_pay'] * 1,
            'last_pay'      => $user['last_pay'] * 1,
            'register_at'   => $user['register_at'] * 1, // /apiServer使用
            'register_date' => $user['register_date'], // /apiServer使用
            'register_ip'   => strval($user['register_ip']),
            'login_ip'      => strval($user['login_ip']),
            'is_disabled'   => $user['is_disabled'] * 1,

            'online' => UserActiveService::has($user['_id']) ? 'y' : 'n',

            // 创作者
            'creator' => [
                'post_total' => value(function () use ($user, $upRow) {
                    $count = PostModel::count(['user_id' => intval($user['_id'])]);
                    return strval($count);
                }),
                'post_click_total' => strval($upRow['post_click_total'] ?: 0),

                'movie_total'       => strval($upRow['movie_total'] ?: 0),
                'movie_click_total' => strval($upRow['movie_click_total'] ?: 0),
                'movie_fee_rate'    => strval($upRow['movie_fee_rate'] ?: 0),
                'movie_money_limit' => strval($upRow['movie_money_limit'] ?: 0),

                // 上传次数
                'movie_upload_num' => strval($upRow['movie_upload_num'] ?: 0),
                'post_upload_num'  => strval($upRow['post_upload_num'] ?: 0),
            ],
        ];

        foreach ($result as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $result[$key] = strval($value);
        }
        $result['group_end_time']   *= 1;
        $result['group_start_time'] *= 1;
        $keyName = 'user_info_' . $user['_id'];
        cache()->set($keyName, $result, 300);
        return $result;
    }

    /**
     * 系统用户信息
     * @param            $userId
     * @return array
     * @throws Exception
     */
    public static function setSystemUserToCache($userId)
    {
        if (!in_array($userId, [0, 'system', 'service', 'interact'])) {
            return [];
        }

        $result = [
            'id'       => $userId,
            'username' => 'system_user',
            'nickname' => value(function () use ($userId) {
                if ($userId === 'system') {
                    return '系统通知';
                }
                if ($userId === 'service') {
                    return '官方客服';
                }
                if ($userId === 'interact') {
                    return '互动通知';
                }
                return '已注销';
            }),
            'country'               => '',
            'lang'                  => '',
            'area'                  => '',
            'phone'                 => '',
            'account_type'          => '',
            'account'               => '',
            'device_type'           => '',
            'balance'               => 0,
            'balance_freeze'        => 0,
            'balance_income'        => 0,
            'balance_income_freeze' => 0,
            'balance_share'         => 0,
            'balance_share_freeze'  => 0,

            'group_id'         => 0,
            'group_rate'       => 100,
            'group_name'       => '',
            'group_start_time' => 0,
            'group_end_time'   => 0,

            'group_dark_id'         => 0,
            'group_dark_rate'       => 100,
            'group_dark_name'       => '',
            'group_dark_start_time' => 0,
            'group_dark_end_time'   => 0,

            'group_icon' => '',
            'right'      => [],

            'headico' => value(function () use ($userId) {
                if ($userId === 'system') {
                    return '';
                }
                if ($userId === 'service') {
                    return ConfigService::getConfig('system_user_headico');
                }
                if ($userId === 'interact') {
                    return '';
                }
                return '';
            }),
            'headbg' => '',
            'sign'   => '',
            'sex'    => 0,
            'age'    => '',
            'height' => '',
            'weight' => '',
            'fans'   => 0,
            'follow' => 0,
            'love'   => 0,
            'share'  => 0,

            'channel_name' => '',
            'parent_id'    => 0,
            'is_vip'       => value(function () use ($userId) {
                if ($userId === 'system') {
                    return 'n';
                }
                if ($userId === 'service') {
                    return 'y';
                }
                if ($userId === 'interact') {
                    return 'n';
                }
                return 'n';
            }),
            'is_dark_vip' => value(function () use ($userId) {
                if ($userId === 'system') {
                    return 'n';
                }
                if ($userId === 'service') {
                    return 'y';
                }
                if ($userId === 'interact') {
                    return 'n';
                }
                return 'n';
            }),
            'is_up' => value(function () use ($userId) {
                if ($userId === 'system') {
                    return 'n';
                }
                if ($userId === 'service') {
                    return 'y';
                }
                if ($userId === 'interact') {
                    return 'n';
                }
                return 'n';
            }),
            // 商户
            'is_mer' => value(function () use ($userId) {
                if ($userId === 'system') {
                    return 'n';
                }
                if ($userId === 'service') {
                    return 'y';
                }
                if ($userId === 'interact') {
                    return 'n';
                }
                return 'n';
            }),
            // 官方
            'is_official' => value(function () use ($userId) {
                if ($userId === 'system') {
                    return 'y';
                }
                if ($userId === 'service') {
                    return 'y';
                }
                if ($userId === 'interact') {
                    return 'y';
                }
                return 'n';
            }),

            'first_pay'     => 0,
            'last_pay'      => 0,
            'register_at'   => '',
            'register_date' => '',
            'register_ip'   => '',
            'is_disabled'   => 0,

            'online' => value(function () use ($userId) {
                if ($userId === 'system') {
                    return 'n';
                }
                if ($userId === 'service') {
                    return 'y';
                }
                if ($userId === 'interact') {
                    return 'n';
                }
                return 'n';
            }),

            // 创作者
            'creator' => [
                'post_total'       => '0',
                'post_click_total' => strval('0'),

                'movie_total'       => strval('0'),
                'movie_click_total' => strval('0'),
                'movie_fee_rate'    => strval('0'),
                'movie_money_limit' => strval('0'),

                // 上传次数
                'movie_upload_num' => strval('0'),
                'post_upload_num'  => strval('0'),
            ],
        ];
        $keyName = 'user_info_' . $userId;
        cache()->set($keyName, $result, 180);
        return $result;
    }

    /**
     * 是否禁用
     * @param  array             $userRow
     * @throws BusinessException
     */
    public static function checkDisabled($userRow)
    {
        if (empty($userRow) || $userRow['is_disabled']) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'id:' . ($userRow['_id'] ?? $userRow['id']) . " 账户已冻结 原因:{$userRow['error_msg']}");
        }
    }

    /**
     * 验证密码
     * @param  string $password
     * @param  string $hashPwd
     * @return bool
     */
    public static function checkPassword(string $password, string $hashPwd)
    {
        // 直接明文判断
        return $password == $hashPwd;
        $password = env()->path('app.name') . '_' . $password;
        return password_verify($password, $hashPwd);
    }

    /**
     * 二维码找回
     * @param                    $userId
     * @param                    $code
     * @return array
     * @throws BusinessException
     */
    public static function doBackQR($userId, $code)
    {
        if (strpos($code, '==>') < 1) {
            throw new BusinessException(StatusCode::DATA_ERROR, '凭证内容错误!');
        }
        $code = explode('==>', $code);

        $username = $code[0];
        if (empty($username)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '凭证内容错误!');
        }
        $text = self::getAccountSlat($username);
        if (empty($code[1]) || $code[1] != $text) {
            throw new BusinessException(StatusCode::DATA_ERROR, '凭证内容错误!');
        }
        $newUser = UserModel::findFirst(['username' => $username]);
        if (empty($newUser)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '用户不存在!');
        }
        if ($newUser['is_disabled']) {
            throw new BusinessException(StatusCode::DATA_ERROR, '用户已被禁用!');
        }
        $oldUser = UserModel::findByID($userId);
        if (empty($oldUser) || $oldUser['is_disabled']) {
            throw new BusinessException(StatusCode::DATA_ERROR, '账号已经禁用!');
        }
        if ($newUser['device_id'] == $oldUser['device_id']) {
            throw new BusinessException(StatusCode::DATA_ERROR, '当前账号和待找回的账号一样!');
        }
        self::doChangeDevice($oldUser, $newUser);
        // /返回新用户id,Repository层执行业务逻辑,返回token
        return $newUser['_id'];
    }

    /**
     * 获取账号密钥
     * @param         $username
     * @return string
     */
    public static function getAccountSlat($username)
    {
        $appid = ConfigService::getConfig('mms_appid');
        return md5($username . '_' . $appid);
    }

    /**
     * 交互两个用户的设备编号
     * @param                    $user1
     * @param                    $user2
     * @return bool
     * @throws BusinessException
     */
    public static function doChangeDevice($user1, $user2)
    {
        $deviceId1          = $user1['account'];
        $deviceAccountType1 = $user1['account_type'];
        $deviceType1        = $user1['device_type'];

        $deviceId2          = $user2['account'];
        $deviceAccountType2 = $user2['account_type'];
        $deviceType2        = $user2['device_type'];
        // 判断找回次数 7天>3次 封禁用户
        $startAt = strtotime('-7day');
        //        $count = $this->userFindLogService->count(['$or'=>
        //            [
        //                ['user_id'=>intval($user1['_id']), 'created_at'=>['$gte'=>$startAt]],
        //                ['to_user_id'=>intval($user2['_id']), 'created_at'=>['$gte'=>$startAt]],
        //            ]
        //        ]);
        $count = 0;
        if ($count >= 3) {
            self::doDisabled($user1['_id'], '找回频繁,已被系统禁用,请联系管理员解除');
            self::doDisabled($user2['_id'], '找回频繁,已被系统禁用,请联系管理员解除');
            $serviceEmail = ConfigService::getConfig('service_email');
            throw new BusinessException(StatusCode::DATA_ERROR, '找回频繁,已冻结,请联系管理员解冻!' . $serviceEmail);
        }

        $update1 = [
            '_id'          => $user2['_id'],
            'account'      => $deviceId1 . '_temp',
            'account_type' => $deviceAccountType1,
            'device_type'  => $deviceType1
        ];
        UserModel::save($update1);

        $update2 = [
            '_id'          => $user1['_id'],
            'account'      => $deviceId2,
            'account_type' => $deviceAccountType2,
            'device_type'  => $deviceType2
        ];
        UserModel::save($update2);

        $update3 = [
            '_id'          => $user2['_id'],
            'account'      => $deviceId1,
            'account_type' => $deviceAccountType1,
            'device_type'  => $deviceType1
        ];

        UserModel::save($update3);

        TokenService::del($user1['_id']);
        TokenService::del($user2['_id']);
        // 记录
        //        UserFindLogService::insert([
        //            'user_id'   =>$user1['_id'],
        //            'to_user_id'=>$user2['_id'],
        //        ]);
        return true;
    }

    /**
     * 禁用用户
     * @param        $userId
     * @param        $error
     * @return mixed
     */
    public static function doDisabled($userId, $error = '')
    {
        $userId = intval($userId);
        $result = UserModel::updateRaw(['$set' => ['is_disabled' => 1, 'error_msg' => $error]], ['_id' => $userId]);
        if ($result) {
            self::setInfoToCache($userId);
        }
        return $result;
    }

    /**
     * 同步到es
     * @param       $id
     * @return bool
     */
    public static function asyncEs($id)
    {
        $id        = intval($id);
        $row       = UserModel::findByID($id, '_id', ['_id', 'username', 'nickname', 'headico', 'is_disabled']);
        $row['id'] = $id;
        unset($row['_id']);
        try {
            self::checkDisabled($row);
        } catch (\Exception $e) {
            return false;
        }
        return ElasticService::save($row['id'], $row, 'user', 'user');
    }

    /**
     * 搜索
     * @param  array $filter
     * @return array
     */
    public static function doSearch(array $filter = [])
    {
        $page     = $filter['page'] ?: 1;
        $pageSize = $filter['page_size'] ?: 16;
        $keywords = $filter['keywords'];
        $ids      = $filter['ids'];
        $order    = $filter['order'];
        $from     = ($page - 1) * $pageSize;
        $source   = [];
        $query    = [
            'from'      => $from,
            'size'      => $pageSize,
            'min_score' => 1.0,
            '_source'   => $source,
            'query'     => []
        ];
        //        switch ($order){
        //            case "":
        //                break;
        //        }

        // 关键字
        if ($keywords) {
            $query['query']['bool']['should'] = value(function () use ($keywords) {
                if (CommonUtil::strIsZh($keywords)) {
                    $should[] = [
                        'wildcard' => [
                            'nickname.wild' => [
                                'value'            => "*{$keywords}*",
                                'case_insensitive' => true,
                            ]
                        ]
                    ];
                } else {
                    $should[] = [
                        'wildcard' => [
                            'username.wild' => [
                                'value'            => "{$keywords}*",
                                'case_insensitive' => true,
                            ]
                        ]
                    ];
                }
                return $should;
            });
            //            $query['track_scores'] = true;///调试用,如果添加了自定义sort _source将会返回null,所以可以手动开启
        }
        if (!empty($ids)) {
            $tempIds = explode(',', $ids);
            $idArr   = [];
            foreach ($tempIds as $tempId) {
                $tempId *= 1;
                if ($tempId) {
                    $idArr [] = $tempId;
                }
            }
            if ($idArr) {
                $query['query']['bool']['must'][] = [
                    'terms' => ['id' => $idArr]
                ];
            }
            unset($ids, $idArr, $tempIds);
        }
        $items  = [];
        $result = ElasticService::search($query, 'user', 'user');
        foreach ($result['hits']['hits'] as $item) {
            $item = $item['_source'];
            $item = [
                'id'       => strval($item['id']),
                'username' => strval($item['username']),
                'nickname' => strval($item['nickname']),
                'headico'  => strval(CommonService::getCdnUrl($item['headico'])),
            ];
            $items[] = $item;
        }

        $items  = array_values($items);
        $result = [
            'data'         => $items,
            'total'        => $result['hits']['total']['value'] ? strval($result['hits']['total']['value']) : '0',
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
        ];
        $result['last_page'] = strval(ceil($result['total'] / $pageSize));
        return $result;
    }
}
