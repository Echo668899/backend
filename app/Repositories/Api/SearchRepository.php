<?php

namespace App\Repositories\Api;

use App\Core\Repositories\BaseRepository;
use App\Services\Comics\ComicsService;
use App\Services\Movie\MovieService;
use App\Services\User\UserUpService;

class SearchRepository extends BaseRepository
{
    public static function do($keywords)
    {
        // 每个板块搜一点数据
        $result = [
            'up' => value(function () use ($keywords) {
                $result = UserUpService::doSearch(['keywords' => $keywords, 'page_size' => 2]);
                return [
                    'items' => $result['data'],
                    'total' => strval($result['total']),
                ];
            }),
            'movie' => value(function () use ($keywords) {
                $result = MovieService::doSearch(['keywords' => $keywords, 'page_size' => 8]);
                return [
                    'items' => $result['data'],
                    'total' => strval($result['total']),
                ];
            }),
            'comics' => value(function () use ($keywords) {
                $result = ComicsService::doSearch(['keywords' => $keywords, 'page_size' => 6]);
                return [
                    'items' => $result['data'],
                    'total' => strval($result['total']),
                ];
            }),
        ];
        return $result;
    }
}
