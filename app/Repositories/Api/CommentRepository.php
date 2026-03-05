<?php

namespace App\Repositories\Api;

use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Services\Common\CommentLoveService;
use App\Services\Common\CommentService;

class CommentRepository extends BaseRepository
{
    /**
     * @param                    $userId
     * @param                    $type
     * @param                    $id
     * @param                    $content
     * @param  int               $time
     * @param  string            $commentType
     * @return bool|int|mixed
     * @throws BusinessException
     */
    public static function doComment($userId, $type, $id, $content, $time = 0, $commentType = 'text')
    {
        return CommentService::do($userId, $type, $id, $content, $time, false, $commentType);
    }

    /**
     * @param                    $userId
     * @param                    $id
     * @param                    $type
     * @param                    $page
     * @return array
     * @throws BusinessException
     */
    public static function commentList($userId, $id, $type, $page)
    {
        return CommentService::getCommentList($userId, $id, $type, $page);
    }

    /**
     * 获取回复
     * @param        $id
     * @param        $page
     * @return array
     */
    public static function replyList($id, $page)
    {
        return CommentService::getReplyList($id, $page);
    }

    /**
     * @param                    $userId
     * @param                    $commentId
     * @return bool
     * @throws BusinessException
     */
    public static function doLove($userId, $commentId)
    {
        return CommentLoveService::do($userId, $commentId);
    }
}
