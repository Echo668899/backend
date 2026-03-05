<?php

namespace App\Controller;

use App\Core\Controller\BaseController;
use App\Utils\CommonUtil;

/**
 * 客服系统
 */
class BaseCustomerController extends BaseController
{
    public function initialize()
    {
        $configs = env()->path('app.whitelist')->toArray();
        $ips     = $configs['customer_notice_ips'];
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

    protected function sendSuccessResult($data = null)
    {
        $result = [
            'code' => 200,
            'msg'  => 'success',
            'tips' => '成功',
            'data' => $data
        ];
        $this->sendJson($result);
    }

    protected function sendErrorResult($error = '', $errorCode = 4002)
    {
        $result = [
            'code' => $errorCode,
            'msg'  => 'error',
            'tips' => $error,
            'data' => null
        ];
        $this->sendJson($result);
    }
}
