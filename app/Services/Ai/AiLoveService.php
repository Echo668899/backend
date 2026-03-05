<?php

namespace App\Services\Ai;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\Ai\AiLoveModel;

/**
 * 点赞
 */
class AiLoveService extends BaseService
{
    /**
     * @param                    $userId
     * @param                    $orderId
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $orderId)
    {
        $objectId = md5($userId . '_' . $orderId);
        if (self::has($userId, $orderId)) {
            AiLoveModel::deleteById($objectId);
            AiService::handler('unLove', $orderId);
            return false;
        }
        if (AiService::has($orderId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '作品不存在!');
        }
        AiLoveModel::insert([
            '_id'      => $objectId,
            'order_id' => $orderId,
            'user_id'  => intval($userId)
        ]);
        AiService::handler('love', $orderId);

        return true;
    }

    /**
     * 是否收藏
     * @param       $userId
     * @param       $orderId
     * @return bool
     */
    public static function has($userId, $orderId)
    {
        $objectId = md5($userId . '_' . $orderId);
        $count    = AiLoveModel::count(['_id' => $objectId]);
        return $count > 0;
    }

    /**
     * 获取点赞列表
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 10)
    {
        $userId = intval($userId);
        $query  = ['user_id' => $userId];
        $count  = AiLoveModel::count($query);
        $rows   = AiLoveModel::find($query, ['order_id'], ['created_at' => -1], ($page - 1) * $pageSize, $pageSize);
        $ids    = array_column($rows, 'order_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }
}
