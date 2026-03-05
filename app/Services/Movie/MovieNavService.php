<?php

namespace App\Services\Movie;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Movie\MovieNavModel;

class MovieNavService extends BaseService
{
    /**
     * 删除缓存
     * @return void
     * @throws \Phalcon\Storage\Exception
     */
    public static function deleteCache()
    {
        cache()->delete(CacheKey::MOVIE_NAV);
    }

    /**
     * @param  null|mixed $position
     * @return array
     */
    public static function getAll($position = null)
    {
        $keyName = CacheKey::MOVIE_NAV;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query = [
                'is_disabled' => 0
            ];
            $result = MovieNavModel::find($query, [], ['sort' => -1], 0, 1000);
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
