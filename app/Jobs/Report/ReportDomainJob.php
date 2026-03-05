<?php

namespace App\Jobs\Report;

use App\Jobs\BaseJob;
use App\Models\Common\ConfigModel;
use App\Models\Common\DomainModel;
use App\Services\Common\ConfigService;
use App\Utils\LogUtil;
use Phalcon\Manager\AgentV3Service;

/**
 * 上报域名
 */
class ReportDomainJob extends BaseJob
{
    /** @var AgentV3Service */
    public $agentService;
    /**
     * 域名类型映射 getDomainType
     * @var string[]
     */
    private $mapping = [
        'main' => 'web',
        'site' => 'channel_web',
        'nav'  => 'channel_web',
        'api'  => 'api',
    ];

    public function __construct()
    {
        $agentUrl           = ConfigService::getConfig('channel_system_url');
        $agentId            = ConfigService::getConfig('channel_system_app_id');
        $agentKey           = ConfigService::getConfig('channel_system_app_key');
        $this->agentService = new AgentV3Service($agentUrl, $agentId, $agentKey);
    }

    public function handler($_id)
    {
        $this->ip();

        $query     = ['is_disabled' => 0];
        $count     = DomainModel::count($query);
        $pageSize  = 200;
        $totalPage = ceil($count / $pageSize);
        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $pageSize;
            $items = DomainModel::find($query, [], ['_id' => -1], $skip, $pageSize);

            $this->push($items);
            LogUtil::info(sprintf(__CLASS__ . ' push %s/%s ok', $page, $totalPage));
            $this->pull($items);
            LogUtil::info(sprintf(__CLASS__ . ' pull %s/%s ok', $page, $totalPage));
        }
    }

    /**
     * 同步ip白名单
     * @return void
     */
    public function ip()
    {
        // 同步白名单
        try {
            $ips = $this->agentService->pullAdminIp();
            ConfigModel::update(['value' => join("\r\n", $ips)], ['code' => 'whitelist_ip']);
            ConfigService::deleteCache();
        } catch (\Exception $e) {
            LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
    }

    /**
     * 推送域名
     * @param             $items
     * @return void
     * @throws \Exception
     */
    public function push($items)
    {
        foreach ($items as &$item) {
            $item = [
                'domain'   => preg_replace('#^https?://#i', '', $item['domain']),
                'type'     => $this->mapping[$item['type']],
                'is_https' => 1,
                // -1 禁用,0正常
                'status' => value(function () use ($item) {
                    if ($item['is_disabled'] == 1) {
                        return -1;
                    }
                    return 0;
                }),
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
            ];
            unset($item);
        }
        if (empty($items)) {
            return;
        }
        $this->agentService->pushDomain($items);
    }

    /**
     * 同步域名检测情况
     * @param             $items
     * @return void
     * @throws \Exception
     */
    public function pull($items)
    {
        foreach ($items as &$item) {
            $item['domain'] = preg_replace('#^https?://#i', '', $item['domain']);
            unset($item);
        }
        $items  = array_column($items, 'domain');
        $result = $this->agentService->pullDomain($items);
        foreach ($result['items'] as $item) {
            $check = [];
            foreach ($result['cities'] as $city) {
                $check[] = [
                    'city'   => $city,
                    'status' => $item[$city] == '0' ? '正常' : '异常',
                ];
            }
            if (!empty($check)) {
                // /不更新时间
                DomainModel::updateRaw(['$set' => [
                    'check' => $check
                ]], ['domain' => $item['domain']]);
            }
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
