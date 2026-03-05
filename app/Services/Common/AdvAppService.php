<?php

namespace App\Services\Common;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Common\AdvAppModel;
use App\Utils\CommonUtil;

class AdvAppService extends BaseService
{
    public static $position = [
        'recommend' => '推荐',
        'video'     => '看片',
        'dating'    => '约炮',
        'gamble'    => '棋牌',
        'game'      => '黄油',
    ];

    /**
     * 删除缓存
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public static function deleteCache()
    {
        cache()->delete(CacheKey::ADV_APP);
    }

    /**
     * @param             $limit
     * @param  null|mixed $position
     * @param  null|mixed $isHot
     * @return array
     */
    public static function getAll($position = null, $isHot = null, $limit = 100)
    {
        $keyName = CacheKey::ADV_APP;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query = [
                'is_disabled' => 0,
            ];
            $result = AdvAppModel::find($query, [], ['sort' => -1], 0, 1000);
            foreach ($result as &$item) {
                $item['image'] = CommonService::getCdnUrl($item['image'], 'image');
                unset($item);
            }
            cache()->set($keyName, $result, 300);
        }

        $rows = [];
        foreach ($result as $item) {
            if (!empty($position) && !in_array($position, $item['position'])) {
                continue;
            }
            if (!empty($isHot) && $isHot != $item['is_hot']) {
                continue;
            }
            if (count($rows) >= $limit) {
                break;
            }
            $rows[] = [
                'id'           => strval($item['_id']),
                'name'         => $item['name'],
                'description'  => $item['description'],
                'image'        => $item['image'],
                'download'     => CommonUtil::formatNum(intval($item['download'])) . '下载',
                'download_url' => strval($item['download_url']),
            ];
        }
        return $rows;
    }
}
