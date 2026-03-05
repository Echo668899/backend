<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Services\Admin\AdminRoleService;

/**
 * 系统用户管理
 *
 * @package App\Controller\Backend
 */
class AdminUserController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/adminUser');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AdminUserRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('roles', AdminRoleService::getRoles());
    }

    public function doAction()
    {
        $act = $this->getRequest('act');
        $ids = $this->getRequest('id');
        if (empty($act) || empty($ids)) {
            $this->sendErrorResult('操作错误!');
        }
        $ids = explode(',', $ids);
        if ($act == 'disable') {
            foreach ($ids as $id) {
                AdminUserRepository::doDisable($id);
            }
        }
        $this->sendSuccessResult();
    }
}
