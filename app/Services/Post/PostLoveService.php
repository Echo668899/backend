<?php

namespace App\Services\Post;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Post\PostLovePayload;
use App\Models\Post\PostLoveModel;
use App\Services\Common\JobService;

/**
 * 点赞
 */
class PostLoveService extends BaseService
{
    /**
     *
     * @param $userId
     * @param $postId
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $postId)
    {
        $objectId = md5($userId . '_' . $postId);
        if (self::has($userId, $postId)) {
            PostLoveModel::deleteById($objectId);
            PostService::handler('unLove', $postId, $userId);
            JobService::create(new EventBusJob(new PostLovePayload($userId, $postId,false)));
            return false;
        } else {
            if (PostService::has($postId) == false) {
                throw new BusinessException(StatusCode::DATA_ERROR, '帖子不存在!');
            }
            PostLoveModel::insert([
                '_id' => $objectId,
                'post_id' => $postId,
                'user_id' => intval($userId)
            ]);
            PostService::handler('love', $postId, $userId);
            JobService::create(new EventBusJob(new PostLovePayload($userId, $postId,true)));
        }
        return true;
    }

    /**
     * 是否收藏
     * @param $userId
     * @param $postId
     * @return bool
     */
    public static function has($userId, $postId)
    {
        $objectId = md5($userId . '_' . $postId);
        $count = PostLoveModel::count(['_id' => $objectId]);
        return $count > 0;
    }

    /**
     * 获取点赞列表
     * @param $userId
     * @param $page
     * @param $pageSize
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 12,$cursor='')
    {
        $userId = intval($userId);
        $query = ['user_id' => $userId];
        $count = PostLoveModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows = PostLoveModel::find($query, ['post_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        }else{
            $rows = PostLoveModel::find($query, ['post_id', 'updated_at'], ['updated_at' => -1], ($page-1)*$pageSize, $pageSize);
        }
        $ids = array_column($rows, 'post_id');
        return [
            'ids' => $ids,
            'total' => $count,
            'current_page' => $page,
            'page_size' => $pageSize,
            'last_page' => strval(ceil($count / $pageSize)),
            'cursor'    => !empty($rows)?strval($rows[count($rows)-1]['updated_at']):'',
        ];
    }
}
