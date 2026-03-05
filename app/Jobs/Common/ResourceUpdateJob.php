<?php

namespace App\Jobs\Common;

use App\Jobs\BaseJob;
use App\Models\Audio\AudioModel;
use App\Models\Comics\ComicsModel;
use App\Models\Movie\MovieModel;
use App\Models\Novel\NovelModel;
use App\Services\Audio\AudioService;
use App\Services\Comics\ComicsService;
use App\Services\Movie\MovieService;
use App\Services\Novel\NovelService;
use App\Utils\LogUtil;
use Exception;

/**
 * 资源更新
 * 漫画,小说,有声,视频
 */
class ResourceUpdateJob extends BaseJob
{
    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function handler($_id)
    {
        if (method_exists($this, $this->type)) {
            try {
                $this->{$this->type}();
            } catch (Exception $e) {
            }
        } else {
            LogUtil::error("ResourceUpdateJob don't action:{$this->type}");
        }
    }

    public function error($_id, Exception $e)
    {
    }

    public function movie()
    {
        /**
         * 13    成人短视频
         * 12    VR
         * 11    电影解说
         * 10    音乐
         * 9    短剧
         * 8    纪录片
         * 7    动漫
         * 6    电影
         * 5    连续剧
         * 4    综艺
         * 3    GC
         * 2    DM
         * 1    AV
         */
        $where = [
            'cat_id'        => ['$in' => [4, 5, 7, 8, 9]],
            'update_status' => 0,
            '$expr'         => [
                '$gte' => [['$size' => '$links'], 2]
            ]
        ];

        $count     = MovieModel::count();
        $pageSize  = 100;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $pageSize;
            $items = MovieModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            $_ids  = array_column($items, '_id');
            MovieService::asyncMrsByIds($_ids);
            LogUtil::info('update movie ok');
        }
    }

    public function success($_id)
    {
    }

    /**
     * 更新漫画
     *
     * @return void
     */
    private function comics()
    {
        $categories = ['国漫', '韩漫', '日漫', '腐漫'];
        $where      = ['cat_id' => ['$in' => $categories], 'update_status' => 0];
        $count      = ComicsModel::count();
        $pageSize   = 100;
        $totalPage  = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $pageSize;
            $items = ComicsModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            $_ids  = array_column($items, '_id');
            ComicsService::asyncMrsByIds($_ids);
            LogUtil::info('update comics ok');
        }
    }

    /**
     * 更新小说
     *
     * @return void
     */
    private function novel()
    {
        $where     = ['cat_id' => [], 'update_status' => 0];
        $count     = NovelModel::count();
        $pageSize  = 100;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $pageSize;
            $items = NovelModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            $_ids  = array_column($items, '_id');
            NovelService::asyncMrsByIds($_ids);
            LogUtil::info('update novel ok');
        }
    }

    /**
     * 更新音频
     *
     * @return void
     */
    private function audio()
    {
        $where     = ['cat_id' => ['$in' => []], 'update_status' => 0];
        $count     = AudioModel::count();
        $pageSize  = 100;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $pageSize;
            $items = AudioModel::find($where, ['_id'], ['_id' => -1], $skip, $pageSize);
            $_ids  = array_column($items, '_id');
            AudioService::asyncMrsByIds($_ids);
            LogUtil::info('update audio ok');
        }
    }
}
