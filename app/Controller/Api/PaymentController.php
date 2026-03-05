<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Core\Controller\BaseController;
use App\Services\Common\PaymentService;
use App\Utils\CommonUtil;

/**
 * Class MovieController
 * @package App\Controller\Api
 */
class PaymentController extends BaseController
{
    public function initialize()
    {
        $configs = env()->path('app.whitelist')->toArray();
        $ips     = $configs['payment_notice_ips'];
        $ips     = explode(',', $ips);
        foreach ($ips as $index => $item) {
            if (empty($item)) {
                unset($ips[$index]);
            }
        }
        $ips = array_values($ips);
        // /为空则不做限制
        if (empty($ips)) {
            return;
        }

        // /在白名单不做限制
        if (in_array(CommonUtil::getClientIp(), $ips)) {
            return;
        }

        exit('error');
    }

    /**
     * 支付通知
     * @param $paymentId
     * @param $orderId
     * @param $type
     */
    public function notifyAction($paymentId, $orderId, $type)
    {
        if (empty($paymentId) || empty($orderId) || empty($type)) {
            exit('error');
        }
        PaymentService::notify($paymentId, intval($orderId), $type);
    }

    /**
     * 订单回退
     * 取消订单拉黑用户
     */
    public function doBackAction()
    {
        try {
            $orderSn = $_REQUEST['order_sn'];
            $result  = PaymentService::doBack($orderSn);
            if ($result) {
                echo json_encode(['status' => 'y', 'data' => ['user_id' => $result['user_id'], 'nickname' => $result['nickname']]]);
                exit;
            }
            throw new \Exception('服务器内部错误');
        } catch (\Exception $e) {
            echo json_encode(['status' => 'n', 'message' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * 返回
     */
    public function returnAction()
    {
        header('Content:text/html;charset=utf8');
        exit('请打开官网!');
    }
}
