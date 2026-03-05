<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Services\Common\DomainService;

/**
 * h5k线路检查
 */
class LineController extends BaseApiController
{
    public function initialize()
    {
    }

    /**
     * 1.获取线路
     */
    public function indexAction()
    {
        $rows = DomainService::getAll('h5');
        $rows = array_column($rows, 'domain');
        $id   = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
        $data = [
            'url' => strval($rows[$id])
        ];
        if ($_REQUEST['callback']) {
            ob_clean();
            echo $_REQUEST['callback'] . '(' . json_encode($data) . ')';
            exit;
        }
        $this->sendJson($data);
    }

    /**
     * 2.ping
     */
    public function pingAction()
    {
        $data = [
            'status' => 'y'
        ];
        if ($_REQUEST['callback']) {
            ob_clean();
            echo $_REQUEST['callback'] . '(' . json_encode($data) . ')';
            exit;
        }
        $this->sendJson($data);
    }
}
