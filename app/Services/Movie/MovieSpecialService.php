<?php

namespace App\Services\Movie;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Movie\MovieSpecialModel;
use App\Utils\CommonUtil;

class MovieSpecialService extends BaseService
{
    /**
     * 专题位置
     * @var array
     */
    public static $position = [
        'year' => '年度精选',
    ];

    /**
     * 获取所有
     * @param                             $position
     * @param                             $page
     * @param                             $pageSize
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function get($position, $page = 1, $pageSize = 10)
    {
        $keyName = CacheKey::MOVIE_SPECIAL;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query = [
                'is_disabled' => 0
            ];
            $result = MovieSpecialModel::find($query, [], ['sort' => -1, '_id' => 1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if ($item['position'] != $position) {
                continue;
            }
            $rows[] = [
                'id'     => strval($item['_id']),
                'name'   => strval($item['name']),
                'filter' => strval($item['filter']),
                'style'  => strval($item['style']),
                'num'    => strval($item['num']),
            ];
        }
        return CommonUtil::arrayPage($rows, $page, $pageSize);
    }
}
