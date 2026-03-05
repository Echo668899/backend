<?php

namespace App\Services\Report;

use App\Core\Services\BaseService;
use App\Models\Movie\MovieModel;
use App\Models\Report\ReportMovieLogModel;

class ReportMovieLogService extends BaseService
{
    public static $keyNames = [
        'click'   => 'stats_movie:%s:click',
        'click1'  => 'stats_movie:%s:click1',
        'click7'  => 'stats_movie:%s:click7',
        'click30' => 'stats_movie:%s:click30',

        'love'   => 'stats_movie:%s:love',
        'love1'  => 'stats_movie:%s:love1',
        'love7'  => 'stats_movie:%s:love7',
        'love30' => 'stats_movie:%s:love30',

        'favorite'   => 'stats_movie:%s:favorite',
        'favorite1'  => 'stats_movie:%s:favorite1',
        'favorite7'  => 'stats_movie:%s:favorite7',
        'favorite30' => 'stats_movie:%s:favorite30',

        'buy'   => 'stats_movie:%s:buy',
        'buy1'  => 'stats_movie:%s:buy1',
        'buy7'  => 'stats_movie:%s:buy7',
        'buy30' => 'stats_movie:%s:buy30',
    ];

    /**
     * @param        $movieId
     * @param        $field
     * @param  int   $value
     * @return mixed
     */
    public static function inc($movieId, $field, $value = 1)
    {
        self::do($movieId, $field, $value);
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
            $position = $row['position'] ?? 'normal';

            $clickKey    = sprintf(self::$keyNames["click{$range}"], $position);
            $loveKey     = sprintf(self::$keyNames["love{$range}"], $position);
            $favoriteKey = sprintf(self::$keyNames["favorite{$range}"], $position);
            $buyKey      = sprintf(self::$keyNames["buy{$range}"], $position);

            $pipe->zAdd($clickKey, $row['click'], $row['movie_id']);
            $pipe->zAdd($loveKey, $row['love'], $row['movie_id']);
            $pipe->zAdd($favoriteKey, $row['favorite'], $row['movie_id']);
            $pipe->zAdd($buyKey, $row['buy'], $row['movie_id']);
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
        $keys   = redis()->keys('stats_movie:*');
        $prefix = redis()->getOption(\Redis::OPT_PREFIX);
        foreach ($keys as &$key) {
            $key = str_replace($prefix, '', $key);
            unset($key);
        }
        redis()->del($keys);
    }

    /**
     * @param                           $movieId
     * @param                           $field
     * @param                           $value
     * @return bool|int|mixed|void|null
     */
    private static function do($movieId, $field, $value = 1)
    {
        $movieId = strval($movieId);
        if (!in_array($field, ['click', 'love', 'favorite', 'buy_num', 'buy_total', 'download'])) {
            return;
        }

        $movieModel = MovieModel::findByID($movieId);
        if (!$movieModel) {
            return;
        }

        $date = date('Y-m-d');
        $_id  = $movieId . '_' . $date;

        $update = [
            '$inc' => [$field => $value],
            '$set' => [
                'position'      => $movieModel['position'],
                'pay_type'      => $movieModel['pay_type'],
                'update_status' => $movieModel['update_status'],

                'updated_at' => time()
            ],
            '$setOnInsert' => [
                '_id'        => $_id,
                'movie_id'   => $movieId,
                'name'       => $movieModel['name'],
                'label'      => $date,
                'click'      => 0,
                'love'       => 0,
                'favorite'   => 0,
                'buy_num'    => 0,
                'buy_total'  => 0,
                'download'   => 0,
                'created_at' => time(),
            ]
        ];

        // 避免与 $inc 字段冲突
        unset($update['$setOnInsert'][$field]);

        $result = ReportMovieLogModel::findAndModify(
            ['_id' => $_id],
            $update,
            [],
            true
        );

        // 更新收藏率逻辑
        if (in_array($field, ['click', 'favorite'])) {
            MovieModel::updateById([
                'name'          => $movieModel['name'],
                'favorite_rate' => round($movieModel['real_click'] > 0 ? $movieModel['real_favorite'] / ($movieModel['real_click']) : 0, 2)
            ], $movieId);
        }

        return $result;
    }
}
