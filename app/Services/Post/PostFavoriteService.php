<?php

namespace App\Services\Post;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Post\PostFavoritePayload;
use App\Models\Post\PostFavoriteModel;
use App\Services\Common\JobService;

/**
 * 收藏
 */
class PostFavoriteService extends BaseService
{
    /**
     * @param                    $userId
     * @param                    $postId
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $postId)
    {
        $objectId = md5($userId . '_' . $postId);
        if (self::has($userId, $postId)) {
            PostFavoriteModel::deleteById($objectId);
            PostService::handler('unFavorite', $postId, $userId);
            JobService::create(new EventBusJob(new PostFavoritePayload($userId, $postId, false)));
            return false;
        }
        if (PostService::has($postId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '帖子不存在!');
        }
        PostFavoriteModel::insert([
            '_id'     => $objectId,
            'post_id' => $postId,
            'user_id' => intval($userId)
        ]);
        PostService::handler('favorite', $postId, $userId);
        JobService::create(new EventBusJob(new PostFavoritePayload($userId, $postId, true)));

        return true;
    }

    /**
     * 是否收藏
     * @param       $userId
     * @param       $postId
     * @return bool
     */
    public static function has($userId, $postId)
    {
        $objectId = md5($userId . '_' . $postId);
        $count    = PostFavoriteModel::count(['_id' => $objectId]);
        return $count > 0;
    }

    /**
     * 获取收藏列表
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        $userId = intval($userId);
        $query  = ['user_id' => $userId];
        $count  = PostFavoriteModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = PostFavoriteModel::find($query, ['post_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = PostFavoriteModel::find($query, ['post_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'post_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize)),
            'cursor'       => !empty($rows) ? strval($rows[count($rows) - 1]['updated_at']) : '',
        ];
    }
}
