<?php

namespace App\Repositories\Api;
use App\Core\Repositories\BaseRepository;
use App\Services\Comics\ComicsBlockService;
use App\Services\Comics\ComicsNavService;
use App\Services\Common\AdvAppService;
use App\Services\Common\AdvService;
use App\Services\Movie\MovieBlockService;
use App\Services\Movie\MovieNavService;
use App\Services\Novel\NovelBlockService;
use App\Services\Novel\NovelNavService;
use App\Services\Post\PostBlockService;
use App\Services\Post\PostNavService;

class AdvRepository extends BaseRepository
{
    /**
     * 广告位列表
     * @param $code
     * @param $limit
     * @return array
     */
    public static function getListByCode($code, $limit = 100){
        return AdvService::getAll($code, null, $limit);
    }

    /**
     * 首页顶部模块列表
     * @param $type
     * @return array
     */
    public static function getBlockList($type){
        $typeList = ['dark', 'video', 'cartoon', 'comics', 'novel', 'post'];
        $position = 'normal';
        if(!in_array($type, $typeList)){
            $type = 'video';
        }

        $navList = [];
        switch($type){
            case 'dark':
                $position = 'dark';
                $navList = self::getMovieBlock($position);
                break;
            case 'video':
                $position = 'normal';
                $navList = self::getMovieBlock($position);
                break;
            case 'cartoon':
                $position = 'cartoon';
                $navList = self::getMovieBlock($position);
                break;
            case 'comics':
                $navList = self::getComicsBlock();
                break;
            case 'novel':
                $navList = self::getNovelBlock();
                break;
            case 'post':
                $navList = self::getPostBlock();
                break;
        }

        return $navList;
    }

    /**
     * 视频模块列表
     * @param $position
     * @return array
     */
    public static function getMovieBlock($position){
        $navList = MovieNavService::getAll($position);
        $blocks = [];
        if($navList){
            foreach ($navList as $k => $v){
                $block = MovieBlockService::get($v['id']);
                if($block){
                    $blocks = array_merge($blocks, $block);
                }
            }
        } 
        return $blocks;
    }

    /**
     * 漫画模块列表
     * @return array
     */
    public static function getComicsBlock(){
        $navList = ComicsNavService::getAll();
        $blocks = [];
        if($navList){
            foreach($navList as $k => $v){
                $block = ComicsBlockService::get($v['id']);
                if($block){
                    $blocks = array_merge($blocks, $block);
                }
            }
        }

        return $blocks;
    }

    /**
     * 小说模块列表
     * @return array
     */
    public static function getNovelBlock(){
        $navList = NovelNavService::getAll();
        $blocks = [];
        if($navList){
            foreach($navList as $k => $v){
                $block = NovelBlockService::get($v['id']);
                if($block){
                    $blocks = array_merge($blocks, $block);
                }
            }
        }

        return $blocks;
    }

    /**
     * 帖子模块列表
     * @return array
     */
    public static function getPostBlock(){
        $navList = PostNavService::getAll();
        $blocks = [];
        if($navList){
            foreach($navList as $k => $v){
                $block = PostBlockService::get($v['id']);
                if($block){
                    $blocks = array_merge($blocks, $block);
                }
            }
        }

        return $blocks;
    }

    /**
     * 应用列表
     */
    public static function appList(){
        $pos = AdvAppService::$position;

        $res = [];
        foreach($pos as $position => $name){
                $res[$position]['name'] = $name;
                $res[$position]['list'] = AdvAppService::getAll($position);
        }

        return $res;
    }
}