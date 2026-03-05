<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Ai\AiTagRepository;

/**
 * @package App\Controller\Backend
 */
class AiTagController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/aiTag');
    }

    /**
     * 公共
     */
    public function initData()
    {
        $this->view->setVar('hotArr', CommonValues::getIs());
        $this->view->setVar('typeArr', CommonValues::getAiTplType());
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AiTagRepository::getList($_REQUEST);
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
            $result = AiTagRepository::getDetail($id);
            $this->view->setVar('row', json_encode($result));
        }
        $this->initData();
    }

    /**
     * 保存
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = AiTagRepository::save($_POST);
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
                AiTagRepository::delete($id);
            }
        } elseif ($act == 'update') {
            $ids       = explode(',', $idStr);
            $parentId  = $this->getRequest('parent_id', 'string');
            $attribute = $this->getRequest('attribute', 'string');
            $isHot     = $this->getRequest('is_hot', 'string');
            foreach ($ids as $id) {
                $data = [
                    '_id' => intval($id)
                ];
                if ($parentId != '') {
                    $data['parent_id'] = intval($parentId);
                }
                if ($attribute != '') {
                    $data['attribute'] = strval($attribute);
                }
                if ($isHot != '') {
                    $data['is_hot'] = intval($isHot);
                }
                AiTagRepository::update($data);
            }
        }
        $this->sendSuccessResult();
    }

    /**
     * 批量设置
     */
    public function updateAction()
    {
        $this->view->setVar('ids', $this->getRequest('ids', 'string'));
        $this->initData();
    }
}
