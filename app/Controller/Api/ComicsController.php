<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\ComicsRepository;

class ComicsController extends BaseApiController
{
    /**
     * nav列表
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public function navListAction(){
        $res = ComicsRepository::navList();
        $this->sendSuccessResult($res);
    }

    /**
     * 列表
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public function ComicsListAction(){
        $allowKeys = [
            'page','page_size','keywords','icon',
            'pay_type','cat_id','tag_id','is_end','ids',
            'not_ids','order','update_date','update_status','ad_code',
            'language'
        ];
        $params = $this->request->get();
        $keys = array_flip($allowKeys);
        foreach($params as $k => $v){
            if(!isset($keys[$k])){
                unset($params[$k]);
            }
        }
        
        if (empty($params)) {
            $this->sendErrorResult('参数错误');
        }
        $res = ComicsRepository::doSearch($params);
        $this->sendSuccessResult($res);
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
        $result = ComicsRepository::navBlock($navId, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * nav下filter
     * @return void
     */
    public function navFilterAction()
    {
        $navId  = $this->getRequest('id', 'int');
        $result = ComicsRepository::navFilter($navId);
        $this->sendSuccessResult($result);
    }

    /**
     * 筛选页面
     * @return void
     */
    public function filterAction()
    {
        $result = ComicsRepository::filter();
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
        $result  = ComicsRepository::getBlockDetail($blockId);
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
        $result = ComicsRepository::getDetail($userId, $id);
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
        $result = ComicsRepository::getChapterDetail($userId, $id);
        $this->sendSuccessResult($result);
    }

    /**
     * 标签详情
     * @return void
     */
    public function tagDetailAction()
    {
        $tagId  = $this->getRequest('id', 'int');
        $result = ComicsRepository::getTagDetail($tagId);
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
        $result = ComicsRepository::doLove($userId, $comicsId);
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
        $result = ComicsRepository::getLoveList($homeId, $page, 12, $cursor);
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
        $result = ComicsRepository::doFavorite($userId, $comicsId);
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
        $result = ComicsRepository::getFavoriteList($homeId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 添加播放记录
     */
    public function doHistoryAction()
    {
        $userId    = $this->getUserId();
        $comicsId  = $this->getRequest('id', 'string');
        $chapterId = $this->getRequest('chapter_id', 'string');
        $index     = $this->getRequest('index', 'int');
        ComicsRepository::doHistory($userId, $comicsId, $chapterId, $index);
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
        ComicsRepository::delHistory($userId, $audioIds);
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
        $result = ComicsRepository::getHistoryList($userId, $page, 12, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 购买漫画
     * @return void
     * @throws BusinessException
     */
    public function doBuyAction()
    {
        $userId   = $this->getUserId();
        $comicsId = $this->getRequest('id', 'string');
        //        $chapterId = $this->getRequest('chapter_id', 'string');//一般不用单章解锁
        $chapterId = '';
        ComicsRepository::doBuy($userId, $comicsId, $chapterId);
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
        $result = ComicsRepository::getBuyLogList($userId, $page, 20, $cursor);
        $this->sendSuccessResult($result);
    }

    /**
     * 站点地图
     * @return void
     */
    public function sitemapAction()
    {
        $page   = $this->getRequest('page', 'int', 1);
        $result = ComicsRepository::sitemap($page);
        $this->sendSuccessResult($result);
    }
}
