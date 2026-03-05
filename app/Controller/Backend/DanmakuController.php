<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Common\DanmakuRepository;

/**
 * Class DanmakuController
 * @package App\Controller\Backend
 */
class DanmakuController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/danmaku');
    }

    /**
     * 各种弹幕
     */
    public function listAction()
    {
        $this->checkPermission('/comment' . ucwords($_REQUEST['object_type']));
        $objectId   = $this->getRequest('object_id', 'string');
        $objectType = $this->getRequest('object_type', 'string');
        if ($this->isPost()) {
            $result = DanmakuRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->pick('danmaku/list');
        $this->view->setVar('object_title', CommonValues::getCommentType($objectType));
        $this->view->setVar('statusArr', CommonValues::getCommentStatus());
        $this->view->setVar('object_id', $objectId);
        $this->view->setVar('object_type', $objectType);
    }

    /**
     * 操作
     */
    public function doAction()
    {
        $ids = $this->getRequest('id');
        $act = $this->getRequest('act');
        if (empty($ids) || empty($act)) {
            $this->sendErrorResult('参数错误!');
        }

        $ids = explode(',', $ids);
        $ids = array_unique($ids);
        switch ($act) {
            case 'pass':
                foreach ($ids as $id) {
                    DanmakuRepository::pass($id);
                }
                break;
            case 'del':
                foreach ($ids as $id) {
                    DanmakuRepository::delete($id);
                }
                break;
            case 'delAndDis':
                foreach ($ids as $id) {
                    DanmakuRepository::delete($id, true);
                }
                break;
        }

        $this->sendSuccessResult();
    }
}
