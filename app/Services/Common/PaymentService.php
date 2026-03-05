<?php

namespace App\Services\Common;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Common\PaymentJob;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\User\UserDoRechargeSuccessPayload;
use App\Jobs\Event\Payload\User\UserDoVipSuccessPayload;
use App\Models\Common\CollectionsModel;
use App\Models\Common\PaymentLogModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Services\Common\Chat\ChatService;
use App\Services\User\AccountService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;
use App\Utils\LogUtil;
use Exception;
use Phalcon\Manager\MmsService;

class PaymentService extends BaseService
{
    /**
     * 获取支付方式列表
     * @param  string $paymentType
     * @param  string $deviceType
     * @return array
     */
    public static function getPaymentList(string $paymentType, string $deviceType)
    {
        $result = [];
        try {
            $payments = self::_getPaymentList($paymentType, $deviceType);
            foreach ($payments as $payment) {
                $paymentUseType = $payment['can_use_type'];
                if ($paymentUseType != 'all' && strpos($paymentUseType, $paymentType) === false) {
                    continue;
                }
                $result[$payment['payment_id']] = [
                    'payment_id'     => strval($payment['payment_id']),
                    'payment_name'   => strval($payment['payment_name']),
                    'payment_ico'    => CommonService::getCdnUrl($payment['payment_ico']),
                    'can_use_amount' => strval($payment['can_use_amount']),
                    'type'           => strval($payment['type'])
                ];
            }
        } catch (Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        return $result;
    }

    /**
     * @return MmsService
     */
    public static function getMmsService()
    {
        $url    = ConfigService::getConfig('mms_url');
        $appid  = ConfigService::getConfig('mms_appid');
        $appkey = ConfigService::getConfig('mms_appkey');
        return new MmsService($url, $appid, $appkey);
    }

    /**
     * @param                    $type
     * @param                    $paymentId
     * @param                    $orderId
     * @param                    $price
     * @param                    $orderSn
     * @param                    $deviceType
     * @param                    $user
     * @return array
     * @throws BusinessException
     */
    public static function createPayLink($type, $paymentId, $orderId, $price, $orderSn, $deviceType, $user)
    {
        if (is_numeric($user)) {
            $user = UserService::getInfoFromCache($user);
        }
        $notifyHost = ConfigService::getConfig('pay_notice_url');

        $notifyUrl = createUrl('/payment/notify/' . $paymentId . '/' . $orderId . '/' . $type, [], 'Api');
        $notifyUrl = $notifyHost . $notifyUrl;
        if (!in_array($type, ['vip', 'point'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '创建支付错误!');
        }
        if (!in_array($deviceType, ['android', 'ios', 'web'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '创建支付错误!' . $deviceType);
        }
        if ($type == 'point') {
            $type = 'recharge';
        }
        $data = [
            'payment_id' => $paymentId,
            'order_sn'   => $orderSn,
            'amount'     => $price,
            'user_id'    => $user['_id'],
            'username'   => $user['username'],
            'ip'         => CommonUtil::getClientIp(),
            'notice_url' => $notifyUrl,
            // 仅支持pc,ios,android
            'device_type' => $deviceType == 'web' ? 'pc' : $deviceType,
            'type'        => $type,
            'can_sdk'     => '1',
            'channel'     => $user['channel_name'],
            'reg_at'      => $user['register_at'],
            'reg_ip'      => $user['register_ip'],
        ];

        try {
            if (kProdMode) {
                $mmsService = self::getMmsService();
                $result     = $mmsService->createPayLink($data);
            } else {
                $result = [
                    'payment_url'  => 'https://www.baidu.com',
                    'payment_type' => 'url',
                    'payment_id'   => '1'
                ];
            }
            LogUtil::debug(__CLASS__ . " createPayLink userId:{$user['_id']} orderSn:{$orderSn} payId:" . ($result ? $result['payment_id'] : $paymentId) . ' res:' . ($result ? "ok url:{$result['payment_url']}" : 'error '));
            if (empty($result)) {
                //            $this->wssService->joinActionQueue($user['id'], 'do_pay', sprintf('%s||%s||%s', $type == 'recharge' ? 'point' : $type, $paymentId, ''), $data['ip']);
                throw new BusinessException(StatusCode::DATA_ERROR, '创建支付错误!');
            }
            //            $this->wssService->joinActionQueue($user['id'], 'do_pay', sprintf('%s||%s||%s', $type == 'recharge' ? 'point' : $type, $result['payment_id'], $result['payment_url']), $data['ip']);

            return $result;
        } catch (Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        throw new BusinessException(StatusCode::DATA_ERROR, '创建支付错误!');
    }

    /**
     * 支付通知处理
     * @param $paymentId
     * @param $orderId
     * @param $type
     */
    public static function notify($paymentId, $orderId, $type)
    {
        $requestData   = $_REQUEST['data'];
        $requestStatus = $_REQUEST['status'];
        $requestSign   = $_REQUEST['sign'];

        try {
            $mmsService = self::getMmsService();
            $result     = $mmsService->checkNotify($requestData, $requestSign);

            $tradeNo = $result['trade_no'];
            $orderSn = $result['order_sn'];
            $payAt   = $result['pay_at'];
            $payRate = $result['pay_rate'];
            $money   = $result['real_amount'];

            $doResult = false;
            if ($result && $tradeNo && $money > 0) {
                $doResult = self::addPaymentLogs($type, $orderId, $orderSn, $tradeNo, $money, $payAt, $payRate);
            }
            $mmsService->stopNotify($doResult);
        } catch (Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        exit('error');
    }

    /**
     * 添加到付款日志表
     * @param       $type
     * @param       $orderId
     * @param       $orderSn
     * @param       $tradeNo
     * @param       $money
     * @param       $payAt
     * @param       $payRate
     * @return bool
     */
    public static function addPaymentLogs($type, $orderId, $orderSn, $tradeNo, $money, $payAt, $payRate)
    {
        $uniqueId = md5($type . '_' . $orderId);
        $count    = PaymentLogModel::count(['unique_id' => $uniqueId]);
        if ($count > 0) {
            return true;
        }

        $id = PaymentLogModel::insert([
            'unique_id' => $uniqueId,
            'type'      => $type,
            'order_id'  => $orderId,
            'order_sn'  => $orderSn,
            'status'    => 0,
            'trade_no'  => $tradeNo,
            'money'     => doubleval($money),
            'pat_at'    => intval($payAt),
            'pay_rate'  => doubleval($payRate)
        ]);
        // 直接交给job执行了
        JobService::create(new PaymentJob($id), 'default', 'mongodb');
        return true;
    }

    /**
     * 执行支付
     */
    public static function doPaidJob()
    {
        PaymentLogModel::updateRaw(['$set' => ['status' => 0]], ['status' => -1]);
        $items = PaymentLogModel::find(['status' => 0], ['_id', 'type'], ['_id' => -1], 0, 50);
        foreach ($items as $item) {
            $res = self::doPaidOrder($item['_id']);
            LogUtil::info(sprintf('Do paid order: %s=>%s res:%s', $item['type'], $item['_id'], $res ? 'ok' : 'error'));
        }
    }

    /**
     * @param       $id
     * @return bool
     */
    public static function doPaidOrder($id)
    {
        $row = PaymentLogModel::findByID($id);
        if (empty($item) || $item['status'] != 0) {
            return false;
        }
        $exception = '';
        try {
            $type    = $row['type'];
            $orderId = $row['order_id'];
            $tradeNo = $row['trade_no'];
            $money   = $row['money'] * 1;
            $payAt   = $row['pat_at'] * 1;
            $payRate = $row['pay_rate'] * 1;
            PaymentLogModel::updateById(['status' => 1], $row['_id']);
            switch ($type) {
                case 'vip':
                    $doResult = self::doPaidVipOder($orderId, $tradeNo, $money, $payAt, $payRate, true);
                    break;
                case 'point':
                    $doResult = self::doPaidPointOrder($orderId, $tradeNo, $money, $payAt, $payRate, true);
                    break;
                default:
                    throw new Exception('不能识别的类型');
            }
            if ($doResult) {
                return true;
            }
        } catch (BusinessException $e) {
            $exception = sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine());
        } catch (Exception $e) {
            $exception = sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine());
        }
        PaymentLogModel::updateById(['status' => -1, 'error_msg' => $exception], $row['_id']);
        LogUtil::error($exception);
        return false;
    }

    /**
     * vip订单
     * @param                    $orderId
     * @param                    $tradeNo
     * @param                    $money
     * @param                    $payAt
     * @param                    $payRate
     * @return true|void
     * @throws BusinessException
     */
    public static function doPaidVipOder($orderId, $tradeNo, $money, $payAt, $payRate)
    {
        $orderId = intval($orderId);
        $order   = UserOrderModel::findByID($orderId);
        if (empty($order) || $order['status'] == -1) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '订单不存在');
        }
        if ($order['status'] == 1) {
            return true;
        }
        $checkMoney = ($money - $order['price']);
        if ($checkMoney > 7 || $checkMoney < -5) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '订单金额不匹配');
        }
        $user = UserModel::findByID(intval($order['user_id']));
        if (empty($user)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '用户不存在！');
        }
        try {
            $result1 = UserOrderModel::findAndModify(
                ['_id' => $orderId, 'status' => 0],
                [
                    '$set' => [
                        'status'     => 1,
                        'pay_at'     => $payAt * 1,
                        'pay_rate'   => $payRate * 1,
                        'pay_date'   => date('Y-m-d', $payAt),
                        'trade_sn'   => $tradeNo,
                        'real_price' => doubleval($money)
                    ]
                ],
                ['_id'],
                false
            );
            $result2 = UserService::doChangeGroup($user, $order['day_num'], $order['group_id']);
            // 是否赠送金币
            if ($order['gift_num'] > 0) {
                AccountService::addBalance($user, $order['order_sn'], $order['gift_num'], 1, 'balance', '购买会员,赠送金币', 'order_' . $orderId);
            }
            $data = [
                'order_sn'      => $order['order_sn'],
                'trade_sn'      => $tradeNo,
                'user_id'       => $order['user_id'],
                'device_type'   => $order['device_type'],
                'price'         => intval($order['price']),
                'real_price'    => doubleval($money),
                'record_type'   => 'vip',
                'object_id'     => $orderId,
                'pay_id'        => $order['pay_id'],
                'pay_name'      => $order['pay_name'],
                'pay_at'        => $payAt,
                'pay_date'      => date('Y-m-d', $payAt),
                'channel_name'  => strval($order['channel_name']),
                'register_at'   => $order['register_at'],
                'order_at'      => $order['created_at'],
                'jet_lag'       => UserService::regDiff($user),
                'register_date' => $order['register_date'],
            ];
            $result3 = CollectionsModel::insert($data);
            if ($result1 && $result2 && $result3) {
                //                //是否赠送观影券
                //                if($order['discount_coupon']>0){
                //                    $this->userCouponService->toUser($order['user_id'],$order['discount_coupon'],'movie',20);
                //                }
                UserModel::updateRaw([
                    '$set' => [
                        'first_pay' => $user['first_pay'] ?: $payAt,
                        'last_pay'  => $payAt,
                    ],
                    '$inc' => [
                        'pay_total' => $money,
                    ]
                ], ['_id' => intval($order['user_id'])]);

                JobService::create(new EventBusJob(new UserDoVipSuccessPayload($order['user_id'], $orderId, $order['group_id'], $order['pay_id'])));
                UserService::setInfoToCache($order['user_id']);
                try {
                    ChatService::sendSystemMessage($order['user_id'], 'funds.vip', [
                        'content'  => sprintf('会员:%s 开通成功', $order['group_name']),
                        'order_sn' => $order['order_sn'],
                        'link'     => 'inner://vip'
                    ]);
                } catch (\Exception $_) {
                }
                //                $this->userAgentService->orderMLM($order['user_id'],$order['price']);
                return true;
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 金币订单
     * @param                    $orderId
     * @param                    $tradeNo
     * @param                    $money
     * @param                    $payAt
     * @param                    $payRate
     * @return bool
     * @throws BusinessException
     */
    public static function doPaidPointOrder($orderId, $tradeNo, $money, $payAt, $payRate)
    {
        $orderId = intval($orderId);
        $order   = UserRechargeModel::findByID($orderId);
        if (empty($order) || $order['status'] == -1 || $order['record_type'] != 'point') {
            throw  new BusinessException(StatusCode::DATA_ERROR, '订单不存在');
        }
        if ($order['status'] == 1) {
            return true;
        }
        $checkMoney = abs($money - $order['amount']);
        if ($checkMoney > 5) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '订单金额不匹配');
        }
        $user = UserModel::findByID(intval($order['user_id']));
        if (empty($user)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '用户不存在！');
        }
        try {
            $result1 = UserRechargeModel::findAndModify(
                ['_id' => $orderId, 'status' => 0],
                ['$set' => [
                    'status'      => 1,
                    'pay_at'      => $payAt * 1,
                    'pay_rate'    => $payRate * 1,
                    'pay_date'    => date('Y-m-d', $payAt),
                    'trade_sn'    => $tradeNo,
                    'real_amount' => $money,
                    'updated_at'  => time()
                ]],
                ['_id'],
                false
            );
            $result2 = AccountService::addBalance($user, $order['order_sn'], intval($order['num'] + $order['give']), 1, 'balance', '充值金币', 'recharge_' . $orderId);
            // 是否赠送vip
            if ($order['vip'] > 0) {
                UserService::doChangeGroup($user, $order['vip'], 1);
            }
            $data = [
                'order_sn'      => $order['order_sn'],
                'trade_sn'      => $tradeNo,
                'user_id'       => $order['user_id'],
                'device_type'   => $order['device_type'],
                'price'         => $order['amount'],
                'real_price'    => $money,
                'record_type'   => 'point',
                'object_id'     => $orderId,
                'pay_id'        => $order['pay_id'],
                'pay_name'      => $order['pay_name'],
                'pay_at'        => $payAt,
                'pay_date'      => date('Y-m-d', $payAt),
                'channel_name'  => strval($order['channel_name']),
                'register_at'   => $order['register_at'],
                'order_at'      => $order['created_at'],
                'jet_lag'       => UserService::regDiff($user),
                'register_date' => $order['register_date'],
            ];
            $result3 = CollectionsModel::insert($data);
            if ($result1 && $result2 && $result3) {
                UserModel::updateRaw([
                    '$set' => [
                        'first_pay' => $user['first_pay'] ? $user['first_pay'] : $payAt,
                        'last_pay'  => $payAt,
                    ],
                    '$inc' => [
                        'pay_total' => $money,
                    ]
                ], ['_id' => intval($order['user_id'])]);

                UserService::setInfoToCache($order['user_id']);

                JobService::create(new EventBusJob(new UserDoRechargeSuccessPayload($order['user_id'], $orderId, $order['group_id'], $order['pay_id'])));
                try {
                    ChatService::sendSystemMessage($order['user_id'], 'funds.recharge', [
                        'content'  => sprintf('金币充值:%s 成功', $order['num']),
                        'order_sn' => $order['order_sn'],
                        'link'     => 'inner://recharge'
                    ]);
                } catch (\Exception $_) {
                }
                return true;
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 订单回退
     * 取消订单拉黑用户
     * @param            $orderSn
     * @return array
     * @throws Exception
     */
    public static function doBack($orderSn)
    {
        $orderSn = strval($orderSn);
        $mmsUrl  = ConfigService::getConfig('mms_url');
        $mmsUrl  = str_replace('http://', '', $mmsUrl);
        $mmsUrl  = str_replace('https://', '', $mmsUrl);
        if (CommonUtil::getClientIp() != $mmsUrl) {
            throw new Exception('请求不被允许');
        }
        if (empty($orderSn)) {
            throw new Exception('参数错误');
        }
        $order = CollectionsModel::findFirst(['order_sn' => $orderSn]);
        if (empty($order)) {
            throw new Exception("订单 {$orderSn} 不存在");
        }
        // 订单撤回
        if ($order['record_type'] == 'vip') {
            UserOrderModel::update(['status' => -1], ['order_sn' => $orderSn]);
        }
        if ($order['record_type'] == 'point') {
            UserRechargeModel::update(['status' => -1], ['order_sn' => $orderSn]);
        }

        $user = UserModel::findByID($order['user_id']);
        if (empty($user)) {
            throw new Exception("订单 {$orderSn} 用户不存在");
        }
        // 拉黑用户
        UserService::doDisabled($order['user_id'], '恶意退款,系统自动拉黑');
        return [
            'user_id'  => $user['_id'],
            'nickname' => $user['nickname']
        ];
    }
    /**
     * 获取支付列表
     * @param                $paymentType
     * @param                $deviceType
     * @return array|array[]
     * @throws Exception
     */
    private static function _getPaymentList($paymentType, $deviceType)
    {
        if (kProdMode) {
            $mmsService = self::getMmsService();
            $payments   = cache()->get('payment_list');
            if (is_null($payments)) {
                $payments = $mmsService->getPaymentList($paymentType, $deviceType);
                cache()->set('payment_list', $payments, 30);
            }
        } else {
            $payments = [
                [
                    'payment_id'     => '1000',
                    'payment_name'   => '支付宝',
                    'payment_ico'    => CommonService::getCdnUrl('/hc237/uploads/default/other/2026-01-08/cd379f463420e0a3f85155d6eace3fff.png'),
                    'can_use_amount' => '30,500,50,100,200,300',
                    'is_sdk'         => '0',
                    'device_type'    => 'pc,ios,android,web',
                    'type'           => 'alipay',
                    'can_use_type'   => 'all',
                ],
                [
                    'payment_id'     => '2000',
                    'payment_name'   => '微信',
                    'payment_ico'    => CommonService::getCdnUrl('/hc237/uploads/default/other/2026-01-08/e7f58ccb94603c77cdaefa060ad3b504.png'),
                    'can_use_amount' => '100,200',
                    'is_sdk'         => '0',
                    'device_type'    => 'pc,ios,android,web',
                    'type'           => 'wechat',
                    'can_use_type'   => 'all',
                ],
                [
                    'payment_id'     => '3000',
                    'payment_name'   => 'USDT',
                    'payment_ico'    => CommonService::getCdnUrl('/hc237/uploads/default/other/2026-01-08/602295a82a6721f2b5eea66459dbb387.png'),
                    'can_use_amount' => '50,200,1000',
                    'is_sdk'         => '0',
                    'device_type'    => 'pc,ios,android,web',
                    'type'           => 'usdt',
                    'can_use_type'   => 'all',
                ]
            ];
        }
        return $payments;
    }
}
