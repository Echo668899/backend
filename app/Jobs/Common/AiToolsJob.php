<?php

namespace App\Jobs\Common;

use App\Jobs\BaseJob;
use App\Services\Ai\AiToolsService;

/**
 * AI工具的带出任务
 */
class AiToolsJob extends BaseJob
{
    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handler($_id)
    {
        // /尝试2次
        try {
            AiToolsService::exit($this->userId);
        } catch (\Exception $e) {
            try {
                AiToolsService::exit($this->userId);
            } catch (\Exception $e) {
            }
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
