<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Admin\AdminRoleRepository;
use App\Services\Admin\AuthorityService;

/**
 * 用户角色
 * Class SystemResourceController
 * @package App\Controller\Backend
 */
class AdminRoleController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/adminRole');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AdminRoleRepository::getList($_REQUEST);
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
            $result = AdminRoleRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('authorities', AuthorityService::getTree());
    }

    /**
     * 保存数据
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = AdminRoleRepository::save($_POST);
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
                AdminRoleRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
