<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Post\PostModel;
use App\Models\Post\PostTagModel;
use App\Utils\LogUtil;

/**
 * 帖子标签统计
 */
class StatsPostTagJob extends BaseJob
{
    public function handler($_id)
    {
        $rows = PostTagModel::find([], [], [], 0, 1000);
        foreach ($rows as $row) {
            $countInfo = PostModel::aggregate([
                ['$match' => ['tags' => $row['_id']]],
                ['$group' => [
                    '_id'      => null,
                    'click'    => ['$sum' => ['$add' => ['$click', '$real_click']]],
                    'love'     => ['$sum' => ['$add' => ['$love', '$real_love']]],
                    'favorite' => ['$sum' => ['$add' => ['$favorite', '$real_favorite']]],
                    'count'    => ['$sum' => 1],
                ]]
            ]);

            $countInfo = [
                'count'    => $countInfo['count'] ?? 0,
                'click'    => $countInfo['click'] ?? 0,
                'love'     => $countInfo['love'] ?? 0,
                'favorite' => $countInfo['favorite'] ?? 0,
            ];
            PostTagModel::update($countInfo, ['_id' => $row['_id']]);
            LogUtil::info(__CLASS__ . " tagId:{$row['_id']} count:{$countInfo['count']} 观看:{$countInfo['click']} 点赞:{$countInfo['love']} 收藏:{$countInfo['favorite']} 关注人数:{$countInfo['follow']}");
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
