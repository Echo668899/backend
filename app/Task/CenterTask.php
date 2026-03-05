<?php

namespace App\Tasks;

use App\Core\BaseTask;
use App\Jobs\Center\CenterAdvJob;
use App\Jobs\Center\CenterDataJob;
use App\Services\Common\JobService;
use App\Utils\LogUtil;

/**
 * 中心任务
 */
class CenterTask extends BaseTask
{
    /**
     * 广告
     * @param $action
     * @return void
     */
    public function advAction($action='sync')
    {
        JobService::create(new CenterAdvJob($action));
    }

    /**
     * 数据
     * @return void
     */
    public function dataAction()
    {
        JobService::create(new CenterDataJob());
    }

}
