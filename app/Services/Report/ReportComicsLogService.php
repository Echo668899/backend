<?php

namespace App\Services\Report;

use App\Core\Services\BaseService;
use App\Models\Comics\ComicsModel;
use App\Models\Report\ReportComicsLogModel;

class ReportComicsLogService extends BaseService
{
    public static $keyNames = [
        'click'   => 'stats_comics:%s:click',
        'click1'  => 'stats_comics:%s:click1',
        'click7'  => 'stats_comics:%s:click7',
        'click30' => 'stats_comics:%s:click30',

        'love'   => 'stats_comics:%s:love',
        'love1'  => 'stats_comics:%s:love1',
        'love7'  => 'stats_comics:%s:love7',
        'love30' => 'stats_comics:%s:love30',

        'favorite'   => 'stats_comics:%s:favorite',
        'favorite1'  => 'stats_comics:%s:favorite1',
        'favorite7'  => 'stats_comics:%s:favorite7',
        'favorite30' => 'stats_comics:%s:favorite30',
    ];

    /**
     * @param        $comicsId
     * @param        $field
     * @param  int   $value
     * @return mixed
     */
    public static function inc($comicsId, $field, $value = 1)
    {
        self::do($comicsId, $field, $value);
    }

    /**
     * @param                     $orderType
     * @param                     $page
     * @param                     $pageSize
     * @param                     $category
     * @param  mixed              $type
     * @param  mixed              $period
     * @return array|false|\Redis
     */

    /**
     * 获取排行ids
     * @param  string             $type     指标,click,love,favorite
     * @param  string             $period   周期
     * @param                     $page
     * @param                     $pageSize
     * @param                     $category
     * @return array|false|\Redis
     */
    public static function getIds($type, $period, $page = 1, $pageSize = 100, $category = 'normal')
    {
        switch ($period) {
            case 'day':
                $keyName = self::$keyNames[$type . '1'];
                break;
            case 'week':
                $keyName = self::$keyNames[$type . '7'];
                break;
            case 'month':
                $keyName = self::$keyNames[$type . '30'];
                break;
            default:
                $keyName = self::$keyNames[$type];
        }

        $keyName = sprintf($keyName, $category);

        $start = ($page - 1) * $pageSize;
        $end   = $start + $pageSize - 1;

        $ids = redis()->zRevRange($keyName, $start, $end, false);
        return $ids;
    }

    /**
     * 统计
     * @param  array $rows
     * @return void
     */
    public static function setStats(array $rows, string $range)
    {
        $redis = redis();
        $pipe  = $redis->multi(\Redis::PIPELINE);

        foreach ($rows as $row) {
            $category = $row['category'] ?? 'normal';

            $clickKey    = sprintf(self::$keyNames["click{$range}"], $category);
            $loveKey     = sprintf(self::$keyNames["love{$range}"], $category);
            $favoriteKey = sprintf(self::$keyNames["favorite{$range}"], $category);

            $pipe->zAdd($clickKey, $row['click'], $row['comics_id']);
            $pipe->zAdd($loveKey, $row['love'], $row['comics_id']);
            $pipe->zAdd($favoriteKey, $row['favorite'], $row['comics_id']);
        }
        // 执行所有命令
        $pipe->exec();
    }

    /**
     * 重置统计
     * @return void
     */
    public static function resetStats()
    {
        $keys   = redis()->keys('stats_comics:*');
        $prefix = redis()->getOption(\Redis::OPT_PREFIX);
        foreach ($keys as &$key) {
            $key = str_replace($prefix, '', $key);
            unset($key);
        }
        redis()->del($keys);
    }

    /**
     * @param                           $comicsId
     * @param                           $field
     * @param                           $value
     * @return bool|int|mixed|void|null
     */
    private static function do($comicsId, $field, $value = 1)
    {
        $comicsId = strval($comicsId);
        if (!in_array($field, ['click', 'love', 'favorite'])) {
            return;
        }
        $comicsModel = ComicsModel::findByID($comicsId);
        if (!$comicsModel) {
            return;
        }

        $date = date('Y-m-d');
        $_id  = $comicsId . '_' . $date;

        $update = [
            '$inc' => [$field => $value],
            '$set' => [
                'category'      => $comicsModel['cat_id'],
                'pay_type'      => $comicsModel['pay_type'],
                'update_status' => $comicsModel['update_status'],

                'updated_at' => time()
            ],
            '$setOnInsert' => [
                '_id'        => $_id,
                'comics_id'  => $comicsId,
                'name'       => $comicsModel['name'],
                'label'      => date('Y-m-d'),
                'click'      => 0,
                'love'       => 0,
                'favorite'   => 0,
                'created_at' => time(),
            ]
        ];

        // 避免与 $inc 冲突的字段
        unset($update['$setOnInsert'][$field]);

        $result = ReportComicsLogModel::findAndModify(
            ['_id' => $_id],
            $update,
            [],
            true
        );

        // /更新收藏率
        if (in_array($field, ['click', 'favorite'])) {
            ComicsModel::updateById([
                'name'          => $comicsModel['name'],
                'favorite_rate' => round($comicsModel['real_click'] > 0 ? $comicsModel['real_favorite'] / ($comicsModel['real_click']) : 0, 2)
            ], $comicsId);
        }
        return $result;
    }
}
