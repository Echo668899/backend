<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Services\Activity\ActivityService;
use App\Repositories\Api\UserRepository;

class ActivityRepository extends BaseRepository
{
    public static function list($userId = null, $scope = 'all'){
        $code = '';
        $userIsVip = null;

        if($userId){
            $userInfo = UserRepository::getInfo($userId);
            $userIsVip = $userInfo['is_vip']; //$userInfo['is_vip'] == 'y' 
        }

        $list = ActivityService::getAll($code, $userIsVip);
        return $list;
    }
}