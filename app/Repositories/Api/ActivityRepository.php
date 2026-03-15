<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Services\Activity\ActivityService;

class ActivityRepository extends BaseRepository
{
    public static function list($scope = 'all'){
        $code = '';
        $userIsVip = null;
        $list = ActivityService::getAll($code, $userIsVip);
        return $list;
    }
}