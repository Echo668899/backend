<?php

namespace App\Services\Comics;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Comics\ComicsLovePayload;
use App\Models\Comics\ComicsLoveModel;
use App\Services\Common\JobService;

/**
 * 点赞
 */
class ComicsLoveService extends BaseService
{
    /**
     * @param                    $userId
     * @param                    $comicsId
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $comicsId)
    {
        $objectId = md5($userId . '_' . $comicsId);
        if (self::has($userId, $comicsId)) {
            ComicsLoveModel::deleteById($objectId);
            ComicsService::handler('unLove', $comicsId);
            JobService::create(new EventBusJob(new ComicsLovePayload($userId, $comicsId, false)));
            return false;
        }
        if (ComicsService::has($comicsId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '视频不存在!');
        }
        ComicsLoveModel::insert([
            '_id'       => $objectId,
            'comics_id' => $comicsId,
            'user_id'   => intval($userId)
        ]);
        ComicsService::handler('love', $comicsId);
        JobService::create(new EventBusJob(new ComicsLovePayload($userId, $comicsId, true)));

        return true;
    }

    /**
     * 是否收藏
     * @param       $userId
     * @param       $comicsId
     * @return bool
     */
    public static function has($userId, $comicsId)
    {
        $objectId = md5($userId . '_' . $comicsId);
        $count    = ComicsLoveModel::count(['_id' => $objectId]);
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
    public static function getIds($userId, $page = 1, $pageSize = 10, $cursor = '')
    {
        $userId = intval($userId);
        $query  = ['user_id' => $userId];
        $count  = ComicsLoveModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = ComicsLoveModel::find($query, ['comics_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = ComicsLoveModel::find($query, ['comics_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'comics_id');
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
