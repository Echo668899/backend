<?php

namespace App\Services\Common;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Common\ConfigModel;

class ConfigService extends BaseService
{
    private static $_configs;

    /**
     * 删除缓存
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public static function deleteCache()
    {
        cache()->delete(CacheKey::SYSTEM_CONFIG);
    }

    /**
     * @param             $code
     * @return mixed|null
     */
    public static function getConfig($code)
    {
        $result = self::getAll();
        return $result[$code] ?? null;
    }

    /**
     * 获取所有
     * @return mixed|null
     */
    public static function getAll()
    {
        if (empty(self::$_configs)) {
            self::$_configs = cache()->get(CacheKey::SYSTEM_CONFIG);
        }
        if (empty(self::$_configs)) {
            $items = ConfigModel::find([], [], ['sort' => -1], 0, 1000);
            foreach ($items as $item) {
                self::$_configs[$item['code']] = $item['value'];
            }
            cache()->set(CacheKey::SYSTEM_CONFIG, self::$_configs, 300);
        }
        return self::$_configs;
    }
}
