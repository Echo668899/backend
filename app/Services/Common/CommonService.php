<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Utils\CommonUtil;
use Phalcon\Manager\CdnService;

class CommonService extends BaseService
{
    /**
     * 清除缓存
     */
    public static function clearCache()
    {
        cache()->clear();
    }


    /**
     * 获取cdn链接
     * @param $link
     * @param $contentType
     * @param $cdnDrive
     * @return array|mixed|string|string[]
     */
    public static function getCdnUrl($link, $contentType = 'image', $cdnDrive = null)
    {
        if (empty($link) || strpos($link, '://') > 0) {
            return strval($link);
        }
        if (strpos($link, "media://") !== false) {
            $link = str_replace('media://', '', $link);
        }

        $cdnDrive = $cdnDrive ?: self::getCdnDrive($contentType);
        $domain = self::getCdnDomain($contentType, $cdnDrive);
        if (empty($domain) || empty($cdnDrive)) {
            return '';
        }

        $ext = self::getLinkExt($link);
        if (!in_array($cdnDrive,['free','source']) && in_array($ext, array('.txt', '.gif', '.jpg', '.jpeg', '.bmp', '.png', '.webp'))) {
            $link = str_replace($ext, '.bnc', $link);
        }
        try {
            switch ($cdnDrive) {
                case 'source':
                    return CdnService::getFreeUrl($domain, $link);
                case 'free':
                    return CdnService::getFreeUrl($domain, $link);
                case 'tencent':
                    return CdnService::getTencentUrl($domain, $link, env()->path('cdn.tencent'));
                case 'kingshan':
                    return CdnService::getKingshanUrl($domain, $link, env()->path('cdn.kingshan'));
                case 'aws':
                default:
                    $needCache = false;
                    if (strpos($link, '.bnc') !== false) {
                        $needCache = true;
                        $cacheKey = 'cdn_aws:' . md5($link);
                    }
                    if ($needCache) {
                        $fullLink = redis()->get($cacheKey);
                        if (!empty($fullLink) && strpos($fullLink, $domain) !== false) {
                            return $fullLink;
                        }
                    }
                    $fullLink = CdnService::getAwsUrl($domain, $link, APP_PATH . '/Resource/ssl/private_key.pem', env()->path('cdn.aws'));
                    if ($needCache) {
                        redis()->set($cacheKey, $fullLink, 2 * 3600);
                    }
                    return $fullLink;
            }
        } catch (\Error $e) {

        }
        return $domain . $link;
    }

    /**
     * 获取cdn驱动
     * @param $contentType
     * @return mixed|null
     */
    public static function getCdnDrive($contentType)
    {
        return ConfigService::getConfig('cdn_drive_' . $contentType);
    }

    /**
     * 获取cdn域名
     * @param $contentType
     * @param $cdnDrive
     * @return mixed|string
     */
    public static function getCdnDomain($contentType, $cdnDrive)
    {
        $domains = ConfigService::getConfig('cdn_' . $contentType);

        $split = CommonUtil::getSplitChar($domains);
        $domains = explode($split, $domains);

        $backup = '';
        foreach ($domains as $domain) {
            $domain = explode('=>', $domain);
            if ($domain[0] === $cdnDrive) {
                return $domain[1];
            }
            if ($domain[0] == 'free') {
                $backup = $domain[1];
            }
        }
        return $backup;
    }

    /**
     * 获取后缀
     * @param $filename
     * @return mixed|string
     */
    private static function getLinkExt($filename)
    {
        $exts = array(
            '.txt', '.gif', '.jpg', '.jpeg', '.bmp', '.png', '.webp'
        );
        foreach ($exts as $ext) {
            if (strpos($filename, $ext) > 0) {
                return $ext;
            }
        }
        return '';
    }

    /**
     * 限流动作检查
     * @param $keyName
     * @param int $seconds
     * @param int $num
     * @return bool
     */
    public static function checkActionLimit($keyName, $seconds = 60, $num = 3)
    {
        $count = redis()->incrBy($keyName, 1);
        if ($count == 1) {
            redis()->expire($keyName, $seconds);
        }
        return $count > $num ? false : true;
    }

    /**
     * 获取计数器
     * @param $keyName
     * @return float|int
     */
    public static function getRedisCounter($keyName)
    {
        $count = redis()->get($keyName);
        return $count * 1;
    }

    /**
     * 获取计数器-批量
     * @param $keyNames
     * @return array
     */
    public static function getRedisCounters($keyNames)
    {
        $values = redis()->mget($keyNames);
        $result = [];
        foreach ($keyNames as $index => $keyName) {
            $result[$keyName] = intval($values[$index] ?? 0);
        }
        return $result;
    }

    /**
     * 更新设置计数器
     * @param $keyName
     * @param int $value
     * @param integer $timeout
     * @return float|int
     */
    public static function setRedisCounter($keyName, int $value, $timeout = null)
    {
        redis()->set($keyName, $value, $timeout);
        return $value * 1;
    }

    /**
     * 更新设置计数器
     * @param $keyName
     * @param int $value
     * @return float|int
     */
    public static function updateRedisCounter($keyName, int $value)
    {
        $value = redis()->incrBy($keyName, $value);
        return $value * 1;
    }

    public static function getUploadImageUrl($configs)
    {
        /**
         * 根据项目情况打开
         */

        /*其他*/
        return sprintf('%s/upload/image?key=%s', $configs['upload_url'], $configs['upload_key']);

        /*tx*/
        return sprintf('%s/upload/image?project=%s&upload_token=%s', $configs['upload_url'],env()->path('app.name'), md5(strval(microtime(true))));
    }

    public static function getUploadFileUrl($configs)
    {
        /**
         * 根据项目情况打开
         */

        /*其他*/
        return sprintf('%s/upload/byte?key=%s', $configs['upload_url'], $configs['upload_key']);

        /*tx*/
        return sprintf('%s/upload/video?project=%s&upload_token=%s', $configs['upload_url'],env()->path('app.name'),md5(strval(microtime(true))));

    }

    public static function getUploadFileQueryUrl($configs)
    {
        /**
         * 根据项目情况打开
         */
        /*其他*/
        return sprintf('%s/upload/query?key=%s', $configs['upload_url'], $configs['upload_key']);

        /*tx*/
        return sprintf('%s/upload/query?project=%s&upload_token=%s', $configs['upload_url'],env()->path('app.name'),md5(strval(microtime(true))));
    }
}
