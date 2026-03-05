<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Repositories\Api\CommentRepository;

/**
 * Class CommentController
 * @package App\Controller\Api
 */
class CommentController extends BaseApiController
{
    /**
     * @throws \App\Exception\BusinessException
     */
    public function doCommentAction()
    {
        $userId      = $this->getUserId();
        $id          = $this->getRequest('id', 'string');
        $content     = $this->getRequest('content', 'string');
        $time        = $this->getRequest('time', 'string');
        $type        = $this->getRequest('type', 'string', 'movie');
        $commentType = $this->getRequest('comment_type', 'string', 'text');
        $result      = CommentRepository::doComment($userId, $type, $id, $content, $time, $commentType);
        $this->sendSuccessResult($result);
    }

    /**
     * 评论列表
     * @throws \App\Exception\BusinessException
     */
    public function listAction()
    {
        $userId = $this->getUserId(false);
        $id     = $this->getRequest('id', 'string');
        $page   = $this->getRequest('page', 'int', 1);
        $type   = $this->getRequest('type', 'string', 'movie');
        $result = CommentRepository::commentList($userId, $id, $type, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * 回复列表
     */
    public function replyListAction()
    {
        $id     = $this->getRequest('id', 'string');
        $page   = $this->getRequest('page', 'int', 1);
        $result = CommentRepository::replyList($id, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * 评论点赞
     * @throws \App\Exception\BusinessException
     */
    public function doLoveAction()
    {
        $userId = $this->getUserId();
        $id     = $this->getRequest('id', 'string');
        $result = CommentRepository::doLove($userId, $id);
        $this->sendSuccessResult($result ? 'y' : 'n');
    }
}
