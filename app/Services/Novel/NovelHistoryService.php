<?php

namespace App\Services\Novel;

use App\Core\Services\BaseService;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Novel\NovelViewPayload;
use App\Models\Novel\NovelHistoryModel;
use App\Services\Common\JobService;

/**
 * 观看历史
 */
class NovelHistoryService extends BaseService
{
    /**
     * @param                $userId
     * @param                $novelId
     * @param  array         $chapter
     * @param                $index
     * @return bool|int|null
     */
    public static function do($userId, $novelId, array $chapter, $index = 0)
    {
        self::setTable($userId);

        $userId  = intval($userId);
        $novelId = strval($novelId);
        $index   = intval($index);

        // 注意 小说此处粒度为"部",不为"章"
        $itemId = self::fmt($userId, $novelId);

        if (NovelHistoryModel::count(['_id' => $itemId]) == 0) {
            NovelService::handler('click', $novelId);
        }
        NovelHistoryModel::findAndModify([
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
                'novel_id'   => $novelId,
                'created_at' => time(),
            ]
        ], [], true, true);
        JobService::create(new EventBusJob(new NovelViewPayload($userId, $novelId, $chapter['_id'])));
        return true;
    }

    /**
     * @param         $userId
     * @param         $novelId
     * @return string
     */
    public static function fmt($userId, $novelId)
    {
        return md5($userId . '_' . $novelId);
    }

    /**
     * 获取已经播放次数
     * @param        $userId
     * @param        $novelId
     * @return mixed
     */
    public static function getPlayNum($userId, $novelId = '')
    {
        self::setTable($userId);

        $countWhere = [
            'user_id' => intval($userId),
            'label'   => date('Y-m-d'),
        ];
        if ($novelId) {
            $countWhere['_id'] = ['$ne' => self::fmt($userId, $novelId)];
        }
        return NovelHistoryModel::count($countWhere);
    }

    /**
     * 获取观看记录id
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        self::setTable($userId);

        $userId = intval($userId);
        $query  = ['user_id' => $userId, 'status' => 1];
        $count  = NovelHistoryModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows                = NovelHistoryModel::find($query, ['novel_id', 'updated_at'], ['updated_at' => -1], 0, $pageSize);
        } else {
            $rows = NovelHistoryModel::find($query, ['novel_id', 'updated_at'], ['updated_at' => -1], ($page - 1) * $pageSize, $pageSize);
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

    /**
     * 获取漫画上次阅读
     * @param        $userId
     * @param        $novelId
     * @return array
     */
    public static function getLastRead($userId, $novelId)
    {
        self::setTable($userId);

        $userId  = intval($userId);
        $novelId = strval($novelId);
        $itemId  = md5($userId . '_' . $novelId);

        $row = NovelHistoryModel::findByID($itemId);
        if ($row) {
            return [
                'novel_id'     => strval($novelId),
                'chapter_id'   => strval($row['chapter_id']),
                'chapter_name' => strval($row['chapter_name']),
                'index'        => strval($row['index']),
            ];
        }
        return [];
    }

    /**
     * 删除
     * @param             $userId
     * @param  null|mixed $novelIds
     * @return bool|mixed
     */
    public static function delete($userId, $novelIds = null)
    {
        self::setTable($userId);

        $userId = intval($userId);
        if ($novelIds == 'all') {
            NovelHistoryModel::update(['status' => 0], ['user_id' => $userId]);
        } else {
            $ids = explode(',', $novelIds);
            foreach ($ids as $key => $id) {
                if (empty($id)) {
                    unset($ids[$key]);
                    continue;
                }
                $ids[$key] = self::fmt($userId, $id);
            }
            $ids = array_values($ids);
            if (!empty($ids)) {
                NovelHistoryModel::update(['status' => 0], ['_id' => ['$in' => $ids]]);
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
        NovelHistoryModel::$collection = 'novel_history_' . ($userId % 100);
    }
}
