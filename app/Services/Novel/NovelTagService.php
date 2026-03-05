<?php

declare(strict_types=1);

namespace App\Services\Novel;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Novel\NovelTagModel;

/**
 * 标签
 * @package App\Services
 */
class NovelTagService extends BaseService
{
    /**
     * 获取所有
     * @param  bool  $hot
     * @return array
     */
    public static function getAll($hot = null)
    {
        $keyName = CacheKey::NOVEL_TAG;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query  = [];
            $result = NovelTagModel::find($query, [], ['sort' => -1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if ($hot && $item['is_hot'] == false) {
                continue;
            }
            $rows[] = [
                'id'        => strval($item['_id']),
                'name'      => $item['name'],
                'attribute' => $item['attribute'],
            ];
        }
        return $rows;
    }

    /**
     * 获取分组属性
     * @return array
     */
    public static function getGroupAttrAll()
    {
        $query  = [];
        $result = [];
        $items  = NovelTagModel::find($query, [], ['sort' => -1], 0, 1000);
        foreach ($items as $item) {
            $result[$item['attribute']][] = [
                'id'   => $item['_id'],
                'name' => $item['name'],
            ];
        }
        return $result;
    }

    /**
     * @param  array $ids
     * @return array
     */
    public static function getByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $rows = NovelTagModel::find(['_id' => ['$in' => $ids]], ['_id', 'name'], [], 0, 1000);
        foreach ($rows as &$row) {
            $row = [
                'id'   => strval($row['_id']),
                'name' => strval($row['name']),
            ];
            unset($row);
        }
        return $rows;
    }
}
