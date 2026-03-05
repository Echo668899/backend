<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Comics\ComicsModel;
use App\Models\Report\ReportComicsLogModel;
use App\Services\Report\ReportComicsLogService;
use App\Utils\LogUtil;

/**
 * 漫画排名数据
 */
class StatsComicsJob extends BaseJob
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
        ReportComicsLogService::resetStats();

        $where     = ['status' => 1];
        $count     = ComicsModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $comics = ComicsModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            foreach ($ranges as $range => $label) {
                $this->doCounter($comics, $range, $label);
            }
            LogUtil::info("Starting count comics {$page}/{$totalPage}");
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    private function doCounter($comics, $range, $label)
    {
        $range = $range == 'all' ? '' : $range;
        /**
         * 总榜单
         */
        $result = $this->aggregateBy($comics, $label, ['comics_id']);
        foreach ($result as &$item) {
            $item['_id'] = $item['_id']['comics_id'];
            unset($item);
        }
        $result = array_column($result, null, '_id');

        $rows = [];
        foreach ($comics as $comic) {
            // 总榜单需要所有资源都存在
            $count = $result[$comic['_id']] ?? [];
            $value = [
                'comics_id' => $comic['_id'],
                'click'     => $count['click'] ?: 0,
                'love'      => $count['love'] ?: 0,
                'favorite'  => $count['favorite'] ?: 0,
                'category'  => 'normal',
            ];
            $rows[] = $value;
        }
        ReportComicsLogService::setStats($rows, $range);

        /**
         * 分类榜单
         */
        $result = $this->aggregateBy($comics, $label, ['category', 'comics_id']);
        $rows   = [];
        foreach ($result as $item) {
            $rows[] = [
                'comics_id' => $item['_id']['comics_id'],
                'click'     => $item['click'] ?? 0,
                'love'      => $item['love'] ?? 0,
                'favorite'  => $item['favorite'] ?? 0,
                'category'  => $item['_id']['category'] ?? 'normal',
            ];
        }
        ReportComicsLogService::setStats($rows, $range);

        /**
         * 连载/完结 榜单
         */
        $result = $this->aggregateBy($comics, $label, ['update_status', 'comics_id']);
        $rows   = [];
        foreach ($result as $item) {
            $status = $item['_id']['update_status'] ?? 0;
            $rows[] = [
                'comics_id' => $item['_id']['comics_id'],
                'click'     => $item['click'] ?? 0,
                'love'      => $item['love'] ?? 0,
                'favorite'  => $item['favorite'] ?? 0,
                'category'  => ($status == 1) ? 'finish' : 'update',
            ];
        }
        ReportComicsLogService::setStats($rows, $range);
    }

    private function aggregateBy(array $comics, string $label, array $groupFields)
    {
        $pipeline = [
            [
                '$match' => [
                    'comics_id' => ['$in' => array_column($comics, '_id')],
                    'label'     => ['$gte' => $label],
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

        return ReportComicsLogModel::aggregates($pipeline);
    }
}
