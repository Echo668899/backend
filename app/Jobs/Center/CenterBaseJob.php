<?php

namespace App\Jobs\Center;

use App\Jobs\BaseJob;
use App\Services\Common\ConfigService;
use App\Utils\CommonUtil;

/**
 * 中心
 */
abstract class CenterBaseJob extends BaseJob
{
    /**
     * 获取各个中心配置
     * @param                 $type
     * @return array|string[]
     */
    public static function getCenterConfig($type)
    {
        $configs = [];
        switch ($type) {
            case 'adv':
                $configs = self::adv();
                break;
            case 'data':
                $configs = self::data();
                break;
            case 'customer':
                $configs = self::customer();
                break;
        }
        return $configs;
    }

    /**
     * @return string[]
     */
    private static function adv()
    {
        $configs = ConfigService::getConfig('center_adv');
        $split   = CommonUtil::getSplitChar($configs);
        $configs = explode($split, $configs);
        $result  = [
            'pull_url' => '',
            'push_url' => '',
            'merid'    => '',
            'deptid'   => '',
            'appid'    => '',
            'appkey'   => '',
        ];
        foreach ($configs as $config) {
            $config = explode('=>', $config);
            if (isset($result[$config[0]])) {
                $result[$config[0]] = $config[1];
            }
        }

        return $result;
    }

    /**
     * @return string[]
     */
    private static function data()
    {
        $configs = ConfigService::getConfig('center_data');
        $split   = CommonUtil::getSplitChar($configs);
        $configs = explode($split, $configs);
        $result  = [
            'pull_url' => '',
            'push_url' => '',
            'merid'    => '',
            'deptid'   => '',
            'appid'    => '',
            'appkey'   => '',
        ];
        foreach ($configs as $config) {
            $config = explode('=>', $config);
            if (isset($result[$config[0]])) {
                $result[$config[0]] = $config[1];
            }
        }

        return $result;
    }

    /**
     * 客服中心
     * @return string[]
     */
    private static function customer()
    {
        $configs = ConfigService::getConfig('center_customer');
        $split   = CommonUtil::getSplitChar($configs);
        $configs = explode($split, $configs);

        $result = [
            'url'      => '',
            'appname'  => '',
            'appid'    => '',
            'appkey'   => '',
            'status'   => 'n',
            'test_ids' => ''
        ];
        foreach ($configs as $config) {
            $config = explode('=>', $config);
            if (isset($result[$config[0]])) {
                $result[$config[0]] = $config[1];
            }
        }

        return $result;
    }
}
