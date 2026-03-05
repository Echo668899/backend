<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Models\Post\PostModel;
use App\Repositories\Backend\Post\PostRepository;
use App\Services\Post\PostService;
use App\Services\Post\PostTagService;

/**
 * 帖子
 */
class PostController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/post');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = PostRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('defaultStatus', 1);
        $this->initData();
    }

    /**
     * 仓库
     */
    public function warehouseAction()
    {
        if ($this->isPost()) {
            $result = PostRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('defaultStatus', 0);
        $this->initData();
        $this->view->pick('post/list');
    }

    /**
     * 详情
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = PostRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->initData();
    }

    /**
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function saveAction()
    {
        $result = PostRepository::save($_REQUEST);
        if ($result) {
            $this->sendSuccessResult();
        }
        $this->sendErrorResult('保存错误!');
    }

    /**
     * 同步媒资库
     */
    public function asyncAction()
    {
        //        $userId = $this->getRequest("user_id");
        $idStr  = $this->getRequest('id');
        $source = $this->getRequest('source');
        if (empty($idStr)) {
            $this->sendErrorResult('请输入媒资库ID!');
        }
        //        if(empty($userId)){
        //            $this->sendErrorResult("请输入用户ID!");
        //        }
        if (empty($source)) {
            $this->sendErrorResult('请选择帖子来源!');
        }
        $result = PostRepository::asyncMrs($source, $idStr);
        $this->sendSuccessResult($result);
    }

    /**
     * widget
     */
    public function widgetAction()
    {
        $type = $this->getRequest('type', 'string');
        $ids  = $this->getRequest('ids', 'string');
        if (empty($type)) {
            $this->sendErrorResult('参数错误!');
        }
        $this->initData();
        //        $this->view->setVar('ids',$ids);
        $this->view->pick("post/widget/{$type}");
    }

    /**
     * 各种操作
     */
    public function doAction()
    {
        $ids = $this->getRequest('id');
        $act = $this->getRequest('act');

        if (empty($ids) || empty($act)) {
            $this->sendErrorResult('参数错误!');
        }
        if ($act == 'del') {
            $ids = explode(',', $ids);
            foreach ($ids as $id) {
                PostService::delete($id);
            }
            $this->sendSuccessResult();
        } elseif ($act == 'up') {
            $update = [
                'status' => 1,
            ];
        } elseif ($act == 'down') {
            $update = [
                'status' => -1,
            ];
        } elseif ($act == 'clearTag') {
            $update = [
                'tags' => [],
            ];
        }
        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            if (!empty($update)) {
                PostModel::updateById($update, $id);
            }
            PostService::asyncEs($id);
            PostService::delCache($id);
        }
        $this->sendSuccessResult();
    }

    /**
     * 批量设置(覆盖)
     */
    public function updateAction()
    {
        if ($this->isPost()) {
            $result = PostRepository::update($_POST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('ids', $this->getRequest('ids', 'string'));
        $this->initData();
    }

    /**
     * 批量设置(叠加)
     */
    public function updateOverlayAction()
    {
        if ($this->isPost()) {
            $result = PostRepository::updateOverlay($_POST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('ids', $this->getRequest('ids', 'string'));
        $this->initData();
    }

    public function updateRemoveAction()
    {
        if ($this->isPost()) {
            $result = PostRepository::updateRemove($_POST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('ids', $this->getRequest('ids', 'string'));
        $this->initData();
    }

    protected function initData()
    {
        $this->view->setVar('statusArr', CommonValues::getPostStatus());
        $this->view->setVar('tagArr', PostTagService::getGroupAttrAll());
        $this->view->setVar('posArr', CommonValues::getPostPosition());
        $this->view->setVar('payTypeArr', CommonValues::getPayTypes());
        $this->view->setVar('sourceArr', CommonValues::getMediaSource());
        $this->view->setVar('permissionArr', CommonValues::getPostPermission());
        $this->view->setVar('topArr', CommonValues::getIs());
    }
}
