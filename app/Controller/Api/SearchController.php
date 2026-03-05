<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Repositories\Api\AudioRepository;
use App\Repositories\Api\ComicsRepository;
use App\Repositories\Api\MovieRepository;
use App\Repositories\Api\NovelRepository;
use App\Repositories\Api\PostRepository;
use App\Services\Audio\AudioKeywordsService;
use App\Services\Comics\ComicsKeywordsService;
use App\Services\Movie\MovieKeywordsService;
use App\Services\Novel\NovelKeywordsService;
use App\Services\Post\PostKeywordsService;

class SearchController extends BaseApiController
{
    /**
     * 搜索页面
     */
    public function homeAction()
    {
        $type     = $this->getRequest('type', 'string');// 资源类型 movie post novel comics
        $position = $this->getRequest('position', 'string');// 资源板块

        switch ($type) {
            case 'movie':
                $keywords = MovieKeywordsService::getHotList();
                break;
            case 'comics':
                $keywords = ComicsKeywordsService::getHotList();
                break;
            case 'novel':
                $keywords = NovelKeywordsService::getHotList();
                break;
            case 'audio':
                $keywords = AudioKeywordsService::getHotList();
                break;
            case 'post':
                $keywords = PostKeywordsService::getHotList();
                break;
            default:
                $this->sendErrorResult('参数错误');
                break;
        }

        $this->sendSuccessResult([
            'keywords' => $keywords,
            'filter'   => [
                'order'     => 'click30',
                'position'  => $position,
                'page_size' => 20
            ],
        ]);
    }

    /**
     * @return void
     */
    public function movieAction()
    {
        $result = MovieRepository::doSearch($_REQUEST, $this->getUserId(false));
        $this->sendSuccessResult($result);
    }

    /**
     * @return void
     */
    public function comicsAction()
    {
        $result = ComicsRepository::doSearch($_REQUEST, $this->getUserId(false));
        $this->sendSuccessResult($result);
    }

    /**
     * @return void
     */
    public function novelAction()
    {
        $result = NovelRepository::doSearch($_REQUEST, $this->getUserId(false));
        $this->sendSuccessResult($result);
    }

    /**
     * @return void
     */
    public function audioAction()
    {
        $result = AudioRepository::doSearch($_REQUEST, $this->getUserId(false));
        $this->sendSuccessResult($result);
    }

    /**
     * @return void
     */
    public function postAction()
    {
        $result = PostRepository::doSearch($_REQUEST, $this->getUserId(false));
        $this->sendSuccessResult($result);
    }
}
