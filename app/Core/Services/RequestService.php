<?php

declare(strict_types=1);

namespace App\Core\Services;

/**
 * Class BaseRepository
 * @package App\Core\Repositories
 */
class RequestService
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
        if (is_null($data[$key]) || $data[$key] === '') {
            return $defaultValue;
        }
        $value = $data[$key];
        if (is_array($value)) {
            return $value;
        }
        if (is_array($data[$key])) {
            foreach ($data[$key] as $index => $value) {
                $value = trim($value);
                //                if (get_magic_quotes_gpc() == "on") {
                $value = stripslashes($value);
                //                }
                $data[$key][$index] = addslashes(self::word($value));
            }
            return $data[$key];
        }
        if ($type == 'int') {
            return intval($value);
        } elseif ($type == 'float' || $type == 'double') {
            return doubleval($value);
        } elseif ($type == 'html') {
            $value = trim($data[$key]);
            //            if (get_magic_quotes_gpc() == "on") {
            $value = stripslashes($value);
            //            }
            return addslashes($value);
        }
        return self::filter($data[$key]);
    }

    /**
     * 过滤关键词
     * @param  string $str 要过滤的文本
     * @return string
     */
    public static function word($str)
    {
        $word = ['expression', '@import', 'select ', 'select/*', 'update ', 'update/*', 'delete ', 'delete/*', 'insert ', 'insert/*', 'updatexml', 'concat', '()', '`', '/**/', 'union('];
        foreach ($word as $val) {
            if (stripos($str, $val) !== false) {
                return '';
            }
        }
        if (preg_match('/<(.*)script/isU', $str)) {
            return '';
        }
        if (preg_match('/<(.*)iframe/isU', $str)) {
            return '';
        }
        return $str;
    }

    /**
     * 过滤值
     * @param         $value
     * @return string
     */
    public static function filter($value)
    {
        $value = trim('' . $value);
        //        if (get_magic_quotes_gpc() == "on") {
        $value = stripslashes($value);
        //        }
        return addslashes(self::word($value));
    }
}
