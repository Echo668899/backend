<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Repositories\Api\LoginRepository;

/**
 * 登录
 */
class LoginController extends BaseApiController
{
    /**
     * 账号
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function usernameAction()
    {
        $result = LoginRepository::username($_REQUEST);
        $this->sendSuccessResult($result);
    }

    /**
     * 二维码登录
     * @return void
     */
    public function qrcodeAction()
    {
        $result = LoginRepository::qrcode($_REQUEST);
        $this->sendSuccessResult($result);
    }

    /**
     * 设备登录
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function deviceAction()
    {
        $result = LoginRepository::device($_REQUEST);
        $this->sendSuccessResult($result);
    }

    /**
     * 退出登录
     * @return void
     */
    public function logoutAction()
    {
        $token = $this->getToken(false);
        LoginRepository::logout($token);
        $this->sendSuccessResult();
    }
}
