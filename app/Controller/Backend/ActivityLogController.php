<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Repositories\Backend\Activity\ActivityLogRepository;

/**
 * 活动-记录
 */
class ActivityLogController extends BaseBackendController
{
    /**
     * 抽奖记录
     */
    public function lotteryAction()
    {
        $this->checkPermission('/activityLotteryLog');
        if ($this->isPost()) {
            $result = ActivityLogRepository::getLotteryLog($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }
}
