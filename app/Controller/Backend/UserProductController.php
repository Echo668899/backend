<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Repositories\Backend\User\UserProductRepository;

/**
 * 金币套餐
 *
 * @package App\Controller\Backend
 */
class UserProductController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/userProduct');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = UserProductRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('typeArr', CommonValues::getUserProductType());
    }

    /**
     * 详情
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = UserProductRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('typeArr', CommonValues::getUserProductType());
    }

    /**
     * 保存
     * @throws BusinessException
     */
    public function saveAction()
    {
        $token = $this->getToken();

        if (kProdMode) {
            $keyName = "google_check_product_{$token['user_id']}";
            if (!redis()->get($keyName)) {
                $googleCode = $this->getRequest('google_code');
                if (empty($googleCode) || !AdminUserRepository::verifyGoogleCode($googleCode)) {
                    $this->sendErrorResult('谷歌验证码错误!');
                }
                redis()->set($keyName, 1, 300);
            }
        }

        $result = UserProductRepository::save($_POST);
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
                UserProductRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
