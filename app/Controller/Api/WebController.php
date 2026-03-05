<?php

namespace App\Controller\Api;

use App\Core\Controller\BaseController;
use App\Services\Common\ChannelApkService;
use App\Services\Common\ConfigService;
use App\Services\Common\DomainService;

/**
 * 落地页
 */
class WebController extends BaseController
{
    /**
     * 配置
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public function configAction()
    {
        $configs = ConfigService::getAll();
        $result  = [
            'site_title'  => $configs['site_title'],
            'keywords'    => $configs['keywords'],
            'description' => $configs['description'],
            'site_url'    => $configs['site_url'],

            'service_link'  => $configs['service_link'],
            'service_email' => $configs['service_email'],

            'count_code' => $configs['count_code'], // /全站统计代码

            // 最新域名
            'domains' => value(function () {
                return DomainService::getAll('page');
            }),
            // 下载地址
            'download' => value(function () {
                return ChannelApkService::getAll();
            })
        ];
        // /防止走高防暴露
        $result = base64_encode(json_encode($result));
        $this->sendJson([
            'status' => 'y',
            'data'   => $result
        ]);
    }

    /**
     * 事件
     * {event:"","session_id":"","client_ip":"","channel_code":"","share_code":"","payload":{}}
     * @return void
     */
    public function doEventAction()
    {
    }
}
