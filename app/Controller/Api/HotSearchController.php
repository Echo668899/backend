<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\UserRepository;
use App\Repositories\Api\MovieCusRepository;

class HotSearchController extends BaseApiController
{
    /**
     * 热搜列表
     */
    public function listAction(){
        $type = $this->getRequest('type', 'string');
        $page = $this->getRequest('page','int',1);
        $pageSize = $this->getRequest('pageSize','int',24);
        $res = MovieCusRepository::hotSearch($type, $page, $pageSize);
        $this->sendSuccessResult($res);
    }
}