<?php

namespace App\Services\User;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\User\UserDoFavoritePayload;
use App\Models\User\UserFavoriteModel;
use App\Services\Common\JobService;

/**
 * 用户收藏
 * 仅适用收藏某个板块
 */
class UserFavoriteService extends BaseService
{
    /**
     * @param         $userId
     * @param         $objectType
     * @param         $objectId
     * @return string
     */
    public static function fmt($userId, $objectType, $objectId)
    {
        return md5($userId . '_' . $objectType . '_' . $objectId);
    }

    /**
     * @param       $userId
     * @param       $objectType
     * @param       $objectId
     * @return bool
     */
    public static function has($userId, $objectType, $objectId)
    {
        $_id = self::fmt($userId, $objectType, $objectId);
        return UserFavoriteModel::count(['_id' => $_id]) > 0;
    }

    /**
     * 关注
     * @param       $userId
     * @param       $objectType
     * @param       $objectId
     * @return bool
     */
    public static function do($userId, $objectType, $objectId)
    {
        $userId     = intval($userId);
        $objectType = strval($objectType);
        $objectId   = strval($objectId);
        $_id        = self::fmt($userId, $objectType, $objectId);
        if (!in_array($objectType, ['movie_block', 'audio_block', 'comics_block', 'novel_block', 'movie_tag', 'audio_tag', 'comics_tag', 'novel_tag'])) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '收藏类型错误!');
        }

        if (self::has($userId, $objectType, $objectId)) {
            UserFavoriteModel::deleteById($_id);
            JobService::create(new EventBusJob(new UserDoFavoritePayload($userId, $objectType, $objectId, false)));
            return false;
        }
        UserFavoriteModel::insert([
            '_id'         => $_id,
            'user_id'     => intval($userId),
            'object_type' => $objectType,
            'object_id'   => $objectId,
        ]);
        JobService::create(new EventBusJob(new UserDoFavoritePayload($userId, $objectType, $objectId, true)));

        return true;
    }

    /**
     * 获取收藏列表
     * @param        $userId
     * @param        $objectType
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function getIds($userId, $objectType, $page = 1, $pageSize = 10)
    {
        $userId = intval($userId);
        $query  = ['user_id' => $userId, 'object_type' => $objectType];
        $count  = UserFavoriteModel::count($query);
        $rows   = UserFavoriteModel::find($query, ['object_id'], ['created_at' => -1], ($page - 1) * $pageSize, $pageSize);
        $ids    = array_column($rows, 'object_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }
}
