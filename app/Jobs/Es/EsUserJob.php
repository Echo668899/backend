<?php

namespace App\Jobs\Es;

use App\Jobs\BaseJob;
use App\Models\User\UserModel;
use App\Services\Common\ElasticService;
use App\Utils\LogUtil;

/**
 * 同步用户到Es
 * Class MovieJob
 * @package App\Jobs\Async
 */
class EsUserJob extends BaseJob
{
    public function handler($uniqid)
    {
        //       $this->before();
        $this->run();
    }

    public function run()
    {
        $where     = [];
        $count     = UserModel::count($where);
        $pageSize  = 10000;
        $totalPage = ceil($count / $pageSize);
        $lastId    = null;// 游标式分页
        for ($page = 1; $page <= $totalPage; $page++) {
            if ($lastId) {
                $where['_id'] = ['$gt' => $lastId];
            }
            $items = UserModel::find($where, ['_id', 'username', 'nickname', 'headico', 'is_disabled', 'login_num'], ['_id' => 1], 0, $pageSize);
            $ops   = [];
            foreach ($items as $item) {
                $lastId = $item['_id'];

                // 排除已禁用的用户
                if ($item['is_disabled']) {
                    continue;
                }
                // 排除登录没怎么登录的用户
                if ($item['login_num'] < 5) {
                    continue;
                }

                $ops[] = [
                    '_id' => strval($item['_id']),
                    'doc' => [
                        'id'       => strval($item['_id']),
                        'username' => strval($item['username']),
                        'nickname' => strval($item['nickname']),
                        'headico'  => strval($item['headico']),
                    ]
                ];
            }
            if (!empty($ops)) {
                ElasticService::bulk($ops, 'user', 'user');
            }
            LogUtil::info('Async user to es page:' . $page . ' total:' . $totalPage);
        }
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
            'username' => [
                'type'            => 'text',
                'analyzer'        => 'standard',
                'search_analyzer' => 'standard', // 查询时用
                'fields'          => [
                    'wild' => [
                        'type' => 'wildcard'
                    ]
                ]
            ],
            'nickname' => [
                'type'            => 'text',
                'analyzer'        => 'standard',
                'search_analyzer' => 'standard', // 查询时用
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
        ElasticService::initIndex('user', $analysis, $mapping);
    }

    public function success($uniqid)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
