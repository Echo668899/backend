<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Movie\MovieBlockRepository;
use App\Services\Movie\MovieNavService;

/**
 * 视频模块管理
 *
 * @package App\Controller\Backend
 */
class MovieBlockController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/movieBlock');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = MovieBlockRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->initData();
    }

    /**
     * 详情
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = MovieBlockRepository::getDetail($id);
            $this->view->setVar('row', $result);
        }
        $this->initData();
    }

    /**
     * 保存
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = MovieBlockRepository::save($_POST);
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
                MovieBlockRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }

    protected function initData()
    {
        $this->view->setVar('posArr', MovieNavService::getAll());
        $this->view->setVar('styleArr', CommonValues::getMovieBlockStyle());
        $this->view->setVar('disArr', CommonValues::getIs());
        $this->view->setVar('routeArr', CommonValues::getMovieBlockRoute());
    }
}
