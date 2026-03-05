<?php

namespace App\Services\Movie;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Movie\MovieFavoritePayload;
use App\Models\Movie\MovieFavoriteModel;
use App\Services\Common\JobService;

/**
 * 视频收藏
 */
class MovieFavoriteService extends BaseService
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
            MovieFavoriteModel::deleteById($objectId);
            MovieService::handler('unFavorite', $movieId);
            JobService::create(new EventBusJob(new MovieFavoritePayload($userId, $movieId, false)));
            return false;
        }
        if (MovieService::has($movieId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '视频不存在!');
        }
        MovieFavoriteModel::insert([
            '_id'      => $objectId,
            'movie_id' => $movieId,
            'user_id'  => intval($userId)
        ]);
        MovieService::handler('favorite', $movieId);
        JobService::create(new EventBusJob(new MovieFavoritePayload($userId, $movieId, true)));

        return true;
    }

    /**
     * 是否收藏
     * @param       $userId
     * @param       $movieId
     * @return bool
     */
    public static function has($userId, $movieId)
    {
        $objectId = md5($userId . '_' . $movieId);
        $count    = MovieFavoriteModel::count(['_id' => $objectId]);
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
    public static function getIds($userId, $page = 1, $pageSize = 10, $cursor = '')
    {
        $userId = intval($userId);
        $query  = ['user_id' => $userId];
        $count  = MovieFavoriteModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = MovieFavoriteModel::find($query, ['movie_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = MovieFavoriteModel::find($query, ['movie_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'movie_id');
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
