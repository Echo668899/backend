<?php

namespace App\Services\Report;

use App\Core\Services\BaseService;
use App\Models\Novel\NovelModel;
use App\Models\Report\ReportNovelLogModel;

class ReportNovelLogService extends BaseService
{
    public static $keyNames = [
        'click'   => 'stats_novel:%s:click',
        'click1'  => 'stats_novel:%s:click1',
        'click7'  => 'stats_novel:%s:click7',
        'click30' => 'stats_novel:%s:click30',

        'love'   => 'stats_novel:%s:love',
        'love1'  => 'stats_novel:%s:love1',
        'love7'  => 'stats_novel:%s:love7',
        'love30' => 'stats_novel:%s:love30',

        'favorite'   => 'stats_novel:%s:favorite',
        'favorite1'  => 'stats_novel:%s:favorite1',
        'favorite7'  => 'stats_novel:%s:favorite7',
        'favorite30' => 'stats_novel:%s:favorite30',
    ];

    /**
     * @param        $novelId
     * @param        $field
     * @param  int   $value
     * @return mixed
     */
    public static function inc($novelId, $field, $value = 1)
    {
        self::do($novelId, $field, $value);
    }

    /**
     * @param        $orderType
     * @param        $page
     * @param        $pageSize
     * @param  mixed $type
     * @param  mixed $period
     * @param  mixed $category
     * @return array
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

            $pipe->zAdd($clickKey, $row['click'], $row['novel_id']);
            $pipe->zAdd($loveKey, $row['love'], $row['novel_id']);
            $pipe->zAdd($favoriteKey, $row['favorite'], $row['novel_id']);
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
        $keys   = redis()->keys('stats_novel:*');
        $prefix = redis()->getOption(\Redis::OPT_PREFIX);
        foreach ($keys as &$key) {
            $key = str_replace($prefix, '', $key);
            unset($key);
        }
        redis()->del($keys);
    }

    /**
     * @param                           $novelId
     * @param                           $field
     * @param                           $value
     * @return bool|int|mixed|void|null
     */
    private static function do($novelId, $field, $value = 1)
    {
        $novelId = strval($novelId);
        if (!in_array($field, ['click', 'love', 'favorite'])) {
            return;
        }
        $novelModel = NovelModel::findByID($novelId);
        if (!$novelModel) {
            return;
        }

        $date   = date('Y-m-d');
        $_id    = $novelId . '_' . $date;
        $update = [
            '$inc' => [$field => $value],
            '$set' => [
                'category'      => $novelModel['cat_id'],
                'pay_type'      => $novelModel['pay_type'],
                'update_status' => $novelModel['update_status'],

                'updated_at' => time()
            ],
            '$setOnInsert' => [
                '_id'        => $_id,
                'novel_id'   => $novelId,
                'name'       => $novelModel['name'],
                'label'      => date('Y-m-d'),
                'click'      => 0,
                'love'       => 0,
                'favorite'   => 0,
                'created_at' => time(),
            ]
        ];

        // 避免与 $inc 冲突的字段
        unset($update['$setOnInsert'][$field]);

        $result = ReportNovelLogModel::findAndModify(
            ['_id' => $_id],
            $update,
            [],
            true
        );

        // /更新收藏率
        if (in_array($field, ['click', 'favorite'])) {
            NovelModel::updateById([
                'name'          => $novelModel['name'],
                'favorite_rate' => round($novelModel['real_click'] > 0 ? $novelModel['real_favorite'] / ($novelModel['real_click']) : 0, 2)
            ], $novelId);
        }

        return $result;
    }
}
