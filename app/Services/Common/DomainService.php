<?php

namespace App\Services\Common;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Common\DomainModel;
use Phalcon\Storage\Exception;

/**
 * 域名
 */
class DomainService extends BaseService
{
    /**
     * 删除缓存
     * @return void
     * @throws Exception
     */
    public static function deleteCache()
    {
        cache()->delete(CacheKey::DOMAIN);
    }

    /**
     * 获取所有
     * @param                             $type
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function getAll($type = '')
    {
        $keyName = CacheKey::DOMAIN;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $result = DomainModel::find(['is_disabled' => 0], [], ['created_at' => -1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if (!empty($type) && $item['type'] != $type) {
                continue;
            }
            $rows[] = [
                'domain'        => strval($item['domain']),
                'tracking_code' => strval(stripslashes($item['tracking_code'])),
            ];
        }
        return $rows;
    }
}
