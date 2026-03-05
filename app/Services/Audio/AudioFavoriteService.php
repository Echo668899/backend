<?php

namespace App\Services\Audio;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Audio\AudioFavoritePayload;
use App\Models\Audio\AudioFavoriteModel;
use App\Services\Common\JobService;

/**
 * 收藏
 */
class AudioFavoriteService extends BaseService
{
    /**
     * @param                    $userId
     * @param                    $audioId
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $audioId)
    {
        $objectId = md5($userId . '_' . $audioId);
        if (self::has($userId, $audioId)) {
            AudioFavoriteModel::deleteById($objectId);
            AudioService::handler('unFavorite', $audioId);
            JobService::create(new EventBusJob(new AudioFavoritePayload($userId, $audioId, false)));
            return false;
        }
        if (AudioService::has($audioId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '有声不存在!');
        }
        AudioFavoriteModel::insert([
            '_id'      => $objectId,
            'audio_id' => $audioId,
            'user_id'  => intval($userId)
        ]);
        AudioService::handler('favorite', $audioId);
        JobService::create(new EventBusJob(new AudioFavoritePayload($userId, $audioId, true)));

        return true;
    }

    /**
     * 是否收藏
     * @param       $userId
     * @param       $audioId
     * @return bool
     */
    public static function has($userId, $audioId)
    {
        $objectId = md5($userId . '_' . $audioId);
        $count    = AudioFavoriteModel::count(['_id' => $objectId]);
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
        $count  = AudioFavoriteModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = AudioFavoriteModel::find($query, ['audio_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = AudioFavoriteModel::find($query, ['audio_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'audio_id');
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
