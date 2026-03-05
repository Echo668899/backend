<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Repositories\Backend\Common\AdvRepository;
use App\Services\Common\AdvPosService;

class AdvController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/adv');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AdvRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('posArr', AdvPosService::getAll());
        $this->view->setVar('typeArr', CommonValues::getAdType());
        $this->view->setVar('disArr', CommonValues::getIs());
    }

    /**
     * 详情
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = AdvRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('posArr', AdvPosService::getAll());
        $this->view->setVar('typeArr', CommonValues::getAdType());
        $this->view->setVar('disArr', CommonValues::getIs());
        $this->view->setVar('protocolArr', CommonValues::getAdvProtocol());
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        $token = $this->getToken();

        if (kProdMode) {
            $keyName = "google_check_adv_{$token['user_id']}";
            if (!redis()->get($keyName)) {
                $googleCode = $this->getRequest('google_code');
                if (empty($googleCode) || !AdminUserRepository::verifyGoogleCode($googleCode)) {
                    $this->sendErrorResult('谷歌验证码错误!');
                }
                redis()->set($keyName, 1, 300);
            }
        }

        $result = AdvRepository::save($_POST);
        if ($result) {
            $this->sendSuccessResult();
        }
        $this->sendErrorResult('保存错误!');
    }

    /**
     * 批量操作
     */
    public function doAction()
    {
        $idStr = $this->getRequest('id');
        $act   = $this->getRequest('act');
        if (empty($idStr) || empty($act)) {
            $this->sendErrorResult('操作错误!');
        }
        if ($act == 'del') {
            $ids = explode(',', $idStr);
            foreach ($ids as $id) {
                AdvRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
