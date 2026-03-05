<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Movie\MovieModel;
use App\Models\Report\ReportMovieLogModel;
use App\Utils\LogUtil;

/**
 * 视频热度计算
 */
class StatsMovieHotJob extends BaseJob
{
    public function handler($_id)
    {
        $where     = ['status' => 1];
        $count     = MovieModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            LogUtil::info(__CLASS__ . " 开始视频热度计算 {$page}/{$totalPage}");
            $skip   = ($page - 1) * $pageSize;
            $movies = MovieModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            $this->doCounter($movies);
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    private function doCounter($movies)
    {
        foreach ($movies as $movie) {
            /**
             * （视频、文章）热度计算公式：
             *
             * 收藏率=总收藏量/(总播放量+1)
             * 点赞率=总点赞量/（总点赞量+总点踩量+1）
             * 热度值 = （最近三天播放数（阅读数）/8+最近三天收藏量）*收藏率*点赞率
             * 数值结果四舍五入取整
             */
            $movieRow     = MovieModel::findByID($movie['_id']);
            $favoriteRate = number_format($movieRow['real_favorite'] / ($movieRow['real_click'] + 1), 2);
            $loveRate     = number_format($movieRow['real_love'] / ($movieRow['real_love'] + $movieRow['real_dislove'] + 1), 2);

            $movieCount = ReportMovieLogModel::aggregate([
                [
                    '$match' => [
                        'movie_id' => strval($movie['_id']), // 注意 movie_id 是 string
                        'label'    => [
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
            $movieCount['click']    = $movieCount['click'] ?? 0;
            $movieCount['favorite'] = $movieCount['favorite'] ?? 0;
            $hotRate                = intval(round(($movieCount['click'] / 8 + $movieCount['favorite']) * $favoriteRate * $loveRate));
            MovieModel::updateRaw(['$set' => ['hot_rate' => $hotRate]], ['_id' => $movie['_id']]);
        }
    }
}
