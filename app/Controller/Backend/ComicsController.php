<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Models\Comics\ComicsModel;
use App\Repositories\Backend\Comics\ComicsRepository;
use App\Services\Comics\ComicsChapterService;
use App\Services\Comics\ComicsService;
use App\Services\Comics\ComicsTagService;

class ComicsController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/comics');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = ComicsRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('defaultStatus', 1);
        $this->initData();
    }

    /**
     * 公共
     */
    public function initData()
    {
        $this->view->setVar('payTypeArr', CommonValues::getPayTypes());
        $this->view->setVar('statusArr', CommonValues::getComicsStatus());
        $this->view->setVar('updateStatusArr', CommonValues::getComicsUpdateStatus());
        $this->view->setVar('catArr', CommonValues::getComicsCategories());
        $this->view->setVar('tagArr', ComicsTagService::getGroupAttrAll());
        $this->view->setVar('iconArr', CommonValues::getComicsIcon());
        $this->view->setVar('sourceArr', CommonValues::getMediaSource());
        $this->view->setVar('weekArr', CommonValues::getComicsWeek());
    }

    /**
     * 仓库
     */
    public function warehouseAction()
    {
        if ($this->isPost()) {
            $result = ComicsRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('defaultStatus', 0);
        $this->initData();
        $this->view->pick('comics/list');
    }

    /**
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = ComicsRepository::getDetail($id);
            $this->view->setVar('row', $result);
            $this->view->setVar('chapterList', ComicsChapterService::getChapterList($id));
        }
        $this->initData();
    }

    /**
     * 章节详情
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function chapterDetailAction()
    {
        $id = $this->getRequest('id');
        if (empty($id)) {
            $this->sendErrorResult('章节错误!');
        }
        $chapter = ComicsRepository::getChapterDetail($id);
        if (empty($chapter)) {
            $this->sendErrorResult('章节错误!');
        }
        $this->sendSuccessResult($chapter);
    }

    /**
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function saveAction()
    {
        $result = ComicsRepository::save($_POST);
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
            $this->sendErrorResult('请选择视频来源!');
        }
        $result = ComicsRepository::asyncMrs($source, $idStr);
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
        $this->view->pick("comics/widget/{$type}");
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
                ComicsService::delete($id);
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
                ComicsModel::updateById($update, $id);
            }
            ComicsService::asyncEs($id);
            ComicsService::delCache($id);
        }
        $this->sendSuccessResult();
    }

    /**
     * 批量设置(覆盖)
     */
    public function updateAction()
    {
        if ($this->isPost()) {
            $result = ComicsRepository::update($_POST);
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
            $result = ComicsRepository::updateOverlay($_POST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('ids', $this->getRequest('ids', 'string'));
        $this->initData();
    }

    public function updateRemoveAction()
    {
        if ($this->isPost()) {
            $result = ComicsRepository::updateRemove($_POST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('ids', $this->getRequest('ids', 'string'));
        $this->initData();
    }
}
