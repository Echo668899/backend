<?php

namespace App\Constants;

class CommonValues
{
    /**
     * 是否
     * @param  null            $value
     * @return string|string[]
     */
    public static function getIs($value = null)
    {
        $arr = [
            '1' => '是',
            '0' => '否',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 广告类型
     * @param  null            $value
     * @return string|string[]
     */
    public static function getAdType($value = null)
    {
        $arr = [
            'text'  => '文字',
            'image' => '图片',
            'video' => '视频',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取设备类型
     * @param  null            $value
     * @return string|string[]
     */
    public static function getDeviceTypes($value = null)
    {
        $arr = [
            'ios'     => 'IOS',
            'android' => 'Android',
            'web'     => 'Web',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取视频来源
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getMediaSource($val = null)
    {
        $arrs = [
            'default' => '默认库',
            'xiaozu'  => '小组库',
            //            'tangxin' => '糖心库',//糖心项目才开启
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取用户性别
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getUserSex($val = null)
    {
        $arrs = [
            'unknown' => '未知',
            'man'     => '男',
            'woman'   => '女',
        ];
        if ($val !== null && $val !== '') {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 排行榜类型
     * @param                  $val
     * @return string|string[]
     */
    public static function getRankingType($val = null)
    {
        $arrs = [
            'day'   => '日榜',
            'week'  => '周榜',
            'month' => '月榜',
            'all'   => '总榜',
        ];
        if ($val !== null && $val !== '') {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取购买类型
     * @param         $money
     * @return string
     */
    public static function getPayTypeByMoney($money)
    {
        if ($money > 0) {
            return 'money';
        } elseif ($money == 0) {
            return 'vip';
        }
        return 'free';
    }

    /**
     * 获取购买类型
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getPayTypes($val = null)
    {
        $arrs = [
            'money' => '金币',
            'vip'   => 'VIP',
            'free'  => '免费',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取评论类型
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getCommentType($val = null)
    {
        $arrs = [
            'movie'  => '视频',
            'comics' => '漫画',
            'audio'  => '有声',
            'novel'  => '小说',
            'post'   => '帖子',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 评论状态
     * @param                  $val
     * @return string[]|string
     */
    public static function getCommentStatus($val = null)
    {
        $arrs = [
            '0' => '待审核',
            '1' => '正常',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * movie 会员等级
     * @param                  $val
     * @return string[]|string
     */
    public static function getUserLevel($val = null)
    {
        $arrs = [
            '30' => ['name' => '至尊'],
            '20' => ['name' => '终身'],
            '5'  => ['name' => '年度'],
            '4'  => ['name' => '季度'],
            '3'  => ['name' => '月度'],
            '2'  => ['name' => '新人'],
            '1'  => ['name' => '体验'],
            '0'  => ['name' => '游客'],
        ];
        if ($val !== null) {
            return $arrs[$val] ?? [];
        }
        return $arrs;
    }

    /**
     * 会员组分组
     * @param  null            $val
     * @return string|string[]
     */
    public static function getUserGroupType($val = null)
    {
        $arrs = [
            'normal' => '普通',
            'dark'   => '暗网',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 金币套餐类型
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getUserProductType($val = null)
    {
        $arrs = [
            'point' => '金币',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 会员卡类型
     * @param  null            $val
     * @return string|string[]
     */
    public static function getPromotionType($val = null)
    {
        $arrs = [
            0 => '正常价格',
            1 => '新人专享',
            //            3 => '老用户卡',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 兑换码类型
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getUserCodeType($val = null)
    {
        $arrs = [
            'group' => '用户组',
            'point' => '金币',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 兑换码状态
     * @param  null            $val
     * @return string|string[]
     */
    public static function getUserCodeStatus($val = null)
    {
        $arrs = [
            '0'  => '未使用',
            '1'  => '已使用',
            '-1' => '作废',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取视频画布
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getMovieCanvas($val = null)
    {
        $arrs = [
            'long'  => '横屏',
            'short' => '竖屏',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取视频菜单位置
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getMovieNavPosition($val = null)
    {
        $arrs = [
            'normal'  => '视频',
            'dark'    => '暗网',
            'cartoon' => '动漫',
            'short'   => '短剧',
            'movie'   => '影视',
            'bl'      => '蓝颜',
            'douyin'  => '抖音'
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取视频菜单样式
     * @param                  $val
     * @return string[]|string
     */
    public static function getMovieNavStyle($val = null)
    {
        $arrs = [
            'video_1' => '模块模式',
            'video_2' => '列表模式-纯列表',
            'video_3' => '列表模式-tab+列表',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 视频模块的风格 1 2 3 4
     * @param                  $val
     * @return string[]|string
     */
    public static function getMovieBlockStyle($val = null)
    {
        $arrs = [
            // -10到-19为AdvApp
            '-10' => '样式- -10-广告(App),1*5 不可选择',

            // -1到-9为Adv
            '-3' => '样式- -3-广告,横-1*3-横向滑动 不可选择',
            '-2' => '样式- -2-广告,竖-1*3 不可选择',
            '-1' => '样式- -1-广告,横 不可选择',

            // 1到9为横版样式
            '1' => '样式1-横-1*1-横向滑动',
            '2' => '样式2-横-1*1-不可滑动',
            '3' => '样式3-横-1*4-五宫格',
            '4' => '样式4-横-2*2-四宫格',
            // 10到19为竖版样式
            '10' => '样式10-竖-1*n-横向滑动',
            '11' => '样式11-竖-1*2-不可滑动',
            '12' => '样式12-竖-1*3-不可滑动',
            // 特殊样式
            '20' => '样式20-UP头像',
            '30' => '样式30-瀑布流',
            // 特殊样式-tab类型
            '40' => '样式40-tab选项1(1*2)',
            '41' => '样式41-tab选项2(1*3)',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 视频模块路由
     * @param                  $val
     * @return string|string[]
     */
    public static function getMovieBlockRoute($val = null)
    {
        $arrs = [
            ''        => '模块详情',
            'none'    => '禁止跳转',
            'filter'  => '筛选页',
            'ranking' => '排行榜',
            'day'     => '每日更新',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * movie 状态
     * @param                  $val
     * @return string[]|string
     */
    public static function getMovieStatus($val = null)
    {
        $arrs = [
            '-2' => '处理中',
            '-1' => '已下架',
            '0'  => '未上架',
            '1'  => '已上架',
            '2'  => '待审核',
            '3'  => '未通过',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取视频所属板块
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getMoviePosition($val = null)
    {
        $arrs = [
            'normal' => '视频',
            //            'dark' => '暗网',
            'cartoon' => '动漫',
            //            'short' => '短剧',
            //            'douyin'=>'抖音',
            //            'bl'=>'蓝颜',
            //            'movie' => '影视',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取视频所属板块
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getMovieLinkType($val = null)
    {
        $arrs = [
            '0' => '单集',
            '1' => '多集',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取视频的图标类型
     * @param                  $val
     * @return string[]|string
     */
    public static function getMovieIcon($val = null)
    {
        $arrs = [
            'new'        => '最新',
            'hot'        => '热门',
            'recommend'  => '推荐',
            'news'       => '看点',
            'uncensored' => '无码',
            'hd'         => '高清',
            'big_news'   => '黑料',
            'chinese'    => '中文',
            'self'       => '独家',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取漫画菜单位置
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getComicsNavPosition($val = null)
    {
        $arrs = [
            'normal' => '漫画',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取漫画菜单样式
     * @param                  $val
     * @return string[]|string
     */
    public static function getComicsNavStyle($val = null)
    {
        $arrs = [
            'comics_1' => '模块模式',
            'comics_2' => '列表模式-纯列表',
            'comics_3' => '列表模式-tab+列表',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取漫画状态
     * @param        $value
     * @return array
     */
    public static function getComicsStatus($value = '')
    {
        $arr = [
            0  => '未上架',
            1  => '已上架',
            -1 => '已下架',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取漫画更新状态
     * @param                  $val
     * @return string[]|string
     */
    public static function getComicsUpdateStatus($val = null)
    {
        $arrs = [
            '0' => '更新中',
            '1' => '已完结',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取分类
     * @param  null            $value
     * @return string|string[]
     */
    public static function getComicsCategories($value = null)
    {
        $arr = [
            '韩漫'     => '韩漫',
            '日漫'     => '日漫',
            '国漫'     => '国漫',
            '本子'     => '本子',
            '色图'     => '色图',
            '腐漫'     => '腐漫',
            'Cosplay'  => 'Cosplay',
            '3D'       => '3D',
            'CG'       => 'CG',
            '欧美漫画' => '欧美漫画',
            '港台漫画' => '港台漫画',
            '真人漫画' => '真人漫画',
            '同人'     => '同人',
            '写真'     => '写真',
            'AI'       => 'AI',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取分类
     * @param  null            $value
     * @return string|string[]
     */
    public static function getComicsCategoriesCode($value = null)
    {
        $arr = [
            '韩漫'     => 'hanman',
            '日漫'     => 'riman',
            '国漫'     => 'guoman',
            '本子'     => 'benzi',
            '色图'     => 'setu',
            '腐漫'     => 'fuman',
            'Cosplay'  => 'cosplay',
            '3D'       => '3d',
            'CG'       => 'cg',
            '欧美漫画' => 'oumei',
            '港台漫画' => 'gangtai',
            '真人漫画' => 'zhenren',
            '同人'     => 'tongren',
            '写真'     => 'xiezhen',
            'AI'       => 'ai',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取漫画的图标类型
     * @param                  $val
     * @return string[]|string
     */
    public static function getComicsIcon($val = null)
    {
        $arrs = [
            'new'       => '最新',
            'hot'       => '热门',
            'recommend' => '推荐',
            'news'      => '完结',
            'self'      => '独家',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取星期
     * @param  null            $value
     * @return string|string[]
     */
    public static function getComicsWeek($value = null)
    {
        $arr = [
            '周日' => 7,
            '周一' => 1,
            '周二' => 2,
            '周三' => 3,
            '周四' => 4,
            '周五' => 5,
            '周六' => 6,
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value] * 1;
    }

    /**
     * 漫画模块的风格 1 2 3 4
     * @param                  $val
     * @return string[]|string
     */
    public static function getComicsBlockStyle($val = null)
    {
        $arrs = [
            // -10到-19为AdvApp
            '-10' => '样式- -10-广告(App),1*5 不可选择',

            // -1到-9为Adv
            '-2' => '样式- -2-广告,竖-1*3 不可选择',
            '-1' => '样式- -1-广告,横 不可选择',

            // 1到9为横版样式
            '1' => '样式1-横-1*1-横向滑动',
            '2' => '样式2-横-1*1-不可滑动',
            '3' => '样式3-横-1*4-五宫格',
            '4' => '样式4-横-2*2-四宫格',
            // 10到19为竖版样式
            '10' => '样式10-竖-1*n-横向滑动',
            '11' => '样式11-竖-1*2-不可滑动',
            '12' => '样式12-竖-1*3-不可滑动',
            // 特殊样式
            // '20' => '样式20-UP头像',
            '30' => '样式30-瀑布流',
            // 特殊样式-tab类型
            '40' => '样式40-tab选项1(1*2)',
            '41' => '样式41-tab选项2(1*3)',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 漫画模块路由
     * @param                  $val
     * @return string|string[]
     */
    public static function getComicsBlockRoute($val = null)
    {
        $arrs = [
            ''        => '模块详情',
            'none'    => '禁止跳转',
            'filter'  => '筛选页',
            'ranking' => '排行榜',
            'day'     => '每日更新',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取小说菜单样式
     * @param                  $val
     * @return string[]|string
     */
    public static function getNovelNavStyle($val = null)
    {
        $arrs = [
            'novel_1' => '模块模式',
            'novel_2' => '列表模式-纯列表',
            'novel_3' => '列表模式-tab+列表',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取小说模块样式
     * @param                  $val
     * @return string|string[]
     */
    public static function getNovelBlockStyle($val = null)
    {
        $arrs = [
            // -10到-19为AdvApp
            '-10' => '样式- -10-广告(App),1*5 不可选择',

            // -1到-9为Adv
            '-2' => '样式- -2-广告,竖-1*3 不可选择',
            '-1' => '样式- -1-广告,横 不可选择',

            // 1到9为横版样式
            '1' => '样式1-横-1*1-横向滑动',
            '2' => '样式2-横-1*1-不可滑动',
            '3' => '样式3-横-1*4-五宫格',
            '4' => '样式4-横-2*2-四宫格',
            // 10到19为竖版样式
            '10' => '样式10-竖-1*n-横向滑动',
            '11' => '样式11-竖-1*2-不可滑动',
            '12' => '样式12-竖-1*3-不可滑动',
            // 特殊样式
            // '20' => '样式20-UP头像',
            '30' => '样式30-瀑布流',
            // 特殊样式-tab类型
            '40' => '样式40-tab选项1(1*2)',
            '41' => '样式41-tab选项2(1*3)',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 小说模块路由
     * @param                  $val
     * @return string|string[]
     */
    public static function getNovelBlockRoute($val = null)
    {
        $arrs = [
            ''        => '模块详情',
            'none'    => '禁止跳转',
            'filter'  => '筛选页',
            'ranking' => '排行榜',
            'day'     => '每日更新',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取小说菜单位置
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getNovelNavPosition($val = null)
    {
        $arrs = [
            'normal' => '小说',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取小说状态
     * @param        $value
     * @return array
     */
    public static function getNovelStatus($value = '')
    {
        $arr = [
            0  => '未上架',
            1  => '已上架',
            -1 => '已下架',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     *  获取小说更新状态
     * @param                  $val
     * @return string[]|string
     */
    public static function getNovelUpdateStatus($val = null)
    {
        $arrs = [
            '0' => '更新中',
            '1' => '已完结',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取小说分类
     * @param  null            $value
     * @return string|string[]
     */
    public static function getNovelCategories($value = null)
    {
        $arr = [
            '18R'    => '成人',
            'normal' => '贤者',
            //            'audio' => '有声'
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取小说的图标类型
     * @param                  $val
     * @return string[]|string
     */
    public static function getNovelIcon($val = null)
    {
        $arrs = [
            'new'       => '最新',
            'hot'       => '热门',
            'recommend' => '推荐',
            'news'      => '完结',
            'self'      => '独家',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取有声菜单样式
     * @param                  $val
     * @return string[]|string
     */
    public static function getAudioNavStyle($val = null)
    {
        $arrs = [
            'audio_1' => '模块模式',
            'audio_2' => '列表模式-纯列表',
            'audio_3' => '列表模式-tab+列表',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取有声模块样式
     * @param                  $val
     * @return string|string[]
     */
    public static function getAudioBlockStyle($val = null)
    {
        $arrs = [
            // -10到-19为AdvApp
            '-10' => '样式- -10-广告(App),1*5 不可选择',

            // -1到-9为Adv
            '-2' => '样式- -2-广告,竖-1*3 不可选择',
            '-1' => '样式- -1-广告,横 不可选择',

            // 1到9为横版样式
            '1' => '样式1-横-1*1-横向滑动',
            '2' => '样式2-横-1*1-不可滑动',
            '3' => '样式3-横-1*4-五宫格',
            '4' => '样式4-横-2*2-四宫格',
            // 10到19为竖版样式
            '10' => '样式10-竖-1*n-横向滑动',
            '11' => '样式11-竖-1*2-不可滑动',
            '12' => '样式12-竖-1*3-不可滑动',
            // 特殊样式
            // '20' => '样式20-UP头像',
            '30' => '样式30-瀑布流',
            // 特殊样式-tab类型
            '40' => '样式40-tab选项1(1*2)',
            '41' => '样式41-tab选项2(1*3)',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 有声模块路由
     * @param                  $val
     * @return string|string[]
     */
    public static function getAudioBlockRoute($val = null)
    {
        $arrs = [
            ''        => '模块详情',
            'none'    => '禁止跳转',
            'filter'  => '筛选页',
            'ranking' => '排行榜',
            'day'     => '每日更新',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取有声菜单位置
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getAudioNavPosition($val = null)
    {
        $arrs = [
            'normal' => '有声',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取有声状态
     * @param        $value
     * @return array
     */
    public static function getAudioStatus($value = '')
    {
        $arr = [
            0  => '未上架',
            1  => '已上架',
            -1 => '已下架',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     *  获取有声更新状态
     * @param                  $val
     * @return string[]|string
     */
    public static function getAudioUpdateStatus($val = null)
    {
        $arrs = [
            '0' => '更新中',
            '1' => '已完结',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取有声分类
     * @param  null            $value
     * @return string|string[]
     */
    public static function getAudioCategories($value = null)
    {
        $arr = [
            //            '18R' => '成人',
            //            'normal' => '贤者',
            'audio' => '有声'
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取有声的图标类型
     * @param                  $val
     * @return string[]|string
     */
    public static function getAudioIcon($val = null)
    {
        $arrs = [
            'new'       => '最新',
            'hot'       => '热门',
            'recommend' => '推荐',
            'news'      => '完结',
            'self'      => '独家',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     *  获取模块的展示样式
     * @param                  $val
     * @return string[]|string
     */
    public static function getPostNavPosition($val = null)
    {
        $arrs = [
            'normal' => '帖子',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取帖子菜单样式
     * @param                  $val
     * @return string[]|string
     */
    public static function getPostNavStyle($val = null)
    {
        $arrs = [
            //            'post_1' => '模块模式',
            'post_2' => '列表模式-纯列表',
            'post_3' => '列表模式-tab+列表',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     *  获取模块的展示样式
     * @param                  $val
     * @return string[]|string
     */
    public static function getPostBlockStyle($val = null)
    {
        $arrs = [
            // -10到-19为AdvApp
            '-10' => '样式- -10-广告(App),1*5 不可选择',

            // -1到-9为Adv
            '-2' => '样式- -2-广告,竖-1*3 不可选择',
            '-1' => '样式- -1-广告,横 不可选择',

            // 1到9为列表样式
            '1' => '样式1-仿微信列表',
            '2' => '样式2-仿论坛列表',
            // 10到19为卡片样式
            '10' => '样式10-仿小红书卡片',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 帖子模块路由
     * @param                  $val
     * @return string|string[]
     */
    public static function getPostBlockRoute($val = null)
    {
        $arrs = [
            ''        => '模块详情',
            'none'    => '禁止跳转',
            'ranking' => '排行榜',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取帖子所属板块
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getPostPosition($val = null)
    {
        $arrs = [
            'normal' => '社区',
            'file'   => '种子',
            'game'   => '游戏',
            'image'  => '色图',
            'notice' => '公告',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 帖子权限
     * @param                  $val
     * @return string|string[]
     */
    public static function getPostPermission($val = null)
    {
        $arrs = [
            'public'  => '公开',
            'private' => '私密',
            //            'subscribe' => '订阅',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 帖子状态
     * @param                  $val
     * @return string|string[]
     */
    public static function getPostStatus($val = null)
    {
        $arrs = [
            '-2' => '处理失败',
            '-1' => '处理中',
            '0'  => '待审核',
            '1'  => '正常',
            '2'  => '审核不通过',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取视频分类所属板块
     * @param null $val
     */
    public static function getMovieCategoryPosition($val = null)
    {
        $arrs = [
            'all'   => '全部',
            'hot'   => '热点',
            'video' => '视频',
            'media' => '传媒',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取余额类型
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getAccountLogsType($val = null)
    {
        $arrs = [
            1  => '充值',
            2  => '提现',
            3  => '余额支付',
            4  => '退款到余额',
            5  => '佣金入账',
            6  => '佣金退回',
            7  => '提现回滚',
            8  => '余额扣除',
            9  => '打赏',
            10 => '视频分成',
            11 => '帖子分成',
            12 => '活动入账',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     *   获取up分类
     * @param                  $val
     * @return string[]|string
     */
    public static function getUpCategories($val = null)
    {
        $arrs = [
            'person'   => '个人号',
            'original' => '原创号',
            'media'    => '传媒号',
            //            'video' => '视频博主',
            //            'douyin' => '抖音博主',
            //            'post' => '帖子博主',
            //            'fuli' => '福利姬',
            //            'wanghuang' => '网黄',
            //            'zhubo' => '主播',
            //            'zongyi' => '综艺博主',
            //            'jp_av' => '女优',
            //            'gc_av' => '国产女优',
            //            'om_av' => '欧美女优',
            //            'publisher' => '厂牌',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 玩法资源类型
     * @param  null            $value
     * @return string|string[]
     */
    public static function getBuyType($value = null)
    {
        $arr = [
            'movie' => '视频',
            'post'  => '帖子',
            'image' => '色图',
            'up'    => 'UP主',
            'game'  => '游戏',
            'file'  => '种子',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取用户支付订单状态
     * @param                     $val
     * @return array|mixed|string
     */
    public static function getUserOrderStatus($val = null)
    {
        $arrs = [
            '0'  => '未支付',
            '1'  => '已支付',
            '-1' => '退款',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取余额变动操作类型
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getAccountRecordType($val = null)
    {
        $arrs = [
            'point' => '金币',
            'vip'   => '会员',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取ai模板类型
     * @param                  $value
     * @return string|string[]
     */
    public static function getAiTplType($value = null)
    {
        $arr = [
            'change_dress'      => '换装',
            'change_dress_bare' => '脱衣',
            'change_face_image' => '图片换脸',
            'change_face_video' => '视频换脸',
            'text_to_image'     => '文转图片',
            'image_to_video'    => '图转视频',
            'text_to_voice'     => '文转语音',
            'novel'             => '小说',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 获取AI菜单样式
     * @param                  $val
     * @return string[]|string
     */
    public static function getAiNavStyle($val = null)
    {
        $arrs = [
            'ai_1' => '模块模式',
            'ai_2' => '列表模式-纯列表',
            'ai_3' => '列表模式-tab+列表',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取AI菜单位置
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getAiNavPosition($val = null)
    {
        $arrs = [
            'normal' => 'ai',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * AI模块的风格 1 2 3 4
     * @param                  $val
     * @return string[]|string
     */
    public static function getAiBlockStyle($val = null)
    {
        $arrs = [
            // -10到-19为AdvApp
            '-10' => '样式- -10-广告(App),1*5 不可选择',

            // -1到-9为Adv
            '-2' => '样式- -2-广告,竖-1*3 不可选择',
            '-1' => '样式- -1-广告,横 不可选择',

            // 1到9为横版样式
            '1' => '样式1-横-1*1-横向滑动',
            '2' => '样式2-横-1*1-不可滑动',
            '3' => '样式3-横-1*4-五宫格',
            '4' => '样式4-横-2*2-四宫格',
            // 10到19为竖版样式
            '10' => '样式10-竖-1*n-横向滑动',
            '11' => '样式11-竖-1*2-不可滑动',
            '12' => '样式12-竖-1*3-不可滑动',
            // 特殊样式
            // '20' => '样式20-UP头像',
            '30' => '样式30-瀑布流',
            // 特殊样式-tab类型
            '40' => '样式40-tab选项1(1*2)',
            '41' => '样式41-tab选项2(1*3)',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 获取ai订单状态
     * @param                  $val
     * @return string|string[]
     */
    public static function getAiOrderStatus($val = null)
    {
        $arrs = [
            '-2' => '系统错误(技术处理)',
            '-1' => '处理失败(退款)',
            '0'  => '待处理',
            '1'  => '处理中',
            '2'  => '处理成功',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 域名类型
     * @param  null            $value
     * @return string|string[]
     */
    public static function getDomainType($value = null)
    {
        $arr = [
            'main' => '主域名',
            'site' => '网站域名',
            'nav'  => '导航页域名',
            'page' => '落地页域名',
            'api'  => '接口域名',
            'h5k'  => 'H5K域名',
            'h5'   => 'H5线路域名',
        ];
        if ($value === null || $value === '') {
            return $arr;
        }
        return $arr[$value];
    }

    /**
     * 提现状态
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getWithdrawStatus($val = null)
    {
        $arrs = [
            0  => '处理中',
            1  => '已处理',
            -1 => '拒绝'
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 提现方式
     * @param  null               $val
     * @return array|mixed|string
     */
    public static function getWithdrawMethod($val = null)
    {
        $arrs = [
            'bank'   => '银行卡',
            'alipay' => '支付宝',
            'usdt'   => 'USDT',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 广告协议示范
     * @return string[]
     */
    public static function getAdvProtocol()
    {
        /**
         * 对齐广告中心协议
         * 1.   外部跳转：        https://xxx.com 外部链接就用原始协议
         * 2.   内部跳转:          inner://xxxx/read_pido_video_play?id=294352
         * 3.   app下载链接:    downlaod://?android=xxxx&ios=xxxx
         */
        $arrs = [
            '外部网页' => 'http://xxx.com',
            '内部网页' => 'webview://http://xxx.com',
            '会员'     => 'inner://vip',
            '金币'     => 'inner://recharge',
            '邀请'     => 'inner://share',
            '应用中心' => 'inner://appstore',
            '客服'     => 'inner://service',
            '个人主页' => 'inner://profile?id={id}',
            '视频详情' => 'inner://movie?id={id}',
            '帖子详情' => 'inner://post?id={id}',
            '漫画详情' => 'inner://comics?id={id}',
            '小说详情' => 'inner://novel?id={id}',
        ];
        return $arrs;
    }

    /**
     * 活动权限
     * @param                  $val
     * @return string|string[]
     */
    public static function getActivityRight($val = null)
    {
        $arrs = [
            'all'    => '全部用户',
            'normal' => '普通用户',
            'vip'    => '会员用户',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }

    /**
     * 活动权限
     * @param                  $val
     * @return string|string[]
     */
    public static function getBalanceField($val = null)
    {
        $arrs = [
            'balance'               => '充值余额',
            'balance_freeze'        => '充值余额-冻结',
            'balance_income'        => '收益余额',
            'balance_income_freeze' => '收益余额-冻结',
            'balance_share'         => '邀请余额',
            'balance_share_freeze'  => '邀请余额-冻结',
        ];
        if ($val !== null) {
            return $arrs[$val] ?? '';
        }
        return $arrs;
    }
}
