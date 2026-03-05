<?php

declare(strict_types=1);

namespace App\Services\Movie;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Movie\MovieCategoryModel;

class MovieCategoryService extends BaseService
{
    /**
     * 获取所有
     * @param  bool   $hot
     * @param  string $position
     * @return array
     */
    public static function getAll($hot = null, $position = '')
    {
        $keyName = CacheKey::MOVIE_CATEGORY;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query  = [];
            $result = MovieCategoryModel::find($query, [], ['sort' => -1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if ($hot && $item['is_hot'] == false) {
                continue;
            }
            if ($position && $item['position'] != $position) {
                continue;
            }
            $rows[] = [
                'id'   => strval($item['_id']),
                'name' => $item['name'],
            ];
        }
        return $rows;
    }

    /**
     * @param  array $id
     * @return array
     */
    public static function getById($id)
    {
        if (empty($id)) {
            return [];
        }
        $row = MovieCategoryModel::findByID(intval($id));
        if (empty($row)) {
            return [];
        }
        return [
            'id'   => strval($row['_id']),
            'name' => strval($row['name']),
        ];
    }
}
