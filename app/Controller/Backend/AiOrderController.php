<?php

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Models\Ai\AiOrderModel;
use App\Repositories\Backend\Ai\AiOrderRepository;
use App\Services\Ai\AiService;

class AiOrderController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/aiOrder');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AiOrderRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('statusArr', CommonValues::getAiOrderStatus());
        $this->view->setVar('typeArr', CommonValues::getAiTplType());
    }

    /**
     * 详情
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id', 'int');
        if (!empty($id)) {
            $result = AiOrderRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('statusArr', CommonValues::getAiOrderStatus());
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

        $ids = explode(',', $idStr);
        foreach ($ids as $id) {
            if ($act == 'retry') {
                $updateData = [
                    'status'    => 0,
                    'error_msg' => ''
                ];
            } elseif ($act == 'refund') {
                $row = AiOrderModel::findByID(intval($id));
                AiService::doRefund($row);
                $updateData = [
                    'status'     => -1,
                    'updated_at' => time()
                ];
            } elseif ($act == 'del') {
                $updateData = [
                    'is_disabled' => 1
                ];
            }

            if (empty($updateData)) {
                continue;
            }
            AiOrderModel::updateRaw(['$set' => $updateData], ['_id' => strval($id)]);
        }

        $this->sendSuccessResult();
    }
}
