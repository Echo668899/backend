<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Ai\AiTagModel;
use Phalcon\Storage\Exception;

/**
 *  漫画标签
 * @package App\Services
 */
class AiTagService extends BaseService
{
    /**
     * 获取所有
     * @param  bool      $hot
     * @param  mixed     $type
     * @return array
     * @throws Exception
     */
    public static function getAll($type = '', $hot = false)
    {
        $keyName = CacheKey::AI_TAG;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $query  = [];
            $result = AiTagModel::find($query, [], ['sort' => -1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if ($hot && !$item['is_hot']) {
                continue;
            }
            if ($type && $type != $item['type']) {
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
     * @param  array $ids
     * @return array
     */
    public static function getByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $rows = AiTagModel::find(['_id' => ['$in' => $ids]], ['_id', 'name'], [], 0, 1000);
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
