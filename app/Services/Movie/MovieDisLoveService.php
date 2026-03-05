<?php

namespace App\Services\Movie;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\Movie\MovieDisLoveModel;

/**
 * 视频点踩
 */
class MovieDisLoveService extends BaseService
{
    /**
     * @param                    $userId
     * @param                    $movieId
     * @return bool
     * @throws BusinessException
     */
    public static function do($userId, $movieId)
    {
        $objectId = md5($userId . '_' . $movieId);
        if (self::has($userId, $movieId)) {
            MovieDisLoveModel::deleteById($objectId);
            MovieService::handler('unDisLove', $movieId);
            return false;
        }
        if (MovieService::has($movieId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '视频不存在!');
        }
        MovieDisLoveModel::insert([
            '_id'      => $objectId,
            'movie_id' => $movieId,
            'user_id'  => intval($userId)
        ]);
        MovieService::handler('disLove', $movieId);

        return true;
    }

    /**
     * 是否点踩
     * @param       $userId
     * @param       $movieId
     * @return bool
     */
    public static function has($userId, $movieId)
    {
        $objectId = md5($userId . '_' . $movieId);
        $count    = MovieDisLoveModel::count(['_id' => $objectId]);
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
        $count  = MovieDisLoveModel::count($query);
        $rows   = MovieDisLoveModel::find($query, ['movie_id'], ['created_at' => -1], ($page - 1) * $pageSize, $pageSize);
        $ids    = array_column($rows, 'movie_id');
        return [
            'ids'          => $ids,
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize))
        ];
    }
}
