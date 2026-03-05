<?php

namespace App\Services\Audio;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Audio\AudioLovePayload;
use App\Models\Audio\AudioLoveModel;
use App\Services\Common\JobService;

/**
 * 点赞
 */
class AudioLoveService extends BaseService
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
            AudioLoveModel::deleteById($objectId);
            AudioService::handler('unLove', $audioId);
            JobService::create(new EventBusJob(new AudioLovePayload($userId, $audioId, false)));
            return false;
        }
        if (AudioService::has($audioId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '有声不存在!');
        }
        AudioLoveModel::insert([
            '_id'      => $objectId,
            'audio_id' => $audioId,
            'user_id'  => intval($userId)
        ]);
        AudioService::handler('love', $audioId);
        JobService::create(new EventBusJob(new AudioLovePayload($userId, $audioId, true)));

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
        $count    = AudioLoveModel::count(['_id' => $objectId]);
        return $count > 0;
    }

    /**
     * 获取点赞列表
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
        $count  = AudioLoveModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = AudioLoveModel::find($query, ['audio_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = AudioLoveModel::find($query, ['audio_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
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
