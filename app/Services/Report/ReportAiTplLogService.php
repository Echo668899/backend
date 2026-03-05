<?php

namespace App\Services\Report;

use App\Core\Services\BaseService;

class ReportAiTplLogService extends BaseService
{
    public static $keyNames = [
        'buy'   => 'stats_ai_tpl:%s:buy',
        'buy1'  => 'stats_ai_tpl:%s:buy1',
        'buy7'  => 'stats_ai_tpl:%s:buy7',
        'buy30' => 'stats_ai_tpl:%s:buy30',
    ];

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
            $category = $row['type'] ?? 'normal';
            $buyKey   = sprintf(self::$keyNames["buy{$range}"], $category);
            dump($buyKey);
            $pipe->zAdd($buyKey, $row['buy'], $row['tpl_id']);
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
        $keys   = redis()->keys('stats_ai_tpl:*');
        $prefix = redis()->getOption(\Redis::OPT_PREFIX);
        foreach ($keys as &$key) {
            $key = str_replace($prefix, '', $key);
            unset($key);
        }
        redis()->del($keys);
    }
}
