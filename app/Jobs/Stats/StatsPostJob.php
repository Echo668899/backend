<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Post\PostModel;
use App\Models\Report\ReportPostLogModel;
use App\Services\Common\JobService;
use App\Services\Report\ReportPostLogService;
use App\Utils\LogUtil;

/**
 * 帖子排名数据
 */
class StatsPostJob extends BaseJob
{
    public function handler($_id)
    {
        $ranges = [
            '1'   => date('Y-m-d', strtotime('-1 day')),    // 昨天
            '7'   => date('Y-m-d', strtotime('-7 day')),    // 7天前
            '30'  => date('Y-m-d', strtotime('-30 day')),  // 30天前
            'all' => date('Y-m-d', strtotime('-5 year')),  // 所有
        ];
        // /先重置所有统计,因为帖子可能下架
        ReportPostLogService::resetStats();

        $where     = ['status' => 1];
        $count     = PostModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $pageSize;
            $posts = PostModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            foreach ($ranges as $range => $label) {
                $this->doCounter($posts, $range, $label);
            }
            LogUtil::info("Starting count post {$page}/{$totalPage}");
        }
    }

    public function success($_id)
    {
        JobService::create(new StatsPostTagJob());
        JobService::create(new StatsPostHotJob());
    }

    public function error($_id, \Exception $e)
    {
    }

    private function doCounter($posts, $range, $label)
    {
        $range = $range == 'all' ? '' : $range;
        /**
         * 总榜单
         */
        $result = $this->aggregateBy($posts, $label, ['post_id']);
        foreach ($result as &$item) {
            $item['_id'] = $item['_id']['post_id'];
            unset($item);
        }
        $result = array_column($result, null, '_id');
        $rows   = [];
        foreach ($posts as $post) {
            // 总榜单需要所有资源都存在
            $count = $result[$post['_id']] ?? [];
            $value = [
                'post_id'  => $post['_id'],
                'click'    => $count['click'] ?: 0,
                'love'     => $count['love'] ?: 0,
                'favorite' => $count['favorite'] ?: 0,
                'position' => 'normal',
            ];
            $rows[] = $value;
        }
        ReportPostLogService::setStats($rows, $range);

        /**
         * 分区榜单
         */
        $result = $this->aggregateBy($posts, $label, ['position', 'post_id']);
        $rows   = [];
        foreach ($result as $item) {
            $rows[] = [
                'post_id'  => $item['_id']['post_id'],
                'click'    => $item['click'] ?? 0,
                'love'     => $item['love'] ?? 0,
                'favorite' => $item['favorite'] ?? 0,
                'position' => $item['_id']['position'] ?? 'normal',
            ];
        }
        ReportPostLogService::setStats($rows, $range);
    }

    private function aggregateBy(array $posts, string $label, array $groupFields)
    {
        $pipeline = [
            [
                '$match' => [
                    'post_id' => ['$in' => array_column($posts, '_id')],
                    'label'   => ['$gte' => $label],
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
                ],
            ],
        ];

        return ReportPostLogModel::aggregates($pipeline);
    }
}
