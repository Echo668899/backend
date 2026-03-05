<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Jobs\Report\ReportAgentV3Job;
use App\Repositories\Backend\Admin\AdminUserRepository;
use App\Repositories\Backend\Report\ReportRepository;
use App\Services\Common\CommonService;
use App\Utils\CommonUtil;

class SystemController extends BaseBackendController
{
    /**
     * 系统主页
     */
    public function homeAction()
    {
        $this->checkPermission('/systemHome');
        $data = ReportRepository::getReportData();
        $this->view->setVar('data', $data);
    }

    /**
     * 小时统计
     * @return void
     */
    public function hourAction()
    {
        $this->checkPermission('/systemHour');
        if ($this->isPost()) {
            $result = ReportRepository::getHour($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 渠道统计//渠道侧
     */
    public function channelAction()
    {
        $this->checkPermission('/systemChannel');
        if ($this->isPost()) {
            $result = ReportRepository::getChannel($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 渠道统计//用户侧
     * 用户邀请也看作一个渠道
     */
    public function userChannelAction()
    {
        $this->checkPermission('/systemUserChannel');
        if ($this->isPost()) {
            $result = ReportRepository::getUserChannel($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->pick('system/channel');
    }

    /**
     * 视频统计
     * @return void
     */
    public function movieAction()
    {
        $this->checkPermission('/systemMovie');
        if ($this->isPost()) {
            $result = ReportRepository::getMovie($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 漫画统计
     * @return void
     */
    public function comicsAction()
    {
        $this->checkPermission('/systemComics');
        if ($this->isPost()) {
            $result = ReportRepository::getComics($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 有声统计
     * @return void
     */
    public function audioAction()
    {
        $this->checkPermission('/systemAudio');
        if ($this->isPost()) {
            $result = ReportRepository::getAudio($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 小说统计
     * @return void
     */
    public function novelAction()
    {
        $this->checkPermission('/systemNovel');
        if ($this->isPost()) {
            $result = ReportRepository::getNovel($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 帖子统计
     * @return void
     */
    public function postAction()
    {
        $this->checkPermission('/systemPost');
        if ($this->isPost()) {
            $result = ReportRepository::getPost($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 广告统计
     * @return void
     */
    public function advAction()
    {
        $this->checkPermission('/systemAdv');
        if ($this->isPost()) {
            $result = ReportRepository::getAdv($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 广告统计
     * @return void
     */
    public function advAppAction()
    {
        $this->checkPermission('/systemAdvApp');
        if ($this->isPost()) {
            $result = ReportRepository::getAdvApp($_REQUEST);
            $this->sendSuccessResult($result);
        }
    }

    /**
     * 系统主题
     */
    public function themeAction()
    {
        $this->view->setMainView('');
    }

    /**
     * 修改用户密码
     */
    public function passwordAction()
    {
        if ($this->isPost()) {
            $oldPassword = $this->getRequest('old_password');
            $newPassword = $this->getRequest('new_password');
            if (empty($oldPassword) || empty($newPassword)) {
                $this->sendErrorResult('参数错误!');
            }
            $result = AdminUserRepository::changePassword($oldPassword, $newPassword);
            if ($result) {
                $this->sendSuccessResult();
            }
            $this->sendErrorResult('修改错误!');
        }
        $this->view->setMainView('');
    }

    /**
     * 运营签到
     * @return void
     */
    public function doAdminSignAction()
    {
        $token = $this->getToken();
        if (kProdMode) {
            ReportAgentV3Job::doAdminLog('checkin', $token['username'], CommonUtil::getClientIp());
        }
        $this->sendSuccessResult();
    }

    /**
     * 清理缓存
     */
    public function clearCacheAction()
    {
        CommonService::clearCache();
        $this->sendSuccessResult();
    }
}
