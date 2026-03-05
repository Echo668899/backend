<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Novel\NovelModel;
use App\Models\Report\ReportNovelLogModel;
use App\Services\Report\ReportNovelLogService;
use App\Utils\LogUtil;

/**
 * 小说排名数据
 */
class StatsNovelJob extends BaseJob
{
    public function handler($_id)
    {
        $ranges = [
            '1'   => date('Y-m-d', strtotime('-1 day')),    // 昨天
            '7'   => date('Y-m-d', strtotime('-7 day')),    // 7天前
            '30'  => date('Y-m-d', strtotime('-30 day')),  // 30天前
            'all' => date('Y-m-d', strtotime('-5 year')),  // 所有
        ];
        // /先重置所有统计,因为小说可能下架
        ReportNovelLogService::resetStats();

        $where     = ['status' => 1];
        $count     = NovelModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $novels = NovelModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            foreach ($ranges as $range => $label) {
                $this->doCounter($novels, $range, $label);
            }
            LogUtil::info("Starting count novel {$page}/{$totalPage}");
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    private function doCounter($novels, $range, $label)
    {
        $range = $range == 'all' ? '' : $range;
        /**
         * 总榜单
         */
        $result = $this->aggregateBy($novels, $label, ['novel_id']);
        foreach ($result as &$item) {
            $item['_id'] = $item['_id']['novel_id'];
            unset($item);
        }
        $result = array_column($result, null, '_id');

        $rows = [];
        foreach ($novels as $audio) {
            // 总榜单需要所有资源都存在
            $count = $result[$audio['_id']] ?? [];
            $value = [
                'novel_id' => $audio['_id'],
                'click'    => $count['click'] ?: 0,
                'love'     => $count['love'] ?: 0,
                'favorite' => $count['favorite'] ?: 0,
                'category' => 'normal',
            ];
            $rows[] = $value;
        }
        ReportNovelLogService::setStats($rows, $range);

        /**
         * 分类榜单
         */
        $result = $this->aggregateBy($novels, $label, ['category', 'novel_id']);
        $rows   = [];
        foreach ($result as $item) {
            $rows[] = [
                'novel_id' => $item['_id']['novel_id'],
                'click'    => $item['click'] ?? 0,
                'love'     => $item['love'] ?? 0,
                'favorite' => $item['favorite'] ?? 0,
                'category' => $item['_id']['category'] ?? 'normal',
            ];
        }
        ReportNovelLogService::setStats($rows, $range);

        /**
         * 连载/完结 榜单
         */
        $result = $this->aggregateBy($novels, $label, ['update_status', 'novel_id']);
        $rows   = [];
        foreach ($result as $item) {
            $status = $item['_id']['update_status'] ?? 0;
            $rows[] = [
                'novel_id' => $item['_id']['novel_id'],
                'click'    => $item['click'] ?? 0,
                'love'     => $item['love'] ?? 0,
                'favorite' => $item['favorite'] ?? 0,
                'category' => ($status == 1) ? 'finish' : 'update',
            ];
        }
        ReportNovelLogService::setStats($rows, $range);
    }

    private function aggregateBy(array $novels, string $label, array $groupFields)
    {
        $pipeline = [
            [
                '$match' => [
                    'novel_id' => ['$in' => array_column($novels, '_id')],
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
                ],
            ],
        ];

        return ReportNovelLogModel::aggregates($pipeline);
    }
}
