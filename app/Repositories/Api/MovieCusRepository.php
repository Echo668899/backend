<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Movie\MovieBlockModel;
use App\Models\Movie\MovieModel;
use App\Models\Movie\MovieNavModel;
use App\Models\Movie\MovieTagModel;
use App\Services\Activity\ActivityService;
use App\Services\Common\AdvService;
use App\Services\Common\ApiService;
use App\Services\Common\CommonService;
use App\Services\Common\IpService;
use App\Services\Common\M3u8Service;
use App\Services\Movie\MovieBlockService;
use App\Services\Movie\MovieNavService;
use App\Services\Movie\MovieCategoryService;
use App\Services\Movie\MovieDisLoveService;
use App\Services\Movie\MovieDownloadService;
use App\Services\Movie\MovieFavoriteService;
use App\Services\Movie\MovieHistoryService;
use App\Services\Movie\MovieLoveService;
use App\Services\Movie\MovieService;
use App\Services\Movie\MovieTagService;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserGroupService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

class MovieCusRepository extends BaseRepository
{
    /**
     * nav列表
     * @param $position string
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function navList($position = null){
        $navNames = CommonValues::getMovieNavPosition();
        if($position && !isset($navNames[$position])){
            $position = null;
        }
        $res = MovieNavService::getAll($position);
        $ret = [];
        if(!$res){
            return $ret;
        }

        
        foreach($res as $item){
            $item['blocks'] = [];
            if($item['style'] == 'video_1'){
                $blocks = MovieBlockService::get($item['id']);
                if($blocks){
                    foreach($blocks as $k => $block){
                        $blocks[$k]['style_name'] = CommonValues::getMovieBlockStyle($block['style']);
                    }
                }

                $item['blocks'] = $blocks;
            }
            $item['style_name'] = CommonValues::getMovieNavStyle($item['style']);
            $ret[$item['position']][] = $item;
        }

        $navs = [];
        foreach($ret as $position => $l){
            $navs[] = ['title' => $navNames[$position], 'position' => $position, 'list' => $l];
        }
        return $navs;
    }

    /**
     * up主video列表
     * @param $homeId
     * @param $type
     * @param $page
     * @param $pageSize
     * @return array
     */
    public static function getUpContent($homeId, $type, $order = 'hot', $page = 1, $pageSize = 12){
        $typeList = ['long_video', 'short_video', 'cartoon', 'comics', 'novel', 'post'];
        $orderList = ['new', 'click', 'love', 'favorite', 'hot'];

        if(!in_array($order, $orderList)){
            $order = 'hot';
        }

        if(!in_array($type, $typeList)){
            $type = 'long_video';
        }
        $position = 'normal';

        $ret = [];
        switch($type){
            case 'short_video':
                $position = 'douyin';
                $filter = ['position' => $position, 'home_id' => $homeId, 'order' => $order, 'page' => $page, 'page_size' => $pageSize];
                $ret = MovieRepository::doSearch($filter);
                break;
            case 'cartoon':
                $position = 'cartoon';
                $filter = ['position' => $position, 'home_id' => $homeId, 'order' => $order, 'page' => $page, 'page_size' => $pageSize];
                $ret = MovieRepository::doSearch($filter);
                break;
            case 'long_video':
                $filter = ['position' => $position, 'home_id' => $homeId, 'order' => $order, 'page' => $page, 'page_size' => $pageSize];
                $ret = MovieRepository::doSearch($filter);
                break;
            case 'comics':
                $ret = [];
                break;
            case 'novel':
                $ret = [];
                break;
            case 'post':
                $filter = ['home_id' => $homeId, 'order' => $order, 'page' => $page, 'page_size' => $pageSize];
                $ret = PostRepository::doSearch($filter);
                break;
        }

        return $ret;
    }

    /**
     * 热搜列表
     * @param $type
     * @param $page
     * @param $pageSize
     * @return array
     */
    public static function hotSearch($type, $page = 1, $pageSize = 12){
        //使用标签id，标签后台添加
        $typeList = ['video' => 1293, 'short_video' => 1, 'cartoon' => 767, 'comics' => 39, 'novel' => 117, 'post' => 18];

        if(!isset($typeList[$type])){
            $type = 'video';
        }
        $filter = ['tag_id' => $typeList[$type], 'page' => $page, 'page_size' => $pageSize];

        $ret = [];
        switch($type){
            case 'short_video':
                $ret = MovieRepository::doSearch($filter);
                break;
            case 'cartoon':
                $ret = MovieRepository::doSearch($filter);
                break;
            case 'video':
                $ret = MovieRepository::doSearch($filter);
                break;
            case 'comics':
                $ret = ComicsRepository::doSearch($filter);
                break;
            case 'novel':
                $ret = NovelRepository::doSearch($filter);
                break;
            case 'post':
                $ret = PostRepository::doSearch($filter);
                break;
        }

        return $ret;
    }
}