<?php

namespace App\Tasks;

use App\Core\BaseTask;
use App\Services\Common\JobService;

/**
 * 任务
 * Class JobTask
 * @package App\Tasks
 */
class JobTask extends BaseTask
{
    /**
     * 消费任务
     * @param                  $queueName
     * @param                  $drive
     * @param                  $serverName
     * @return void
     * @throws \RedisException
     */
    public function queueAction($queueName = 'default', $drive = 'mongodb', $serverName = 'master')
    {
        $runTime   = 297;// 可执行时间/秒
        $startTime = time();
        while (true) {
            if (time() - $startTime >= $runTime) {
                break;
            }
            JobService::onQueue($queueName, $drive, $serverName);
        }
    }
}
