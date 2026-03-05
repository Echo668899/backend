<?php

namespace App\Services\Audio;

use App\Core\Services\BaseService;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Audio\AudioViewPayload;
use App\Models\Audio\AudioHistoryModel;
use App\Services\Common\JobService;

/**
 * 观看历史
 */
class AudioHistoryService extends BaseService
{
    /**
     * @param       $userId
     * @param       $audioId
     * @param       $chapterId
     * @param       $time
     * @param       $code
     * @return bool
     */
    public static function do($userId, $audioId, $chapterId, $time = 0, $code = '')
    {
        self::setTable($userId);

        if (empty($chapterId)) {
            return false;
        }
        $userId  = intval($userId);
        $audioId = strval($audioId);
        $time    = intval($time);
        // 注意 漫画此处粒度为"部",不为"章"
        $itemId = self::fmt($userId, $audioId);

        if (AudioHistoryModel::count(['_id' => $itemId]) == 0) {
            AudioService::handler('click', $audioId);
        }
        AudioHistoryModel::findAndModify([
            '_id' => $itemId,
        ], [
            '$set' => [
                'status'     => 1,
                'label'      => date('Y-m-d'),
                'chapter_id' => $chapterId,
                'time'       => $time,
                'code'       => strval($code),
                'updated_at' => time()
            ],
            '$setOnInsert' => [
                '_id'        => $itemId,
                'user_id'    => $userId,
                'audio_id'   => $audioId,
                'created_at' => time(),
            ]
        ], [], true, true);
        JobService::create(new EventBusJob(new AudioViewPayload($userId, $audioId, $chapterId)));
        return true;
    }

    /**
     * @param         $userId
     * @param         $audioId
     * @return string
     */
    public static function fmt($userId, $audioId)
    {
        return md5($userId . '_' . $audioId);
    }

    /**
     * 获取已经播放次数
     * @param            $userId
     * @param            $audioId
     * @return float|int
     */
    public static function getPlayNum($userId, $audioId = '')
    {
        self::setTable($userId);
        $countWhere = [
            'user_id' => intval($userId),
            'label'   => date('Y-m-d'),
        ];
        if ($audioId) {
            $countWhere['_id'] = ['$ne' => self::fmt($userId, $audioId)];
        }
        return AudioHistoryModel::count($countWhere);
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
        $count = AudioHistoryModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = AudioHistoryModel::find($query, ['audio_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = AudioHistoryModel::find($query, ['audio_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'audio_id');
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
     * 获取漫画上次阅读
     * @param        $userId
     * @param        $audioId
     * @return array
     */
    public static function getLastRead($userId, $audioId)
    {
        self::setTable($userId);

        $userId  = intval($userId);
        $audioId = strval($audioId);

        $itemId = self::fmt($userId, $audioId);
        $row    = AudioHistoryModel::findByID($itemId);

        if ($row) {
            return [
                'audio_id'   => strval($audioId),
                'chapter_id' => strval($row['chapter_id']),
                'time'       => strval($row['time'] ?: '0'),
                'code'       => strval($row['code'] ?: ''), // 上次观看线路
            ];
        }
        return [];
    }

    /**
     * 删除
     * @param       $userId
     * @param       $audioIds
     * @return true
     */
    public static function delete($userId, $audioIds = null)
    {
        self::setTable($userId);

        $userId = intval($userId);
        if ($audioIds == 'all') {
            AudioHistoryModel::update(['status' => 0], ['user_id' => $userId]);
        } else {
            $ids = explode(',', $audioIds);
            foreach ($ids as $key => $id) {
                if (empty($id)) {
                    unset($ids[$key]);
                    continue;
                }
                $ids[$key] = self::fmt($userId, $id);
            }
            $ids = array_values($ids);
            if (!empty($ids)) {
                AudioHistoryModel::update(['status' => 0], ['_id' => ['$in' => $ids]]);
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
        AudioHistoryModel::$collection = 'audio_history_' . ($userId % 100);
    }
}
