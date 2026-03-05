<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Repositories\Backend\User\UserGroupRepository;
use App\Services\Activity\ActivityService;
use App\Services\User\UserGroupService;

/**
 * 用户组
 *
 * @package App\Controller\Backend
 */
class UserGroupController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/userGroup');
    }

    /**
     * 所有会员卡
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = UserGroupRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 详情
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = UserGroupRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('userGroupItems', CommonValues::getUserGroupType());
        $this->view->setVar('rightArr', UserGroupService::$right);
        $this->view->setVar('activityArr', ActivityService::getAdminAll('countdown'));
    }

    /**
     * 保存
     */
    public function saveAction()
    {
        $token = $this->getToken();

        if (kProdMode) {
            $keyName = "google_check_user_group_{$token['user_id']}";
            if (!redis()->get($keyName)) {
                $googleCode = $this->getRequest('google_code');
                if (empty($googleCode) || !AdminUserRepository::verifyGoogleCode($googleCode)) {
                    $this->sendErrorResult('谷歌验证码错误!');
                }
                redis()->set($keyName, 1, 300);
            }
        }

        $result = UserGroupRepository::save($_POST);
        if ($result) {
            $this->sendSuccessResult();
        }
        $this->sendErrorResult('保存错误!');
    }

    /**
     * 批量操作
     */
    public function doAction()
    {
        $idStr = $this->getRequest('id');
        $act   = $this->getRequest('act');
        if (empty($idStr) || empty($act)) {
            $this->sendErrorResult('操作错误!');
        }
        if ($act == 'del') {
            $ids = explode(',', $idStr);
            foreach ($ids as $id) {
                UserGroupRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
