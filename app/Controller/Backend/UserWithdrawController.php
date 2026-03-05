<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Repositories\Backend\User\UserWithdrawRepository;

/**
 * 用户提现管理
 *
 * @package App\Controller\Backend
 */
class UserWithdrawController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/userWithdraw');
    }

    /**
     * 金币提现列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $_REQUEST['record_type'] = 'point';
            $result                  = UserWithdrawRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('recordType', 'point');
        $this->initData();
    }

    /**
     * 金币提现详情 point
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (empty($id)) {
            $this->sendErrorResult('参数错误');
        }
        $row = UserWithdrawRepository::getDetail($id);
        $this->view->setVar('row', $row);
        $this->initData();
    }

    /**
     * 金币提现操作 point
     * @return void
     * @throws BusinessException
     * @throws \RedisException
     */
    public function doAction()
    {
        $token = $this->getToken();
        if (kProdMode) {
            $keyName = "google_check_withdraw_{$token['user_id']}";
            if (!redis()->get($keyName)) {
                $googleCode = $this->getRequest('google_code');
                if (empty($googleCode) || !AdminUserRepository::verifyGoogleCode($googleCode)) {
                    $this->sendErrorResult('谷歌验证码错误!');
                }
                redis()->set($keyName, 1, 300);
            }
        }
        $result = UserWithdrawRepository::save($_POST);
        if ($result) {
            $this->sendSuccessResult();
        }
        $this->sendErrorResult('保存错误!');
    }

    protected function initData()
    {
        $this->view->setVars([
            'statusArr' => CommonValues::getWithdrawStatus(),
            'methodArr' => CommonValues::getWithdrawMethod(),
        ]);
    }
}
