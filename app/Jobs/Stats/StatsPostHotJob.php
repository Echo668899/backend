<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Post\PostModel;
use App\Models\Report\ReportPostLogModel;
use App\Utils\LogUtil;

/**
 * 帖子热度计算
 */
class StatsPostHotJob extends BaseJob
{
    public function handler($_id)
    {
        $where     = ['status' => 1];
        $count     = PostModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            LogUtil::info(__CLASS__ . " 开始帖子热度计算 {$page}/{$totalPage}");
            $skip  = ($page - 1) * $pageSize;
            $posts = PostModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            $this->doCounter($posts);
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    private function doCounter($posts)
    {
        foreach ($posts as $post) {
            /**
             * （视频、文章）热度计算公式：
             *
             * 收藏率=总收藏量/(总播放量+1)
             * 点赞率=总点赞量/（总点赞量+总点踩量+1）
             * 热度值 = （最近三天播放数（阅读数）/8+最近三天收藏量）*收藏率*点赞率
             * 数值结果四舍五入取整
             */
            $postRow      = PostModel::findByID($post['_id']);
            $favoriteRate = number_format($postRow['real_favorite'] / ($postRow['real_click'] + 1), 2);
            $loveRate     = number_format($postRow['real_love'] / ($postRow['real_love'] + $postRow['real_dislove'] + 1), 2);

            $postCount = ReportPostLogModel::aggregate([
                [
                    '$match' => [
                        'post_id' => strval($post['_id']), // 注意 post_id 是 string
                        'label'   => [
                            '$in' => [
                                date('Y-m-d'),
                                date('Y-m-d', strtotime('-1 day')),
                                date('Y-m-d', strtotime('-2 day')),
                            ]
                        ],
                    ]
                ],
                [
                    '$group' => [
                        '_id'      => null,
                        'click'    => ['$sum' => '$click'],     // 最近三天播放数
                        'favorite' => ['$sum' => '$favorite'],  // 最近三天收藏数
                    ]
                ]
            ]);
            $postCount['click']    = $postCount['click'] ?? 0;
            $postCount['favorite'] = $postCount['favorite'] ?? 0;
            $hotRate               = intval(round(($postCount['click'] / 8 + $postCount['favorite']) * $favoriteRate * $loveRate));
            PostModel::updateRaw(['$set' => ['hot_rate' => $hotRate]], ['_id' => $post['_id']]);
        }
    }
}
