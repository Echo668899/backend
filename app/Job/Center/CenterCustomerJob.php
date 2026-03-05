<?php

namespace App\Jobs\Center;

use Phalcon\Manager\Center\CenterDataService;

/**
 * 客服中心
 * 无逻辑,主要是需要基础类获取配置
 */
class CenterCustomerJob extends CenterBaseJob
{

    public function handler($_id)
    {

    }


    public function success($_id)
    {

    }

    public function error($_id, \Exception $e)
    {

    }
}
