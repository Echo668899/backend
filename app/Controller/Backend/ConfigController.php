<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Repositories\Backend\Common\ConfigRepository;

class ConfigController extends BaseBackendController
{
    /**
     * 基础配置
     */
    public function baseAction()
    {
        $this->checkPermission('/configBase');
        $this->initData('base');
    }

    /**
     * 高级配置
     */
    public function otherAction()
    {
        $this->checkPermission('/configOther');
        $this->initData('other');
    }

    /**
     * App配置
     */
    public function appAction()
    {
        $this->checkPermission('/configApp');
        $this->initData('app');
    }

    /**
     * APK配置
     */
    public function apkAction()
    {
        $this->checkPermission('/configApk');
        $this->initData('apk');
    }

    /**
     * CDN配置
     */
    public function cdnAction()
    {
        $this->checkPermission('/configCdn');
        $this->initData('cdn');
    }

    /**
     * Ai配置
     */
    public function aiAction()
    {
        $this->checkPermission('/configAi');
        $this->initData('ai');
    }

    /**
     * Center配置
     */
    public function centerAction()
    {
        $this->checkPermission('/configCenter');
        $this->initData('center');
    }

    /**
     * 保存
     */
    public function saveAction()
    {
        ConfigRepository::save($_POST);
        $this->sendSuccessResult();
    }

    /**
     * 初始化数据
     * @param $group
     */
    protected function initData($group)
    {
        $result = ConfigRepository::getGroupList($group);
        $this->view->setVar('items', $result);
        $this->view->render('config', 'info');
    }
}
