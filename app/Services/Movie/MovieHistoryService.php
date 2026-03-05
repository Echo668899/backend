<?php

namespace App\Services\Movie;

use App\Core\Services\BaseService;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Movie\MovieViewCompletePayload;
use App\Jobs\Event\Payload\Movie\MovieViewPayload;
use App\Models\Movie\MovieHistoryModel;
use App\Services\Common\ConfigService;
use App\Services\Common\JobService;
use App\Services\Report\ReportChannelLogService;
use App\Services\User\UserService;

/**
 * 观看历史
 */
class MovieHistoryService extends BaseService
{
    /**
     * @param       $userId
     * @param       $movieId
     * @param       $linkId
     * @param       $playTime
     * @param       $viewTime
     * @param       $code
     * @param       $event
     * @return bool
     */
    public static function do($userId, $movieId, $linkId, $playTime = 0, $viewTime = 0, $code = '', $event = '')
    {
        self::setTable($userId);

        if (empty($linkId)) {
            return false;
        }
        $userId   = intval($userId);
        $movieId  = strval($movieId);
        $playTime = intval($playTime);
        // 注意 视频此处粒度为"部",不为"集"
        $itemId = self::fmt($userId, $movieId);

        $hasRow = MovieHistoryModel::findByID($itemId);
        // 整个周期只记录一次
        if (empty($hasRow)) {
            MovieService::handler('click', $movieId);
        }

        MovieHistoryModel::findAndModify([
            '_id' => $itemId,
        ], [
            '$set' => [
                'status'     => 1,
                'label'      => date('Y-m-d'),
                'link_id'    => $linkId,
                'time'       => $playTime,
                'code'       => strval($code),
                'updated_at' => time()
            ],
            '$setOnInsert' => [
                '_id'        => $itemId,
                'user_id'    => $userId,
                'movie_id'   => $movieId,
                'created_at' => time(),
            ]
        ], [], true, true);

        $isDayFirst = false;
        // 每人每天每个视频只记录一次
        if (empty($hasRow) || date('Y-m-d', $hasRow['updated_at']) != date('Y-m-d')) {
            // 观影次数统计
            $userInfo = UserService::getInfoFromCache($userId);
            if ($userInfo && $userInfo['id'] > 0) {
                ReportChannelLogService::doView($userInfo['channel_name']);
                ReportChannelLogService::doUserView($userInfo['parent_id']);
                $isDayFirst = true;
            }
        }
        JobService::create(new EventBusJob(new MovieViewPayload($userId, $movieId, $linkId, $playTime, $viewTime, $isDayFirst)));
        // 观看完成
        if (in_array($event, ['complete'])) {
            JobService::create(new EventBusJob(new MovieViewCompletePayload($userId, $movieId, $linkId, $playTime, $viewTime)));
        }
        return true;
    }

    /**
     * @param         $userId
     * @param         $movieId
     * @return string
     */
    public static function fmt($userId, $movieId)
    {
        return md5($userId . '_' . $movieId);
    }

    /**
     * 获取已经播放次数
     * @param        $userId
     * @param        $movieId
     * @param  mixed $linkId
     * @return mixed
     */
    public static function getPlayNum($userId, $movieId = '', $linkId = '')
    {
        self::setTable($userId);
        $countWhere = [
            'user_id' => intval($userId),
            'label'   => date('Y-m-d'),
        ];
        if ($movieId && $linkId) {
            $countWhere['_id'] = ['$ne' => self::fmt($userId, $movieId)];
        }
        return MovieHistoryModel::count($countWhere);
    }

    /**
     * 获取允许观看次数
     * @return int
     */
    public static function getCanPlayNum()
    {
        $canPlayNum = ConfigService::getConfig('can_play_num');
        return intval($canPlayNum);
    }

    /**
     * 获取观看记录id
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param        $cursor   //游标
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        self::setTable($userId);
        $query = ['user_id' => $userId, 'status' => 1];
        $count = MovieHistoryModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = MovieHistoryModel::find($query, ['movie_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = MovieHistoryModel::find($query, ['movie_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'movie_id');
        return [
            'ids'          => $ids ?: [],
            'total'        => $count,
            'current_page' => $page,
            'page_size'    => $pageSize,
            'last_page'    => strval(ceil($count / $pageSize)),
            'cursor'       => !empty($rows) ? strval($rows[count($rows) - 1]['updated_at']) : '',
        ];
    }

    /**
     * 获取视频上次观看
     * @param        $userId
     * @param        $movieId
     * @return array
     */
    public static function getLastRead($userId, $movieId)
    {
        self::setTable($userId);

        $userId  = intval($userId);
        $movieId = strval($movieId);
        $_id     = self::fmt($userId, $movieId);
        $row     = MovieHistoryModel::findByID($_id);
        if ($row) {
            return [
                'movie_id' => strval($movieId),
                'link_id'  => strval($row['link_id']),
                'time'     => strval($row['time'] ?: '0'),
                'code'     => strval($row['code'] ?: ''), // 上次观看线路
            ];
        }
        return [];
    }

    /**
     * 删除
     * @param       $userId
     * @param       $movieIds
     * @return true
     */
    public static function delete($userId, $movieIds = null)
    {
        self::setTable($userId);

        $userId = intval($userId);
        if ($movieIds == 'all') {
            MovieHistoryModel::update(['status' => 0], ['user_id' => $userId]);
        } else {
            $ids = explode(',', $movieIds);
            foreach ($ids as $key => $id) {
                if (empty($id)) {
                    unset($ids[$key]);
                    continue;
                }
                $ids[$key] = self::fmt($userId, $id);
            }
            $ids = array_values($ids);
            if (!empty($ids)) {
                MovieHistoryModel::update(['status' => 0], ['_id' => ['$in' => $ids]]);
            }
        }
        return true;
    }

    /**
     * 设置表
     * @param       $userId
     * @return void
     */
    public static function setTable($userId)
    {
        MovieHistoryModel::$collection = 'movie_history_' . ($userId % 100);
    }
}
