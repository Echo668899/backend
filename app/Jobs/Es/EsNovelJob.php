<?php

namespace App\Jobs\Es;

use App\Jobs\BaseJob;
use App\Models\Novel\NovelModel;
use App\Services\Common\ElasticService;
use App\Services\Novel\NovelService;
use App\Utils\LogUtil;

/**
 * 同步小说到Es
 * Class MovieJob
 * @package App\Jobs\Async
 */
class EsNovelJob extends BaseJob
{
    public function handler($uniqid)
    {
        $this->before();
        $this->run();
    }

    public function before()
    {
        $analysis = [
            'analyzer' => [
                'ik_smart_len2' => [ // 自定义查询分词器
                    'type'      => 'custom',
                    'tokenizer' => 'ik_smart',
                    'filter'    => ['length_filter']
                ]
            ],
            'filter' => [
                'length_filter' => [
                    'type' => 'length',
                    'min'  => 2 // 只保留长度 >= 2 的 token
                ]
            ]
        ];
        $mapping = [
            'name' => [
                'type'            => 'text',
                'analyzer'        => 'ik_smart', // 索引时用 ik_max_word会最大化切 会有单字
                'search_analyzer' => 'ik_smart_len2', // 查询时用
                'fields'          => [
                    'raw' => [
                        'type' => 'keyword'
                    ],
                    'wild' => [
                        'type' => 'wildcard'
                    ]
                ]
            ],
        ];
        ElasticService::initIndex('novel', $analysis, $mapping);
    }

    public function run()
    {
        $where    = ['status' => 1];
        $pageSize = 1000;
        $lastId   = null;
        while (true) {
            if ($lastId !== null) {
                $where['_id'] = ['$lt' => $lastId];
            }
            $items = NovelModel::find($where, ['_id'], ['_id' => -1], 0, $pageSize);
            if (empty($items)) {
                break;
            }
            foreach ($items as $item) {
                if (NovelService::asyncEs($item['_id'])) {
                    LogUtil::info('Async novel to es ok:' . $item['_id']);
                } else {
                    LogUtil::error('Async novel to es error:' . $item['_id']);
                }
                $lastId = $item['_id'];
            }
        }
    }

    public function success($uniqid)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
