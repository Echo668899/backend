<?php

namespace App\Utils;

class CommonUtil
{
    public static function getId()
    {
        return md5(microtime(true) . mt_rand(1000, 9000));
    }

    /**
     * 获取域名
     * @return string
     */
    public static function getServerHost()
    {
        $host = $_SERVER['HTTP_HOST']; // 默认 fallback

        // Cloudflare
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return 'https://' . $host;
        }
        // AWS
        if (isset($_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO']) && $_SERVER['HTTP_CLOUDFRONT_FORWARDED_PROTO'] === 'https') {
            return 'https://' . $host;
        }

        // 服务器配置
        if (($_SERVER['REQUEST_SCHEME'] == 'https') || ($_SERVER['HTTPS'] == 'on') || ($_SERVER['SERVER_PORT'] == '443')) {
            return 'https://' . $host;
        }

        // 默认返回 HTTP
        return 'http://' . $host;
    }

    /**
     * 二维数组排序
     * @param        $array
     * @param        $key
     * @param        $ids
     * @return mixed
     */
    public static function arraySort($array, $key, $ids)
    {
        $sortIndex = array_flip($ids);
        foreach ($array as &$item) {
            $item['_sort_key'] = $sortIndex[$item[$key]];
            unset($item);
        }

        array_multisort(array_column($array, '_sort_key'), SORT_ASC, $array);
        foreach ($array as &$item) {
            unset($item['_sort_key'], $item);
        }
        return $array;
    }

    /**
     * 二维数组分组
     * @param        $array
     * @param        $key
     * @return mixed
     */
    public static function arrayGroup($array, $key)
    {
        return array_reduce($array, function ($result, $item) use ($key) {
            $result[$item[$key]][] = $item;
            return $result;
        }, []);
    }

    /**
     * 从数组中删除某个值
     * @param        $array
     * @param        $value
     * @return mixed
     */
    public static function arrayRemove($array, $value)
    {
        if (in_array($value, $array)) {
            unset($array[array_search($value, $array)]);
        }
        return $array;
    }

    /**
     * 获取数组的最大嵌套层级
     * @param            $array
     * @return int|mixed
     */
    public static function arrayDepth($array)
    {
        $max = 1; // 只有一层时为 1
        foreach ($array as $v) {
            if (is_array($v)) {
                $max = max($max, self::arrayDepth($v) + 1);
            }
        }
        return $max;
    }

    /**
     * 数组分页
     * @param  array $arr
     * @param  int   $page
     * @param  int   $pageSize
     * @return array
     */
    public static function arrayPage($arr = [], $page = 1, $pageSize = 15)
    {
        $page     = (int) $page;
        $pageSize = (int) $pageSize;
        if (empty($arr) || !$page || !$pageSize) {
            return [];
        }
        $end_index = count($arr);
        $start     = ($page - 1) * $pageSize;
        $end       = $start + $pageSize;
        if ($end > $end_index) {
            $end = $end_index;
        }
        if ($start < 0) {
            $start = 0;
        }
        $new_arr = [];
        for ($i = $start; $i < $end; $i++) {
            $new_arr[] = $arr[$i];
        }
        return $new_arr;
    }

    /**
     * 数字缩写
     * @param         $num
     * @return string
     */
    public static function formatNum($num)
    {
        if ($num > 10000) {
            $thousand = floor($num / 10000);
            $hundred  = floor(($num - $thousand * 10000) / 1000);
            $num      = $thousand . '.' . $hundred . 'w';
        } elseif ($num > 1000) {
            $thousand = floor($num / 1000);
            $hundred  = floor(($num - $thousand * 1000) / 100);
            $num      = $thousand . '.' . $hundred . 'k';
        } else {
            $num = strval($num * 1);
        }
        return $num;
    }

    /**
     * 格式化秒
     * @param         $times
     * @param         $forShort
     * @return string
     */
    public static function formatSecond($times, $forShort = false)
    {
        $t = max(0, (int) $times);

        if ($forShort) {
            $mm = intdiv($t, 60);   // 累计分钟
            $ss = $t % 60;
            return sprintf('%02d:%02d', $mm, $ss);
        }

        $hh = intdiv($t, 3600);
        $mm = intdiv($t % 3600, 60);
        $ss = $t % 60;

        return sprintf('%02d:%02d:%02d', $hh, $mm, $ss);
    }

    /**
     * 验证手机号格式
     * @param            $phoneNumber
     * @return false|int
     */
    public static function checkPhone($phoneNumber)
    {
        // 正则表达式验证中国大陆的手机号
        $pattern = '/^1[3-9]\d{9}$/';
        return preg_match($pattern, $phoneNumber);
    }

    /**
     * 格式化手机号码
     * @param         $phone
     * @param         $label
     * @return string
     */
    public static function formatPhone($phone, $label = '*')
    {
        $phone  = trim(strval($phone));
        $result = '';
        for ($i = 0; $i < strlen($phone); $i++) {
            if ($i > 2 && $i < 7) {
                $result .= $label;
            } else {
                $result .= substr($phone, $i, 1);
            }
        }
        return $result;
    }

    /**
     * 格式化聊天时间
     * @param               $timestamp
     * @return false|string
     */
    public static function formatChatTime($timestamp)
    {
        $now            = time();
        $todayStart     = strtotime('today');
        $yesterdayStart = strtotime('yesterday');
        $weekStart      = strtotime('this week Monday');

        $hour     = (int) date('H', $timestamp);
        $minute   = date('i', $timestamp);
        $timePart = date('H:i', $timestamp);

        // 时间段判断：上午/下午/晚上
        if ($hour < 12) {
            $period = '上午';
        } elseif ($hour < 18) {
            $period = '下午';
        } else {
            $period = '晚上';
        }

        if ($timestamp >= $todayStart) {
            return $timePart;
        } elseif ($timestamp >= $yesterdayStart) {
            return "昨天 $timePart";
        } elseif ($timestamp >= $weekStart) {
            $weekdays = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
            $weekday  = $weekdays[date('w', $timestamp)];
            return "$weekday $timePart";
        }
        $month = date('n', $timestamp);
        $day   = date('j', $timestamp);
        return "{$month}月{$day}日 {$period}{$timePart}";
    }

    /**
     * 手机号处理
     * @param         $phone
     * @return string
     */
    public static function filterPhone($phone)
    {
        if (strstr($phone, 'system_')) {
            return '';
        }
        if (strstr($phone, 'device_')) {
            return '';
        }
        if (strstr($phone, 'phone_')) {
            return '';
        }
        if (strstr($phone, 'web_')) {
            return '';
        }
        return strval($phone);
    }

    /**
     * 获取客户端ip
     * @return array|false|mixed|string
     */
    public static function getClientIp()
    {
        if (isset($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR']) && !empty($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) {
            $ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($HTTP_SERVER_VARS['HTTP_CLIENT_IP']) && !empty($HTTP_SERVER_VARS['HTTP_CLIENT_IP'])) {
            $ip = $HTTP_SERVER_VARS['HTTP_CLIENT_IP'];
        } elseif (isset($HTTP_SERVER_VARS['REMOTE_ADDR']) && !empty($HTTP_SERVER_VARS['REMOTE_ADDR'])) {
            $ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = 'Unknown';
        }
        if (strpos($ip, ',') > 0) {
            return substr($ip, 0, strpos($ip, ','));
        }
        return $ip;
    }

    /**
     * 请求json数据
     * @param  string      $url
     * @param  mixed       $data
     * @param  int         $timeout
     * @param  array       $header
     * @return bool|string
     */
    public static function httpJson($url, $data, $timeout = 40, $header = [])
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: ' . strlen($data);
        $ch       = self::initCurl($url, $header, $timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $rs = curl_exec($ch);
        curl_close($ch);
        return $rs;
    }

    /**
     * 初始化一个curl
     * @param  string         $url
     * @param  array          $header
     * @param  int            $timeout
     * @return false|resource
     */
    public static function initCurl($url, $header = [], $timeout = 40)
    {
        if (!function_exists('curl_init')) {
            die('undefined function curl_init');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CAINFO, BASE_PATH . 'app/Resource/cacert.pem');
        /**************測試環境先不驗證ssl準確性**************/
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        /**************測試環境先不驗證ssl準確性**************/
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:32.0) Gecko/20100101 Firefox/32.0');
        return $ch;
    }

    /**
     * 请求json数据
     * @param  string      $url
     * @param  mixed       $data
     * @param  int         $timeout
     * @param  array       $header
     * @return bool|string
     */
    public static function httpRaw($url, $data, $timeout = 40, $header = [])
    {
        $header[] = 'Content-Type: Content-Type: application/octet-stream';
        $header[] = 'Content-Length: ' . strlen($data);
        $ch       = self::initCurl($url, $header, $timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $rs = curl_exec($ch);
        curl_close($ch);
        return $rs;
    }

    /**
     * http post
     * @param              $url
     * @param              $data
     * @param  int         $timeout
     * @param  array       $header
     * @return bool|string
     */
    public static function httpPost($url, $data, $timeout = 40, $header = [])
    {
        $ch = self::initCurl($url, $header, $timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $rs = curl_exec($ch);
        curl_close($ch);
        return $rs;
    }

    /**
     * http get
     * @param              $url
     * @param  int         $timeout
     * @param  array       $header
     * @param              $referer
     * @return bool|string
     */
    public static function httpGet($url, $timeout = 40, $header = [], $referer = '')
    {
        $ch = self::initCurl($url, $header, $timeout);
        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * @param              $url
     * @param  int         $timeout
     * @param  array       $header
     * @param  string      $referer
     * @param  string      $proxy
     * @return bool|string
     */
    public static function httpGetProxy($url, $timeout = 40, $header = [], $referer = '', $proxy = '')
    {
        $ch = self::initCurl($url, $header, $timeout);
        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }

        if ($proxy) {
            if (stripos($proxy, '|') !== false) {
                list($host, $auth) = explode('|', $proxy);

                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $auth);
            } else {
                $host = $proxy;
            }

            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);// 使用了SOCKS5代理
            curl_setopt($ch, CURLOPT_PROXY, $host);
        }

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 获取今天24点59分59秒的时间
     * @return number
     */
    public static function getTodayEndTime()
    {
        $time = date('Y-m-d 23:59:59');
        return strtotime($time);
    }

    /**
     * 获取周一
     * @return false|int
     */
    public static function getWeekFirst()
    {
        return strtotime(date('Y-m-d 00:00:00', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600)));
    }

    /**
     * 获取周日
     * @return false|int
     */
    public static function getWeekEnd()
    {
        return strtotime(date('Y-m-d 00:00:00', (time() + (7 - (date('w') == 0 ? 7 : date('w'))) * 24 * 3600)));
    }

    /**
     * 获取本月第一天
     * @return false|int
     */
    public static function getMonthStart()
    {
        return strtotime(date('Y-m', time()) . '-01 00:00:00');
    }

    /**
     * @param         $time1
     * @param  null   $time2
     * @return string
     * @desc 计算两个时间到时间差，社会化显示
     */
    public static function showTimeDiff($time1, $time2 = null)
    {
        if (empty($time1) && empty($time2)) {
            return '';
        }

        $time2 = !$time2 ? time() : $time2;

        $timeDiff = $time2 - $time1;

        if ($timeDiff >= 172800) {
            // 两天前
            return date('m-d H:i', $time1 * 1);
        } elseif ($timeDiff >= 86400) {
            // 昨天
            $todayStart = self::getTodayZeroTime() - 86400;
            if ($time1 >= $todayStart) {
                return '昨日 ' . date('H:i', $time1 * 1);
            }
            return '前日 ' . date('H:i', $time1 * 1);
        } elseif ($timeDiff >= 43200) {
            // 超过半天(但可能涉及今日0点)
            $todayStart = self::getTodayZeroTime();
            if ($time1 >= $todayStart) {
                return '今天 ' . date('H:i', $time1 * 1);
            }
            return '昨日 ' . date('H:i', $time1 * 1);
        } elseif ($timeDiff >= 3600) {
            $str   = '';
            $hours = floor($timeDiff / 3600);
            if ($hours > 0) {
                $str .= $hours . '小时 ';
            }
            $str .= '前';
            return $str;
        } elseif ($timeDiff >= 60) {
            $hours = ceil($timeDiff / 60);
            return $hours . '分钟前';
        }
        return '刚刚';
    }

    /**
     * 获取今天零点时间
     * @return number
     */
    public static function getTodayZeroTime()
    {
        $time = date('Y-m-d 00:00:00');
        return strtotime($time);
    }

    /**
     * 检测关键字
     * @param       $content
     * @return bool
     */
    public static function checkKeywords($content)
    {
        $content  = CommonUtil::makeSemiangle($content);
        $content  = preg_replace('/ /iU', '', $content);
        $checkArr = [
            '.me', '.com', '.top', '.info', '.cn', '.net', '.://', '.xyz', '.vip', '.org', '.edu', '.tv', '.uk', '.jp', '.club', '.cc', '.porn', '.app', '.live', '.hk', '.site',
            '人兽', '人妻', '幼女', '幼钕', '御姐', '乖乖水', '药物', '約炮', '包', '企鹅', '微信', 'vx', '抠', '筘', '扣', '捃', '加', '联系', '破解', '聊', '私',
            '约炮', '裙', 'Q', 'q', '管方', '交友', '肏茓', '网址', '约-炮', '群', '桾', '峮', '百分百', '同城', '约炮', '约泡', '泡', '佰芬佰',
            '全国约炮', '騒女', '极品', '粉嫩', '骚穴', 'Ｑ', '箹', '帝王', '服务', '电话调情', '骚女', '陪約', '快来玩',
            '㈠', '㈡', '㈢', '㈣', '㈤', '㈥', '㈦', '㈧', '㈨', 'ⓠ',
            '❶', '❷', '❸', '❹', '❺', '❻', '❼', '❽', '❾',
            '①', '②', '③', '④', '⑤', '⑥', '⑦', '⑧', '⑨',
            '（一）', '（二）', '（三）', '（四）', '（五）', '（六）', '（七）', '（八）', '（九）',
            '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '零',
            '¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹', '⁰',
            '🐧',
        ];
        foreach ($checkArr as $check) {
            if (strpos($content, $check) !== false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 全角转换半角
     * @param         $str
     * @return string
     */
    public static function makeSemiangle($str)
    {
        $arr = ['０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５'     => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ'     => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ'     => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ'     => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ'     => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ'     => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ'     => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ'     => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ'     => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ'     => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ'     => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ'     => 'y', 'ｚ' => 'z',
            '（'     => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
            '】'     => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
            '‘'      => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
            '》'     => '>', '■' => '.',
            '％'     => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
            '：'     => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
            '；'     => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
            '”'      => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
            '　'     => ' ', '＄' => '$', '＠' => '@', '＃' => '#', '＾' => '^', '＆' => '&', '＊' => '*',
            '＂'     => '"'];

        return strtr($str, $arr);
    }

    /**
     * 格式化时间
     * @param               $ptime
     * @return false|string
     */
    public static function ucTimeAgo($ptime)
    {
        //        $etime = time() - $ptime + 1;
        //        switch ($etime) {
        //            case $etime <= 60:
        //                $msg = '刚刚';
        //                break;
        //            case $etime > 60 && $etime <= 60 * 60:
        //                $msg = floor($etime / 60) . '分钟前';
        //                break;
        //            case $etime > 60 * 60 && $etime <= 24 * 60 * 60:
        //                $msg = date('Ymd', $ptime) == date('Ymd', time()) ? '今天' . date('H:i', $ptime) : '昨天';
        //                break;
        //            case $etime > 24 * 60 * 60 && $etime <= 2 * 24 * 60 * 60:
        //                $msg = date('Ymd', $ptime) + 1 == date('Ymd', time()) ? '昨天' . date('H:i', $ptime) : '前天 ';
        //                break;
        //            case $etime > 2 * 24 * 60 * 60 && $etime <= 12 * 30 * 24 * 60 * 60:
        //                $msg = date('Y', $ptime) == date('Y', time()) ? date('m-d', $ptime) : date('Y-m-d', $ptime);
        //                break;
        //            default:
        //                $msg = date('m-d', $ptime);
        //        }
        //        return $msg;

        $current = time();
        $etime   = $current - $ptime;

        // 处理今天和昨天的时间范围
        $todayStart      = strtotime('today');
        $yesterdayStart  = $todayStart - 86400;
        $twoDaysAgoStart = $yesterdayStart - 86400;

        switch (true) {
            case ($etime < 60):
                return '刚刚';
            case ($etime < 3600): // 60分钟
                return floor($etime / 60) . '分钟前';
            case ($ptime >= $todayStart): // 今天
                return '今天 ' . date('H:i', $ptime);
            case ($ptime >= $yesterdayStart): // 昨天
                return '昨天 ' . date('H:i', $ptime);
            case ($ptime >= $twoDaysAgoStart): // 前天
                return '前天 ' . date('H:i', $ptime);
            case ($etime < 604800): // 7天内 (7*86400)
                $days = floor($etime / 86400);
                return $days . '天前';
            case ($etime < 2592000): // 30天内 (30*86400)
                $weeks = floor($etime / 604800);
                return $weeks . '周前';
            case ($etime < 31536000): // 1年内 (365*86400)
                $months = floor($etime / 2592000);
                return $months . '月前';
            default: // 超过1年
                $years = floor($etime / 31536000);
                return $years . '年前';
        }
    }

    /**
     * 生成订单号
     * @param  string $per
     * @return string
     */
    public static function createOrderNo($per = '')
    {
        if ($per) {
            return $per . date('ymdHis') . mt_rand(10000, 99999);
        }
        return date('ymdHis') . mt_rand(10000, 99999);
    }

    /**
     * 汉字转拼音
     * @param         $s
     * @param         $isFirst
     * @return string
     */
    public static function pinyin($s, $isFirst = false)
    {
        static $pinyins;
        $s   = trim($s);
        $len = strlen($s);
        if ($len < 3) {
            return $s;
        }
        if (!isset($pinyins)) {
            $data    = file_get_contents(BASE_PATH . '/app/Resource/pinyin.data');
            $a1      = explode('|', $data);
            $pinyins = [];
            foreach ($a1 as $v) {
                $a2              = explode(':', $v);
                $pinyins[$a2[0]] = $a2[1];
            }
        }
        $rs = '';
        for ($i = 0; $i < $len; $i++) {
            $o = ord($s[$i]);
            if ($o < 0x80) {
                if (($o >= 48 && $o <= 57) || ($o >= 97 && $o <= 122)) {
                    $rs .= $s[$i]; // 0-9 a-z
                } elseif ($o >= 65 && $o <= 90) {
                    $rs .= strtolower($s[$i]); // A-Z
                } else {
                    $rs .= '_';
                }
            } else {
                $z = $s[$i] . $s[++$i] . $s[++$i];
                if (isset($pinyins[$z])) {
                    $rs .= $isFirst ? $pinyins[$z][0] : $pinyins[$z];
                } else {
                    $rs .= '_';
                }
            }
        }
        return $rs;
    }

    /**
     * 获取口令
     * @param         $id
     * @param         $pre
     * @param         $length
     * @return string
     */
    public static function getKouling($id, $pre, $length = 10)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $kouling    = '';
        if (is_string($id)) {
            $hash          = md5($id);
            $hashLen       = strlen($hash);
            $charactersLen = strlen($characters);
            for ($i = 0; $i < 10; $i++) {
                $index = hexdec($hash[$i % $hashLen]) % $charactersLen;
                $kouling .= $characters[$index];
            }
        } else {
            srand($id);
            for ($i = 0; $i < $length; $i++) {
                $kouling .= $characters[rand(0, strlen($characters) - 1)];
            }
        }

        return $pre . $kouling;
    }

    /**
     * 获取每行的分割符号
     * @param         $content
     * @return string
     */
    public static function getSplitChar($content)
    {
        $split = "\n";
        if (strpos($content, "\r\n") > 0) {
            $split = "\r\n";
        }
        return $split;
    }

    /**
     * 判断字符串是否中文
     * @param  string $str
     * @return bool
     */
    public static function strIsZh(string $str)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str) === 1;
    }

    /**
     * 获取UA
     * @return mixed
     */
    public static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * 获取浏览器类型
     * @return string
     */
    public static function getBrowser()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT'])) {
            $_SERVER['HTTP_USER_AGENT'] = '';// /防下面判断报错
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            return 'wechat';
        } elseif (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mqqbrowser')) {
            /**
             * 判断是qq浏览器还是qq
             * QQ浏览器 [error] mozilla/5.0 (linux; u; android 11; zh-cn; cph1989 build/rp1a.200720.011) applewebkit/537.36 (khtml, like gecko) version/4.0 chrome/89.0.4389.72 mqqbrowser/13.4 mobile safari/537.36 covc/045830 2022-12-06 16:10:16
             * QQ内置 mozilla/5.0 (linux; android 10; m2006c3lc build/qp1a.190711.020; wv) applewebkit/537.36 (khtml, like gecko) version/4.0 chrome/98.0.4758.102 mqqbrowser/6.2 tbs/046316 mobile safari/537.36 v1_and_sq_8.8.90_2828_yyb_d a_8089000 pa qq/8.8.90.7975 nettype/wifi webp/0.3.0 pixel/720 statusbarheight/56 simpleuiswitch/0 qqtheme/1103 inmagicwin/0 studymode/0 currentmode/0 currentfontscale/0.87 globaldensityscale/0.9 appid/537119557
             */
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), '_sq_') === false) {
                return 'qqbrowser';// qq浏览器
            }
            return 'qq';// qq内置
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            return 'ios';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
            return 'android';
        }
        return 'other';
    }

    /**
     * 判断是否mobile
     * @return bool
     */
    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA']) && strpos($_SERVER['HTTP_VIA'], 'CloudFront') === false) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
        }

        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = [
                'huawei',
                'nokia', 'sony',
                'android', 'xoom',
                'ipad', 'phone', 'ipod',
                'wap', 'ericsson',
                'mot', 'samsung',
                'htc', 'sgh',
                'lg', 'sharp',
                'sie-', 'philips',
                'panasonic', 'alcatel',
                'lenovo', 'iphone', 'ipod',
                'blackberry', 'meizu',
                'android', 'netfront',
                'symbian', 'ucweb',
                'windowsce', 'palm',
                'operamini', 'operamobi',
                'openwave', 'nexusone',
                'cldc', 'midp',
                'wap', 'mobile',
            ];
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match('/(' . implode('|', $clientkeywords) . ')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 是否爬虫
     * @return bool
     */
    public static function isSpider()
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

        if ($ua === '') {
            // 没 UA 的 99% 也是脚本/爬虫
            return true;
        }

        // 1. 正常 bot 关键字
        $botKeywords = [
            'bot',
            'spider',
            'crawler',

            // 内置浏览器,不屏蔽
            //            'yahoo',
            //            'twitter',
            //            'telegram',
            //            'facebook',
            //            'google',
            //            'bing',
            //            'baidu',
            //            'yandex',
            //            'duckduck',
            //            'ahrefs',
            //            'semrush',
            //            'mj12',

            'python', 'scrapy', 'curl', 'wget', 'httpclient', 'java', 'go-http', 'axios', 'node-fetch', 'ruby', 'perl', 'powershell', 'headless', 'phantom'
        ];
        foreach ($botKeywords as $kw) {
            if (strpos($ua, $kw) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取渠道码
     * @param         $channel
     * @return string
     */
    public static function getChannelCode($channel)
    {
        $channel = strval($channel);
        if (strpos($channel, 'channel://') !== false) {
            $channel = str_replace('channel://', '', $channel);
            $channel = trim($channel);
            if (mb_strlen($channel) <= 15) {
                // 移除系统渠道码
                if (in_array($channel, ['_all', 'system'])) {
                    return '';
                }
                return $channel;
            }
        }
        return '';
    }

    /**
     * 获取邀请码
     * @param         $share
     * @return string
     */
    public static function getShareCode($share)
    {
        $share = strval($share);
        if (strpos($share, 'share://') !== false) {
            $share = str_replace('share://', '', $share);
            $share = trim($share);
            if (mb_strlen($share) <= 15) {
                if (in_array($share, ['_all', 'system'])) {
                    return '';
                }
                return $share;
            }
        }
        return '';
    }
}
