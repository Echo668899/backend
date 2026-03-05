<?php

namespace App\Core;

class Url extends \Phalcon\Url
{
    protected $suffix;

    public function get($uri = null, $args = null, $module = null, $baseUri = null): string
    {
        $module    = $module ?: dispatcher()->getModuleName();
        $moduleUrl = env()->path("modules.{$module}");

        $baseUri = $baseUri ? rtrim($baseUri, '/') : '';
        $url     = $baseUri . rtrim($moduleUrl, '/') . '/' . ltrim($uri, '/');

        if (!empty($args)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($args);
        }

        $suffix = $this->getSuffix();
        if (empty($suffix)) {
            return $url;
        }

        // 如果 URL 不包含 .html 且不是以 / 结尾
        if (strpos($url, $this->suffix) === false && substr($url, -1) !== '/') {
            // /URL中是否有.xxx
            if (strpos(parse_url($url, PHP_URL_PATH), '.') !== false) {
                return $url;
            }

            // 如果 URL 包含查询参数
            if (strpos($url, '?') !== false) {
                $parts = explode('?', $url);
                $url   = $parts[0] . $this->suffix . '?' . $parts[1];
            } else {
                $url .= $this->suffix;
            }
        }
        return $url;
    }

    public function getSuffix()
    {
        return strval($this->suffix);
    }

    /**
     * 设置后缀
     * @param  string $suffix
     * @return void
     */
    public function setSuffix(string $suffix = '.html')
    {
        $this->suffix = $suffix;
    }
}
