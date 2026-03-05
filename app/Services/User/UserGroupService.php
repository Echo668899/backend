<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\User\UserGroupModel;

/**
 *  用户组
 * @package App\Services
 */
class UserGroupService extends BaseService
{
    public static $right = [
        // /显示权益组
        'show' => [
            'yuepao' => [
                'name'  => '全国约炮资源',
                'desc'  => '每次约炮享9折',
                'image' => '/ce234/uploads/default/other/2025-06-25/3d758ea3ee4936a52c951cf9f6d4bffd.png',
            ],
            'yuanwei' => [
                'name'  => '抽女优原味',
                'desc'  => '抖阴学院女优原味',
                'image' => '/ce234/uploads/default/other/2025-06-25/fe9fc270561501a896ed9ddddbf2c39b.png',
            ],
            'tequan' => [
                'name'  => '钻石特权',
                'desc'  => '免费看钻石视频',
                'image' => '/ce234/uploads/default/other/2024-08-22/c7a1cdd7a2afdcd18d66756963525098.png',
            ],
            'zhekou' => [
                'name'  => '专属折扣',
                'desc'  => '钻石解锁享8折',
                'image' => '/ce234/uploads/default/other/2024-10-23/563a90a496a0567ecdbfa24428236939.png',
            ],
            'ziyuan' => [
                'name'  => '海量片源',
                'desc'  => '50万+影片无限看',
                'image' => '/ce234/uploads/default/other/2024-08-22/9e037d50ae304d3c01b619a9cca3b939.png',
            ],
            'rigeng' => [
                'name'  => '每日更新',
                'desc'  => '每日更新500+视频',
                'image' => '/ce234/uploads/default/other/2024-08-22/7c5609cff299c249d1ab642d4653631a.png',
            ],
            'sixin' => [
                'name'  => '私信聊天',
                'desc'  => '与心仪女优互动',
                'image' => '/ce234/uploads/default/other/2024-08-22/330b42a469041d3c61c40fd4630f6479.png',
            ],
            'kefu' => [
                'name'  => '专属客服',
                'desc'  => '直接客服免排队',
                'image' => '/ce234/uploads/default/other/2024-08-22/e73ca0c4fbeb85d6b49e46b7e7bdd34f.png',
            ],
        ],
        // /业务逻辑权益组
        'logic' => [
            'movie' => [
                'name' => 'VIP视频',
            ],
            'comics' => [
                'name' => 'VIP漫画',
            ],
            'post' => [
                'name' => 'VIP帖子',
            ],
            'novel' => [
                'name' => 'VIP小说',
            ],
            'audio' => [
                'name' => 'VIP有声',
            ],
            'do_nickname' => [
                'name' => '修改昵称',
            ],
            'do_headico' => [
                'name' => '修改头像',
            ],
            'do_headbg' => [
                'name' => '修改背景',
            ],
            'do_sign' => [
                'name' => '修改签名',
            ],
            'do_danmaku' => [
                'name' => '发表弹幕',
            ],
            'do_comment' => [
                'name' => '发表评论',
            ],
            'do_post' => [
                'name' => '发布帖子',
            ],
            'do_movie' => [
                'name' => '发布视频',
            ],
            'do_download' => [
                'name' => '下载视频',
            ],
            'do_chat' => [
                'name' => '私信聊天',
            ],
            'do_chat_call' => [
                'name' => '音视频通话',
            ],
            'do_live' => [
                'name' => '开启直播',
            ],
        ],
    ];

    /**
     * 获取所有可用用户套餐
     * @param  mixed $group
     * @return array
     */
    public static function getEnableAll($group = '')
    {
        $result = self::getAll($group);
        foreach ($result as $index => $item) {
            if ($item['is_disabled'] == 'y') {
                unset($result[$index]);
            }
        }
        return $result ? array_values($result) : [];
    }

    /**
     * 获取所有
     * @param  mixed $group
     * @return array
     */
    public static function getAll($group = '')
    {
        $result = cache()->get(CacheKey::USER_GROUP);
        if ($result == null) {
            $result = UserGroupModel::find([], [], ['sort' => -1], 0, 1000);
            cache()->set(CacheKey::USER_GROUP, $result, 180);
        }
        $rows = [];
        foreach ($result as $item) {
            if (empty($group) || $group == $item['group']) {
                $rows[$item['_id']] = [
                    'id'           => strval($item['_id']),
                    'name'         => strval($item['name']),
                    'description'  => strval($item['description']),
                    'img'          => strval($item['img']),
                    'icon'         => strval($item['icon']),
                    'group'        => strval($item['group']),
                    'rate'         => strval(intval($item['rate'])),
                    'gift_num'     => strval($item['gift_num'] ?: 0),
                    'coupon_num'   => strval($item['coupon_num'] ?: 0),
                    'price'        => strval($item['price'] * 1),
                    'old_price'    => strval($item['old_price'] * 1),
                    'day_num'      => strval($item['day_num'] * 1),
                    'download_num' => strval($item['download_num'] * 1),
                    'day_tips'     => strval($item['day_tips']),
                    'price_tips'   => strval($item['price_tips']),
                    'right'        => $item['right'],
                    'is_disabled'  => $item['is_disabled'] ? 'y' : 'n',
                    'activity_id'  => strval($item['activity_id']),
                    'tips'         => strval($item['tips'])
                ];
            }
        }
        return $rows;
    }

    /**
     * 获取用户组信息
     * @param             $groupId
     * @return mixed|null
     */
    public static function getInfo($groupId)
    {
        $groups = self::getAll();
        return $groups[$groupId] ?? null;
    }

    /**
     * 获取最高折扣的一条信息
     * @param        $group
     * @return mixed
     */
    public static function getMaxRateGroup($group = '')
    {
        $rows = self::getEnableAll($group);
        if (empty($rows)) {
            return [];
        }
        $minRow = array_reduce($rows, function ($carry, $item) {
            if ($carry === null || $item['rate'] < $carry['rate']) {
                return $item;
            }
            return $carry;
        });
        return $minRow;
    }
}
