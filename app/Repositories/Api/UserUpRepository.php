<?php

namespace App\Repositories\Api;
use App\Core\Repositories\BaseRepository;
use App\Services\User\UserUpService;

class UserUpRepository extends BaseRepository
{

    /**
     * up主搜索
     * @param $keyword
     * @param $page
     * @param $pageSize
     * @return array
     */
    public static function search($keyword, $userId, $page = 1, $pageSize = 24){
        $filter = ['keywords' => $keyword, 'order' => 'movie_favorite', 'page_size' => $pageSize, 'page' => $page];
        $res = UserUpService::doSearch($filter, $userId);
        return $res;
    }

    /**
     * up主list
     * @param $keyword
     * @param $page
     * @param $pageSize
     * @return array
     */
    public static function list($userId, $order = 'recommend',  $page = 1, $pageSize = 24){
        $enum = ['recommend' => 'movie_click', 'most'=>'fans', 'new'=>'new'];
        if(!isset($enum[$order])){
            $order = 'recommend';
        }
        return UserUpService::doSearch(['order' => $enum[$order], 'page_size' => $pageSize, 'page' => $page], $userId);
    }

}