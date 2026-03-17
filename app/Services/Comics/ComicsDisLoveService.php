<?php

namespace App\Services\Comics;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\Comics\ComicsDisLoveModel;
/**
 * 漫画点踩
 */
class ComicsDisLoveService extends BaseService
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
            ComicsDisLoveModel::deleteById($objectId);
            return false;
        }
        if (ComicsService::has($comicsId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '漫画不存在!');
        }
        ComicsDisLoveModel::insert([
            '_id'      => $objectId,
            'comics_id' => $comicsId,
            'user_id'  => intval($userId)
        ]);

        return true;
    }

    /**
     * 是否点踩
     * @param       $userId
     * @param       $comicsId
     * @return bool
     */
    public static function has($userId, $comicsId)
    {
        $objectId = md5($userId . '_' . $comicsId);
        $count    = ComicsDisLoveModel::count(['_id' => $objectId]);
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
        $count  = ComicsDisLoveModel::count($query);
        $rows   = ComicsDisLoveModel::find($query, ['comics_id'], ['created_at' => -1], ($page - 1) * $pageSize, $pageSize);
        $ids    = array_column($rows, 'comics_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }
}
