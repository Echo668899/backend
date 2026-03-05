<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\User\UserCodeLogRepository;

/**
 * 兑换码管理
 *
 * @package App\Controller\Backend
 */
class UserCodeLogController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/userCodeLog');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = UserCodeLogRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('codeStatus', CommonValues::getUserCodeStatus());
        $this->view->setVar('codeTypes', CommonValues::getUserCodeType());
    }
}
