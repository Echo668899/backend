<?php

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Utils\CommonUtil;

class M3u8Service extends BaseService
{
    private static $prefix   = 'm3u8:';
    private static $m3u8_dir = RUNTIME_PATH . '/m3u8/';

    /**
     * @param  string $m3u8Url
     * @param         $cdnDrive
     * @param         $module
     * @return string
     */
    public static function encode($m3u8Url, $cdnDrive, $module = 'Api')
    {
        if (empty($m3u8Url)) {
            return '';
        }
        // 每个token可以使用6个小时  但是业务系统每2个小时换token
        $cdnDriveUrl    = CommonService::getCdnDomain('video', $cdnDrive);
        $fileKeyMaxTime = 2 * 3600;
        $tokenMaxTime   = 6 * 3600;
        /*******腾讯的key最多3小时************/
        if ($cdnDrive == 'tencent') {
            $fileKeyMaxTime = 180;
            $tokenMaxTime   = 3600 * 2;
        }

        $fileKey   = md5($m3u8Url . '-' . $cdnDrive . '-' . $cdnDriveUrl . '-' . $fileKeyMaxTime);
        $tokenInfo = redis()->get(self::$prefix . $fileKey);
        if (empty($tokenInfo)) {
            $token     = CommonUtil::getId();
            $tokenInfo = ['token' => $token, 'time' => time()];
            redis()->set(self::$prefix . $fileKey, json_encode($tokenInfo), $fileKeyMaxTime);
            redis()->set(self::$prefix . $token, json_encode(['m3u8' => $m3u8Url, 'cdnDrive' => $cdnDrive, 'time' => time()]), $tokenMaxTime);
        } else {
            $tokenInfo      = json_decode($tokenInfo, true);
            $fileInfoExists = redis()->exists(self::$prefix . $tokenInfo['token']);
            if (!$fileInfoExists) {
                redis()->set(self::$prefix . $tokenInfo['token'], json_encode(['m3u8' => $m3u8Url, 'cdnDrive' => $cdnDrive, 'time' => time()]), $tokenMaxTime);
            }
        }
        $prefix = env()->path("modules.$module");
        // /如果是前台根路径,则不要前缀
        if ($prefix == '/') {
            $prefix = '';
        }
        return $prefix . '/m3u8/p/' . $tokenInfo['token'] . '.m3u8';
    }

    /**
     * 解码前端
     * @param        $token
     * @return mixed
     */
    public static function decode($token)
    {
        $fileInfo = redis()->get(self::$prefix . $token);
        if (empty($fileInfo)) {
            return null;
        }
        $fileInfo = json_decode($fileInfo, true);
        if (empty($fileInfo['m3u8'])) {
            return null;
        }
        if (empty($fileInfo['content'])) {
            $result = self::parseMrsM3u8($fileInfo['m3u8'], $fileInfo['cdnDrive']);
            if (empty($result)) {
                return null;
            }

            // 常规库(非老司机)
            $dirPath = pathinfo($fileInfo['m3u8'], PATHINFO_DIRNAME);
            foreach ($result['files'] as $file) {
                // /如果返回的m3u8文件已经有http //老司机库
                if (strpos($file, 'http') !== false) {
                    break;
                }

                // 常规库(非老司机),走自行签名
                if (strpos($file, '.key') !== false) {
                    $cdnPath = CommonService::getCdnUrl($file, 'video', $fileInfo['cdnDrive']);
                } elseif (strpos($file, '.ts') !== false) {
                    $cdnPath = CommonService::getCdnUrl($dirPath . '/' . (ltrim($file, '/')), 'video', $fileInfo['cdnDrive']);
                }
                if (!empty($cdnPath)) {
                    $result['content'] = str_replace($file, $cdnPath, $result['content']);
                }
            }
            $fileInfo['content'] = $result['content'];
            //            redis()->set(self::$prefix.$token,json_encode($fileInfo),3600*1);
        }
        return ['time' => $fileInfo['time'], 'content' => $fileInfo['content']];
    }

    /**
     * 解析m3u8
     * @param             $m3u8Url
     * @param             $cdnDrive
     * @return array|null
     */
    public static function parseMrsM3u8($m3u8Url, $cdnDrive)
    {
        $cacheKey    = md5($m3u8Url);
        $contentFile = self::$m3u8_dir . substr($cacheKey, 2, 3);
        if (!file_exists($contentFile)) {
            mkdir($contentFile, 0777, true);
        }
        $contentFile .= '/' . $cacheKey . '.m3u8';

        if (!file_exists($contentFile)) {
            /**========二选一,主库是糖心库无签名,主库是老司机和小组有签名=========**/
            // /糖心库
            //            $cdnM3u8Url = ConfigService::getConfig('media_url_m3u8').$m3u8Url;
            // /老司机库
            $cdnM3u8Url = CommonService::getCdnDomain('video', $cdnDrive) . $m3u8Url . value(function () {
                /**
                 * LSG库需要拼接签名
                 * sign 签名
                 * time  当前时间
                 * expired 过期时间  可以为空 默认为3小时
                 * rand 随机数 长度自己定义
                 */
                $time = time();
                $rand = uniqid();
                $key  = ConfigService::getConfig('media_key');
                $uid  = ConfigService::getConfig('media_appid');
                $sign = md5($time . '-' . $rand . '-' . $key);
                return "?time={$time}&rand={$rand}&uid={$uid}&sign={$sign}";
            });
            $cdnM3u8Url = preg_replace('#^https?://[^/]+#', ConfigService::getConfig('media_url_m3u8'), $cdnM3u8Url);// 因为lsj的media_url_m3u8没/m3c m3f m3v这个后缀,所以需要替换一次

            $content = CommonUtil::httpGet($cdnM3u8Url, 5, []);

            if (stripos($content, '#EXTM3U') === false) {
                return null;
            }
            $saveContent = $content;
            /**
             * 判断是常规地址还是解析器地址,常规地址 /xx/xx/xx.m3u8 解析器地址 /xxxx.m3u8 只有一个/
             * TODO 1.解析器,返回的已经处理后的m3u8文件,解析器也有缓存,当内容改变后,这边不知道,会导致ts加载失败 所以不用存储
             * TODO 2.由于每次回源太慢,可以考虑替换掉路径本地存储,但是LSJ库返回的播放地址是解析器地址,不是cdn地址
             */
            //            if(strpos($m3u8Url,'/')===0&substr_count($m3u8Url,'/')==1){
            //                $split = CommonUtil::getSplitChar($saveContent);
            //                $lines = explode($split, $saveContent);
            //                foreach ($lines as $line) {
            //                    if(strpos($line,'http')===false){continue;}
            //                    if(strpos($line, 'URI=') !==false){
            //                        $matches = array();
            //                        preg_match('/URI=\"(.*)\"/', $line, $matches);
            //                        if ($matches[1]) {
            //                            $saveContent=str_replace($matches[1], parse_url($matches[1], PHP_URL_PATH), $saveContent);
            //                        }
            //                    }else if (strpos($line, '.ts') !==false) {
            //                        $saveContent=str_replace($line, parse_url($line, PHP_URL_PATH), $saveContent);
            //                    }
            //                }
            //            }
            if (strpos($saveContent, '#EXTM3U') !== false) {
                // /糖心库储存到本地
                if (strpos($m3u8Url, '/media2') !== false) {
                    file_put_contents($contentFile, $saveContent);
                }
            }
        } else {
            $content = file_get_contents($contentFile);
        }
        if (empty($content)) {
            return null;
        }

        return [
            'content' => $content,
            'files'   => value(function () use ($content) {
                $downloadList = [];
                $split        = CommonUtil::getSplitChar($content);
                $lines        = explode($split, $content);
                foreach ($lines as $line) {
                    if (strpos($line, '.ts') > 0) {
                        $tsFile         = trim($line);
                        $downloadList[] = $tsFile;
                    } elseif (strpos($line, 'URI=') > -1) {
                        $matches = [];
                        preg_match('/URI=\"(.*)\"/', $line, $matches);
                        $keyFile = '';
                        if ($matches[1]) {
                            $keyFile = $matches[1];
                        }
                        if ($keyFile) {
                            $downloadList[] = trim($keyFile);
                        }
                    }
                }
                return $downloadList;
            })
        ];
    }

    /**
     * 下载m3u8
     * @param             $m3u8Url
     * @return array|null
     */
    public static function doDownload($m3u8Url)
    {
        $isChina = IpService::isChina(CommonUtil::getClientIp());

        $result = self::parseMrsM3u8($m3u8Url, $isChina ? 'tencent' : 'aws');
        // 常规库(非老司机)
        $dirPath = pathinfo($m3u8Url, PATHINFO_DIRNAME);
        foreach ($result['files'] as $index => $file) {
            // /如果返回的m3u8文件已经有http //老司机库
            if (strpos($file, 'http') !== false) {
                break;
            }

            // 常规库(非老司机),走自行签名
            if (strpos($file, '.key') !== false) {
                $cdnPath = CommonService::getCdnUrl($file, 'video', );
            } elseif (strpos($file, '.ts') !== false) {
                $cdnPath = CommonService::getCdnUrl($dirPath . '/' . (ltrim($file, '/')), 'video');
            }
            if (!empty($cdnPath)) {
                $result['content']       = str_replace($file, $cdnPath, $result['content']);
                $result['files'][$index] = $cdnPath;
            }
        }
        return $result;
    }
}
