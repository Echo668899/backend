<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Exception\BusinessException;
use App\Repositories\Api\ActivityRepository;

class ActivityController extends BaseApiController
{
    /**
     * 获取activity
     * @return void
     */
    public function listAction()
    {
        $scope = $this->getRequest('scope', 'string', 'all');
        $result = ActivityRepository::list($scope);
        $this->sendSuccessResult($result);
    }
}