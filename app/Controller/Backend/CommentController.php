<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Common\CommentRepository;

/**
 * Class CommentController
 * @package App\Controller\Backend
 */
class CommentController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 各种评论
     */
    public function listAction()
    {
        $this->checkPermission('/comment' . ucwords($_REQUEST['object_type']));
        $objectId   = $this->getRequest('object_id', 'string');
        $objectType = $this->getRequest('object_type', 'string');
        if ($this->isPost()) {
            $result = CommentRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->pick('comment/list');
        $this->view->setVar('object_title', CommonValues::getCommentType($objectType));
        $this->view->setVar('statusArr', CommonValues::getCommentStatus());
        $this->view->setVar('object_id', $objectId);
        $this->view->setVar('object_type', $objectType);
    }

    /**
     * 评论发布页面
     */
    public function commentAction()
    {
        $objectId   = $this->getRequest('object_id', 'string');
        $objectType = $this->getRequest('object_type', 'string');
        $this->view->setVar('object_id', $objectId);
        $this->view->setVar('object_type', $objectType);
        $this->view->pick('comment/comment');
    }

    /**
     * 发布评论
     * @throws \App\Exception\BusinessException
     */
    public function doCommentAction()
    {
        $objectId   = $this->getRequest('object_id', 'string');
        $objectType = $this->getRequest('object_type', 'string');
        $userId     = $this->getRequest('user_id', 'int');
        $content    = $this->getRequest('content', 'string');
        $result     = CommentRepository::doComment($userId, $objectType, $objectId, $content);
        $this->sendSuccessResult($result);
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
                    CommentRepository::pass($id);
                }
                break;
            case 'del':
                foreach ($ids as $id) {
                    CommentRepository::delete($id);
                }
                break;
            case 'delAndDis':
                foreach ($ids as $id) {
                    CommentRepository::delete($id, true);
                }
                break;
        }

        $this->sendSuccessResult();
    }
}
