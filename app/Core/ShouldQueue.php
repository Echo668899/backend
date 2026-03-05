<?php

namespace App\Core;

abstract class ShouldQueue
{
    /**
     * 自定义任务编号
     * @var
     */
    public $_id;

    private $jobDrive;

    /**
     * 执行
     * @param        $_id
     * @return mixed
     */
    abstract public function handler($_id);

    /**
     * 成功
     * @param        $_id
     * @return mixed
     */
    abstract public function success($_id);

    /**
     * 失败
     * @param        $_id
     * @return mixed
     */
    abstract public function error($_id, \Exception $e);

    /**
     * 获取驱动
     * @return mixed
     */
    public function getJobDrive()
    {
        return $this->jobDrive;
    }

    /**
     * 设置驱动
     * @param $drive
     */
    public function setJobDrive($drive)
    {
        $this->jobDrive = $drive;
    }
}
