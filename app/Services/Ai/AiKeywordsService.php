<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Ai\AiKeywordsModel;

/**
 *  关键字
 * @package App\Services
 */
class AiKeywordsService extends BaseService
{
    /**
     * 获取热门关键字
     * @param  int    $limit
     * @param  string $position
     * @return array
     */
    public static function getHotList($limit = 10, $position = '')
    {
        $keyName = CacheKey::MOVIE_KEYWORDS;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $result = AiKeywordsModel::find(['is_hot' => 1], [], ['sort' => -1], 0, intval($limit));
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if (!empty($position) && $item['position'] != $position) {
                continue;
            }
            $rows[] = [
                'id'   => $item['_id'],
                'name' => strval($item['name']),
            ];
        }
        return $rows;
    }

    /**
     * 写入关键字
     * @param       $keywords
     * @return bool
     */
    public static function do($keywords)
    {
        $keywords = trim($keywords);
        $id       = md5($keywords);
        if (AiKeywordsModel::count(['_id' => $id])) {
            AiKeywordsModel::updateRaw(['$inc' => ['num' => 1]], ['_id' => $id]);
        } else {
            AiKeywordsModel::insert([
                '_id'    => $id,
                'name'   => $keywords,
                'is_hot' => 0,
                'sort'   => 0,
                'num'    => 1,
            ]);
        }
        return true;
    }
}
