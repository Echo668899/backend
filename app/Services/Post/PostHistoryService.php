<?php

namespace App\Services\Post;

use App\Core\Services\BaseService;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Post\PostViewPayload;
use App\Models\Post\PostHistoryModel;
use App\Services\Common\JobService;

/**
 * 观看历史
 */
class PostHistoryService extends BaseService
{
    /**
     * @param       $userId
     * @param       $postId
     * @return true
     */
    public static function do($userId, $postId)
    {
        self::setTable($userId);

        $userId = intval($userId);
        $postId = strval($postId);
        $itemId = self::fmt($userId, $postId);

        if (PostHistoryModel::count(['_id' => $itemId]) == 0) {
            PostService::handler('click', $postId);
        }
        PostHistoryModel::findAndModify([
            '_id' => $itemId,
        ], [
            '$set' => [
                'status'     => 1,
                'label'      => date('Y-m-d'),
                'updated_at' => time(),
            ],
            '$setOnInsert' => [
                '_id'        => $itemId,
                'user_id'    => $userId,
                'post_id'    => $postId,
                'created_at' => time(),
            ]
        ], [], true, true);
        JobService::create(new EventBusJob(new PostViewPayload($userId, $postId)));
        return true;
    }

    /**
     * @param         $userId
     * @param         $postId
     * @return string
     */
    public static function fmt($userId, $postId)
    {
        return md5($userId . '_' . $postId);
    }

    /**
     * 获取已经播放次数
     * @param        $userId
     * @param        $postId
     * @return mixed
     */
    public static function getPlayNum($userId, $postId = '')
    {
        self::setTable($userId);

        $countWhere = [
            'user_id' => intval($userId),
            'label'   => date('Y-m-d'),
        ];
        if ($postId) {
            $countWhere['_id'] = ['$ne' => self::fmt($userId, $postId)];
        }
        return PostHistoryModel::count($countWhere);
    }

    /**
     * 获取观看记录id
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        self::setTable($userId);
        $query = ['user_id' => $userId, 'status' => 1];
        $count = PostHistoryModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = PostHistoryModel::find($query, ['post_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = PostHistoryModel::find($query, ['post_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'post_id');
        return [
            'ids'          => $ids ?: [],
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize)),
            'cursor'       => !empty($rows) ? strval($rows[count($rows) - 1]['updated_at']) : '',
        ];
    }

    /**
     * 删除
     * @param             $userId
     * @param  null|mixed $postIds
     * @return bool|mixed
     */
    public static function delete($userId, $postIds = null)
    {
        self::setTable($userId);

        $userId = intval($userId);
        if ($postIds == 'all') {
            PostHistoryModel::update(['status' => 0], ['user_id' => $userId]);
        } else {
            $ids = explode(',', $postIds);
            foreach ($ids as $key => $id) {
                if (empty($id)) {
                    unset($ids[$key]);
                }
                $ids[$key] = self::fmt($userId, $id);
            }
            $ids = array_values($ids);
            if (!empty($ids)) {
                PostHistoryModel::update(['status' => 0], ['_id' => ['$in' => $ids]]);
            }
        }
        return true;
    }

    /**
     * 设置表
     * @param       $userId
     * @return void
     */
    public static function setTable($userId)
    {
        PostHistoryModel::$collection = 'post_history_' . ($userId % 100);
    }
}
