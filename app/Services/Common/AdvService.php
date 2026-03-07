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
     * @param $positionCode
     * @param $userIsVip
     * @param int $limit
     * @return mixed|null
     * @throws Exception
     */
    public static function getRandAd($positionCode, $userIsVip, $limit = 100)
    {
        $items = self::getAll($positionCode, $userIsVip, $limit);
        if (empty($items)) return null;
        return $items[mt_rand(0, count($items) - 1)];
    }

    /**
     * @param $positionCode
     * @param $userIsVip
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public static function getAll($positionCode, $userIsVip = null, $limit = 100): array
    {
        $keyName = CacheKey::ADV;
        $result = cache()->get($keyName);
        if (is_null($result)) {
            $nowTime = time();
            $query = array(
                'is_disabled' => 0,
                'start_time' => array('$lte' => $nowTime),
                'end_time' => array('$gte' => $nowTime)
            );
            $result = AdvModel::find($query, array(), array('sort' => -1), 0, 1000);

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
            if($item['right']!='all'&&!empty($userIsVip)){
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
                'id' => strval($item['_id']),
                'name' => strval($item['name']),
                'description' => strval($item['description']),
                'type' => strval($item['type']),
                'content' => strval($item['content']),
                'link' => strval($item['link']),
                'time' => strval($item['show_time']),
            ];
        }
        return $rows;
    }

    /**
     * 插入广告到列表,支持分页
     * @param array $list
     * @param string $adCode
     * @param int $index 广告插入起始偏移位置
     * @param int $period 插入间隔
     * @param int $page
     * @param int $pageSize
     * @param bool $loop 是否循环(广告不足时循环插入)
     * @return array
     * @throws Exception
     */
    public static function insertAdsToListByPage(array $list, string $adCode, int $index, int $period, int $page = 1, int $pageSize = 10, bool $loop = false): array {
        $ads = AdvService::getAll($adCode);
        if (empty($ads)) return $list;

//        shuffle($ads);//不用打乱广告

        foreach ($ads as &$ad) {
            $ad['_ad'] = true;
        }
        unset($ad);

        $adCount = count($ads);

        // 当前页在“原始 list（不含广告）”中的全局起始下标
        $page = max(1, $page);
        $pageSize = max(1, $pageSize);
        $start = ($page - 1) * $pageSize;
        $endExclusive = $start + $pageSize;

        // 该全局位置对应的“第几个广告插入点”
        // 插入点：pos(k) = index + k * period   （pos 是在原始 list 中的下标）
        // 需要找到所有 pos(k) 落在 [start, endExclusive) 的插入点
        $result = $list;

        // k 的最小值：pos(k) >= start  => k >= (start - index) / period
        $kStart = (int)ceil(($start - $index) / $period);
        if ($kStart < 0) $kStart = 0;

        // k 的最大值：pos(k) < endExclusive
        $kEnd = (int)floor(($endExclusive - 1 - $index) / $period);

        if ($kEnd < $kStart) {
            return $result; // 当前页没有广告插入点
        }

        // 为了在同一个页内 splice 时位置不被后续插入影响，从后往前插
        // 在“当前页 list”里的插入位置：localPos = pos(k) - start
        for ($k = $kEnd; $k >= $kStart; $k--) {
            $posInOrigin = $index + $k * $period;

            // 如果当前页实际返回的 list 不是满 pageSize（例如最后一页），要保护一下
            $localPos = $posInOrigin - $start;
            if ($localPos < 0 || $localPos > count($result)) {
                continue;
            }

            // 选广告：第 k 次插入用第 k 个广告（保持跨页稳定）
            if (!$loop && $k >= $adCount) {
                continue; // 不循环则广告用完后不再插
            }
            $adIndex = $loop ? ($k % $adCount) : $k;
            $currentAd = $ads[$adIndex];

            array_splice($result, $localPos, 0, [$currentAd]);
        }

        return $result;
    }

    /**
     * 插入广告块到列表,支持分页
     * @param array $list
     * @param string $adCode
     * @param int $index
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws Exception
     */
    public static function insertAdsAllToListByPage(array $list, string $adCode, int $index, int $period, int $page = 1, int $pageSize = 10, bool $loop = false): array {
        $ads = AdvService::getAll($adCode);
        if (empty($ads)) return $list;

        $page     = max(1, $page);
        $pageSize = max(1, $pageSize);

        $result       = $list;
        $currentCount = count($result);
        if ($currentCount === 0) {
            return $result;
        }

        // 当前页在“原始 list（不含广告）”中的全局起始/结束下标
        $start        = ($page - 1) * $pageSize;
        // 用当前页实际条数来算结束位置，兼容最后一页不足 pageSize 的情况
        $endExclusive = $start + $currentCount;  // [start, endExclusive)

        // 插入点：pos(k) = index + k * period   （pos 是在原始 list 中的下标）
        // 需要找到所有 pos(k) 落在 [start, endExclusive) 的插入点

        // k 的最小值：pos(k) >= start  => k >= (start - index) / period
        $kStart = (int)ceil(($start - $index) / $period);
        if ($kStart < 0) $kStart = 0;

        // k 的最大值：pos(k) <= endExclusive - 1
        $kEnd = (int)floor(($endExclusive - 1 - $index) / $period);

        if ($kEnd < $kStart) {
            return $result; // 当前页没有广告插入点
        }

        // 为了在同一个页内 splice 时位置不被后续插入影响，从后往前插
        // 在“当前页 list”里的插入位置：localPos = pos(k) - start
        for ($k = $kEnd; $k >= $kStart; $k--) {
            // 不循环时，只插第一组（全局第一个插入点）
            if (!$loop && $k > 0) {
                continue;
            }

            $posInOrigin = $index + $k * $period;

            $localPos = $posInOrigin - $start;
            if ($localPos < 0 || $localPos > count($result)) {
                continue;
            }

            // ⭐ 关键：在这里插入的是「一个广告块对象」，不是把 $ads 展开插进去
            $adBlock = [
                'ads' => $ads,
                '_ad' => true,
            ];

            array_splice($result, $localPos, 0, [$adBlock]);
        }

        return $result;
    }
}
