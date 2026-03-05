<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Repositories\Api\SystemRepository;

/**
 * Class ServerController
 * @package App\Controller\Api
 */
class ServerController extends BaseApiController
{
    public function initialize()
    {
    }

    /**
     * 系统检测
     */
    public function checkAction()
    {
        $result = SystemRepository::getServerStatus();
        $this->sendJson($result);
    }

    /**
     * 词库
     * @return void
     */
    public function ikAction()
    {
        try {
            $rows = '';
            foreach (glob(APP_PATH . '/Resource/dic/*.dic') as $file) {
                if (strpos($file, 'common') !== false) {
                    continue;
                }
                $rows .= file_get_contents($file);
            }
            echo $rows;
        } catch (\Exception $e) {
            echo '';
        }
    }
}
