<?php

namespace App\Controller;

use App\Core\Controller\BaseController;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\AdminUserService;
use App\Services\Common\ConfigService;

class BaseBackendController extends BaseController
{
    public const NO_LOGIN_CONTROLLERS = [
        '\App\Controller\Backend\LoginController'
    ];
    private $token;

    /**
     * 初始化
     */
    public function initialize()
    {
        $this->view->setMainView('./layouts/main');
        $this->data();
        $this->login();
        $this->_addAdminLog();
    }

    /**
     * 获取token
     * @return array
     */
    protected function getToken()
    {
        if (empty($this->token)) {
            $this->token = AdminUserService::getToken();
        }
        return $this->token;
    }

    /**
     * 检查权限
     * @param $path
     */
    protected function checkPermission($path)
    {
        if (!AdminUserRepository::checkPermission($path)) {
            $this->sendErrorResult('无权操作!');
        }
    }

    private function data()
    {
        $this->view->setVar('APP_NAME', env()->path('app.name'));
        $this->token = $this->getToken();
        $configs     = ConfigService::getAll();
        $this->view->setVar('configs', $configs);
        $this->view->setVar('mediaUrl', $configs['media_url']);
        $this->view->setVar('mediaUrlVideo', $configs['media_url_video']);

        $this->view->setVar('uploadUrl', $configs['upload_url'] . '/tools/upload?key=' . $configs['upload_key']);
        $this->view->setVar('uploadVideoUrl', $configs['upload_url'] . '/upload/byte?key=' . $configs['upload_key']);
        $this->view->setVar('uploadVideoQueryUrl', $configs['upload_url'] . '/upload/query?key=' . $configs['upload_key']);

        $this->view->setVar('systemName', $configs['system_name']);
        $this->view->setVar('username', $this->token['username']);
    }

    private function login()
    {
        if (!in_array(dispatcher()->getControllerClass(), self::NO_LOGIN_CONTROLLERS)) {
            if (empty($this->token)/* || $this->token['ip'] != $ip  //bug 不能判断ip 会重复重定向 */) {
                $this->redirect('/login');
            }
        }
    }

    /**
     * @return void
     */
    private function _addAdminLog()
    {
        if (!$this->isPost()) {
            return;
        }
        $dispatcher     = container()->get('dispatcher');
        $controllerName = $dispatcher->getControllerName();
        $actionName     = $dispatcher->getActionName();
        if (in_array($controllerName, ['feedback'])) {
            return;
        }
        if (in_array($actionName, ['save', 'do'])) {
            $params = $_REQUEST;
            AdminLogService::do(json_encode($params, JSON_UNESCAPED_UNICODE));
        }
    }
}
