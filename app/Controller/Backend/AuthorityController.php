<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Admin\AuthorityRepository;
use App\Services\Admin\AuthorityService;

/**
 * 系统资源表
 * Class SystemResourceController
 * @package App\Controller\Backend
 */
class AuthorityController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/authority');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AuthorityRepository::getList($_REQUEST);
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
            $result = AuthorityRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('options', AuthorityService::getTreeOptions());
    }

    /**
     * 保存数据
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = AuthorityRepository::save($_POST);
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
                AuthorityRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
