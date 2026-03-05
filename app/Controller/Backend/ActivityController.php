<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Activity\ActivityRepository;
use App\Services\Activity\ActivityTplService;

/**
 * 活动
 */
class ActivityController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/activity');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = ActivityRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('disArr', CommonValues::getIs());
        $this->view->setVar('tplArr', ActivityTplService::getAll());
        $this->view->setVar('rightArr', CommonValues::getActivityRight());
    }

    /**
     * 详情
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
        $id    = $this->getRequest('_id');
        $tplId = $this->getRequest('tpl_id');
        if (!empty($id)) {
            $result = ActivityRepository::getDetail($id);
            $tplId  = $result['tpl_id'];
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('disArr', CommonValues::getIs());
        $this->view->setVar('tpl', ActivityTplService::get($tplId));
        $this->view->setVar('rightArr', CommonValues::getActivityRight());
        $this->view->pick("activity/detail_{$tplId}");
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        $result = ActivityRepository::save($_POST);
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
                ActivityRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
