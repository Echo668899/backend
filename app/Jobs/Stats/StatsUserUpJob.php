<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Movie\MovieModel;
use App\Models\Post\PostModel;
use App\Models\User\UserModel;
use App\Models\User\UserUpModel;
use App\Utils\LogUtil;

/**
 * up主统计
 */
class StatsUserUpJob extends BaseJob
{
    public $userId;
    public function __construct($userId = null)
    {
        $this->userId = $userId;
    }

    public function handler($_id)
    {
        if ($this->userId) {
            $this->doCounter($this->userId);
        } else {
            $query     = [];
            $count     = UserUpModel::count($query);
            $pageSize  = 1000;
            $totalPage = ceil($count / $pageSize);
            for ($page = 1; $page <= $totalPage; $page++) {
                LogUtil::info(__CLASS__ . " 开始UP主热度计算 {$page}/{$totalPage}");
                $skip  = ($page - 1) * $pageSize;
                $items = UserUpModel::find($query, [], ['_id' => 1], $skip, $pageSize);
                foreach ($items as $item) {
                    $this->doCounter($item['_id']);
                }
            }
        }
    }

    public function doCounter($userId)
    {
        $userId  = intval($userId);
        $userRow = UserModel::findByID($userId);
        if (empty($userRow)) {
            LogUtil::error(__CLASS__ . " 用户不存在 uid:{$userId}");
            return;
        }
        $postCount = PostModel::aggregate([
            ['$match' => ['user_id' => $userId, 'status' => 1]],
            ['$group' => [
                '_id'           => null,
                'count'         => ['$sum' => 1],
                'click'         => ['$sum' => '$click'],
                'real_click'    => ['$sum' => '$real_click'],
                'favorite'      => ['$sum' => '$favorite'],
                'real_favorite' => ['$sum' => '$real_favorite'],
            ]],
        ]);
        $movieCount = MovieModel::aggregate([
            ['$match' => ['user_id' => $userId, 'status' => 1]],
            ['$group' => [
                '_id'           => null,
                'click'         => ['$sum' => '$click'],
                'real_click'    => ['$sum' => '$real_click'],
                'favorite'      => ['$sum' => '$favorite'],
                'real_favorite' => ['$sum' => '$real_favorite'],
            ]],
        ]);
        $movieNum = MovieModel::count(['user_id' => $userId, 'status' => 1]);

        $movieRow = MovieModel::findFirst(['user_id' => $userId, 'status' => 1], [], ['hot_rate' => -1]);
        UserUpModel::updateRaw(['$set' => [
            'post_total'          => intval($postCount['count'] ?? 0),
            'post_click_total'    => intval(($postCount['click'] ?? 0) + ($postCount['real_click'] ?? 0)),
            'post_favorite_total' => intval(($postCount['favorite'] ?? 0) + ($postCount['real_favorite'] ?? 0)),

            'movie_total' => intval($movieNum),

            'movie_click_total'    => intval(($movieCount['click'] ?? 0) + ($movieCount['real_click'] ?? 0)),
            'movie_favorite_total' => intval(($movieCount['favorite'] ?? 0) + ($movieCount['real_favorite'] ?? 0)),
            'movie_hot'            => intval($movieRow['hot_rate'] ?? 0),
            'fans_total'           => intval($userRow['fans']),
        ]], ['_id' => $userId]);
    }

    public function success($_id)
    {
        // TODO: Implement success() method.
    }

    public function error($_id, \Exception $e)
    {
        // TODO: Implement error() method.
    }
}
