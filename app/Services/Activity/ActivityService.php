<?php

namespace App\Services\Activity;

use App\Constants\CacheKey;
use App\Constants\StatusCode;
use App\Core\Services\BaseService;
use App\Exception\BusinessException;
use App\Models\Activity\ActivityModel;
use App\Models\User\UserModel;
use App\Services\Common\CommonService;
use App\Services\User\UserService;

/**
 * 活动类
 */
class ActivityService extends BaseService
{

    /**
     * @param $code
     * @return mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getAdminAll($code='')
    {
        $keyName = CacheKey::ACTIVITY;
        $result = cache()->get($keyName);
        if (is_null($result)) {
            $nowTime = time();
            $query = array(
                'is_disabled' => 0,
                'start_time' => array('$lte' => $nowTime),
                'end_time' => array('$gte' => $nowTime)
            );
            $result = ActivityModel::find($query, [], ['sort'=>-1], 0, 2000);
            cache()->set($keyName, $result, 300);
        }
        foreach ($result as $index=>$item) {
            if($code&&$item['tpl_id']!=$code){
                unset($result[$index]);
            }else{
                $result[$index]['id'] = strval($item['_id']);
            }
        }
        return array_values($result);
    }

    /**
     * 获取所有活动
     * @param $code
     * @param $userIsVip
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function getAll($code='',$userIsVip=null)
    {
        $result = self::getAdminAll($code);

        $rows = [];
        foreach ($result as $item) {
            if($item['right']!='all'&&!empty($userIsVip)){
                if ($userIsVip == 'y' && $item['right'] != 'vip') {
                    continue;
                }
                if ($userIsVip == 'n' && $item['right'] != 'normal') {
                    continue;
                }
            }
            $rows[] = [
                'id'            => strval($item['_id']),
                'name'          => strval($item['name']),
                'img_x'         => strval(CommonService::getCdnUrl($item['img_x'])),
                'description'   => strval($item['description']),
                'right'         => strval($item['right']),
                'start_time'    => strval($item['start_time']),
                'end_time'      => strval($item['end_time']),
                'tpl_config'    => $item['tpl_config'],
            ];
        }
        return $rows;
    }

    /**
     * 获取抽奖活动
     * @param $code
     * @param $userIsVip
     * @return mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getLotteryOne($code='',$userIsVip=null)
    {
        $rows = self::getAll($code,$userIsVip);
        if (empty($rows)) return null;
        return $rows[0];
    }

    /**
     * 获取所有倒计时活动
     * @param $userInfo
     * @return array|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getCountdownAll($userInfo=null)
    {
        $rows = self::getAll('countdown',$userInfo['is_vip']);
        foreach ($rows as $index=>&$row) {
            if(empty($userInfo)){continue;}
            $row['start_time']=value(function ()use($row,$userInfo){
                if($row['right']=='all'){
                    return strval($row['start_time']);
                }elseif ($row['right']=='normal'){
                    //前端从 注册时间 + 等待时长 起开始显示该条广告
                    return strval($userInfo['register_at']+3600*$row['tpl_config']['wait_time']);
                }elseif ($row['right']=='vip'){
                    //只对有会员身份的用户展示 会员到期时间 大于x小时 时展示|| 会员到期时间 小时x小时时展示
                    if($row['tpl_config']['end_type']=='gt'){
                        if($userInfo['group_end_time']>time()+3600*$row['tpl_config']['time']){
                            return strval($row['start_time']);
                        }
                    }else{
                        if($userInfo['group_end_time']<time()+3600*$row['tpl_config']['time']){
                            return strval($row['start_time']);
                        }
                    }
                }
                //不满足条件,返回未开始时间,结束
                return strval(strtotime("2099-01-01"));
            });
            $row['end_time']= value(function ()use($row,$userInfo){
                if($row['right']=='all'){
                    return strval($row['end_time']);
                }elseif ($row['right']=='normal'){
                    //前端显示的倒计时时长 = 注册时间 + 等待时长 + 展示时长 - 当前时间//ps:由于是返回结束时间给前端计算,所以这里不需要减去
                    return strval($userInfo['register_at']+3600*$row['tpl_config']['wait_time']+3600*$row['tpl_config']['show_time']);
                }elseif ($row['right']=='vip'){
                    if($row['tpl_config']['end_type']=='gt'){
                        //前端在 会员剩余时长 >  输入时长  时显示该条广告
                        //前端显示的倒计时时长 = 会员结束时间 - 输入时长 - 当前时间
                        if($userInfo['group_end_time']-time()>(3600*$row['tpl_config']['time'])){
                            return strval($userInfo['group_end_time']-3600*$row['tpl_config']['time']);//ps:由于是返回结束时间给前端计算,所以这里不需要减去
                        }
                    }else{
                        //前端在 会员剩余时长 <  输入时长  时显示该条广告
                        //前端显示的倒计时时长 = 会员结束时间 - 当前时间
                        if($userInfo['group_end_time']<time()+3600*$row['tpl_config']['time']){
                            return strval($userInfo['group_end_time']);//ps:由于是返回结束时间给前端计算,所以这里不需要减去
                        }
                    }
                }
                //不满足条件,返回已结束时间,结束
                return strval(strtotime("2000-01-01"));
            });
            //过滤掉时间不满足的
            if(time()<$row['start_time']||time()>$row['end_time']){
                unset($rows[$index]);
            }
        }
        unset($row);

        if (empty($rows)) return null;
        $rows = array_values($rows);
        return  $rows;
    }

    /**
     * 获取倒计时活动
     * @param $userInfo
     * @return mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getCountdownOne($userInfo=null)
    {
        $rows = self::getCountdownAll($userInfo);
        if (empty($rows)) return null;
        $result = $rows[0];
        //优先全部用户活动
        if(count($rows)>=2){
            foreach ($rows as $row){
                if($row['right']=='all'){
                    $result = $row;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * 获取签到活动
     * 上层判断prizes中是否参加
     * @param $userInfo
     * @return mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    public static function getSignOne($userInfo=null)
    {
        $rows = self::getAll('sign',$userInfo);
        if (empty($rows)) return null;
        $result = $rows[0];
        //优先全部用户活动
        if(count($rows)>=2){
            foreach ($rows as $row){
                if($row['right']=='all'){
                    $result = $row;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @param $userId
     * @param $activityId
     * @return mixed
     * @throws BusinessException
     */
    public static function do($userId,$activityId)
    {
        $userId = intval($userId);
        $activityId = strval($activityId);
        $activityRow = ActivityModel::findByID($activityId);
        $userInfo = UserService::getInfoFromCache($userId);
        if(empty($activityRow)||$activityRow['is_disabled']==1){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'活动不存在');
        }
        if ($activityRow['start_time'] > time()) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'活动尚未开始');
        }
        if ($activityRow['end_time'] < time()) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'活动已结束');
        }

        if($activityRow['right']=='normal'&&$userInfo['is_vip']=='y'){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'当前活动仅对普通用户开放,您没有权限参加活动');
        }
        if($activityRow['right']=='vip'&&$userInfo['is_vip']=='n'){
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'当前活动仅对会员开放,您没有权限参加活动');
        }


        $tpl = ActivityTplService::get($activityRow['tpl_id']);
        if (empty($tpl)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'活动模板已下线');
        }



        $handlerClass = $tpl['handler'];
        if (!class_exists($handlerClass)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR,'活动处理器不存在');
        }

        //避免并发
        if (!CommonService::checkActionLimit("do_activity:{$activityId}_{$userId}",2,1)) {
            throw new BusinessException(StatusCode::DATA_ERROR, '慢点慢点,我受不了!');
        }

        /** @var \App\Services\Activity\Handler\BaseHandler $handler */
        $handler = new $handlerClass();

        return $handler->execute($userId, $activityRow);
    }
}
