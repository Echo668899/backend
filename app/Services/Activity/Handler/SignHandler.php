<?php

namespace App\Services\Activity\Handler;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Models\Activity\ActivitySignLogModel;
use App\Models\User\UserModel;
use App\Services\User\AccountService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

/**
 * 签到
 */
class SignHandler extends BaseHandler
{
    public function code(): string
    {
        return 'sign';
    }

    public function name(): string
    {
        return '签到';
    }

    public function description(): string
    {
        return '签到，包含虚拟奖励、实物奖励';
    }

    public function schema(): array
    {
        return [
            // 1. 奖品配置（奖项、概率、展示名称）
            'prizes' => [
                'type' => 'list',
                'label' => '奖品列表',
                'item_schema' => [
                    'name' => ['type' => 'text', 'label' => '奖品名称'],
                    'image' => ['type' => 'image', 'label' => '奖品图片'],
                    'options' => [
                        'vip'       => '会员',
                        'point'     => '金币',
                        'credit'    => '积分',
                        'entity'    => '实物',
                        'none'      => '无奖品',
                    ],
                ],
            ],
        ];
    }

    public function execute(int $userId, array $activityRow)
    {
        $userRow = UserModel::findByID($userId);
        $orderSn = CommonUtil::createOrderNo('ACT');

        $date = empty($date)?date('Y-m-d'):$date;
        //获取今天的奖品
        $prize = [];
        foreach ($activityRow['tpl_config']['prizes'] as $item) {
            if($item['_id']==$date){
                $prize = $item;
                break;
            }
        }
        //小于今天,暂时不支持补签
        if(strtotime($date)<strtotime(date('Y-m-d'))){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'无法补签');
        }

        if(self::has($userId,$date)){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'您已经签到过了');
        }

        //记录日志
        ActivitySignLogModel::insert([
            '_id'         => self::fmt($userId,$date),
            'user_id'     => intval($userId),
            'username'    => strval($userRow['username']),
            'activity_id' => strval($activityRow['_id']),
            'order_sn'    => $orderSn,
            'prize_name'  => strval($prize['name']),
            'prize_type'  => strval($prize['type']),
            'prize_image'  => strval($prize['image']),
        ]);

        //发放虚拟奖励
        if($prize['type']=='vip'){
            UserService::doChangeGroup($userRow,max($prize['num'],0),1);
        }elseif($prize['type']=='point'){
            AccountService::addBalance($userRow,$orderSn,$prize['num'],12,'balance');
        }elseif($prize['type']=='credit'){
            // 加积分 //TODO,暂未实现积分字段
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'暂不支持积分功能');
        }

    }

    public static function fmt(int $userId,string $date)
    {
        return "{$userId}_{$date}";
    }

    public static function has(int $userId,string $date)
    {
        $_id = self::fmt($userId,$date);
        return boolval(ActivitySignLogModel::count(['_id'=>$_id]));
    }

}
