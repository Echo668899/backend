<?php

declare(strict_types=1);

namespace App\Core\Controller;

use App\Core\Services\RequestService;
use Phalcon\Mvc\Controller;

abstract class BaseController extends Controller
{
    /**
     * 获取请求
     * @param         $key
     * @param  string $type
     * @param         $defaultValue
     * @return string
     */
    public function getRequest($key, $type = 'string', $defaultValue = null)
    {
        return RequestService::getRequest($_REQUEST, $key, $type, $defaultValue);
    }

    /**
     * 发送正确请求结果
     * @param string $data
     */
    protected function sendSuccessResult($data = null)
    {
        $result = [
            'status' => 'y',
            'data'   => $data
        ];
        $this->sendJson($result);
    }

    /**
     * 发送json
     * @param $data
     */
    protected function sendJson($data)
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        ob_clean();
        header('Content-Type:application/json; charset=utf-8');
        header('Content-Length:' . strlen(strval($data)));
        echo $data;
        exit();
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
        $this->sendJson($result);
    }

    /**
     * 判读是否post请求
     * @return bool
     */
    protected function isPost()
    {
        if ($this->request->isPost()) {
            return true;
        }
        return false;
    }

    /**
     * 判读是否get请求
     * @return bool
     */
    protected function isGet()
    {
        if ($this->request->isGet()) {
            return true;
        }
        return false;
    }

    /**
     * 重定向
     * @param string $url
     * @param array  $params
     * @param mixed  $module
     */
    protected function redirect($url, $params = [], $module = '')
    {
        if (strpos($url, 'http') !== false) {
            if (!empty($params)) {
                if (strpos($url, '?') !== false) {
                    $url .= '&' . http_build_query($params);
                } else {
                    $url .= '?' . http_build_query($params);
                }
            }
        } else {
            $url = createUrl($url, $params, $module);
        }
        ob_clean();
        header('Location:' . $url);
        exit;
    }

    /**
     * 发送404错误信息
     */
    protected function send404()
    {
        ob_clean();
        header('http/1.1 404 not found');
        header('status: 404 not found');
        exit();
    }
}
