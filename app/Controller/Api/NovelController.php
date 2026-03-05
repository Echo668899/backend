<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\NovelRepository;

/**
 * 小说
 */
class NovelController extends BaseApiController
{
    /**
     * nav下模块,常规模块,带items
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public function navBlockAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $page   = $this->getRequest('page', 'int', 1);
        $result = NovelRepository::navBlock($navId, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * nav下filter
     * @return void
     */
    public function navFilterAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $result = NovelRepository::navFilter($navId);
        $this->sendSuccessResult($result);
    }

    /**
     * 筛选页面
     * @return void
     */
    public function filterAction()
    {
        $result = NovelRepository::filter();
        $this->sendSuccessResult($result);
    }

    /**
     * 模块详情
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function blockDetailAction()
    {
        $blockId = $this->getRequest('id', 'int');
        $result  = NovelRepository::getBlockDetail($blockId);
        $this->sendSuccessResult($result);
    }

    /**
     * 漫画详情
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
        $userId = $this->getUserId(false);
        $id     = $this->getRequest('id', 'string');
        $result = NovelRepository::getDetail($userId, $id);
        $this->sendSuccessResult($result);
    }

    /**
     * 章节详情-阅读
     * @return void
     * @throws \App\Exception\BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public function chapterAction()
    {
        $userId = $this->getUserId(false);
        $id     = $this->getRequest('id', 'string');
        $result = NovelRepository::getChapterDetail($userId, $id);
        $this->sendSuccessResult($result);
    }

    /**
     * 标签详情
     * @return void
     */
    public function tagDetailAction()
    {
        $tagId  = $this->getRequest('id', 'int');
        $result = NovelRepository::getTagDetail($tagId);
        $this->sendSuccessResult($result);
    }

    /**
     * 去点赞
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doLoveAction()
    {
        $userId   = $this->getUserId();
        $comicsId = $this->getRequest('id', 'string');
        if (empty($comicsId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = NovelRepository::doLove($userId, $comicsId);
        $this->sendSuccessResult(['status' => $result ? 'y' : 'n']);
    }

    /**
     * 点赞列表
     * @return void
     */
    public function loveAction()
    {
        $userId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $homeId = $this->getRequest('home_id', 'string');
        $cursor = $this->getRequest('cursor', 'string');
        if (empty($homeId)) {
            $homeId = $userId;
        }
        $result = NovelRepository::getLoveList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 去收藏
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doFavoriteAction()
    {
        $userId   = $this->getUserId();
        $comicsId = $this->getRequest('id', 'string');
        if (empty($comicsId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = NovelRepository::doFavorite($userId, $comicsId);
        $this->sendSuccessResult(['status' => $result ? 'y' : 'n']);
    }

    /**
     * 收藏列表
     * @return void
     */
    public function favoriteAction()
    {
        $userId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $homeId = $this->getRequest('home_id', 'string');
        $cursor = $this->getRequest('cursor', 'string');
        if (empty($homeId)) {
            $homeId = $userId;
        }
        $result = NovelRepository::getFavoriteList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 添加播放记录
     */
    public function doHistoryAction()
    {
        $userId    = $this->getUserId();
        $novelId   = $this->getRequest('id', 'string');
        $chapterId = $this->getRequest('chapter_id', 'string');
        $index     = $this->getRequest('index', 'int');
        NovelRepository::doHistory($userId, $novelId, $chapterId, $index);
        /* $this->sendSuccessResult();//没有响应的意义,浪费出口带宽 */
    }

    /**
     * 删除历史
     * @return void
     */
    public function delHistoryAction()
    {
        $userId   = $this->getUserId();
        $novelIds = $this->getRequest('ids');// 逗号分割或是all
        NovelRepository::delHistory($userId, $novelIds);
        $this->sendSuccessResult();
    }

    /**
     * 历史列表
     * @return void
     */
    public function historyAction()
    {
        $userId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $cursor = $this->getRequest('cursor', 'string');
        $result = NovelRepository::getHistoryList($userId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 购买小说
     * @return void
     * @throws BusinessException
     */
    public function doBuyAction()
    {
        $userId  = $this->getUserId();
        $novelId = $this->getRequest('id', 'string');
        //        $chapterId = $this->getRequest('chapter_id', 'string');//一般不用单章解锁
        $chapterId = '';
        NovelRepository::doBuy($userId, $novelId, $chapterId);
        $this->sendSuccessResult();
    }

    /**
     * 购买记录
     * @return void
     */
    public function buyLogAction()
    {
        $userId = $this->getUserId();
        $page   = $this->getRequest('page', 'int', 1);
        $cursor = $this->getRequest('cursor', 'string');
        $result = NovelRepository::getBuyLogList($userId, $page, 20, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 站点地图
     * @return void
     */
    public function sitemapAction()
    {
        $page   = $this->getRequest('page', 'int', 1);
        $result = NovelRepository::sitemap($page);
        $this->sendSuccessResult($result);
    }
}
