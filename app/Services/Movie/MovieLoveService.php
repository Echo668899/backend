<?php

namespace App\Services\Movie;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Movie\MovieLovePayload;
use App\Models\Movie\MovieLoveModel;
use App\Services\Common\JobService;

/**
 * 视频点赞
 */
class MovieLoveService extends BaseService
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
            MovieLoveModel::deleteById($objectId);
            MovieService::handler('unLove', $movieId);
            JobService::create(new EventBusJob(new MovieLovePayload($userId, $movieId, false)));
            return false;
        }
        if (MovieService::has($movieId) == false) {
            throw new BusinessException(StatusCode::DATA_ERROR, '视频不存在!');
        }
        MovieLoveModel::insert([
            '_id'      => $objectId,
            'movie_id' => $movieId,
            'user_id'  => intval($userId)
        ]);
        MovieService::handler('love', $movieId);
        JobService::create(new EventBusJob(new MovieLovePayload($userId, $movieId, true)));

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
        $count    = MovieLoveModel::count(['_id' => $objectId]);
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
        $count  = MovieLoveModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = MovieLoveModel::find($query, ['movie_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = MovieLoveModel::find($query, ['movie_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
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
