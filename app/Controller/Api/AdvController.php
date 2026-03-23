<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\AdvRepository;

class AdvController extends BaseApiController
{
    /**
     * 广告位列表
     */
    public function listAction(){
        $code = $this->getRequest('code', 'string');
        $limit = $this->getRequest('limit', 'int', 20);
        $result = AdvRepository::getListByCode($code, $limit);
        $this->sendSuccessResult($result);
    }

    /**
     * 首页顶部模块列表
     */
    public function blockListAction(){
        $type = $this->getRequest('type', 'string');
        $list = AdvRepository::getBlockList($type);
        $this->sendSuccessResult($list);
    }

    /**
     * 应用列表
     */
    public function appListAction(){
        $list = AdvRepository::appList();
        $this->sendSuccessResult($list);
    }
}