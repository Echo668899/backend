<?php

namespace App\Repositories\Api;

use App\Core\Repositories\BaseRepository;
use App\Jobs\Center\CenterCustomerJob;
use App\Jobs\Event\EventBusJob;
use App\Jobs\Event\Payload\Common\AdvClickPayload;
use App\Models\Report\ReportServerLogModel;
use App\Services\Audio\AudioNavService;
use App\Services\Comics\ComicsNavService;
use App\Services\Common\AdvAppService;
use App\Services\Common\AdvService;
use App\Services\Common\ApiService;
use App\Services\Common\ArticleService;
use App\Services\Common\ChannelApkService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Common\DomainService;
use App\Services\Common\JobService;
use App\Services\Movie\MovieNavService;
use App\Services\Novel\NovelNavService;
use App\Services\Post\PostNavService;
use App\Services\Report\ReportAdvAppLogService;
use App\Services\Report\ReportAdvLogService;
use App\Services\User\UserService;

class SystemRepository extends BaseRepository
{
    /**
     * @param                             $userId
     * @param                             $request
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function info($userId, $request = [])
    {
        $configs    = ConfigService::getAll();
        $deviceType = ApiService::getDeviceType();
        $result     = [
            'version'             => strval($deviceType == 'ios' ? $configs['ios_version'] : $configs['android_version']),
            'min_version'         => strval($deviceType == 'ios' ? $configs['ios_min_version'] : $configs['android_min_version']),
            'version_description' => strval($deviceType == 'ios' ? $configs['ios_version_desc'] : $configs['android_version_desc']),
            'download_url'        => ChannelApkService::getApk($deviceType),

            'site_url' => strval($configs['site_url']),

            'img_key'    => strval($configs['media_encode_key']), // 图片解密key
            'cdn_header' => strval($configs['cdn_referer']),

            'service_link'         => strval($configs['service_link']), // 客服链接
            'group_link'           => strval($configs['group_link']), // 官方社区
            'permanent_url'        => strval($configs['permanent_url']), // 永久网址
            'service_email'        => strval($configs['service_email']), // 客服邮箱
            'place_ad'             => strval($configs['place_ad']), // 投放广告
            'contact_us'           => strval($configs['contact_us']), // 联系我们
            'backup_url'           => strval($configs['backup_url']), // 备用网址
            'channel_cooperation'  => strval($configs['channel_cooperation']), // 渠道合作
            'business_cooperation' => strval($configs['business_cooperation']), // 商务合作

            'upload_image_url'        => CommonService::getUploadImageUrl($configs),
            'upload_file_url'         => CommonService::getUploadFileUrl($configs),
            'upload_file_query_url'   => CommonService::getUploadFileQueryUrl($configs),
            'upload_file_max_length'  => strval(600 * 1024 * 1024),
            'upload_image_max_length' => strval(1 * 1024 * 1024),

            'domain_statistic' => DomainService::getAll(), // /指定域名统计代码
            'global_statistic' => $configs['count_code'], // /全站统计代码

            // 启动弹窗
            'layers' => value(function () {
                $rows   = [];
                $advs   = AdvService::getAll('app_layer');
                $apps   = AdvAppService::getAll('recommend', 1, 100);// 启动弹窗-应用中心
                $notice = ArticleService::getAnnouncement();
                foreach ($advs as $item) {
                    $rows[] = [
                        'type' => 'adv',
                        'data' => $item,
                    ];
                }

                if (count($apps) > 0) {
                    $rows[] = [
                        'type' => 'app',
                        'data' => $apps,
                    ];
                }
                if (!empty($notice)) {
                    $rows[] = [
                        'type' => 'notice',
                        'data' => $notice,
                    ];
                }
                return $rows;
            }),
            // 启动页广告
            'splash'           => AdvService::getAll('app_start'),
            'splash_time'      => '5', // 启动页广告倒计时
            'splash_auto_jump' => 'n', // 启动页自动跳转

            // 附加广告
            'ads' => [
                // 全局广告
                'app_float_left'         => AdvService::getRandAd('app_float_left', null), // 页面浮动-左
                'app_float_right'        => AdvService::getRandAd('app_float_right', null), // 页面浮动-右
                'app_float_bottom'       => AdvService::getAll('app_float_bottom'), // 页面浮动-底
                'app_float_bottom_left'  => AdvService::getAll('app_float_bottom_left'), // 页面浮动-左下
                'app_float_bottom_right' => AdvService::getAll('app_float_bottom_right'), // 页面浮动-右下
            ],
            'domain' => value(function () {
                $rows = DomainService::getAll('site');
                if (empty($rows)) {
                    return [];
                }
                $rows = array_column($rows, 'domain');
                return array_values($rows);
            }),

            // 中心-客服系统开关
            'center_customer_status' => value(function () use ($userId) {
                $configs = CenterCustomerJob::getCenterConfig('customer');
                if ($configs['status'] == 'y') {
                    return 'y';
                }
                $uids = $configs['test_ids'] ? explode(',', trim($configs['test_ids'])) : [];
                if ($userId && $uids && in_array($userId, $uids)) {
                    return 'y';
                }

                return 'n';
            }),
            'center_customer_headico' => strval(ConfigService::getConfig('system_user_headico')),
        ];
        $result['movie_nav'] = value(function () {
            $rows = MovieNavService::getAll('normal');
            foreach ($rows as $index => &$row) {
                $row = [
                    'id'     => strval($row['id']),
                    'name'   => strval($row['name']),
                    'code'   => strval($row['code']),
                    'style'  => strval($row['style']),
                    'select' => $index == 0 ? 'y' : 'n',
                    'items'  => [],
                ];
                unset($row);
            }
            return $rows;
        });
        $result['dark_nav'] = value(function () {
            $rows = MovieNavService::getAll('dark');
            foreach ($rows as $index => &$row) {
                $row = [
                    'id'     => strval($row['id']),
                    'name'   => strval($row['name']),
                    'code'   => strval($row['code']),
                    'style'  => strval($row['style']),
                    'select' => $index == 0 ? 'y' : 'n',
                    'items'  => [],
                ];
                unset($row);
            }
            return $rows;
        });
        $result['post_nav'] = value(function () {
            $rows = PostNavService::getAll('normal');
            foreach ($rows as $index => &$row) {
                $row = [
                    'id'     => strval($row['id']),
                    'name'   => strval($row['name']),
                    'code'   => strval($row['code']),
                    'style'  => strval($row['style']),
                    'select' => $index == 0 ? 'y' : 'n',
                    'items'  => [],
                ];
                unset($row);
            }
            return $rows;
        });
        $result['comics_nav'] = value(function () {
            $rows = ComicsNavService::getAll('normal');
            foreach ($rows as $index => &$row) {
                $row = [
                    'id'     => strval($row['id']),
                    'name'   => strval($row['name']),
                    'code'   => strval($row['code']),
                    'style'  => strval($row['style']),
                    'select' => $index == 0 ? 'y' : 'n',
                    'items'  => [],
                ];
                unset($row);
            }
            return $rows;
        });
        $result['novel_nav'] = value(function () {
            $rows = NovelNavService::getAll('normal');
            foreach ($rows as $index => &$row) {
                $row = [
                    'id'     => strval($row['id']),
                    'name'   => strval($row['name']),
                    'code'   => strval($row['code']),
                    'style'  => strval($row['style']),
                    'select' => $index == 0 ? 'y' : 'n',
                    'items'  => [],
                ];
                unset($row);
            }
            return $rows;
        });
        $result['audio_nav'] = value(function () {
            $rows = AudioNavService::getAll('normal');
            foreach ($rows as $index => &$row) {
                $row = [
                    'id'     => strval($row['id']),
                    'name'   => strval($row['name']),
                    'code'   => strval($row['code']),
                    'style'  => strval($row['style']),
                    'select' => $index == 0 ? 'y' : 'n',
                    'items'  => [],
                ];
                unset($row);
            }
            return $rows;
        });

        return $result;
    }

    /**
     * 获取最新地址
     * @return string[]
     */
    public static function getDomains()
    {
        $rows   = DomainService::getAll('site');
        $result = [];
        foreach ($rows as $index => $item) {
            if ($index < 8) {
                $result[] = [
                    'url'  => "https://{$item['domain']}",
                    'name' => '立即进入'
                ];
            }
        }
        return $result;
    }

    /**
     * @param       $objectType
     * @param       $objectId
     * @param       $objectName
     * @param       $userId
     * @return void
     */
    public static function addTrackQueue($objectType, $objectId, $objectName, $userId = null)
    {
        $channelName = '';
        $parentId    = '';
        if (!empty($userId)) {
            $userInfo    = UserService::getInfoFromCache($userId);
            $channelName = $userInfo['channel_name'];
            $parentId    = $userInfo['parent_id'];
        }

        try {
            switch ($objectType) {
                case 'adv':
                    ReportAdvLogService::inc($objectId, $objectName, 'click', $channelName, 1);
                    ReportAdvLogService::incUser($objectId, $objectName, 'click', $parentId, 1);
                    break;
                case 'adv-app':
                    ReportAdvAppLogService::inc($objectId, $objectName, 'click', $channelName, 1);
                    ReportAdvAppLogService::incUser($objectId, $objectName, 'click', $parentId, 1);
                    break;
            }
            JobService::create(new EventBusJob(new AdvClickPayload($userId, $objectType, $objectId)));
        } catch (\Exception $e) {
        }
    }

    /**
     * 获取服务状态
     * @return array
     */
    public static function getServerStatus()
    {
        exec('curl 127.0.0.1:8088/ngx_status', $nginxStatus);
        return [
            'mysql_status' => 'y',
            'mongo_status' => value(function () {
                return ReportServerLogModel::getInsertId() > 0 ? 'y' : 'n';
            }),
            'redis_status'   => CommonService::updateRedisCounter('check_server', 1) > 0 ? 'y' : 'n',
            'disk_free_size' => value(function () {
                $free = disk_free_space('/');
                return round($free / (1024 * 1024 * 1024), 2) . ' GB';
            }),
            'os_name'          => '',
            'memory_free_size' => value(function () {
                exec('free -m', $sys_info);
                $rs = preg_replace("/\s{2,}/", ' ', $sys_info[1]);
                $hd = explode(' ', $rs);
                return $hd[3] . 'M';
            }),
            'cpu_num' => value(function () {
                // 使用 nproc 命令获取逻辑核心数
                $num = (int) shell_exec('nproc');
                return $num > 0 ? $num : 'unknown';
            }),
            'load_average' => value(function () {
                // 方式 A: 使用系统函数 (推荐)
                if (function_exists('sys_getloadavg')) {
                    $load = sys_getloadavg();
                    return implode(', ', $load); // 返回 1, 5, 15 分钟的负载
                }

                // 方式 B: 兜底使用 uptime 命令解析
                exec('uptime', $sys_info);
                if (empty($sys_info) || empty($sys_info[0])) {
                    return '';
                }
                return trim(substr($sys_info[0], strpos($sys_info[0], 'average:') + 8));
            }),
            'nginx_active_connections' => str_replace('Active connections: ', '', trim($nginxStatus[0])),
            'nginx_requests'           => str_replace(' ', ',', trim($nginxStatus[2])),
            'nginx_status'             => value(function () use ($nginxStatus) {
                $status = $nginxStatus[3];
                $status = str_replace('Reading: ', '', $status);
                $status = str_replace('Writing: ', '', $status);
                $status = str_replace('Waiting: ', '', $status);
                $status = str_replace(' ', ',', $status);
                return $status;
            })
        ];
    }
}
