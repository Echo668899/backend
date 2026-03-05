<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Services\Common\M3u8Service;

/**
 * Class M3u8Controller
 */
class M3u8Controller extends BaseApiController
{
    public function initialize()
    {
        //        header('Access-Control-Allow-Origin: *');
        //        header("Access-Control-Allow-Methods: GET, OPTIONS");
    }

    /**
     * m3u8播放地址
     * @param string $token
     */
    public function pAction($token = '')
    {
        if (empty($token) || strpos($token, '.m3u8') === false) {
            $this->send404();
        }
        $token  = str_replace('.m3u8', '', $token);
        $result = M3u8Service::decode($token);
        if ($result) {
            ob_clean();
            header('content-type: application/vnd.apple.mpegurl');
            header('Content-Length: ' . strlen($result['content']));
            echo $result['content'];
            exit;
        }
        $this->send404();
    }
}
