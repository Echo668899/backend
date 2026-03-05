<?php

namespace App\Services\Post;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Post\PostNavModel;

class PostNavService extends BaseService
{
    /**
     * 删除缓存
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public static function deleteCache()
    {
        cache()->delete(CacheKey::POST_NAV);
    }

    /**
     * @param                             $position
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function getAll($position = null)
    {
        $keyName = CacheKey::POST_NAV;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query = [
                'is_disabled' => 0
            ];
            $result = PostNavModel::find($query, [], ['sort' => -1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if (!empty($position) && $item['position'] != $position) {
                continue;
            }
            $rows[] = [
                'id'       => $item['_id'],
                'name'     => $item['name'],
                'code'     => $item['code'],
                'position' => $item['position'],
                'style'    => $item['style'],
                'filter'   => $item['filter'] ? json_decode($item['filter'], true) : []
            ];
        }
        return $rows;
    }
}
