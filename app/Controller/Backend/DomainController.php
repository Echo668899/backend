<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Common\DomainRepository;

/**
 * 域名
 */
class DomainController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/domain');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = DomainRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('typeArr', CommonValues::getDomainType());
        $this->view->setVar('disArr', CommonValues::getIs());
    }

    /**
     * 详情
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = DomainRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('typeArr', CommonValues::getDomainType());
        $this->view->setVar('disArr', CommonValues::getIs());
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        $result = DomainRepository::save($_POST);
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
                DomainRepository::delete($id);
            }
        } elseif ($act == 'check') {
            $ids = explode(',', $idStr);
            foreach ($ids as $id) {
                DomainRepository::check($id);
            }
        }
        $this->sendSuccessResult();
    }
}
