<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Repositories\Api\DanmakuRepository;

/**
 * 弹幕
 */
class DanmakuController extends BaseApiController
{
    /**
     * 彈幕留言
     * @throws \App\Exception\BusinessException
     */
    public function doAction()
    {
        $userId     = $this->getUserId();
        $objectId   = $this->getRequest('object_id', 'string');
        $subId      = $this->getRequest('sub_id', 'string');
        $objectType = $this->getRequest('object_type', 'string');
        $pos        = $this->getRequest('pos', 'string');
        $size       = $this->getRequest('size', 'string', '12');
        $color      = $this->getRequest('color', 'string', '16777215');
        $pool       = $this->getRequest('pool', 'string', '0');
        $content    = $this->getRequest('content', 'string');

        $result = DanmakuRepository::do($userId, $objectId, $objectType, $pos, $size, $color, $pool, $content, $subId);
        $this->sendSuccessResult($result ? 'y' : 'n');
    }

    /**
     * 弹幕列表
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function listAction()
    {
        $objectId   = $this->getRequest('object_id', 'string');
        $subId      = $this->getRequest('sub_id', 'string');
        $objectType = $this->getRequest('object_type', 'string');
        $startPos   = $this->getRequest('start_pos', 'int');
        $endPos     = $this->getRequest('end_pos', 'int');
        $result     = DanmakuRepository::getList($objectId, $objectType, $startPos, $endPos, $subId);
        $this->sendSuccessResult($result);
    }
}
