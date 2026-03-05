<?php

namespace App\Controller\Customer;

use App\Controller\BaseCustomerController;
use App\Repositories\Customer\UserRepository;
use App\Utils\LogUtil;

/**
 * 客服系统=>业务
 * 暴露给客服系统的接口
 */
class UserController extends BaseCustomerController
{
    /**
     * 订单记录
     * @return void
     */
    public function rechargeAction()
    {
        try {
            $result = UserRepository::order($_REQUEST);
            $this->sendSuccessResult($result);
        } catch (\Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
            $this->sendErrorResult($e->getMessage());
        }
    }

    /**
     * 用户背包信息-对方是游戏设计概念
     * @return void
     */
    public function backpackAction()
    {
        try {
            $result = UserRepository::backpack($_REQUEST);
            $this->sendSuccessResult($result);
        } catch (\Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
            $this->sendErrorResult($e->getMessage());
        }
    }
}
