<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Repositories\Backend\Common\LogsRepository;

class LogsController extends BaseBackendController
{
    /**
     * 管理员日志
     */
    public function adminAction()
    {
        if ($this->isPost()) {
            $result = LogsRepository::getAdminList($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 短信日志
     */
    public function smsAction()
    {
        if ($this->isPost()) {
            $result = LogsRepository::getSmsList($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }
}
