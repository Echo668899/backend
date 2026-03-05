<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Ai\AiOrderModel;
use App\Models\Ai\AiTplModel;
use App\Services\Report\ReportAiTplLogService;
use App\Utils\LogUtil;

/**
 * AI模板排名数据
 */
class StatsAiJob extends BaseJob
{
    public function handler($_id)
    {
        $ranges = [
            '1'   => date('Y-m-d', strtotime('-1 day')),    // 昨天
            '7'   => date('Y-m-d', strtotime('-7 day')),    // 7天前
            '30'  => date('Y-m-d', strtotime('-30 day')),  // 30天前
            'all' => date('Y-m-d', strtotime('-5 year')),  // 所有
        ];
        // /先重置所有统计,因为模板可能下架
        ReportAiTplLogService::resetStats();

        $where     = ['type' => ['$in' => ['change_face_image', 'change_face_video']], 'is_disabled' => 0];
        $count     = AiTplModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip = ($page - 1) * $pageSize;
            $tpls = AiTplModel::find($where, ['_id', 'type'], ['_id' => -1], $skip, $pageSize);
            foreach ($ranges as $range => $label) {
                $this->doCounter($tpls, $range, $label);
            }
            LogUtil::info("Starting count ai {$page}/{$totalPage}");
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    private function doCounter($tpls, $range, $label)
    {
        $range = $range == 'all' ? '' : $range;
        /**
         * 总榜单
         */
        $result = $this->aggregateBy($tpls, $label, ['tpl_id']);
        foreach ($result as &$item) {
            $item['_id'] = $item['_id']['tpl_id'];
            unset($item);
        }

        $result = array_column($result, null, '_id');
        $rows   = [];

        foreach ($tpls as $tpl) {
            // 总榜单需要所有资源都存在
            $count = $result[$tpl['_id']] ?? [];
            $value = [
                'tpl_id' => $tpl['_id'],
                'buy'    => $count['buy'] ?: 0,
                'type'   => $tpl['type'],
            ];
            $rows[] = $value;
        }
        ReportAiTplLogService::setStats($rows, $range);
    }

    private function aggregateBy(array $tpls, string $label, array $groupFields)
    {
        $pipeline = [
            [
                '$match' => [
                    'tpl_id' => ['$in' => array_column($tpls, '_id')],
                    'label'  => ['$gte' => $label],
                ],
            ],
            [
                '$group' => [
                    '_id' => array_combine(
                        $groupFields,
                        array_map(fn ($f) => '$' . $f, $groupFields)
                    ),
                    'buy' => ['$sum' => 1]
                ],
            ],
        ];
        return AiOrderModel::aggregates($pipeline);
    }
}
