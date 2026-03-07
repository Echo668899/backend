<?php

namespace App\Services\User;

use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\User\UserShareLogModel;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;

/**
 * 用户分享
 */
class UserShareService extends BaseService
{
    public static $task=[
        1 => [
            'vip_id' => 1,
            'vip_day' => 1,
            'point' => 0,
            'super_item' => '1天vip',
            'alert_text' => '',
            'img'=>'/ce234/uploads/default/other/2025-12-05/be3196ebc0279151f0b1443bed631d91.png',
        ],
        3 => [
            'vip_id' => 1,
            'vip_day' => 7,
            'point' => 0,
            'super_item' => '7天vip',
            'alert_text' => '',
            'img'=>'/ce234/uploads/default/other/2025-12-05/4c02ef8922a0ee5135a116cb87f213da.png',
        ],
    ];

    public static function fmt($userId,$shareId)
    {
        return md5($userId.'_'.$shareId);
    }

    /**
     * 生成分享记录
     * @param $userId
     * @param $shareId
     * @param bool $status 是否自动发放
     * @return true
     */
    public static function do($userId,$shareId,bool $status)
    {
        $_id = self::fmt($userId,$shareId);
        UserShareLogModel::findAndModify(
            ['_id' => $_id],
            [
                '_id'       => $_id,
                'user_id'   => $userId,
                'share_id'  => $shareId,
                'status'    => intval($status),
                'label'     => date('Y-m-d'),
                'created_at' => time(),
                'updated_at' => time(),
            ],
            [],
            true
        );
        return true;
    }


    /**
     * 获取分享列表
     * @param $userId
     * @param $page
     * @param $pageSize
     * @param $cursor
     * @return array
     */
    public static function getIds($userId, $page = 1, $pageSize = 20,$cursor='')
    {
        $userId = intval($userId);
        $query = ['user_id' => $userId];
        $count = UserShareLogModel::count($query);
        if (!empty($cursor)) {
            $query['updated_at'] = ['$lt' => intval($cursor)];
            $rows = UserShareLogModel::find($query, ['share_id'], ['updated_at'=>-1], 0, $pageSize);
        }else{
            $rows = UserShareLogModel::find($query, ['share_id'], ['created_at' => -1], ($page - 1) * $pageSize, $pageSize);
        }
        $ids = array_column($rows, 'share_id');
        return [
            'ids' => $ids,
            'total' => $count,
            'current_page' => $page,
            'page_size' => $pageSize,
            'last_page' => strval(ceil($count / $pageSize)),
            'cursor'    => !empty($rows)?strval($rows[count($rows)-1]['updated_at']):'',
        ];
    }

    /**
     * 获取分享信息
     * @param $userId
     * @return array
     */
    public static function getShareInfo($userId)
    {
        $userInfo = UserService::getInfoFromCache($userId);
        $siteUrl = ConfigService::getConfig('site_url');
        $shareLink = $siteUrl . '?_s=' . $userInfo['username'];

        $shareNum = UserShareService::getShareNum($userId);

        return array(
            'balance_share'=>strval($userInfo['balance_share']),
            'share_user_id' => strval($userInfo['id']),
            'share_code' => strval($userInfo['username']),
            'share_link' => strval($shareLink),
            'share_num'  => strval($shareNum),
            'share_desc' =>value(function () {
                return "1、邀请好友，赚现金奖励\n" .
                    "每成功邀请1人获得 1 元现金，奖金无上限\n\n" .
                    "2、邀请任务：\n" .
                    "完成累计邀请，领会员、钻石、免费约炮奖励\n\n" .
                    "3、申请代理：\n" .
                    "累计邀请 300 个好友后，即可申请代理。\n" .
                    "成为代理，获得80%高额返佣，轻松日入千元";
            }),
            'task'=>value(function ()use($userInfo,$shareNum) {
                $rows=[];
                foreach (self::$task as $people=>$task) {
                    $rows[]=[
                        'id'     => strval($people),
                        'people' => strval($people),
                        'desc'   => "邀请{$people}人",
                        'img'    => CommonService::getCdnUrl($task['img']),
                    ];
                }
                return $rows;
            }),
        );
    }

    /**
     * 获取邀请数量
     * @param $userId
     * @param $startAt
     * @param $endAt
     * @return float|int
     */
    public static function getShareNum($userId,$startAt=null,$endAt=null)
    {
        $query=['user_id' => intval($userId)];
        if($startAt){
            $query['created_at'] = ['$gte'=>$startAt];
        }
        if($endAt){
            $query['created_at'] = ['$lte'=>$endAt];
        }
        return UserShareLogModel::count($query,'user_id');
    }
}
