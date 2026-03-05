<?php

namespace App\Tasks;

use App\Core\BaseTask;
use App\Jobs\Center\CenterAdvJob;
use App\Jobs\Center\CenterDataJob;
use App\Services\Common\JobService;

/**
 * 中心任务
 */
class CenterTask extends BaseTask
{
    /**
     * 广告
     * @param       $action
     * @return void
     */
    public function advAction($action = 'sync')
    {
        JobService::create(new CenterAdvJob($action));
    }

    /**
     * 数据中心-埋点队列
     * @return void
     */
    public function dataAction()
    {
        JobService::create(new CenterDataJob('queue'));
    }

    /**
     * 数据中心-对账推送
     * @return void
     */
    public function dataReportAction()
    {
        JobService::create(new CenterDataJob('report'));
    }
}
