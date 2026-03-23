<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\UserRepository;
use App\Repositories\Api\MovieCusRepository;
use App\Repositories\Api\UserUpRepository;

class UserUpController extends BaseApiController
{

    /**
     * up主搜索
     * @return void
     */
    public function searchAction(){
        $userId = $this->getUserId();
        $kw = $this->getRequest('kw');
        $page = $this->getRequest('page','int',1);
        $pageSize = $this->getRequest('pageSize','int',24);
        $res = UserUpRepository::search($kw, $userId, $page, $pageSize);
        $this->sendSuccessResult($res);
    }

    /**
     * up主list
     * @return void
     */
    public function listAction(){
        $userId = $this->getUserId();
        $order = $this->getRequest('order', 'string');
        $page = $this->getRequest('page','int',1);
        $pageSize = $this->getRequest('pageSize','int',24);
        $res = UserUpRepository::list($userId, $order, $page, $pageSize);
        $this->sendSuccessResult($res);
    }
}