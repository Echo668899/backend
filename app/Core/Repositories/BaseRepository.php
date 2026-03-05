<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Services\RequestService;
use App\Services\Common\ConfigService;

/**
 * Class BaseRepository
 * @package App\Core\Repositories
 */
abstract class BaseRepository
{
    /**
     * @param                                    $data
     * @param  string                            $key
     * @param  string                            $type
     * @param                                    $defaultValue
     * @return array|float|int|mixed|string|null
     */
    public static function getRequest($data, string $key, string $type = 'string', $defaultValue = null)
    {
        if (empty($data)) {
            $data = $_REQUEST;
        }
        return RequestService::getRequest($data, $key, $type, $defaultValue);
    }

    /**
     * @param  string $title
     * @param         $keywords
     * @param         $description
     * @param  string $img
     * @return array
     */
    public static function setSeo($title, $keywords = '', $description = '', $img = '')
    {
        $configs = ConfigService::getAll();
        // /基础结构体必须和system/info的header一样
        return [
            // /基础
            'site_title'  => $title . ' - ' . $configs['site_title'],
            'keywords'    => $keywords,
            'description' => $description,
            // /其他
            'img' => $img,
        ];
    }
}
