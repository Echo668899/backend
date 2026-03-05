<?php

namespace App\Controller\Backend;

use App\Controller\BaseBackendController;
use App\Repositories\Backend\Common\ChannelApkRepository;

/**
 * 渠道包
 */
class ChannelApkController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/channelApk');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = ChannelApkRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
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
            $result = ChannelApkRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
    }

    /**
     * 保存
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function saveAction()
    {
        $result = ChannelApkRepository::save($_POST);
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
                ChannelApkRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
