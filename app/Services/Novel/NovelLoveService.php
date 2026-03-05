<?php

namespace App\Services\Novel;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Novel\NovelLovePayload;
use App\Models\Novel\NovelLoveModel;
use App\Services\Common\JobService;

/**
 * 点赞
 */
class NovelLoveService extends BaseService
{
    /**
     * @param                    $userId
     * @param                    $novelId
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $novelId)
    {
        $objectId = md5($userId . '_' . $novelId);
        if (self::has($userId, $novelId)) {
            NovelLoveModel::deleteById($objectId);
            NovelService::handler('unLove', $novelId);
            JobService::create(new EventBusJob(new NovelLovePayload($userId, $novelId, false)));
            return false;
        }
        if (NovelService::has($novelId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '小说不存在!');
        }
        NovelLoveModel::insert([
            '_id'      => $objectId,
            'novel_id' => $novelId,
            'user_id'  => intval($userId)
        ]);
        NovelService::handler('love', $novelId);
        JobService::create(new EventBusJob(new NovelLovePayload($userId, $novelId, true)));

        return true;
    }

    /**
     * 是否收藏
     * @param       $userId
     * @param       $novelId
     * @return bool
     */
    public static function has($userId, $novelId)
    {
        $objectId = md5($userId . '_' . $novelId);
        $count    = NovelLoveModel::count(['_id' => $objectId]);
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
        $count  = NovelLoveModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = NovelLoveModel::find($query, ['novel_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = NovelLoveModel::find($query, ['novel_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'novel_id');
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
