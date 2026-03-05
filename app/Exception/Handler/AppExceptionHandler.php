<?php

namespace App\Exception\Handler;

use App\Constants\StatusCode;
use App\Services\Common\ApiService;
use App\Utils\LogUtil;
use Exception;

class AppExceptionHandler
{
    public function __construct(Exception $exception)
    {
        $errorCode = $exception->getCode();
        if ($errorCode >= 1000) {
            $errorMessage = $exception->getMessage();
            if (empty($errorMessage) && isset(StatusCode::ERRORS[$errorCode])) {
                $errorMessage = StatusCode::ERRORS[$errorCode];
            }
        } elseif ($errorCode > 0) {
            $errorMessage = '网络异常-01,请稍后再试!';
            LogUtil::error($exception);
        } else {
            $errorMessage = '网络异常-02,请稍后再试!';
            LogUtil::error($exception);
        }
        if (php_sapi_name() == 'cli') {
            return;
        }
        $this->sendErrorResult($errorMessage, $errorCode);
    }

    /**
     * 发送错误请求结果
     * @param string $error
     * @param string $errorCode
     */
    protected function sendErrorResult($error = '', $errorCode = '')
    {
        $result = [
            'status'    => 'n',
            'error'     => $error,
            'errorCode' => $errorCode
        ];
        $module = dispatcher()->getModuleName();
        if ($module == 'Backend') {
            $this->sendJson($result);
        } elseif ($module == 'Api') {
            $result = ApiService::encryptData($result);
            $this->sendJson($result);
        } else {
            $this->sendJson($result);
        }
    }

    /**
     * 发送json
     * @param $data
     */
    protected function sendJson($data)
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        ob_clean();
        header('Content-Type:application/json; charset=utf-8');
        header('Content-Length:' . strlen($data));
        echo $data;
        exit();
    }
}
