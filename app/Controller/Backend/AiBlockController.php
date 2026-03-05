<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Ai\AiBlockRepository;
use App\Services\Ai\AiNavService;

/**
 * AI模块管理
 *
 * @package App\Controller\Backend
 */
class AiBlockController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/aiBlock');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AiBlockRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->initData();
    }

    /**
     * 详情
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = AiBlockRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->initData();
    }

    /**
     * 保存
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = AiBlockRepository::save($_POST);
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
                AiBlockRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }

    protected function initData()
    {
        $this->view->setVar('posArr', AiNavService::getAll());
        $this->view->setVar('styleArr', CommonValues::getAiBlockStyle());
        $this->view->setVar('disArr', CommonValues::getIs());
    }
}
