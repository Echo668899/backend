<?php

declare(strict_types=1);

namespace App\Controller\Backend;

use App\Constants\CommonValues;
use App\Controller\BaseBackendController;
use App\Exception\BusinessException;
use App\Repositories\Backend\Movie\MovieCategoryRepository;

/**
 * 视频分类
 *
 * @package App\Controller\Backend
 */
class MovieCategoryController extends BaseBackendController
{
    /**
     * 初始化
     */
    public function initialize()
    {
        parent::initialize();
        $this->checkPermission('/movieCategory');
    }

    /**
     * 列表
     */
    public function listAction()
    {
        if ($this->isPost()) {
            $result = MovieCategoryRepository::getList($_REQUEST);
            $this->sendSuccessResult($result);
        }
        $this->view->setVar('hotArr', CommonValues::getIs());
        $this->view->setVar('posArr', CommonValues::getMovieCategoryPosition());
    }

    /**
     * 详情
     * @throws BusinessException
     */
    public function detailAction()
    {
        $id = $this->getRequest('_id');
        if (!empty($id)) {
            $result = MovieCategoryRepository::getDetail($id);
            $this->view->setVar('row', json_encode($result));
        }
        $this->view->setVar('categories', []);
        $this->view->setVar('hotArr', CommonValues::getIs());
        $this->view->setVar('posArr', CommonValues::getMovieCategoryPosition());
    }

    /**
     * 保存
     * @throws BusinessException
     */
    public function saveAction()
    {
        $result = MovieCategoryRepository::save($_POST);
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
                MovieCategoryRepository::delete($id);
            }
        }
        $this->sendSuccessResult();
    }
}
