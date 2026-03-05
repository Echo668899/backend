<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\User\AccountRepository;

/**
 * Class AccountLogsController
 * @package App\Controller\Backend
 */
class AccountController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/account');
    }

    /**
     * 账号余额日志
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AccountRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('fieldArr', CommonValues::getBalanceField());
    }
}
