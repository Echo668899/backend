<?php

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Ai\AiTplRepository;
use App\Services\Ai\AiTagService;

class AiTplController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/aiTpl');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AiTplRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('typeArr', CommonValues::getAiTplType());
        $this->view->setVar('tagArr', AiTagService::getAll());
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
            $result = AiTplRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('typeArr', CommonValues::getAiTplType());
        $this->view->setVar('tagArr', AiTagService::getAll());
    }

    /**
     * 保存
     * @return void
     */
    public function saveAction()
    {
        $result = AiTplRepository::save($_POST);
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
                AiTplRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
