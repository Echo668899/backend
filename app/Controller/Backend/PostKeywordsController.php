<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Post\PostKeywordsRepository;

/**
 * 关键字管理
 */
class PostKeywordsController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/postKeywords');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = PostKeywordsRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('hotArr', CommonValues::getIs());
        $this->view->setVar('posArr', CommonValues::getPostPosition());
    }

    /**
     * 详情
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = PostKeywordsRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('hotArr', CommonValues::getIs());
        $this->view->setVar('posArr', CommonValues::getPostPosition());
    }

    /**
     * 保存
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = PostKeywordsRepository::save($_POST);
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
                PostKeywordsRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
