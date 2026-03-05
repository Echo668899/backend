<?php

namespace App\Controller;

use App\Constants\StatusCode;
use App\Core\Controller\BaseController;
use App\Services\Common\ApiService;

class BaseApiController extends BaseController
{
    /**
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function initialize()
    {
        if ($this->isGet()) {
            $this->send404();
        }
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: version, deviceType, time, requestId,sessionId,sign');
        ApiService::handler();
    }

    /**
     * 发送正确请求结果
     * @param string $data
     */
    protected function sendSuccessResult($data = null)
    {
        $result = [
            'status' => 'y',
            'data'   => $data,
            'time'   => date('Y-m-d H:i:s')
        ];
        $result = ApiService::encryptData($result);
        $this->send($result);
    }

    /**
     * 发送数据
     * @param $result
     */
    protected function send($result)
    {
        if (ApiService::isDebug()) {
            $this->sendJson($result);
        }
        ob_clean();
        header('Content-type:application/octet-stream');
        header('Content-Length:' . strlen($result));
        redis()->close();
        echo $result;
        exit;
    }

    /**
     * 获取用户id
     * @param  bool        $isExits
     * @return string|null
     */
    protected function getUserId($isExits = true)
    {
        $token = $this->getToken($isExits);
        return $token['user_id'] ?: null;
    }

    /**
     * 获取token
     * @param  bool       $isExits
     * @return mixed|null
     */
    protected function getToken($isExits = true)
    {
        $token = ApiService::getToken();
        if (empty($token) && $isExits) {
            $this->sendErrorResult('您还没有登录!', StatusCode::NO_LOGIN_ERROR);
        }
        return $token;
    }

    /**
     * 发送错误请求结果
     * @param string $error
     * @param int    $errorCode
     */
    protected function sendErrorResult($error = '', $errorCode = 2008)
    {
        $result = [
            'status'    => 'n',
            'error'     => $error,
            'errorCode' => $errorCode
        ];
        $result = ApiService::encryptData($result);
        $this->send($result);
    }
}
