<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Jobs\Report\ReportAgentV3Job;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Utils\CommonUtil;


/**
 * Class LoginController
 * @package App\Controller\Backend
 */
class LoginController extends BaseBackendController
{
    /**
     * 登录
     */
    public function indexAction()
    {
        $token = $this->getToken();
        if (!empty($token)) {
            $this->redirect('/index');
        }
    }

    /**
     * 登录系统
     * @throws BusinessException
     */
    public function doAction()
    {
        $username = $this->getRequest("username");
        $password = $this->getRequest("password");
        $googleCode = $this->getRequest("google_code");
        if (empty($username) || empty($password) || empty($googleCode)) {
            $this->sendErrorResult("参数错误!");
        }
        $token = AdminUserRepository::login($username, $password, $googleCode);
        if ($token) {
            if(kProdMode){
                ReportAgentV3Job::doAdminLog('login',$username,CommonUtil::getClientIp());
            }
            $this->sendSuccessResult($token);
        }
        $this->sendErrorResult("登陆失败!");
    }

    /**
     * 登录
     */
    public function exitAction()
    {
        AdminUserRepository::logout();
        $this->redirect('/login');
    }
}
