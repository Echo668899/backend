<?php

namespace App\Jobs\Stats;

use App\Jobs\BaseJob;
use App\Models\Audio\AudioModel;
use App\Models\Report\ReportAudioLogModel;
use App\Services\Report\ReportAudioLogService;
use App\Utils\LogUtil;

/**
 * 有声排名数据
 */
class StatsAudioJob extends BaseJob
{
    public function handler($_id)
    {
        $ranges = [
            '1'   => date('Y-m-d', strtotime('-1 day')),    // 昨天
            '7'   => date('Y-m-d', strtotime('-7 day')),    // 7天前
            '30'  => date('Y-m-d', strtotime('-30 day')),  // 30天前
            'all' => date('Y-m-d', strtotime('-5 year')),  // 所有
        ];
        // /先重置所有统计,因为有声可能下架
        ReportAudioLogService::resetStats();

        $where     = ['status' => 1];
        $count     = AudioModel::count($where);
        $pageSize  = 1000;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $pageSize;
            $audios = AudioModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            foreach ($ranges as $range => $label) {
                $this->doCounter($audios, $range, $label);
            }
            LogUtil::info("Starting count audio {$page}/{$totalPage}");
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }

    private function doCounter($audios, $range, $label)
    {
        $range = $range == 'all' ? '' : $range;
        /**
         * 总榜单
         */
        $result = $this->aggregateBy($audios, $label, ['audio_id']);
        foreach ($result as &$item) {
            $item['_id'] = $item['_id']['audio_id'];
            unset($item);
        }
        $result = array_column($result, null, '_id');

        $rows = [];
        foreach ($audios as $audio) {
            // 总榜单需要所有资源都存在
            $count = $result[$audio['_id']] ?? [];
            $value = [
                'audio_id' => $audio['_id'],
                'click'    => $count['click'] ?: 0,
                'love'     => $count['love'] ?: 0,
                'favorite' => $count['favorite'] ?: 0,
                'category' => 'normal',
            ];
            $rows[] = $value;
        }
        ReportAudioLogService::setStats($rows, $range);

        /**
         * 分类榜单
         */
        $result = $this->aggregateBy($audios, $label, ['category', 'audio_id']);
        $rows   = [];
        foreach ($result as $item) {
            $rows[] = [
                'audio_id' => $item['_id']['audio_id'],
                'click'    => $item['click'] ?? 0,
                'love'     => $item['love'] ?? 0,
                'favorite' => $item['favorite'] ?? 0,
                'category' => $item['_id']['category'] ?? 'normal',
            ];
        }
        ReportAudioLogService::setStats($rows, $range);

        /**
         * 连载/完结 榜单
         */
        $result = $this->aggregateBy($audios, $label, ['update_status', 'audio_id']);
        $rows   = [];
        foreach ($result as $item) {
            $status = $item['_id']['update_status'] ?? 0;
            $rows[] = [
                'audio_id' => $item['_id']['audio_id'],
                'click'    => $item['click'] ?? 0,
                'love'     => $item['love'] ?? 0,
                'favorite' => $item['favorite'] ?? 0,
                'category' => ($status == 1) ? 'finish' : 'update',
            ];
        }
        ReportAudioLogService::setStats($rows, $range);
    }

    private function aggregateBy(array $audios, string $label, array $groupFields)
    {
        $pipeline = [
            [
                '$match' => [
                    'audio_id' => ['$in' => array_column($audios, '_id')],
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

        return ReportAudioLogModel::aggregates($pipeline);
    }
}
