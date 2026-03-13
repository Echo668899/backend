<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\PostRepository;

class PostController extends BaseApiController
{

    /**
     * nav 列表
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public function navListAction(){
        $result = PostRepository::navList();
        $this->sendSuccessResult($result);
    }

    /**
     * nav下模块,常规模块,带items
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public function navBlockAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $page   = $this->getRequest('page', 'int', 1);
        $result = PostRepository::navBlock($navId, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * nav下filter
     * @return void
     */
    public function navFilterAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $result = PostRepository::navFilter($navId);
        $this->sendSuccessResult($result);
    }

    /**
     * 模块详情
     * @return void
     * @throws BusinessException
     */
    public function blockDetailAction()
    {
        $blockId = $this->getRequest('id', 'int');
        $result  = PostRepository::getBlockDetail($blockId);
        $this->sendSuccessResult($result);
    }

    /**
     * 标签详情
     * @return void
     */
    public function tagDetailAction()
    {
        $tagId  = $this->getRequest('id', 'int');
        $result = PostRepository::getTagDetail($tagId);
        $this->sendSuccessResult($result);
    }

    /**
     * 去点赞
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doLoveAction()
    {
        $userId = $this->getUserId();
        $postId = $this->getRequest('id', 'string');
        if (empty($postId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = PostRepository::doLove($userId, $postId);
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
        $result = PostRepository::getLoveList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 去收藏
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doFavoriteAction()
    {
        $userId = $this->getUserId();
        $postId = $this->getRequest('id', 'string');
        if (empty($postId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = PostRepository::doFavorite($userId, $postId);
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
        $result = PostRepository::getFavoriteList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 删除历史
     * @return void
     */
    public function delHistoryAction()
    {
        $userId   = $this->getUserId();
        $novelIds = $this->getRequest('ids');// 逗号分割或是all
        PostRepository::delHistory($userId, $novelIds);
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
        $cursor = $this->getRequest('cursor', 'string', '');
        $result = PostRepository::getHistoryList($userId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 帖子详情
     * @return void
     */
    public function detailAction()
    {
        $userId = $this->getUserId();
        $postId = $this->getRequest('id', 'string');
        $result = PostRepository::getDetail($userId, $postId);
        $this->sendSuccessResult($result);
    }

    /**
     * 购买帖子
     * @return void
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public function doBuyAction()
    {
        $userId = $this->getUserId();
        $postId = $this->getRequest('id', 'string');
        PostRepository::doBuy($userId, $postId);
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
        $result = PostRepository::getBuyLogList($userId, $page, 20, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 发布
     * @return void
     */
    public function createAction()
    {
        $userId = $this->getUserId();
        $result = PostRepository::getCreateInfo($userId);
        $this->sendSuccessResult($result);
    }

    /**
     * 发帖
     * @return void
     * @throws BusinessException
     */
    public function doCreateAction()
    {
        $userId = $this->getUserId();
        $result = PostRepository::doCreate($userId, $_REQUEST);
        $this->sendSuccessResult($result ? 'y' : 'n');
    }

    /**
     * 站点地图
     * @return void
     */
    public function sitemapAction()
    {
        $page   = $this->getRequest('page', 'int', 1);
        $result = PostRepository::sitemap($page);
        $this->sendSuccessResult($result);
    }
}
