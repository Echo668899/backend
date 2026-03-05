<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Core\ShouldQueue;
use App\Models\Common\JobModel;
use App\Utils\LogUtil;

class JobService extends BaseService
{
    public const StatusWait = 0;
    public const StatusRun  = 1;
    public const StatusFail = -1;

    public static function create(ShouldQueue $job, $queueName = 'default', $drive = 'sync', int $planAt = 0, string $serverName = 'master')
    {
        $job->setJobDrive($drive);
        $data = [
            '_id'         => $job->_id ?: self::getUniqid($job),
            'queue'       => $queueName,
            'server_name' => $serverName,
            'job'         => serialize($job),
            'exception'   => '',
            'status'      => self::StatusWait,
            'failed_at'   => null,
            'plan_at'     => $planAt ?: time(),
        ];
        if ($drive == 'mongodb') {
            if ($job->_id) {
                // 防重复
                if (JobModel::findByID($job->_id)) {
                    return;
                }
            }
            JobModel::insert($data, false);
        } elseif ($drive == 'redis') {
            redis()->lPush("job_task_{$queueName}_{$serverName}", json_encode($data));
        } else {
            self::executeQueue($job, $data['_id']);
        }
    }

    /**
     * 消费队列
     * @param  string          $queueName
     * @param  string          $drive
     * @param  string          $serverName
     * @return bool|void
     * @throws \RedisException
     */
    public static function onQueue(string $queueName, string $drive, string $serverName)
    {
        if ($drive == 'mongodb') {
            $row = JobModel::findAndModify([
                'queue'       => $queueName,
                'server_name' => $serverName,
                'status'      => self::StatusWait,
                'plan_at'     => ['$lte' => time()]
            ], ['$set' => ['status' => self::StatusRun, 'exception' => '']]);
        } elseif ($drive == 'redis') {
            $row = redis()->rPop("job_task_{$queueName}_{$serverName}");
            $row = json_decode($row, true);
        } else {
            return false;
        }
        if (empty($row)) {
            usleep(2 * 1000 * 1000);
            return true;
        }
        $jobClass = unserialize($row['job']);
        self::executeQueue($jobClass, $row['_id']);
    }

    /**
     * 生成唯一值
     * @param  ShouldQueue $job
     * @return string
     */
    private static function getUniqid(ShouldQueue $job)
    {
        return get_class($job) . '_' . (microtime(true) * 10000);
    }

    /**
     * 执行队列
     * @param ShouldQueue $jobClass
     * @param             $_id
     */
    private static function executeQueue(ShouldQueue $jobClass, $_id)
    {
        $jobDrive = $jobClass->getJobDrive();
        try {
            call_user_func_array([$jobClass, 'handler'], [$_id]);
            LogUtil::info(sprintf('Queue: %s=>success', $_id));
            if ($jobDrive == 'mongodb') {
                JobModel::deleteById($_id);
            }
            call_user_func_array([$jobClass, 'success'], [$_id]);
        } catch (\Exception $e) {
            LogUtil::error(sprintf('Queue: %s %s in %s line %s', $_id, $e->getMessage(), $e->getFile(), $e->getLine()));
            if ($jobDrive == 'mongodb') {
                if (JobModel::count(['_id' => $_id])) {
                    JobModel::update(['exception' => serialize($e), 'failed_at' => time(), 'status' => self::StatusFail], ['_id' => $_id]);
                }
            }
            call_user_func_array([$jobClass, 'error'], [$_id, $e]);
        }
    }
}
