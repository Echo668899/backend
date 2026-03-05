<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Common\CollectionsRepository;
use App\Repositories\Backend\User\UserBuyLogRepository;
use App\Repositories\Backend\User\UserOrderRepository;
use App\Repositories\Backend\User\UserRechargeRepository;
use App\Services\User\UserGroupService;

/**
 * 订单
 * Class OrderController
 * @package App\Controller\Backend
 */
class OrderController extends BaseBackendController
{
    /**
     * 会员订单
     */
    public function vipAction()
    {
        $this->checkPermission('/orderVip');
        if ($this->isPost()) {
            $result = UserOrderRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('statusArr', CommonValues::getUserOrderStatus());
        $this->view->setVar('groupArr', UserGroupService::getAll());
        $this->view->setVar('deviceArr', CommonValues::getDeviceTypes());
    }

    /**
     * 金币订单
     */
    public function pointAction()
    {
        $this->checkPermission('/orderPoint');
        if ($this->isPost()) {
            $_REQUEST['record_type'] = 'point';
            $result                  = UserRechargeRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('statusArr', CommonValues::getUserOrderStatus());
        $this->view->setVar('deviceArr', CommonValues::getDeviceTypes());
    }

    /**
     * 收款
     */
    public function collectionAction()
    {
        $this->checkPermission('/orderCollection');
        if ($this->isPost()) {
            $result = CollectionsRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('groupArr', CommonValues::getAccountRecordType());
        $this->view->setVar('deviceArr', CommonValues::getDeviceTypes());
    }

    /**
     * 用户购买
     */
    public function buyAction()
    {
        $this->checkPermission('/orderBuy');
        $objectType = $this->getRequest('object_type');
        if ($this->isPost()) {
            $post = $_REQUEST;
            if ($objectType) {
                $post['object_type'] = $objectType;
            }
            $result = UserBuyLogRepository::getList($post);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('typeArr', CommonValues::getBuyType());
        $this->view->setVar('objectType', $objectType);
    }
}
