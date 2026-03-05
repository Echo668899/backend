<?php

namespace App\Services\Ai;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Ai\AiTplModel;
use App\Services\Common\CommonService;
use App\Services\Common\M3u8Service;

class AiTplService extends BaseService
{
    public static function getAll($type = null, $isHot = null)
    {
        $keyName = CacheKey::AI_TPL;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query = [
                'is_disabled' => 0
            ];
            $result = AiTplModel::find($query, [], ['sort' => -1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if (!empty($type) && $item['type'] != $type) {
                continue;
            }
            if (!empty($isHot) && $item['is_hot'] != $isHot) {
                continue;
            }
            $config                 = $item['config'];
            $config['preview_m3u8'] = isset($config['m3u8_url']) ? [
                'id'       => '',
                'lid'      => '',
                'code'     => 'line1',
                'name'     => '线路1',
                'm3u8_url' => M3u8Service::encode($config['m3u8_url'], 'tencent')
            ] : [];

            $rows[] = [
                'id'          => strval($item['_id']),
                'name'        => strval($item['name']),
                'img'         => CommonService::getCdnUrl($item['img']),
                'config'      => $config,
                'description' => strval($item['description']),
                'money'       => strval($item['money']),
                'adult'       => $item['adult'] ? 'y' : 'n',
                'tags'        => $item['tags'],
            ];
        }
        return $rows;
    }
}
