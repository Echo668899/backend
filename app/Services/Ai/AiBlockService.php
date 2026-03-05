<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Ai\AiBlockModel;
use App\Utils\CommonUtil;

class AiBlockService extends BaseService
{
    /**
     * 获取所有模块
     * @param                             $navId
     * @param                             $page
     * @param                             $pageSize
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function get($navId, $page = 1, $pageSize = 8)
    {
        $keyName = CacheKey::AI_BLOCK;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query = [
                'is_disabled' => 0
            ];
            $result = AiBlockModel::find($query, [], ['sort' => -1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if ($item['nav_id'] != $navId) {
                continue;
            }
            $rows[] = [
                'id'       => strval($item['_id']),
                'name'     => strval($item['name']),
                'sub_name' => strval($item['sub_name']),
                'filter'   => json_decode($item['filter'], true),
                'style'    => strval($item['style']),
                'num'      => strval($item['num']),
                'icon'     => strval($item['icon']),
            ];
        }
        return CommonUtil::arrayPage($rows, $page, $pageSize);
    }
}
