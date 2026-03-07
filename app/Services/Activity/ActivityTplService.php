<?php

namespace App\Services\Activity;

use App\Core\Services\BaseService;

/**
 * 活动模板
 */
class ActivityTplService extends BaseService
{
    /**
     * 获取全部模板
     */
    public static function getAll(): array
    {
        $rows = [];
        $class=[
            \App\Services\Activity\Handler\LotteryHandler::class,
            \App\Services\Activity\Handler\CountdownHandler::class,
            \App\Services\Activity\Handler\SignHandler::class,
        ];
        foreach ($class as $item) {
            $object = new $item();
            $rows[$object->code()]=[
                'handler'=>$item,
                'code'  =>$object->code(),
                'name'  =>$object->name(),
                'description'=>$object->description(),
                'schema'=>$object->schema(),
            ];
        }
        return $rows;
    }

    /**
     * 获取某个模板
     */
    public static function get(string $tplCode): ?array
    {
        return self::getAll()[$tplCode] ?? null;
    }
}
