<?php

namespace App\Tasks;

use App\Core\BaseTask;
use App\Jobs\Center\CenterAdvJob;
use App\Jobs\Center\CenterDataJob;
use App\Jobs\Center\CenterInfraJob;
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
     * 数据中心
     * @return void
     */
    public function dataAction($action='queue')
    {
        JobService::create(new CenterDataJob($action));
    }

    /**
     * 每日报表
     * 上报到infra_php
     * @return void
     */
    public function infraAction($action='report')
    {
        JobService::create(new CenterInfraJob($action));
    }
}
