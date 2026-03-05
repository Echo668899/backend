<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Common\AdvAppRepository;
use App\Services\Common\AdvAppService;

/**
 * 应用中心管理
 *
 * @package App\Controller\Backend
 */
class AdvAppController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/advApp');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AdvAppRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('posArr', AdvAppService::$position);
        $this->view->setVar('hotArr', CommonValues::getIs());
        $this->view->setVar('disArr', CommonValues::getIs());
    }

    /**
     * 详情
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = AdvAppRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('posArr', AdvAppService::$position);
        $this->view->setVar('hotArr', CommonValues::getIs());
        $this->view->setVar('disArr', CommonValues::getIs());
        $this->view->setVar('protocolArr', CommonValues::getAdvProtocol());
    }

    /**
     * 保存
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = AdvAppRepository::save($_POST);
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
                AdvAppRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
