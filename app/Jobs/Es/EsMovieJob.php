<?php

namespace App\Jobs\Es;

use App\Jobs\BaseJob;
use App\Models\Movie\MovieModel;
use App\Services\Common\ElasticService;
use App\Services\Movie\MovieService;
use App\Utils\LogUtil;

/**
 * 同步视频到Es
 * Class MovieJob
 * @package App\Jobs\Async
 */
class EsMovieJob extends BaseJob
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
                'type'     => 'text',
                'analyzer' => 'ik_smart', // 索引时用 ik_max_word会最大化切 会有单字
                //                "search_analyzer"=>"ik_smart",// 查询时用
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
        ElasticService::initIndex('movie', $analysis, $mapping);
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
            $items = MovieModel::find($where, ['_id'], ['_id' => -1], 0, $pageSize);
            if (empty($items)) {
                break;
            }
            foreach ($items as $item) {
                if (MovieService::asyncEs($item['_id'])) {
                    LogUtil::info('Async movie to es ok:' . $item['_id']);
                } else {
                    LogUtil::error('Async movie to es error:' . $item['_id']);
                }
                $lastId = $item['_id'];
            }
        }
    }

    public function error($_id, \Exception $e)
    {
    }

    public function success($uniqid)
    {
    }
}
