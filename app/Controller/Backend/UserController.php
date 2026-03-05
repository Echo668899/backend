<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Models\User\UserModel;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Repositories\Backend\Common\ChatRepository;
use App\Repositories\Backend\User\UserRepository;
use App\Services\Admin\AdminUserService;
use App\Services\Common\QuickReplyService;
use App\Services\User\UserGroupService;
use App\Services\User\UserService;

class UserController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/user');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = UserRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->initData();
    }

    public function initData()
    {
        $this->view->setVar('groupArr', UserGroupService::getAll('normal'));
        $this->view->setVar('disabledArr', CommonValues::getIs());
        $this->view->setVar('deviceArr', CommonValues::getDeviceTypes());
        $this->view->setVar('sexArr', CommonValues::getUserSex());
        $this->view->setVar('upCateArr', CommonValues::getUpCategories());
    }

    /**
     * 详情
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = UserRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->initData();
    }

    /**
     * 编辑
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = UserRepository::save($_POST);
        if ($result) {
            $this->sendSuccessResult();
        }
        $this->sendErrorResult('保存错误!');
    }

    /**
     * 操作
     */
    public function doAction()
    {
        $ids      = $this->getRequest('id');
        $act      = $this->getRequest('act');
        $errorMsg = $this->getRequest('error_msg');
        if (empty($ids) || empty($act)) {
            $this->sendErrorResult('参数错误!');
        }

        $token = AdminUserService::getToken();
        if ($act == 'up') {
            $update = ['is_disabled' => 0];
        } elseif ($act == 'down') {
            if ($errorMsg) {
                $update = ['is_disabled' => 1, 'error_msg' => $errorMsg . " 操作人:{$token['username']}"];
            } else {
                $update = ['is_disabled' => 1, 'error_msg' => "操作人:{$token['username']}"];
            }
        } elseif ($act == 'es') {
            $update = [];
        } else {
            $this->sendErrorResult('不能理解的操作!');
        }
        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            if ($update) {
                UserModel::updateById($update, intval($id));
            }
            UserService::setInfoToCache($id);
        }
        $this->sendSuccessResult();
    }

    /**
     * 生成用户
     * @throws BusinessException
     */
    public function addAction()
    {
        $num = $this->getRequest('num');
        UserRepository::create($num);
        $this->sendSuccessResult();
    }

    /**
     * 后台充值
     * @throws BusinessException
     */
    public function rechargeAction()
    {
        $money      = $this->getRequest('money');
        $userId     = $this->getRequest('user_id');
        $googleCode = $this->getRequest('google');
        $action     = $this->getRequest('action', 'string', 'point');
        $remark     = $this->getRequest('remark', 'string', '');

        if (empty($money)) {
            $this->sendErrorResult('必填项缺失');
        }

        if (kProdMode) {
            if (empty($googleCode) || !AdminUserRepository::verifyGoogleCode($googleCode)) {
                $this->sendErrorResult('谷歌验证码错误!');
            }
        }

        if ($action == 'point') {
            $result = UserRepository::doRecharge($userId, $money, 'point', $remark ?: '后台充值余额');
        } else {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if ($result) {
            UserService::setInfoToCache($userId);
            $this->sendSuccessResult();
        }
    }

    /**
     * 找回账号页面
     */
    public function findAction()
    {
        if ($this->isPost()) {
            $userId1 = $this->getRequest('user_id1', 'int');
            $userId2 = $this->getRequest('user_id2', 'int');
            if (empty($userId1) || empty($userId2) || $userId1 == $userId2) {
                $this->sendErrorResult('参数错误');
            }
            $rows = UserRepository::getAccList($userId1, $userId2);
            $this->sendSuccessResult($rows);
        }
    }

    /**
     * 确认找回
     * @throws BusinessException
     */
    public function doFindAction()
    {
        $oldUserId  = $this->getRequest('user_id1', 'int');
        $newUserId  = $this->getRequest('user_id2', 'int');
        $googleCode = $this->getRequest('google_code');

        if (empty($oldUserId) || empty($newUserId) || $newUserId == $oldUserId) {
            $this->sendErrorResult('参数错误');
        }

        if (kProdMode) {
            if (empty($googleCode) || !AdminUserRepository::verifyGoogleCode($googleCode)) {
                $this->sendErrorResult('谷歌验证码错误!');
            }
        }

        $result = UserRepository::findAccount($oldUserId, $newUserId);
        if ($result) {
            $this->sendSuccessResult();
        }
        $this->sendErrorResult('找回账号失败!');
    }

    public function chatListAction()
    {
        $userId = $this->getRequest('id', 'int');
        if ($this->isPost()) {
            $_REQUEST['from_id'] = $userId;
            $result              = ChatRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        } else {
            $userInfo = UserModel::findByID($userId);
            if (empty($userInfo)) {
                $this->sendErrorResult('用户不存在');
            }
            $this->view->setVar('user', $userInfo);
        }
    }

    /**
     * 聊天详情
     * @throws BusinessException
     */
    public function chatInfoAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = ChatRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('quickMessages', QuickReplyService::getAll());
        $this->view->pick('chat/detail');
    }
}
