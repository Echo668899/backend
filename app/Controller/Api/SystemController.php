<?php

namespace App\Controller\Api;

use App\Controller\BaseApiController;
use App\Repositories\Api\SystemRepository;
use App\Services\Common\AdvAppService;

class SystemController extends BaseApiController
{
    /**
     * @return void
     */
    public function infoAction()
    {
        $userId = $this->getUserId();
        $result = SystemRepository::info($userId, $_REQUEST);
        $this->sendSuccessResult($result);
    }

    /**
     * 数据跟踪
     */
    public function trackAction()
    {
        $userId = $this->getUserId();

        $objectType = $this->getRequest('object_type');
        $objectId   = $this->getRequest('object_id');
        $objectName = $this->getRequest('object_name');
        SystemRepository::addTrackQueue($objectType, $objectId, $objectName, $userId);
        $this->sendSuccessResult();
    }

    /**
     * 应用中心
     * @return void
     */
    public function appStoreAction()
    {
        $result = value(function () {
            $rows = [
                [
                    'code'   => 'all',
                    'name'   => '全部',
                    'groups' => [], // 分组展示
                    'items'  => [],
                ]
            ];
            foreach (AdvAppService::$position as $code => $name) {
                $items = AdvAppService::getAll($code);
                if (empty($items)) {
                    continue;
                }
                $rows[] = [
                    'code'   => $code,
                    'name'   => $name,
                    'groups' => [],
                    'items'  => $items
                ];
                $rows[0]['groups'][] = [
                    'code'  => $code,
                    'name'  => $name,
                    'items' => $items
                ];
            }
            return $rows;
        });
        $this->sendSuccessResult($result);
    }

    /**
     * 获取最新地址
     * @return void
     */
    public function domainsAction()
    {
        $result = SystemRepository::getDomains();
        $this->sendSuccessResult($result);
    }
}
