<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\MovieRepository;
use Phalcon\Storage\Exception;

class MovieController extends BaseApiController
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
        $result = MovieRepository::navBlock($navId, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * nav下filter
     * @return void
     */
    public function navFilterAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $result = MovieRepository::navFilter($navId);
        $this->sendSuccessResult($result);
    }

    /**
     * 筛选页面
     * @return void
     */
    public function filterAction()
    {
        $result = MovieRepository::filter();
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
        $result  = MovieRepository::getBlockDetail($blockId);
        $this->sendSuccessResult($result);
    }

    /**
     * 详情
     * @return void
     * @throws BusinessException|Exception
     */
    public function detailAction()
    {
        $userId  = $this->getUserId(false);
        $movieId = $this->getRequest('id', 'string');
        $linkId  = $this->getRequest('lid', 'string');
        $result  = MovieRepository::getDetail($userId, $movieId, $linkId);
        $this->sendSuccessResult($result);
    }

    /**
     * 视频详情带推荐列表
     * MovieShortSearchIdFilterView
     * @return void
     * @throws BusinessException
     * @throws Exception
     */
    public function similarSearchAction()
    {
        $userId = $this->getUserId(false);
        $result = MovieRepository::similarSearch($_REQUEST, $userId);
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
        $result = MovieRepository::getTagDetail($tagId);
        $this->sendSuccessResult($result);
    }

    /**
     * 去点赞
     * @throws BusinessException
     */
    public function doLoveAction()
    {
        $userId  = $this->getUserId();
        $movieId = $this->getRequest('id', 'string');
        if (empty($movieId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = MovieRepository::doLove($userId, $movieId);
        $this->sendSuccessResult(['status' => $result ? 'y' : 'n']);
    }

    /**
     * 去点踩
     * @throws BusinessException
     */
    public function doDisLoveAction()
    {
        $userId  = $this->getUserId();
        $movieId = $this->getRequest('id', 'string');
        if (empty($movieId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = MovieRepository::doDisLove($userId, $movieId);
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
        $result = MovieRepository::getLoveList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 去收藏
     * @throws BusinessException
     */
    public function doFavoriteAction()
    {
        $userId  = $this->getUserId();
        $movieId = $this->getRequest('id', 'string');
        if (empty($movieId)) {
            $this->sendErrorResult('参数错误');
        }
        $result = MovieRepository::doFavorite($userId, $movieId);
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
        $result = MovieRepository::getFavoriteList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 添加播放记录
     */
    public function doHistoryAction()
    {
        $userId   = $this->getUserId();
        $movieId  = $this->getRequest('id', 'string');
        $linkId   = $this->getRequest('lid', 'string');
        $code     = $this->getRequest('code', 'string', '');// 可选参数,观看线路
        $playTime = $this->getRequest('time', 'int');// 进度时长
        $viewTime = $this->getRequest('view_time', 'int');// 实际观看时间
        $event    = $this->getRequest('event', 'string');
        MovieRepository::doHistory($userId, $movieId, $linkId, $playTime, $viewTime, $code, $event);
        /* $this->sendSuccessResult();//没有响应的意义,浪费出口带宽 */
    }

    /**
     * 删除历史
     * @return void
     */
    public function delHistoryAction()
    {
        $userId   = $this->getUserId();
        $movieIds = $this->getRequest('ids');// 逗号分割或是all
        MovieRepository::delHistory($userId, $movieIds);
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
        $result = MovieRepository::getHistoryList($userId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 购买视频
     * @return void
     * @throws BusinessException
     */
    public function doBuyAction()
    {
        $userId  = $this->getUserId();
        $movieId = $this->getRequest('id', 'string');
        $linkId  = $this->getRequest('lid', 'string');
        MovieRepository::doBuy($userId, $movieId, $linkId);
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
        $result = MovieRepository::getBuyLogList($userId, $page, 20, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 播放地址
     * @return void
     * @throws BusinessException
     */
    public function playAction()
    {
        $id = $this->getRequest('id', 'string');
        if (empty($id)) {
            $this->sendErrorResult('参数错误');
        }
        $result = MovieRepository::getPlayUrl($id);
        $this->sendSuccessResult($result);
    }

    /**
     * 下载
     * @return void
     * @throws BusinessException
     */
    public function doDownloadAction()
    {
        $userId  = $this->getUserId();
        $movieId = $this->getRequest('id', 'string');
        $linkId  = $this->getRequest('lid', 'string');
        if (empty($movieId) || empty($linkId)) {
            $this->sendErrorResult('请选择您要缓存的视频!');
        }
        $result = MovieRepository::doDownload($userId, $movieId, $linkId);
        $this->sendSuccessResult($result);
    }

    /**
     * 发布
     * @return void
     */
    public function createAction()
    {
        $userId = $this->getUserId();
        $result = MovieRepository::getCreateInfo($userId);
        $this->sendSuccessResult($result);
    }

    /**
     * 保存视频
     * @return void
     * @throws BusinessException
     */
    public function doCreateAction()
    {
        $userId = $this->getUserId();
        $result = MovieRepository::doCreate($userId, $_REQUEST);
        $this->sendSuccessResult($result ? 'y' : 'n');
    }

    /**
     * 站点地图
     * @return void
     */
    public function sitemapAction()
    {
        $page   = $this->getRequest('page', 'int', 1);
        $result = MovieRepository::sitemap($page);
        $this->sendSuccessResult($result);
    }
}
