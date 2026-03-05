<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Plugins\Ip2Region\IPv4;
use App\Plugins\Ip2Region\IPv6;
use App\Plugins\Ip2Region\Searcher;
use App\Plugins\Ip2Region\Util;
use App\Utils\LogUtil;

class IpService extends BaseService
{
    private static $xdb4    = APP_PATH . '/Resource/ip2region_v4.xdb';
    private static $xdb6    = APP_PATH . '/Resource/ip2region_v6.xdb';
    private static $vIndex4 = null;
    private static $vIndex6 = null;

    private static $version4 = null;
    private static $version6 = null;

    /**
     * @param               $ip
     * @return array|string
     */
    public static function parse($ip)
    {
        try {
            $address = self::search($ip);
            $address = explode('|', $address);
            if (empty($address)) {
                throw new \Exception();
            }
            return [
                'country'  => $address[0],
                'province' => $address[1],
                'city'     => $address[2],
            ];
        } catch (\Exception $e) {
        }
        return '';
    }

    /**
     * 是否中国
     * @param       $ip
     * @return bool
     */
    public static function isChina($ip)
    {
        $address = self::parse($ip);
        if ($address['country'] == '中国') {
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    private static function init(): void
    {
        include_once APP_PATH . '/Plugins/Ip2Region/XdbSearcher.php';

        if (self::$vIndex4 === null) {
            if (file_exists(self::$xdb4) === false) {
                LogUtil::error('Ip库不存在 path:' . self::$xdb4);
                return;
            }
            self::$vIndex4  = Util::loadVectorIndexFromFile(self::$xdb4);
            self::$version4 = IPv4::default();
        }
        if (self::$vIndex6 === null) {
            if (file_exists(self::$xdb6) === false) {
                LogUtil::error('Ip库不存在 path:' . self::$xdb6);
                return;
            }
            self::$vIndex6  = Util::loadVectorIndexFromFile(self::$xdb6);
            self::$version6 = IPv6::default();
        }
    }

    /**
     * @param               $ip
     * @return false|string
     * @throws \Exception
     */
    private static function search($ip)
    {
        self::init();
        $isV6    = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        $dbFile  = $isV6 ? self::$xdb6 : self::$xdb4;
        $vIndex  = $isV6 ? self::$vIndex6 : self::$vIndex4;
        $version = $isV6 ? self::$version6 : self::$version4;

        if (!$dbFile || !$vIndex || !$version) {
            throw new \RuntimeException('XDB file or index not loaded for IP version');
        }
        $searcher = Searcher::newWithVectorIndex($version, $dbFile, $vIndex);
        try {
            return $searcher->search($ip);
        } finally {
            $searcher->close();
        }
    }
}
