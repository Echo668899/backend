<?php

namespace App\Jobs\Es;

use App\Jobs\BaseJob;
use App\Models\Comics\ComicsModel;
use App\Services\Comics\ComicsService;
use App\Services\Common\ElasticService;
use App\Utils\LogUtil;

/**
 * 同步漫画到Es
 * Class MovieJob
 * @package App\Jobs\Async
 */
class EsComicsJob extends BaseJob
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
        ElasticService::initIndex('comics', $analysis, $mapping);
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
            $items = ComicsModel::find($where, ['_id'], ['_id' => -1], 0, $pageSize);
            if (empty($items)) {
                break;
            }
            foreach ($items as $item) {
                if (ComicsService::asyncEs($item['_id'])) {
                    LogUtil::info('Async comics to es ok:' . $item['_id']);
                } else {
                    LogUtil::error('Async comics to es error:' . $item['_id']);
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
