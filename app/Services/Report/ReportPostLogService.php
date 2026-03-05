<?php

namespace App\Services\Report;

use App\Core\Services\BaseService;
use App\Models\Post\PostModel;
use App\Models\Report\ReportPostLogModel;

class ReportPostLogService extends BaseService
{
    public static $keyNames = [
        'click'   => 'stats_post:%s:click',
        'click1'  => 'stats_post:%s:click1',
        'click7'  => 'stats_post:%s:click7',
        'click30' => 'stats_post:%s:click30',

        'love'   => 'stats_post:%s:love',
        'love1'  => 'stats_post:%s:love1',
        'love7'  => 'stats_post:%s:love7',
        'love30' => 'stats_post:%s:love30',

        'favorite'   => 'stats_post:%s:favorite',
        'favorite1'  => 'stats_post:%s:favorite1',
        'favorite7'  => 'stats_post:%s:favorite7',
        'favorite30' => 'stats_post:%s:favorite30',
    ];

    /**
     * @param        $postId
     * @param        $field
     * @param  int   $value
     * @return mixed
     */
    public static function inc($postId, $field, $value = 1)
    {
        self::do($postId, $field, $value);
    }

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
            $position = $row['position'] ?? 'normal';

            $clickKey    = sprintf(self::$keyNames["click{$range}"], $position);
            $loveKey     = sprintf(self::$keyNames["love{$range}"], $position);
            $favoriteKey = sprintf(self::$keyNames["favorite{$range}"], $position);

            $pipe->zAdd($clickKey, $row['click'], $row['post_id']);
            $pipe->zAdd($loveKey, $row['love'], $row['post_id']);
            $pipe->zAdd($favoriteKey, $row['favorite'], $row['post_id']);
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
        $keys   = redis()->keys('stats_post:*');
        $prefix = redis()->getOption(\Redis::OPT_PREFIX);
        foreach ($keys as &$key) {
            $key = str_replace($prefix, '', $key);
            unset($key);
        }
        redis()->del($keys);
    }

    /**
     * @param                           $postId
     * @param                           $field
     * @param                           $value
     * @return bool|int|mixed|void|null
     */
    private static function do($postId, $field, $value = 1)
    {
        $postId = strval($postId);
        if (!in_array($field, ['click', 'love', 'favorite', 'buy_num', 'buy_total'])) {
            return;
        }

        $postModel = PostModel::findByID($postId);
        if (!$postModel) {
            return;
        }

        $date = date('Y-m-d');
        $_id  = $postId . '_' . $date;

        $update = [
            '$inc' => [$field => $value],
            '$set' => [
                'position' => $postModel['position'],
                'pay_type' => $postModel['pay_type'],

                'updated_at' => time()
            ],
            '$setOnInsert' => [
                '_id'        => $_id,
                'post_id'    => $postId,
                'name'       => $postModel['name'],
                'label'      => $date,
                'click'      => 0,
                'love'       => 0,
                'favorite'   => 0,
                'buy_num'    => 0,
                'buy_total'  => 0,
                'created_at' => time(),
            ]
        ];

        // 防止 $inc 字段和 $setOnInsert 冲突
        unset($update['$setOnInsert'][$field]);

        $result = ReportPostLogModel::findAndModify(
            ['_id' => $_id],
            $update,
            [],
            true
        );

        // /更新收藏率
        if (in_array($field, ['click', 'favorite'])) {
            PostModel::updateById([
                'name'          => $postModel['name'],
                'favorite_rate' => round($postModel['real_click'] > 0 ? $postModel['real_favorite'] / ($postModel['real_click']) : 0, 2)
            ], $postId);
        }

        return $result;
    }
}
