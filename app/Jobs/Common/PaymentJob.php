<?php

namespace App\Jobs\Common;

use App\Jobs\BaseJob;
use App\Services\Common\PaymentService;

class PaymentJob extends BaseJob
{
    protected $paymentId;

    public function __construct($paymentId)
    {
        $this->paymentId = intval($paymentId);
    }

    public function handler($uniqid)
    {
        PaymentService::doPaidOrder($this->paymentId);
    }

    public function success($uniqid)
    {
    }

    public function error($uniqid, \Exception $e)
    {
    }
}
