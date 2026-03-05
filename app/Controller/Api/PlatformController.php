<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Repositories\Api\PlatformRepository;

/**
 * 第三方平台
 */
class PlatformController extends BaseApiController
{
    /**
     * 进入
     * @return void
     */
    public function enterAction()
    {
        $userId = $this->getUserId();
        $code   = $this->getRequest('code', 'string');
        $result = PlatformRepository::enter($userId, $code);
        $this->sendSuccessResult($result);
    }

    /**
     * 退出
     * @return void
     */
    public function exitAction()
    {
        $userId = $this->getUserId();
        $code   = $this->getRequest('code', 'string');
        PlatformRepository::exit($userId, $code);
        $this->sendSuccessResult();
    }
}
