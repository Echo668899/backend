<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Repositories\Backend\Comics\ComicsNavRepository;

class ComicsNavController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/comicsNav');
    }

    /**
     * @return void
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = ComicsNavRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('posArr', CommonValues::getComicsNavPosition());
        $this->view->setVar('styleArr', CommonValues::getComicsNavStyle());
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
            $result = ComicsNavRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->view->setVar('posArr', CommonValues::getComicsNavPosition());
        $this->view->setVar('styleArr', CommonValues::getComicsNavStyle());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function saveAction()
    {
        $result = ComicsNavRepository::save($_POST);
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
                ComicsNavRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
