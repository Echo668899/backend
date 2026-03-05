<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\AiRepository;

class AiController extends BaseApiController
{
    /**
     * @return void
     */
    public function configAction()
    {
        $result = AiRepository::getConfig();
        $this->sendSuccessResult($result);
    }

    /**
     * 获取顶部菜单,根据type
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public function navFilterAction()
    {
        $type   = $this->getRequest('type', 'int');
        $result = AiRepository::navFilter($type);
        $this->sendSuccessResult($result);
    }

    /**
     * 获取模板
     * @return void
     */
    public function tplAction()
    {
        $type   = $this->getRequest('type');
        $result = AiRepository::getTpl($type, $_REQUEST);
        $this->sendSuccessResult($result);
    }

    /**
     * 提示词
     * @return void
     */
    public function tipsAction()
    {
        $type   = $this->getRequest('type');
        $result = AiRepository::getTips($type);
        $this->sendSuccessResult($result);
    }

    /**
     * 图生视频
     * @return void
     */
    public function doImageToVideoAction()
    {
        $userId  = $this->getUserId();
        $orderId = AiRepository::doImageToVideo($userId, $_REQUEST);
        $this->sendSuccessResult($orderId);
    }

    /**
     * 文转语音
     * @return void
     */
    public function doTextToVoiceAction()
    {
        $userId  = $this->getUserId();
        $orderId = AiRepository::doTextToVoice($userId, $_REQUEST);
        $this->sendSuccessResult($orderId);
    }

    /**
     * 文字生成图片
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doTextToImageAction()
    {
        $userId  = $this->getUserId();
        $orderId = AiRepository::doTextToImage($userId, $_REQUEST);
        $this->sendSuccessResult($orderId);
    }

    /**
     * 换脸
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doChangeFaceAction()
    {
        $userId  = $this->getUserId();
        $orderId = AiRepository::doChangeFace($userId, $_REQUEST);
        $this->sendSuccessResult($orderId);
    }

    /**
     * 小说
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doNovelAction()
    {
        $userId  = $this->getUserId();
        $orderId = AiRepository::doNovel($userId, $_REQUEST);
        $this->sendSuccessResult($orderId);
    }

    /**
     * 换装
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doChangeDressAction()
    {
        $userId  = $this->getUserId();
        $orderId = AiRepository::doChangeDress($userId, $_REQUEST);
        $this->sendSuccessResult($orderId);
    }

    /**
     * 去衣
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doChangeDressBareAction()
    {
        $userId  = $this->getUserId();
        $orderId = AiRepository::doChangeDressBare($userId, $_REQUEST);
        $this->sendSuccessResult($orderId);
    }

    /**
     * 任务列表
     * @return void
     */
    public function taskAction()
    {
        $userId = $this->getUserId();
        $homeId = $this->getRequest('home_id', 'int');
        $page   = $this->getRequest('page', 'int');
        $type   = $this->getRequest('type', 'string');
        $result = AiRepository::getTaskList($userId, $homeId, $type, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * 查看任务进度
     * @return void
     * @throws BusinessException
     */
    public function taskInfoAction()
    {
        $userId  = $this->getUserId();
        $orderId = $this->getRequest('id', 'int');
        $result  = AiRepository::getTaskInfo($userId, $orderId);
        $this->sendSuccessResult($result);
    }

    /**
     * 作品详情
     * @return void
     * @throws BusinessException|\Exception
     */
    public function detailAction()
    {
        $userId  = $this->getUserId(false);
        $orderId = $this->getRequest('id', 'string');
        $result  = AiRepository::getDetail($userId, $orderId);
        $this->sendSuccessResult($result);
    }

    /**
     * 去点赞
     * @throws BusinessException
     */
    public function doLoveAction()
    {
        $userId = $this->getUserId();
        $id     = $this->getRequest('id', 'int');
        if (empty($id)) {
            $this->sendErrorResult('参数错误');
        }
        $result = AiRepository::doLove($userId, $id);
        $this->sendSuccessResult(['status' => $result ? 'y' : 'n']);
    }

    /**
     * 点赞列表
     * @return void
     */
    public function loveAction()
    {
        $userId = $this->getUserId();
        $homeId = $this->getRequest('id', 'int');
        $page   = $this->getRequest('page', 'int', 1);

        // /看他人的视频
        if (!empty($homeId)) {
            $userId = $homeId;
        }
        $result = AiRepository::getLoveList($userId, $page);
        $this->sendSuccessResult($result);
    }

    /**
     * 去收藏
     * @throws BusinessException
     */
    public function doFavoriteAction()
    {
        $userId = $this->getUserId();
        $id     = $this->getRequest('id', 'int');
        if (empty($id)) {
            $this->sendErrorResult('参数错误');
        }
        $result = AiRepository::doFavorite($userId, $id);
        $this->sendSuccessResult(['status' => $result ? 'y' : 'n']);
    }

    /**
     * 收藏列表
     * @return void
     */
    public function favoriteAction()
    {
        $userId   = $this->getUserId();
        $homeId   = $this->getRequest('id', 'int');
        $folderId = $this->getRequest('folder_id', 'string');
        $page     = $this->getRequest('page', 'int', 1);

        // /看他人的视频
        if (!empty($homeId)) {
            $userId = $homeId;
        }
        $result = AiRepository::getFavoriteList($userId, $page, $folderId);
        $this->sendSuccessResult($result);
    }

    /**
     * @return void
     */
    public function doDelAction()
    {
        $userId   = $this->getUserId();
        $orderIds = $this->getRequest('ids');// 逗号分割或是all
        AiRepository::delOrder($userId, $orderIds);
        $this->sendSuccessResult();
    }
}
