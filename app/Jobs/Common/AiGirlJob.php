<?php

namespace App\Jobs\Common;

use App\Jobs\BaseJob;
use App\Services\Ai\AiGirlService;

/**
 * AI女友的带出任务
 */
class AiGirlJob extends BaseJob
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
            AiGirlService::exit($this->userId);
        } catch (\Exception $e) {
            try {
                AiGirlService::exit($this->userId);
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
