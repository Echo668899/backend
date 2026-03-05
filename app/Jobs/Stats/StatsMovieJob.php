<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Movie\MovieModel;
use App\Models\Report\ReportMovieLogModel;
use App\Services\Common\JobService;
use App\Services\Report\ReportMovieLogService;
use App\Utils\LogUtil;

/**
 * 漫画排名数据
 */
class StatsMovieJob extends BaseJob
{
    public function handler($_id)
    {
        $ranges = [
            '1'   => date('Y-m-d', strtotime('-1 day')),    // 昨天
            '7'   => date('Y-m-d', strtotime('-7 day')),    // 7天前
            '30'  => date('Y-m-d', strtotime('-30 day')),  // 30天前
            'all' => date('Y-m-d', strtotime('-5 year')),  // 所有
        ];
        // /先重置所有统计,因为漫画可能下架
        ReportMovieLogService::resetStats();

        $where     = ['status' => 1];
        $count     = MovieModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            LogUtil::info(__CLASS__ . " 开始视频排行榜计算 {$page}/{$totalPage}");
            $skip   = ($page - 1) * $pageSize;
            $movies = MovieModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            foreach ($ranges as $range => $label) {
                $this->doCounter($movies, $range, $label);
            }
        }
    }

    public function success($_id)
    {
        JobService::create(new StatsMovieTagJob());
        JobService::create(new StatsMovieHotJob());
        JobService::create(new StatsUserUpJob());
    }

    public function error($_id, \Exception $e)
    {
    }

    private function doCounter($movies, $range, $label)
    {
        $range = $range == 'all' ? '' : $range;
        /**
         * 总榜单
         */
        $result = $this->aggregateBy($movies, $label, ['movie_id']);
        foreach ($result as &$item) {
            $item['_id'] = $item['_id']['movie_id'];
            unset($item);
        }
        $result = array_column($result, null, '_id');
        $rows   = [];

        foreach ($movies as $movie) {
            // 总榜单需要所有资源都存在
            $count = $result[$movie['_id']] ?? [];
            $value = [
                'movie_id' => $movie['_id'],
                'click'    => $count['click'] ?: 0,
                'love'     => $count['love'] ?: 0,
                'favorite' => $count['favorite'] ?: 0,
                'buy'      => $count['buy'] ?: 0,
                'position' => 'normal',
            ];
            $rows[] = $value;
        }
        ReportMovieLogService::setStats($rows, $range);

        /**
         * 分区榜单
         */
        $result = $this->aggregateBy($movies, $label, ['position', 'movie_id']);
        $rows   = [];
        foreach ($result as $item) {
            $rows[] = [
                'movie_id' => $item['_id']['movie_id'],
                'click'    => $item['click'] ?? 0,
                'love'     => $item['love'] ?? 0,
                'favorite' => $item['favorite'] ?? 0,
                'buy'      => $item['buy'] ?: 0,
                'position' => $item['_id']['position'] ?? 'normal',
            ];
        }
        ReportMovieLogService::setStats($rows, $range);

        /**
         * 连载/完结 榜单
         */
        $result = $this->aggregateBy($movies, $label, ['update_status', 'movie_id']);
        $rows   = [];
        foreach ($result as $item) {
            $status = $item['_id']['update_status'] ?? 0;
            $rows[] = [
                'movie_id' => $item['_id']['movie_id'],
                'click'    => $item['click'] ?? 0,
                'love'     => $item['love'] ?? 0,
                'favorite' => $item['favorite'] ?? 0,
                'buy'      => $item['buy'] ?: 0,
                'position' => ($status == 1) ? 'finish' : 'update',
            ];
        }
        ReportMovieLogService::setStats($rows, $range);
    }

    private function aggregateBy(array $movies, string $label, array $groupFields)
    {
        $pipeline = [
            [
                '$match' => [
                    'movie_id' => ['$in' => array_column($movies, '_id')],
                    'label'    => ['$gte' => $label],
                ],
            ],
            [
                '$group' => [
                    '_id' => array_combine(
                        $groupFields,
                        array_map(fn ($f) => '$' . $f, $groupFields)
                    ),
                    'click'    => ['$sum' => '$click'],
                    'love'     => ['$sum' => '$love'],
                    'favorite' => ['$sum' => '$favorite'],
                    'buy'      => ['$sum' => '$buy_num'],
                ],
            ],
        ];

        return ReportMovieLogModel::aggregates($pipeline);
    }
}
