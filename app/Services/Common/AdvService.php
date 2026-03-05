<?php

namespace App\Services\Common;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Common\AdvModel;
use Phalcon\Storage\Exception;

class AdvService extends BaseService
{
    /**
     * 删除广告缓存
     * @return void
     * @throws Exception
     */
    public static function deleteCache()
    {
        cache()->delete(CacheKey::ADV);
    }

    /**
     * @param             $positionCode
     * @param             $userIsVip
     * @param  int        $limit
     * @return mixed|null
     * @throws Exception
     */
    public static function getRandAd($positionCode, $userIsVip, $limit = 100)
    {
        $items = self::getAll($positionCode, $userIsVip, $limit);
        if (empty($items)) {
            return null;
        }
        return $items[mt_rand(0, count($items) - 1)];
    }

    /**
     * @param            $positionCode
     * @param            $userIsVip
     * @param  int       $limit
     * @return array
     * @throws Exception
     */
    public static function getAll($positionCode, $userIsVip = null, $limit = 100): array
    {
        $keyName = CacheKey::ADV;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $nowTime = time();
            $query   = [
                'is_disabled' => 0,
                'start_time'  => ['$lte' => $nowTime],
                'end_time'    => ['$gte' => $nowTime]
            ];
            $result = AdvModel::find($query, [], ['sort' => -1], 0, 1000);

            foreach ($result as &$item) {
                $item['content'] = CommonService::getCdnUrl($item['content'], $item['type']);
                unset($item);
            }
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if ($positionCode != $item['position_code']) {
                continue;
            }
            if ($item['right'] != 'all' && !empty($userIsVip)) {
                if ($userIsVip == 'y' && $item['right'] != 'vip') {
                    continue;
                }
                if ($userIsVip == 'n' && $item['right'] != 'normal') {
                    continue;
                }
            }

            if (count($rows) >= $limit) {
                break;
            }
            $rows[] = [
                'id'          => strval($item['_id']),
                'name'        => strval($item['name']),
                'description' => strval($item['description']),
                'type'        => strval($item['type']),
                'content'     => strval($item['content']),
                'link'        => strval($item['link']),
                'time'        => strval($item['show_time']),
            ];
        }
        return $rows;
    }

    /**
     * 插入广告到列表 $pos = $n * $period + $index + $insertCount;
     * @param  array     $list
     * @param  string    $adCode
     * @param  int       $index  广告插入的偏移位置
     * @param  int       $period 控制每插入一条广告后，间隔多少个原始数据项
     * @param  bool      $loop
     * @return array
     * @throws Exception
     */
    public static function insertAdsToList(array $list, string $adCode, int $index, int $period, bool $loop = false): array
    {
        $ads = AdvService::getAll($adCode);
        if (empty($ads)) {
            return $list;
        }

        shuffle($ads); // 打乱广告顺序

        // 标记广告
        foreach ($ads as &$ad) {
            $ad['_ad'] = true;
        }
        unset($ad); // 避免引用残留

        $result      = $list;
        $insertCount = 0;

        $adCount = count($ads);
        $adIndex = 0;

        while (true) {
            $pos = $insertCount * $period + $index + $insertCount;

            if ($pos >= count($result)) {
                break;
            }

            // 获取当前广告
            $currentAd = $ads[$adIndex];

            array_splice($result, $pos, 0, [$currentAd]);
            $insertCount++;

            $adIndex++;

            // 如果没有开启循环且广告用完，结束插入
            if (!$loop && $adIndex >= $adCount) {
                break;
            }

            // 循环使用广告
            if ($loop && $adIndex >= $adCount) {
                $adIndex = 0;
            }
        }

        return $result;
    }
}
