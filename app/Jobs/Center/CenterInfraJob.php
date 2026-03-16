<?php


namespace App\Jobs\Center;


use App\Models\Report\ReportChannelLogModel;
use App\Utils\CommonUtil;
use App\Utils\LogUtil;

/**
 * infra也算作一个中心
 * Class ReportInfraDailyJob
 * @package App\Jobs\Report
 */
class CenterInfraJob extends CenterBaseJob
{
    public $action;

    /**
     * @param $action
     */
    public function __construct($action)
    {
        $this->action = $action;
    }


    public function handler($_id)
    {
        switch ($this->action) {
            case 'report':
                $this->report();
                break;
        }
    }

    /**
     * 每日报表上报
     * @param $date
     * @return void
     * @throws \Exception
     */
    public function report($date='')
    {
        $configs = self::getCenterConfig('infra');
        if(empty($configs['url'])||empty($configs['key'])||empty($configs['appid'])||empty($configs['appname'])){
            throw new \Exception("请检查配置项 center_infra");
        }
        if(!in_array($configs['apptype'],['money','web','free'])){
            throw new \Exception("apptype参数错误");
        }

        $time = $date?strtotime($date):strtotime("-1 day");
        $lastMonthTime = strtotime("-30 day", $time);


        //今日总数据(有渠道+无渠道)
        $allTodayReport = ReportChannelLogModel::findFirst(['date'=>date('Y-m-d',$time),'channel_name'=>'_all']);
        //上月今日总数据(有渠道+无渠道)
        $allLastReport = ReportChannelLogModel::findFirst(['date'=>date('Y-m-d',$lastMonthTime),'channel_name'=>'_all']);


        $group = [
            '_id'=>null,
            'reg'=>['$sum'=>'$reg'],
            'order_total' => ['$sum' => '$order.success_order'],//成功订单数
            'order_amount' => ['$sum' => '$order.success_amount'],//成功订单金额

            'recharge_total' => ['$sum' => '$recharge.success_order'],//成功充值数
            'recharge_amount' => ['$sum' => '$recharge.success_amount'],//成功充值金额
        ];
        //今日总数据(有渠道)
        $channelTodayReport = ReportChannelLogModel::aggregate([
            [
                '$match'=>[
                    'date'=>date('Y-m-d',$time),
                    'channel_name'=>['$ne'=>'_all']
                ]
            ],
            ['$group' =>$group]
        ]);
        //上月今日总数据(有渠道)
        $channelLastReport = ReportChannelLogModel::aggregate([
            [
                '$match'=>[
                    'date'=>date('Y-m-d',$lastMonthTime),
                    'channel_name'=>['$ne'=>'_all']
                ]
            ],
            ['$group' =>$group]
        ]);

        $data =[
            'date'      =>date('Y-m-d',$time),//统计日期
            'appid'     =>$configs['appid'],//APP名称
            'appName'   =>$configs['appname'],//APP名称
            'type'      =>$configs['apptype'],//项目类型 web-web项目 money-付费 free-免费
            'object_type'=>'daily',//数据类型

            /**日活**/
            //总日活
            'loginDailyActive'=>intval($allTodayReport['dau_all']??0),
            //上个月同一天总日活
            'lastMonthLoginDailyActive'=>intval($allLastReport['dau_all']??0),
            //日活环比(%)
            'loginDailyActiveRate'=>0,

            /**新增**/
            //总安装
            'allInstall'=>intval($allTodayReport['reg']??0),
            //上月同一天总安装
            'lastMonthAllInstall'=>intval($allLastReport['reg']??0),
            //总安装环比(%)
            'allInstallRate'=>0,

            //导量安装=有渠道码
            'referralInstall'=>intval($channelTodayReport['reg']??0),
            //上月同一天导量安装
            'lastMonthReferralInstall'=>intval($channelLastReport['reg']??0),
            //导量安装环比(%)
            'referralInstallRate'=>0,

            /**金额**/
            //总充值金额
            'rechargeAmountAll'=>intval($allTodayReport['order']['success_amount']+$allTodayReport['recharge']['success_amount']),
            //上个月同一天总充值金额
            'lastMonthRechargeAmountAll'=>intval($allLastReport['order']['success_amount']+$allLastReport['recharge']['success_amount']),
            //总充值金额环比(%)
            'rechargeAmountAllRate'=>0,

            //自身充值(无渠道码)总金额
            'rechargeAmount'=>value(function ()use($allTodayReport,$channelTodayReport){
                $all = intval($allTodayReport['order']['success_amount']+$allTodayReport['recharge']['success_amount']);
                $neAll = intval($channelTodayReport['order_amount']+$channelTodayReport['recharge_amount']);
                //_all渠道汇总-非_all渠道汇总=为空渠道汇总
                return intval($all - $neAll);
            }),
            //自身充值(无渠道码)上个月同一天总金额
            'lastMonthRechargeAmount'=>value(function ()use($allLastReport,$channelLastReport){
                $all = intval($allLastReport['order']['success_amount']+$allLastReport['recharge']['success_amount']);
                $neAll = intval($channelLastReport['order_amount']+$channelLastReport['recharge_amount']);
                //_all渠道汇总-非_all渠道汇总=为空渠道汇总
                return intval($all - $neAll);
            }),
            //自身充值(无渠道码)金额环比(%)
            'rechargeAmountRate'=>0,

            //导量充值(有渠道码)总金额
            'referralRecharge'=>intval($channelTodayReport['order_amount']+$channelTodayReport['recharge_amount']),
            //导量充值(有渠道码)上个月同一天总金额
            'lastMonthReferralRecharge'=>intval($channelLastReport['order_amount']+$channelLastReport['recharge_amount']),
            //导量充值(有渠道码)金额环比(%)
            'referralRechargeRate'=>0,



            /**交易笔数**/
            //成功交易笔数
            'succOrderCount'=>intval($allTodayReport['order']['success_order']+$allTodayReport['recharge']['success_order']),
            //上月同一天成功交易笔数
            'lastMonthSuccOrderCount'=>intval($allLastReport['order']['success_order']+$allLastReport['recharge']['success_order']),
            //成功交易笔数环比(%)
            'succOrderCountRate'=>0,
        ];

        //日活环比(%)
        $data['loginDailyActiveRate']=$this->calcRate($data['loginDailyActive'], $data['lastMonthLoginDailyActive']);
        //总安装环比(%)
        $data['allInstallRate']=$this->calcRate($data['allInstall'], $data['lastMonthAllInstall']);
        //导量安装环比(%)
        $data['referralInstallRate']=$this->calcRate($data['referralInstall'], $data['lastMonthReferralInstall']);
        //总充值金额环比(%)
        $data['rechargeAmountAllRate']=$this->calcRate($data['rechargeAmountAll'], $data['lastMonthRechargeAmountAll']);

        //自身充值(无渠道码)金额环比(%)
        $data['rechargeAmountRate'] = $this->calcRate($data['rechargeAmount'], $data['lastMonthRechargeAmount']);
        //导量充值(有渠道码)金额环比(%)
        $data['referralRechargeRate'] = $this->calcRate($data['referralRecharge'], $data['lastMonthReferralRecharge']);

        //成功交易笔数环比(%)
        $data['succOrderCountRate'] = $this->calcRate($data['succOrderCount'], $data['lastMonthSuccOrderCount']);


        $result = $this->doHttpRequest($configs['url'],$configs['key'],$data);
    }

    public function calcRate($current, $last, $precision = 2)
    {
        $current = (float)$current;
        $last    = (float)$last;

        if ($last == 0) {
            if ($current == 0) return 0;
            return $current > 0 ? 100 : -100;
        }

        return round((($current - $last) / abs($last)) * 100, $precision);
    }

    /**
     * @param $apiDomain
     * @param $apiKey
     * @param $data
     * @param $retry
     * @return bool
     */
    public function doHttpRequest($apiDomain,$apiKey,$data,$retry = 5)
    {
        if ($retry<1){return false;}
        try{
            $requestUrl = $apiDomain.'/api/report/data';
            $reqData = json_encode($data);
            $time = date('Y-m-d H:i:s');
            $requestData = array(
                'time'      => $time,
                'data'      => $reqData,
                'sign'      => md5($time . $apiKey . $reqData)
            );
            LogUtil::info(sprintf(__CLASS__ . " Request url: %s query:%s", $requestUrl,json_encode($requestData)));
            $result = CommonUtil::httpPost($requestUrl,$requestData);
            if (!$result){throw new \Exception();}
            $result = json_decode($result,true);
            return $result['status']=='y'?true:false;
        }catch (\Exception $e){
            return $this->doHttpRequest($apiDomain,$apiKey,$data,--$retry);
        }
    }

    public function success($_id)
    {

    }

    public function error($_id, \Exception $e)
    {

    }

}
