<?php

namespace App\Services\Activity\Handler;

abstract class BaseHandler
{
    /**
     * 唯一标识
     * @return string
     */
    abstract public function code(): string;

    /**
     * 名称
     * @return string
     */
    abstract public function name(): string;

    /**
     * 简介
     * @return string
     */
    abstract public function description(): string;

    /**
     * 配置
     * @return array
     */
    abstract public function schema(): array;

    /**
     * 执行活动逻辑（必须实现）
     */
    abstract public function execute(int $userId, array $activityRow);

    /**
     * 判断用户是否满足参与条件（可重写）
     */
    public function canJoin(int $userId): bool
    {
        return true;
    }
}
