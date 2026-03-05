<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Audio\AudioNavRepository;

class AudioNavController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/audioNav');
    }

    /**
     * @return void
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = AudioNavRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('posArr', CommonValues::getAudioNavPosition());
        $this->view->setVar('styleArr', CommonValues::getAudioNavStyle());
    }

    /**
     * 详情
     * @return void
     * @throws \Exception
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = AudioNavRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('posArr', CommonValues::getAudioNavPosition());
        $this->view->setVar('styleArr', CommonValues::getAudioNavStyle());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function saveAction()
    {
        $result = AudioNavRepository::save($_POST);
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
                AudioNavRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
