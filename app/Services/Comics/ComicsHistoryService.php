<?php

namespace App\Services\Comics;

use App\Core\Services\BaseService;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Comics\ComicsViewPayload;
use App\Models\Comics\ComicsHistoryModel;
use App\Services\Common\ConfigService;
use App\Services\Common\JobService;

/**
 * 观看历史
 */
class ComicsHistoryService extends BaseService
{
    /**
     * @param                $userId
     * @param                $comicsId
     * @param  array         $chapter
     * @param                $index
     * @return bool|int|null
     */
    public static function do($userId, $comicsId, array $chapter, $index = 0)
    {
        self::setTable($userId);

        $userId   = intval($userId);
        $comicsId = strval($comicsId);
        $index    = intval($index);
        // 注意 漫画此处粒度为"部",不为"章"
        $itemId = self::fmt($userId, $comicsId);
        if (ComicsHistoryModel::count(['_id' => $itemId]) == 0) {
            ComicsService::handler('click', $comicsId);
        }
        ComicsHistoryModel::findAndModify([
            '_id' => $itemId,
        ], [
            '$set' => [
                'status'       => 1,
                'label'        => date('Y-m-d'),
                'chapter_id'   => $chapter['_id'],
                'chapter_name' => $chapter['name'],
                'index'        => $index,
                'updated_at'   => time(),
            ],
            '$setOnInsert' => [
                '_id'        => $itemId,
                'user_id'    => $userId,
                'comics_id'  => $comicsId,
                'created_at' => time(),
            ]
        ], [], true, true);

        JobService::create(new EventBusJob(new ComicsViewPayload($userId, $comicsId)));
        return true;
    }

    /**
     * @param         $userId
     * @param         $comicsId
     * @return string
     */
    public static function fmt($userId, $comicsId)
    {
        return md5($userId . '_' . $comicsId);
    }

    /**
     * 获取已经播放次数
     * @param            $userId
     * @param            $comicsId
     * @return float|int
     */
    public static function getPlayNum($userId, $comicsId = '')
    {
        self::setTable($userId);
        $countWhere = [
            'user_id' => intval($userId),
            'label'   => date('Y-m-d'),
        ];
        if ($comicsId) {
            $countWhere['_id'] = ['$ne' => self::fmt($userId, $comicsId)];
        }
        return ComicsHistoryModel::count($countWhere);
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
     * @param        $cursor
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        self::setTable($userId);
        $query = ['user_id' => $userId, 'status' => 1];
        $count = ComicsHistoryModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = ComicsHistoryModel::find($query, ['comics_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = ComicsHistoryModel::find($query, ['comics_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'comics_id');
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
     * @param        $comicsId
     * @return array
     */
    public static function getLastRead($userId, $comicsId)
    {
        self::setTable($userId);

        $userId   = intval($userId);
        $comicsId = strval($comicsId);
        $itemId   = self::fmt($userId, $comicsId);

        $row = ComicsHistoryModel::findByID($itemId);
        if ($row) {
            return [
                'comics_id'    => strval($comicsId),
                'chapter_id'   => strval($row['chapter_id']),
                'chapter_name' => strval($row['chapter_name']),
                'index'        => strval($row['index']),
            ];
        }
        return [];
    }

    /**
     * 删除
     * @param       $userId
     * @param       $comicsIds
     * @return true
     */
    public static function delete($userId, $comicsIds = null)
    {
        self::setTable($userId);

        $userId = intval($userId);
        if ($comicsIds == 'all') {
            ComicsHistoryModel::update(['status' => 0], ['user_id' => $userId]);
        } else {
            $ids = explode(',', $comicsIds);
            foreach ($ids as $key => $id) {
                if (empty($id)) {
                    unset($ids[$key]);
                    continue;
                }
                $ids[$key] = self::fmt($userId, $id);
            }
            $ids = array_values($ids);
            if (!empty($ids)) {
                ComicsHistoryModel::update(['status' => 0], ['_id' => ['$in' => $ids]]);
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
        ComicsHistoryModel::$collection = 'comics_history_' . ($userId % 100);
    }
}
