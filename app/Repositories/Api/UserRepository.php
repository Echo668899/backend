<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Jobs\Center\CenterCustomerJob;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\User\UserDoRechargePayload;
use App\Jobs\Event\Payload\User\UserDoVipPayload;
use App\Jobs\Event\Payload\User\UserRechargePayload;
use App\Jobs\Event\Payload\User\UserVipPayload;
use App\Models\Movie\MovieModel;
use App\Models\Movie\MovieTagModel;
use App\Models\Post\PostModel;
use App\Models\Post\PostTagModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Services\Activity\ActivityService;
use App\Services\Common\ApiService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\JobService;
use App\Services\Common\PaymentService;
use App\Services\Movie\MovieHistoryService;
use App\Services\User\AccountService;
use App\Services\User\UserActiveService;
use App\Services\User\UserCodeLogService;
use App\Services\User\UserCodeService;
use App\Services\User\UserFansService;
use App\Services\User\UserFavoriteService;
use App\Services\User\UserGroupService;
use App\Services\User\UserProductService;
use App\Services\User\UserService;
use App\Services\User\UserShareService;
use App\Services\User\UserWithdrawService;
use App\Utils\CommonUtil;
use Phalcon\Manager\Center\CenterCustomerService;

class UserRepository extends BaseRepository
{

    /**
     * @param $userId
     * @return array
     */
    public static function getInfo($userId)
    {
        $userInfo = UserService::getInfoFromCache($userId);
        return [
            'id' => strval($userInfo['id']),
            'username' => strval($userInfo['username']),
            'nickname' => $userInfo['nickname'],
            'headico' => CommonService::getCdnUrl($userInfo['headico']),

            'sex' => strval($userInfo['sex']),
            'is_vip' => strval($userInfo['is_vip']),
            'is_up' => strval($userInfo['is_up']),
            'sign' => strval($userInfo['sign']),
            'phone' => strval($userInfo['phone']),

            'follow' => strval($userInfo['follow']),
            'fans' => strval($userInfo['fans']),
            'love' => strval($userInfo['love']),

            //余额
            'balance' => strval($userInfo['balance']),
            'group_name' => strval($userInfo['group_name']),
            'group_icon' => strval($userInfo['group_icon']),
            'group_end_time' => value(function () use ($userInfo) {
                if (!empty($userInfo['group_end_time']) && $userInfo['is_vip'] == 'y') {
                    return ($userInfo['group_end_time'] - $userInfo['group_start_time'] >= 8 * 31536000) ? 'VIP永久有效' : date('Y-m-d', $userInfo['group_end_time']) . ' 到期';
                }
                return '';
            }),

            'play_num' => value(function () use ($userInfo) {
                if ($userInfo['is_vip'] == 'y') {
                    return '';
                }
                //今日可播放次数
                $maxNum = MovieHistoryService::getCanPlayNum();
                //今日已播放次數
                $playNum = MovieHistoryService::getPlayNum($userInfo['id']);
                $num = $maxNum - $playNum;
                return $num < 0 ? "0/$maxNum" : "$num/$maxNum";
            }),
            'register_date' => strval($userInfo['register_date']),
            //账号凭证
            'account_slat' => $userInfo['username'] . '==>' . UserService::getAccountSlat($userInfo['username']),
        ];
    }

    /**
     * 主页
     * @param $userId
     * @param $homeId
     * @return array
     * @throws BusinessException
     */
    public static function getHome($userId, $homeId)
    {
        $userId = intval($userId);
        $homeId = intval($homeId);
        $homeInfo = UserService::getInfoFromCache($homeId);
        if (empty($homeInfo)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '未找到该用户!');
        }
        $result = [
            'id' => strval($homeId),
            'is_my'=>$homeId==$userId?'y':'n',
            'username' => strval($homeInfo['username']),
            'nickname' => strval($homeInfo['nickname']),
            'headico' => CommonService::getCdnUrl($homeInfo['headico']),
            'sign' => strval($homeInfo['sign']),
            'has_follow' => !empty($userId) ? (UserFansService::has($userId, $homeId) ? 'y' : 'n') : 'n',
            'movie_filter' => ['home_id' => $homeId, 'page_size' => 16],
            'post_filter' => ['home_id' => $homeId, 'page_size' => 16],
        ];
        return $result;
    }


    /**
     * 创作中心
     * @param $userId
     * @return array
     * @throws BusinessException
     */
    public static function getCreator($userId)
    {
        $userId = intval($userId);
        $userInfo = UserService::getInfoFromCache($userId);
        if (empty($userInfo)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '未找到该用户!');
        }
        $result =[
            'id' => strval($userId),
            'username' => strval($userInfo['username']),
            'nickname' => strval($userInfo['nickname']),
            'headico' => CommonService::getCdnUrl($userInfo['headico']),
            'sign' => strval($userInfo['sign']),
            'category' => strval($userInfo['category']),

            'balance_income' => strval($userInfo['balance_income']),
            'balance_income_freeze' => strval($userInfo['balance_income_freeze']),

            'fans' => strval($userInfo['fans']),
            'movie_total'         => strval($userInfo['creator']['movie_total']),

            'movie_click_total'   => strval($userInfo['creator']['movie_click_total']),
            'movie_fee_rate'      => strval($userInfo['creator']['movie_fee_rate']),
            'movie_upload_num'    => strval($userInfo['creator']['movie_upload_num']),
            'movie_curr_upload_num'    => value(function ()use($userId){
                $count=MovieModel::count(['user_id' => $userId,'created_at'=>['$gte'=>strtotime(date('Y-m-d')),'$lte'=>strtotime(date('Y-m-d 23:59:59'))]]);
                return strval($count);
            }),

            'post_total'          => strval($userInfo['creator']['post_total']),
            'post_click_total'    => strval($userInfo['creator']['post_click_total']),
            'post_upload_num'     => strval($userInfo['creator']['post_upload_num']),
            'post_curr_upload_num'    => value(function ()use($userId){
                $count=PostModel::count(['user_id' => $userId,'created_at'=>['$gte'=>strtotime(date('Y-m-d')),'$lte'=>strtotime(date('Y-m-d 23:59:59'))]]);
                return strval($count);
            }),
            'movie_filters'=>[
                ['name'=>'全部','filter'=>['home_id'=>$userId,'order'=>'new']],
                ['name'=>'已发布','filter'=>['home_id'=>$userId,'order'=>'new','status'=>1]],
                ['name'=>'审核中','filter'=>['home_id'=>$userId,'order'=>'new','status'=>2]],
                ['name'=>'未通过','filter'=>['home_id'=>$userId,'order'=>'new','status'=>3]],
            ],
            'post_filters'=>[
                ['name'=>'全部','filter'=>['home_id'=>$userId,'order'=>'new']],
                ['name'=>'已发布','filter'=>['home_id'=>$userId,'order'=>'new','status'=>1]],
                ['name'=>'审核中','filter'=>['home_id'=>$userId,'order'=>'new','status'=>0]],
                ['name'=>'未通过','filter'=>['home_id'=>$userId,'order'=>'new','status'=>2]],
            ]
        ];
        return $result;
    }

    /**
     * 修改密码
     * @param $userId
     * @param $oldPassword
     * @param $newPassword
     * @return true
     * @throws BusinessException
     */
    public static function changePassword($userId, $oldPassword, $newPassword)
    {
        $userRow = UserModel::findByID(intval($userId));
        if (UserService::checkPassword($oldPassword, $userRow['password']) === false) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '旧密码错误!');
        }
        if (!preg_match('/^[\x21-\x7E]{6,32}$/', $newPassword)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '新密码长度需在 6 到 32 位之间，且只能包含字母、数字或符号!');
        }
        $password = UserService::makePassword($newPassword);
        UserModel::updateRaw(['$set' => ['password' => $password]], ['_id' => $userId]);
        return true;
    }

    /**
     * 使用兑换码
     * @param $userId
     * @param $code
     * @return bool
     * @throws BusinessException
     */
    public static function doCode($userId, $code)
    {
        return UserCodeService::doCode($userId, $code);
    }

    /**
     * 兑换记录
     * @param $userId
     * @param $page
     * @param $pageSize
     * @param $cursor
     * @return array
     */
    public static function codeLog($userId, $page = 1, $pageSize = 20,$cursor='')
    {
        return UserCodeLogService::getLog($userId, $page, $pageSize,$cursor);
    }

    /**
     * 个人信息修改-多条
     * @param $userId
     * @param $data
     * @return true
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function doMultipleUpdate($userId,$data)
    {
        foreach ($data as $key=>$value) {
            UserService::doSimpleUpdate($userId,$key,$value);
        }
        return true;
    }

    /**
     * 关注用户
     * @param $userId
     * @param $toId
     * @param $action
     * @return bool
     * @throws BusinessException
     */
    public static function doFollow($userId, $toId,$action)
    {
        return UserFansService::do($userId, $toId, $action);
    }

    /**
     * 关注列表
     * @param $userId
     * @param $homeId
     * @param $page
     * @return array
     */
    public static function getFollowList($userId, $homeId,$action, $page = 1,$cursor='')
    {
        $ids = UserFansService::getFollowIds($userId, $homeId,$action, $page,20,$cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = intval($id);
            }
        }
        $data = [];
        if (!empty($ids['ids'])) {
            //方式1.搜es
//            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
//            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
            //方式2.搜数据库
            $relationMap = UserFansService::getMultiRelationStatus($userId,$ids['ids']);
            foreach ($ids['ids'] as $id) {
                $userInfo = UserService::getInfoFromCache($id);
                $data[]=[
                    'id'=>strval($userInfo['id']),
                    'username'=>strval($userInfo['username']),
                    'nickname'=>strval($userInfo['nickname']),
                    'headico'=>CommonService::getCdnUrl($userInfo['headico']),
                    'sign' => strval($userInfo['sign']),
                    'relation'=>$relationMap[$id] ?? 'none'
                ];
            }
        }

        return [
            'data' => $data,
            'total' => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size' => strval($ids['page_size']),
            'last_page' => strval($ids['last_page']),
            'cursor' => strval($ids['cursor']),
        ];
    }

    /**
     * 头像列表
     * @return array
     */
    public static function getHeadImages()
    {
        $dir = ConfigService::getConfig('media_dir');
        $result = array();
        for ($i=202;$i>0;$i--){
            $value = sprintf('%s/common_file/headico/wahaha/%s.jpg',$dir,$i);
            $result[] =  array(
                'value' =>$value ,
                'img' => CommonService::getCdnUrl($value)
            );
        }
        return $result;
    }

    /**
     * 粉丝列表
     * @param $userId
     * @param $homeId
     * @param $page
     * @return array
     */
    public static function getFansList($userId, $homeId, $page = 1,$cursor='')
    {
        $ids = UserFansService::getFansIds($userId, $homeId, $page,20,$cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = intval($id);
            }
        }
        $data = [];
        if (!empty($ids['ids'])) {
            //方式1.搜es
//            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
//            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
            //方式2.搜数据库
            $relationMap = UserFansService::getMultiRelationStatus($userId,$ids['ids']);
            foreach ($ids['ids'] as $id) {
                $userInfo = UserService::getInfoFromCache($id);
                $data[]=[
                    'id'=>strval($userInfo['id']),
                    'username'=>strval($userInfo['username']),
                    'nickname'=>strval($userInfo['nickname']),
                    'headico'=>CommonService::getCdnUrl($userInfo['headico']),
                    'sign' => strval($userInfo['sign']),
                    'relation'=>$relationMap[$id] ?? 'none'
                ];
            }
        }

        return [
            'data' => $data,
            'total' => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size' => strval($ids['page_size']),
            'last_page' => strval($ids['last_page']),
            'cursor' => strval($ids['cursor']),
        ];
    }

    /**
     * 收藏板块
     * @param $userId
     * @param $objectType
     * @param $objectId
     * @return bool
     * @throws BusinessException
     */
    public static function doFavorite($userId,$objectType,$objectId)
    {
        return UserFavoriteService::do($userId, $objectType, $objectId);
    }

    /**
     * 收藏的板块列表
     * @param $userId
     * @param $objectType
     * @param $page
     * @return array
     */
    public static function favorite($userId,$objectType,$page=1)
    {
        $ids = UserFavoriteService::getIds($userId, $objectType, $page);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = intval($id);
            }
        }

        $data = [];
        if (!empty($ids['ids'])) {
            switch ($objectType){
                case 'movie_tag':
                    $data = MovieTagModel::find(['_id'=>['$in'=>$ids['ids']]],[],[],0,count($ids['ids']));
                    break;
                case 'post_tag':
                    $data = PostTagModel::find(['_id'=>['$in'=>$ids['ids']]],[],[],0,count($ids['ids']));
                    break;
            }
            foreach ($data as &$item) {
                $item=[
                    'id'    =>strval($item['_id']),
                    'name'  =>strval($item['name']),
                    'click' => strval(CommonUtil::formatNum($item['click']??0).'次播放'),
                    'favorite' => strval(CommonUtil::formatNum($item['favorite']??0).'次收藏'),
                    'follow'=>strval(CommonUtil::formatNum($item['follow']?:0).'人参与'),
                    //关注的用户
                    'follow_user'=>value(function ()use($item){
                        $num = max(4,$item['follow']??0);
                        $result = [];
                        for($n=1;$n<=$num;$n++){
                            //随机生成头像
                            $userRow = UserService::getDefaultUserRow(null);
                            $result[]= CommonService::getCdnUrl($userRow['headico']);
                        }
                        return $result;
                    }),
                ];
                unset($item);
            }
        }

        return [
            'data' => $data,
            'total' => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size' => strval($ids['page_size']),
            'last_page' => strval($ids['last_page']),
        ];
    }

    /**
     * vip页面
     * @param $userId
     * @param $group
     * @return array
     */
    public static function vipInfo($userId, $group)
    {
        $userInfo = UserService::getInfoFromCache($userId);
        $groups = UserGroupService::getEnableAll($group);
        $payments = PaymentService::getPaymentList('vip', ApiService::getDeviceType());
        $result = [];

        $activity = ActivityService::getCountdownAll($userInfo);
        $activity = array_column($activity,null,'id');
        ///筛选不属于自己的卡
        foreach ($groups as $index=>$group) {
            if(empty($group['activity_id'])){
                continue;
            }
            if(!in_array($group['activity_id'],array_keys($activity))){
                unset($groups[$index]);
                continue;
            }
            $groups[$index]['end_time']=$activity[$group['activity_id']]['end_time'];
        }
        $groups = array_values($groups);

        foreach ($groups as $index => $group) {
            $item = [
                'id'        => $group['id'],
                'name'      => $group['name'],
                'day_num'   => strval($group['day_num'] >= 3650 ? '9999天' : $group['day_num'] . '天'),
                'day_tips'  => $group['day_tips'], //天数提示
                'description' => strval($group['description']),
                'img'       =>CommonService::getCdnUrl($group['img']),
                'price'     => $group['price'],
                'old_price' => strval($group['old_price']),
                'give'      => $group['gift_num'] > 0 ? '送' . $group['gift_num'] . '金币' : "",
                'price_tips' => strval($group['price_tips']),//价钱描述 xx/月
                'end_time'  => strval($group['end_time']??''),
                'icon_text' => strval($group['tips']??''),
                //权益矩阵
                'privilege' => value(function () use ($group) {
                    $rows = [];
                    foreach ($group['right']['show'] as $right) {
                        if (in_array($right,array_keys(UserGroupService::$right['show']))) {
                            $rows[]=[
                                'name'=>strval(UserGroupService::$right['show'][$right]['name']),
                                'desc'=>strval(UserGroupService::$right['show'][$right]['desc']),
                                'image'=>strval(CommonService::getCdnUrl(UserGroupService::$right['show'][$right]['image']))
                            ];
                        }
                    }
                    return $rows;
                }),
                'payments'  => value(function () use ($group, $payments) {
                    $result = [];
                    foreach ($payments as $payment) {
                        $canUse = false;
                        if ($payment['can_use_amount'] == 'unlimit') {
                            $canUse = true;
                        } else {
                            $amounts = explode(',', $payment['can_use_amount']);
                            if (in_array($group['price'], $amounts)) {
                                $canUse = true;
                            }
                        }
                        if ($canUse) {
                            $result[] = [
                                'payment_id' => $payment['payment_id'],
                                'payment_name' => $payment['payment_name'],
                                'payment_ico' => $payment['payment_ico'],
                                'type' => $payment['type']
                            ];
                        }
                    }
                    $result[] = [
                        'payment_id' => '-1',
                        'payment_name' => '钱包',
                        'payment_ico' => CommonService::getCdnUrl('/hc237/uploads/default/other/2026-01-08/60c3463c36f0b2695f47531bf6b6f28b.png'),
                        'type' => 'point'
                    ];
                    return $result;
                })
            ];
            $result[] = $item;
        }

        JobService::create(new EventBusJob(new UserVipPayload($userId)));
        return $result;
    }

    /**
     * 购买会员
     * @param $userId
     * @param $groupId
     * @param $paymentId
     * @return array
     * @throws BusinessException
     */
    public static function doVip($userId, $groupId, $paymentId)
    {
        $userRow = UserModel::findByID(intval($userId));
        UserService::checkDisabled($userRow);
        $group = UserGroupService::getInfo($groupId);
        if (empty($group) || $group['is_disabled'] == 'y') {
            throw new BusinessException(StatusCode::DATA_ERROR, '当前套餐已下架!');
        }
        if (!CommonService::checkActionLimit("user_order_{$userId}", 10, 2)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '慢点慢点,我受不了!');
        }
        $orderData = array(
            'order_sn' => $paymentId == -1 ? CommonUtil::createOrderNo('BL') : CommonUtil::createOrderNo(ConfigService::getConfig('order_prefix').'O'),
            'user_id' => $userId,
            'device_type' => $userRow['device_type'],
            'username' => $userRow['username'],
            'channel_name' => strval($userRow['channel_name']),
            'register_at' => $userRow['register_at'] * 1,
            'group_id' => intval($group['id']),
            'group_name' => $group['name'],
            'status' => 0,
            'day_num' => $group['day_num'] * 1,
            'gift_num' => $group['gift_num'] * 1,
            'download_num' => $group['download_num'] * 1,
            'discount_coupon' => intval($group['coupon_num'] ?: 0),//代金券张数
            'group_rate' => intval($group['rate'] ?: 100),//折扣
            'price' => $group['price'] * 1,
            'real_price' => 0,
            'pay_id' => $paymentId,
            'pay_name' => value(function () use ($paymentId) {
                $paymentItems = PaymentService::getPaymentList('vip', ApiService::getDeviceType());
                return isset($paymentItems[$paymentId]['type']) ? $paymentItems[$paymentId]['type'] : '';
            }),
            'pay_at' => 0,
            'pay_rate' => 0,
            'trade_sn' => '',
            'register_ip' => $userRow['register_ip'],
            'created_ip' => CommonUtil::getClientIp(),
            'register_date' => $userRow['register_date'],


            'jet_lag' => UserService::regDiff($userRow),
            'pay_date' => ''
        );
        if ($paymentId == -1 && $userRow['balance'] < $orderData['price']) {
            throw new BusinessException(StatusCode::DATA_ERROR, '您的金币不足以支付订单!');
        }
        $orderId = UserOrderModel::save($orderData);
        if (empty($orderId)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '支付繁忙,请稍后再试!');
        }

        JobService::create(new EventBusJob(new UserDoVipPayload($userId,$orderId,$groupId,$paymentId)));

        //余额支付
        if ($paymentId == -1) {
            $result = AccountService::reduceBalance($userRow, $orderData['order_sn'], $orderData['price'], 3,'balance', "购买会员:{$orderData['group_name']}");
            if ($result) {
                UserService::doChangeGroup($userRow, $orderData['day_num'], $orderData['group_id']);
                UserService::setInfoToCache($userId);
                UserOrderModel::save(['status' => 1, '_id' => $orderId, 'pay_at' => time()]);
            }
            return ['pay_url' => '', 'pay_type' => 'balance', 'is_jump' => 'n', 'msg' => $result ? '金币支付成功' : '金币支付失败'];
        } else {

            $result = PaymentService::createPayLink('vip', $paymentId, $orderId, $orderData['price'], $orderData['order_sn'], ApiService::getDeviceType(), $userRow);
            UserOrderModel::save(['pay_id' => $result['payment_id'], '_id' => $orderId]);
            return [
                'is_jump' => $result['payment_type']=='url' ? 'y' : 'n',
                'pay_type' => $result['payment_type'],
                'pay_url' => $result['payment_url'],
            ];
        }
    }


    /**
     * 金币充值页面
     * @param $userId
     * @return array
     * @throws BusinessException
     */
    public static function rechargeInfo($userId)
    {
        $userInfo = self::getInfo($userId);
        UserService::checkDisabled($userInfo);
        $result = [
            'user_info'=>$userInfo,
            'rate_tips' => '1金币=1元',
            'can_withdraw' => $userInfo['is_vip'],
            'withdraw_tips' => $userInfo['is_vip'] == 'y' ? '' : '抱歉哦，提现功能仅对会员开放',
            'withdraw_fee' => strval($userInfo['withdraw_fee'] ?: (ConfigService::getConfig('withdraw_fee') ?: '30')),//提现费率
            'withdraw_min' => strval(ConfigService::getConfig('withdraw_min') ?: '1000'),//最低提现
            'group_items' => value(function () use ($userInfo) {
                $groups = UserProductService::getEnableAll('point');
                $payments = PaymentService::getPaymentList('point', ApiService::getDeviceType());

                $result = [];
                foreach ($groups as $group) {
                    $result[] = [
                        'id' => $group['id'],
                        'name' => $group['name'],
                        'num' => strval($group['num']),
                        'price' => strval($group['price']),
                        'old_price' => strval($group['old_price']),
                        'give' => $group['gift_num'] > 0 ? '送' . $group['gift_num'] . '金币' : "",
                        'description' => strval($group['description']),
                        'tips' => strval($group['tips']),
                        'payments' => value(function () use ($payments, $group) {
                            $result = [];
                            foreach ($payments as $payment) {
                                $canUse = false;
                                if ($payment['can_use_amount'] == 'unlimit') {
                                    $canUse = true;
                                } else {
                                    $amounts = explode(',', $payment['can_use_amount']);
                                    if (in_array($group['price'], $amounts)) {
                                        $canUse = true;
                                    }
                                }
                                if ($canUse) {
                                    $result[] = [
                                        'payment_id' => $payment['payment_id'],
                                        'payment_name' => $payment['payment_name'],
                                        'payment_ico' => $payment['payment_ico'],
                                        'type' => $payment['type']
                                    ];
                                }
                            }
                            return $result;
                        })
                    ];
                }

                return $result;
            }),
        ];

        JobService::create(new EventBusJob(new UserRechargePayload($userId)));
        return $result;
    }

    /**
     * 充值
     * @param $type
     * @param $userId
     * @param $productId
     * @param $paymentId
     * @return array
     * @throws BusinessException
     */
    public static function doRecharge($type, $userId, $productId, $paymentId)
    {
        $userRow = UserModel::findByID(intval($userId));
        UserService::checkDisabled($userRow);
        $product = UserProductService::getInfo($productId);
        if (empty($product) || $product['is_disabled'] == 'y' || $product['type'] != $type) {
            throw new BusinessException(StatusCode::DATA_ERROR, '当前套餐已下架!');
        }
        if (!CommonService::checkActionLimit('user_recharge_' . $userId, 20, 2)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '慢点慢点,我受不了!');
        }

        $data = array(
            'order_sn' => CommonUtil::createOrderNo(ConfigService::getConfig('order_prefix').'R'),
            'user_id' => $userId,
            'device_type' => $userRow['device_type'],
            'username' => $userRow['username'],
            'status' => 0,
            'amount' => $product['price'] * 1,
            'real_amount' => 0,
            'product_id' => $productId,
            'give' => $product['gift_num'] * 1,//赠送金币
            'vip' => $product['vip_num'] * 1,//赠送vip
            'num' => $product['num'] * 1,
            'record_type' => $product['type'],
            'fee' => 0,
            'pay_id' => $paymentId,
            'pay_name' => value(function () use ($paymentId) {
                $paymentItems = PaymentService::getPaymentList('point', ApiService::getDeviceType());
                return isset($paymentItems[$paymentId]['type']) ? $paymentItems[$paymentId]['type'] : '';
            }),
            'pay_at' => 0,
            'pay_rate' => 0,
            'pay_date' => '',
            'trade_sn' => '',
            'channel_name' => strval($userRow['channel_name']),
            'register_at' => $userRow['register_at'] * 1,
            'register_date' => $userRow['register_date'],
            'jet_lag' => UserService::regDiff($userRow),
            'register_ip' => $userRow['register_ip'],
            'created_ip' => CommonUtil::getClientIp(),
        );

        $orderId = UserRechargeModel::save($data);
        if (empty($orderId)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '支付繁忙,请稍后再试!');
        }

        $result = PaymentService::createPayLink($data['record_type'], $paymentId, $orderId, $data['amount'], $data['order_sn'], ApiService::getDeviceType(), $userRow);
        UserRechargeModel::save(['pay_id' => $result['payment_id'], '_id' => $orderId]);
        JobService::create(new EventBusJob(new UserDoRechargePayload($userId,$orderId,$productId,$paymentId)));

        return [
            'is_jump' => $result['payment_type']=='url' ? 'y' : 'n',
            'pay_type' => $result['payment_type'],
            'pay_url' => $result['payment_url'],
        ];
    }

    /**
     * 余额日志
     * @param $userId
     * @param $balanceField
     * @param $page
     * @param $cursor
     * @return array
     */
    public static function getAccountLog($userId,$balanceField, $page = 1,$cursor='')
    {
        return AccountService::getLogs($userId,$balanceField, $page,20,$cursor);
    }

    /**
     * 订单记录
     * @param $userId
     * @param $type
     * @param $page
     * @param $pageSize
     * @return array
     */
    public static function getOrderLog($userId,$type,$page=1,$pageSize=20)
    {
        $query = ['user_id'=>intval($userId)];
        switch ($type){
            case 'vip':
                $count = UserOrderModel::count($query);
                $rows = UserOrderModel::find($query,[],['_id'=>-1],($page-1)*$pageSize,$pageSize);
                foreach ($rows as &$row){
                    $row=[
                        'order_sn'  =>strval($row['order_sn']),
                        'name'      =>strval($row['group_name']),
                        'pay_name'  =>strval($row['pay_name']),
                        'price'     =>strval($row['price']),
                        'created_at'=>strval(date('Y-m-d H:i:s', $row['created_at'])),
                        'status'    =>strval($row['status']),
                        'status_text'=>strval(CommonValues::getUserOrderStatus($row['status'])),
                    ];
                    unset($row);
                }
                break;
            case 'recharge':
                $count = UserRechargeModel::count($query);
                $rows = UserRechargeModel::find($query,[],['_id'=>-1],($page-1)*$pageSize,$pageSize);
                foreach ($rows as &$row){
                    $row=[
                        'order_sn'  =>strval($row['order_sn']),
                        'name'      =>strval($row['num'].'钻石'),
                        'pay_name'  =>strval($row['pay_name']),
                        'price'     =>strval($row['amount']),
                        'created_at'=>strval(date('Y-m-d H:i:s', $row['created_at'])),
                        'status'    =>strval($row['status']),
                        'status_text'=>strval(CommonValues::getUserOrderStatus($row['status'])),
                    ];
                    unset($row);
                }
                break;
            default:
                $count=0;
                $rows=[];
        }
        return  [
            'data' => $rows,
            'total' => strval($count),
            'current_page' => strval($page),
            'page_size' => strval($pageSize),
            'last_page' => strval(ceil($count / $pageSize))
        ];
    }

    /**
     * 获取分享信息-因为app可能直接进入分享,而不经过我的页面
     * @param $userId
     * @return array
     */
    public static function getShareInfo($userId)
    {
        return UserShareService::getShareInfo($userId);
    }

    /**
     * 邀请列表
     * @param $userId
     * @param $page
     * @param $pageSize
     * @param $cursor
     * @return array
     */
    public static function getShareLog($userId,$page=1,$pageSize=20,$cursor='')
    {
        $ids =UserShareService::getIds($userId,$page,$pageSize,$cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = intval($id);
            }
        }
        $data = [];
        if (!empty($ids['ids'])) {
            $data = UserModel::find(['_id'=>['$in'=>$ids['ids']]],['_id','nickname','username','headico','created_at'],[],0,count($ids['ids']));
            foreach ($data as &$item) {
                $item=[
                    'id'=>strval($item['id']),
                    'nickname'=>strval($item['nickname']),
                    'username'=>strval($item['username']),
                    'headico'=>strval(CommonService::getCdnUrl(($item['headico']))),
                    'created_at'=> date('Y-m-d H:i:s', $item['created_at']),
                ];
                unset($item);
            }
        }

        return [
            'data' => $data,
            'total' => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size' => strval($ids['page_size']),
            'last_page' => strval($ids['last_page']),
            'cursor'    => strval($ids['cursor']),
        ];
    }

    /**
     * 用户搜索
     * @param array $filter
     * @return array
     */
    public static function doSearch(array $filter = [])
    {
        $query = [];
        $query['page'] = self::getRequest($filter, 'page', 'int', 1);
        $query['page_size'] = self::getRequest($filter, 'page_size', 'int', 12);
        $query['keywords'] = self::getRequest($filter, 'keywords', 'string', '');
        $query['ids'] = self::getRequest($filter, 'ids', 'string', '');
        $query['order'] = self::getRequest($filter, 'order', 'string', '');
        return UserService::doSearch($query);
    }


    /**
     * 用户活跃
     * 上报
     * @param $userId
     * @param $route
     * @param $params
     * @return true
     */
    public static function doActive($userId,$route,$params)
    {
        return UserActiveService::do($userId,$route,$params);
    }

    /**
     * 用户活跃
     * 获取某个路由的在线人数
     * @param $route
     * @param $params
     * @return string
     */
    public static function getActive($route,$params)
    {
        return UserActiveService::getRouteCount($route,$params);
    }


    /**
     * 去提现
     * @param $userId
     * @param $method
     * @param $bankName
     * @param $accountName
     * @param $account
     * @param $num
     * @param $balanceField
     * @return true
     * @throws BusinessException
     */
    public static function doWithdraw($userId,$method,$bankName,$accountName,$account,$num,$balanceField)
    {
        return UserWithdrawService::doWithdraw($userId,$method,$bankName,$accountName,$account,$num,$balanceField);
    }


    /**
     * 提现记录
     * @param $userId
     * @param $balanceField
     * @param $page
     * @param $pageSize
     * @return array
     */
    public static function getWithdrawLog($userId,$balanceField='balance',$page = 1, $pageSize = 20)
    {
        return UserWithdrawService::getLogs($userId,$balanceField,$page,$pageSize);
    }

    /**
     * 获取客服中心网址(h5)
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public static function getCustomerUrl($userId)
    {
        $deviceType = ApiService::getDeviceType();
        $version = ApiService::getVersion();
        $configs  = CenterCustomerJob::getCenterConfig('customer');
        $service = new CenterCustomerService($configs['url'],$configs['appid'],$configs['appkey']);
        return [
            'url'=>$service->getUrl($userId,$deviceType,$deviceType,$version)
        ];
    }
}
