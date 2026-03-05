<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\AudioRepository;

/**
 * 有声
 */
class AudioController extends BaseApiController
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
        $result = AudioRepository::navBlock($navId, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * nav下filter
     * @return void
     */
    public function navFilterAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $result = AudioRepository::navFilter($navId);
        $this->sendSuccessResult($result);
    }

    /**
     * 筛选页面
     * @return void
     */
    public function filterAction()
    {
        $result = AudioRepository::filter();
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
        $result  = AudioRepository::getBlockDetail($blockId);
        $this->sendSuccessResult($result);
    }

    /**
     * 详情
     * @return void
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public function detailAction()
    {
        $userId  = $this->getUserId(false);
        $audioId = $this->getRequest('id', 'string');
        $linkId  = $this->getRequest('lid', 'string');
        $result  = AudioRepository::getDetail($userId, $audioId, $linkId);
        $this->sendSuccessResult($result);
    }

    /**
     * 标签详情
     * @return void
     * @throws BusinessException
     */
    public function tagDetailAction()
    {
        $tagId  = $this->getRequest('id', 'int');
        $result = AudioRepository::getTagDetail($tagId);
        $this->sendSuccessResult($result);
    }

    /**
     * 去点赞
     * @throws BusinessException
     */
    public function doLoveAction()
    {
        $userId  = $this->getUserId();
        $audioId = $this->getRequest('id', 'string');
        if (empty($audioId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = AudioRepository::doLove($userId, $audioId);
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
        $result = AudioRepository::getLoveList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 去收藏
     * @throws BusinessException
     */
    public function doFavoriteAction()
    {
        $userId  = $this->getUserId();
        $audioId = $this->getRequest('id', 'string');
        if (empty($audioId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = AudioRepository::doFavorite($userId, $audioId);
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
        $result = AudioRepository::getFavoriteList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 添加播放记录
     */
    public function doHistoryAction()
    {
        $userId  = $this->getUserId();
        $audioId = $this->getRequest('id', 'string');
        $linkId  = $this->getRequest('lid', 'string');
        $code    = $this->getRequest('code', 'string', '');// 可选参数,观看线路
        $time    = $this->getRequest('time', 'int');
        AudioRepository::doHistory($userId, $audioId, $linkId, $time, $code);
        /* $this->sendSuccessResult();//没有响应的意义,浪费出口带宽 */
    }

    /**
     * 删除历史
     * @return void
     */
    public function delHistoryAction()
    {
        $userId   = $this->getUserId();
        $audioIds = $this->getRequest('ids');// 逗号分割或是all
        AudioRepository::delHistory($userId, $audioIds);
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
        $result = AudioRepository::getHistoryList($userId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 购买有声
     * @return void
     * @throws BusinessException
     */
    public function doBuyAction()
    {
        $userId  = $this->getUserId();
        $audioId = $this->getRequest('id', 'string');
        //        $chapterId = $this->getRequest('lid', 'string');//一般不用单章解锁
        $chapterId = '';
        AudioRepository::doBuy($userId, $audioId, $chapterId);
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
        $result = AudioRepository::getBuyLogList($userId, $page, 20, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 站点地图
     * @return void
     */
    public function sitemapAction()
    {
        $page   = $this->getRequest('page', 'int', 1);
        $result = AudioRepository::sitemap($page);
        $this->sendSuccessResult($result);
    }
}
