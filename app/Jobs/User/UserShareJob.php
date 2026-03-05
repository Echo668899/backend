<?php

namespace App\Jobs\User;

use App\Exception\BusinessException;
use App\Jobs\BaseJob;
use App\Models\User\UserModel;
use App\Services\User\UserService;
use App\Services\User\UserShareService;
use Exception;

/**
 * 用户分享任务
 * Class UserShareJob
 * @package App\Jobs\Common
 */
class UserShareJob extends BaseJob
{
    public $userId;
    public $parentId;

    public function __construct($userId, $parentId)
    {
        $this->userId   = $userId;
        $this->parentId = $parentId;
    }

    public function handler($uniqid)
    {
        $userInfo   = UserModel::findByID($this->parentId);
        $actionName = "doTask{$userInfo['share']}";
        // 生成代理表
        //        $this->userAgentService->userMLM($this->userId);
        UserShareService::do($this->parentId, $this->userId, false);// 上级邀请了我,所以这里参数是反的
        //        if (method_exists($this, $actionName)) {
        //            try {
        //                $this->$actionName($userInfo);
        //            } catch (Exception $e) {
        //
        //            }
        //        }
    }

    /**
     * @param                    $userInfo
     * @throws BusinessException
     */
    public function doTask2($userInfo)
    {
        UserService::doChangeGroup($userInfo, 3, 1);
    }

    /**
     * @param                    $userInfo
     * @throws BusinessException
     */
    public function doTask3($userInfo)
    {
        UserService::doChangeGroup($userInfo, 7, 1);
    }

    /**
     * @param                    $userInfo
     * @throws BusinessException
     */
    public function doTask5($userInfo)
    {
        UserService::doChangeGroup($userInfo, 7, 1);
    }

    /**
     * @param                    $userInfo
     * @throws BusinessException
     */
    public function doTask10($userInfo)
    {
        UserService::doChangeGroup($userInfo, 10, 1);
    }

    public function success($uniqid)
    {
        // TODO: Implement success() method.
    }

    public function error($uniqid, Exception $e)
    {
        // TODO: Implement error() method.
    }
}
