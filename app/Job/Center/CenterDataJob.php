<?php

namespace App\Jobs\Center;

use App\Models\Common\AdvAppModel;
use App\Models\Common\AdvModel;
use App\Models\Common\AdvPosModel;
use Phalcon\Manager\Center\CenterDataService;

/**
 * 数据中心
 */
class CenterDataJob extends CenterBaseJob
{

    public function handler($_id)
    {
        $configs = self::getCenterConfig('data');
        CenterDataService::setRedis(redis());
        CenterDataService::onQueue(function ($rows)use($configs){
            if(!empty($configs['push_url'])){
                CenterDataService::doReportCenter("{$configs['push_url']}/api/eventTracking/batchReport.json",$rows);
            }
        });
    }


    public function success($_id)
    {

    }

    public function error($_id, \Exception $e)
    {

    }
}
