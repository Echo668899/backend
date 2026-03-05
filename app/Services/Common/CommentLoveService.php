<?php

namespace App\Services\Common;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\Common\CommentLoveModel;
use App\Models\Common\CommentModel;

/**
 * Class CommentLoveService
 * @package App\Services
 */
class CommentLoveService extends BaseService
{
    /**
     * @param                    $userId
     * @param                    $commentId
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $commentId)
    {
        $userId    = intval($userId);
        $commentId = strval($commentId);
        $comment   = CommentModel::findByID($commentId);
        if (empty($comment) || empty($comment['status'])) {
            throw new BusinessException(StatusCode::DATA_ERROR, '评论不存在!');
        }

        $id = md5($userId . '_' . $commentId);
        if (self::has($userId, $commentId)) {
            CommentLoveModel::deleteById($id);
            CommentModel::updateRaw(['$inc' => ['love' => -1]], ['_id' => $commentId]);

            return false;
        }
        CommentLoveModel::insert([
            '_id'        => $id,
            'user_id'    => $userId,
            'comment_id' => $commentId,
        ]);
        CommentModel::updateRaw(['$inc' => ['love' => 1]], ['_id' => $commentId]);
        return true;
    }

    /**
     * 是否点赞
     * @param       $userId
     * @param       $commentId
     * @return bool
     */
    public static function has($userId, $commentId)
    {
        $id    = md5($userId . '_' . $commentId);
        $count = CommentLoveModel::count(['_id' => $id]);
        return $count > 0 ? true : false;
    }
}
