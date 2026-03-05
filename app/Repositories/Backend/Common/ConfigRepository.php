<?php

namespace App\Repositories\Backend\Common;

use App\Core\Repositories\BaseRepository;
use App\Models\Common\ConfigModel;
use App\Services\Common\ConfigService;

class ConfigRepository extends BaseRepository
{
    /**
     * 获取分组列表
     * @param  string $group
     * @return mixed
     */
    public static function getGroupList(string $group)
    {
        return ConfigModel::find(['group' => $group], [], ['sort' => -1], 0, 1000);
    }

    /**
     * @param $items
     */
    public static function save($items)
    {
        foreach ($items as $code => $value) {
            ConfigModel::update(['value' => $value], ['code' => $code]);
        }
        ConfigService::deleteCache();
    }
}
