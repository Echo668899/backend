<?php

namespace App\Services\Novel;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\Novel\NovelDisLoveModel;
/**
 * 小说点踩
 */
class NovelDisLoveService extends BaseService
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
            NovelDisLoveModel::deleteById($objectId);
            return false;
        }
        if (NovelService::has($novelId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '小说不存在!');
        }
        NovelDisLoveModel::insert([
            '_id'      => $objectId,
            'novel_id' => $novelId,
            'user_id'  => intval($userId)
        ]);

        return true;
    }

    /**
     * 是否点踩
     * @param       $userId
     * @param       $novelId
     * @return bool
     */
    public static function has($userId, $novelId)
    {
        $objectId = md5($userId . '_' . $novelId);
        $count    = NovelDisLoveModel::count(['_id' => $objectId]);
        return $count > 0;
    }

    /**
     * 获取踩列表
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 10)
    {
        $userId = intval($userId);
        $query  = ['user_id' => $userId];
        $count  = NovelDisLoveModel::count($query);
        $rows   = NovelDisLoveModel::find($query, ['novel_id'], ['created_at' => -1], ($page - 1) * $pageSize, $pageSize);
        $ids    = array_column($rows, 'novel_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }
}
