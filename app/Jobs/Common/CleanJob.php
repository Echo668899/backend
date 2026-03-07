<?php

namespace App\Jobs\Common;

use App\Jobs\BaseJob;
use App\Models\Activity\ActivityLotteryChanceModel;
use App\Models\Activity\ActivityLotteryLogModel;
use App\Models\Activity\ActivitySignLogModel;
use App\Models\Audio\AudioFavoriteModel;
use App\Models\Audio\AudioHistoryModel;
use App\Models\Audio\AudioLoveModel;
use App\Models\Comics\ComicsFavoriteModel;
use App\Models\Comics\ComicsHistoryModel;
use App\Models\Comics\ComicsLoveModel;
use App\Models\Common\AppLogModel;
use App\Models\Common\ChatMessageModel;
use App\Models\Common\ChatModel;
use App\Models\Common\CollectionsModel;
use App\Models\Common\EmailLogModel;
use App\Models\Common\SmsLogModel;
use App\Models\Movie\MovieDisLoveModel;
use App\Models\Movie\MovieDownloadModel;
use App\Models\Movie\MovieFavoriteModel;
use App\Models\Movie\MovieHistoryModel;
use App\Models\Movie\MovieLoveModel;
use App\Models\Movie\MovieModel;
use App\Models\Novel\NovelFavoriteModel;
use App\Models\Novel\NovelHistoryModel;
use App\Models\Novel\NovelLoveModel;
use App\Models\Post\PostFavoriteModel;
use App\Models\Post\PostHistoryModel;
use App\Models\Post\PostLoveModel;
use App\Models\Post\PostModel;
use App\Models\Report\ReportAdvAppLogModel;
use App\Models\Report\ReportAdvLogModel;
use App\Models\Report\ReportAudioLogModel;
use App\Models\Report\ReportChannelLogModel;
use App\Models\Report\ReportComicsLogModel;
use App\Models\Report\ReportHourLogModel;
use App\Models\Report\ReportMovieLogModel;
use App\Models\Report\ReportNovelLogModel;
use App\Models\Report\ReportPostLogModel;
use App\Models\Report\ReportServerLogModel;
use App\Models\Report\ReportUserChannelLogModel;
use App\Models\User\UserFansModel;
use App\Models\User\UserFavoriteModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Models\User\UserShareLogModel;
use App\Models\User\UserUpModel;
use App\Utils\CommonUtil;
use App\Utils\LogUtil;

/**
 * 数据清理脚本
 */
class CleanJob extends BaseJob
{

    /**
     * @var $table
     */
    private $table;

    /**
     * @param $table
     */
    public function __construct($table)
    {
        $this->table=$table;
    }

    public function handler($_id)
    {
        set_time_limit(-1);
        LogUtil::info("清理前,请确认已备份,10秒钟后任务执行");
        sleep(10);
        switch ($this->table){
            case 'user':
                $this->user();
                break;
            case 'app_log':
                $this->appLog();
                break;
            case 'report_log':
                $this->reportLog();
                break;
            case 'user_order':
                $this->userOrder();
                break;
            case 'user_recharge':
                $this->userRecharge();
                break;
            case 'collections':
                $this->collections();
                break;
            case 'movie_history':
                $this->movieHistory();
                break;
            case 'comics_history':
                $this->comicsHistory();
                break;
            case 'audio_history':
                $this->audioHistory();
                break;
            case 'novel_history':
                $this->novelHistory();
                break;
            case 'post_history':
                $this->postHistory();
                break;
        }
    }

    public function user()
    {
        //保留最近60天
        $where =[
            'group_id'=>0,
            'login_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*60],
            'channel_name'=>['$ne'=>'system'],
        ];
        $keyName = __CLASS__.':user';


        //有内容的不进行删除
        $movieIds = MovieModel::aggregates([
            [
                '$unwind'=>'$user_id'
            ],
            [
                '$group' => [
                    '_id' => '$user_id',
                ],
            ],
        ]);
        $postIds = PostModel::aggregates([
            [
                '$group' => [
                    '_id' => '$user_id',
                ],
            ],
        ]);
        $upIds = UserUpModel::aggregates([
            [
                '$group' => [
                    '_id' => '$_id',
                ],
            ],
        ]);

        $movieIds = array_column($movieIds,'_id');
        $postIds = array_column($postIds,'_id');
        $upIds = array_column($upIds,'_id');

        $notIds = array_merge($movieIds,$postIds,$upIds);
        $notIds = array_unique($notIds);

        $page=1;
        $pageSize=10000;
        while (true){
            LogUtil::info(__CLASS__." user 查询 page:{$page}");
            $rows = UserModel::find($where,['_id','group_id','group_dark_id','first_pay','balance'],[],($page-1)*$pageSize,$pageSize);
            if(empty($rows)){
                break;
            }
            foreach ($rows as $index=>$row) {
                //不需要删除
                if(in_array($row['_id'],$notIds)){
                    unset($rows[$index]);
                    continue;
                }
                //不需要删除
                if($row['group_id']>0){
                    unset($rows[$index]);
                    continue;
                }
                //不需要删除
                if($row['group_dark_id']>0){
                    unset($rows[$index]);
                    continue;
                }
                //不需要删除
                if($row['first_pay']>0){
                    unset($rows[$index]);
                    continue;
                }
                //不需要删除
                if($row['balance']>0){
                    unset($rows[$index]);
                    continue;
                }
            }
            $ids = array_column($rows,'_id');
            //临时储存
            redis()->lPush($keyName,json_encode($ids));
            $page++;
        }
        unset($page,$pageSize);
        redis()->expire($keyName,60*60);


        $total=0;
        $page=1;
        while (true){
            $ids = redis()->rPop($keyName);
            if(empty($ids)){
                break;
            }
            $ids = json_decode($ids,true);
            LogUtil::info(__CLASS__." user 清理 page:{$page}");

            ///删除关联的所有数据
            ChatModel::delete(['$or'=>[['from_id'=>['$in'=>$ids]], ['to_id'=>['$in'=>$ids]]]]);
            ChatMessageModel::delete(['$or'=>[['from_id'=>['$in'=>$ids]], ['to_id'=>['$in'=>$ids]]]]);

            UserFansModel::delete(['$or'=>[['user_id'=>['$in'=>$ids]], ['home_id'=>['$in'=>$ids]]]]);
            UserFavoriteModel::delete(['user_id'=>['$in'=>$ids]]);
            UserShareLogModel::delete(['user_id'=>['$in'=>$ids]]);

            ActivityLotteryChanceModel::delete(['user_id'=>['$in'=>$ids]]);
            ActivityLotteryLogModel::delete(['user_id'=>['$in'=>$ids]]);
            ActivitySignLogModel::delete(['user_id'=>['$in'=>$ids]]);

            MovieLoveModel::delete(['user_id'=>['$in'=>$ids]]);
            MovieDisLoveModel::delete(['user_id'=>['$in'=>$ids]]);
            MovieFavoriteModel::delete(['user_id'=>['$in'=>$ids]]);
            MovieDownloadModel::delete(['user_id'=>['$in'=>$ids]]);

            ComicsLoveModel::delete(['user_id'=>['$in'=>$ids]]);
            ComicsFavoriteModel::delete(['user_id'=>['$in'=>$ids]]);

            NovelLoveModel::delete(['user_id'=>['$in'=>$ids]]);
            NovelFavoriteModel::delete(['user_id'=>['$in'=>$ids]]);

            AudioLoveModel::delete(['user_id'=>['$in'=>$ids]]);
            AudioFavoriteModel::delete(['user_id'=>['$in'=>$ids]]);

            PostLoveModel::delete(['user_id'=>['$in'=>$ids]]);
            PostFavoriteModel::delete(['user_id'=>['$in'=>$ids]]);

            $total+=count($ids);
            $page++;
        }

        redis()->del($keyName);
        LogUtil::info(__CLASS__." user 成功清理 {$total} 条数据");
    }

    /**
     * 日活日志
     * @return void
     */
    public function appLog()
    {
        /**
         * TODO 务必比ReportServiceJob中留存计算大1天
         */
        $where =[
            'created_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*16],
        ];

        $total = 0;
        while (true){
            $rows = AppLogModel::find($where,['_id'],[],0,1000);
            if(empty($rows)){
                break;
            }
            $ids = array_column($rows,'_id');
            AppLogModel::delete(['_id'=>['$in'=>$ids]]);
            $total+=count($ids);
        }
        LogUtil::info(__CLASS__." app_log 成功清理 {$total} 条数据");
    }

    /**
     * 各种统计数据
     * @return void
     */
    public function reportLog()
    {
        $tables=[
            ReportAdvLogModel::class=>60,
            ReportAdvAppLogModel::class=>60,

            ReportServerLogModel::class=>60,
            ReportHourLogModel::class=>60,
            ReportChannelLogModel::class=>180,
            ReportUserChannelLogModel::class=>180,

            //TODO 注意Stats/*Job在使用,最大统计维度用到了30天,所以此处保留40天即可
            ReportAudioLogModel::class=>40,
            ReportComicsLogModel::class=>40,
            ReportMovieLogModel::class=>40,
            ReportPostLogModel::class=>40,
            ReportNovelLogModel::class=>40,

            SmsLogModel::class=>60,
            EmailLogModel::class=>60,
        ];
        foreach ($tables as $model=>$day) {
            try {
                $where =[
                    'created_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*$day],
                ];
                $total = 0;
                while (true){
                    $rows = $model::find($where,['_id'],[],0,1000);
                    if(empty($rows)){
                        break;
                    }
                    $ids = array_column($rows,'_id');
                    $model::delete(['_id'=>['$in'=>$ids]]);
                    $total+=count($ids);
                }
                LogUtil::info(__CLASS__." ".($model::$collection)." 成功清理 {$total} 条数据");
            }catch (\Exception $e){
                LogUtil::error(sprintf('%s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
            }
        }

    }

    /**
     * 会员订单
     * @return void
     */
    public function userOrder()
    {
        //保留180天
        $where =[
            'created_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*180],
        ];

        $total = 0;
        while (true){
            $rows = UserOrderModel::find($where,['_id'],[],0,1000);
            if(empty($rows)){
                break;
            }
            $ids = array_column($rows,'_id');
            UserOrderModel::delete(['_id'=>['$in'=>$ids]]);
            $total+=count($ids);
        }
        LogUtil::info(__CLASS__." user_order 成功清理 {$total} 条数据");
    }

    /**
     * 金币订单
     * @return void
     */
    public function userRecharge()
    {
        //保留180天
        $where =[
            'created_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*180],
        ];

        $total = 0;
        while (true){
            $rows = UserRechargeModel::find($where,['_id'],[],0,1000);
            if(empty($rows)){
                break;
            }
            $ids = array_column($rows,'_id');
            UserRechargeModel::delete(['_id'=>['$in'=>$ids]]);
            $total+=count($ids);
        }
        LogUtil::info(__CLASS__." user_recharge 成功清理 {$total} 条数据");
    }

    /**
     * 收款单
     * @return void
     */
    public function collections()
    {
        //保留180天
        $where =[
            'created_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*180],
        ];

        $total = 0;
        while (true){
            $rows = CollectionsModel::find($where,['_id'],[],0,1000);
            if(empty($rows)){
                break;
            }
            $ids = array_column($rows,'_id');
            CollectionsModel::delete(['_id'=>['$in'=>$ids]]);
            $total+=count($ids);
        }
        LogUtil::info(__CLASS__." collections 成功清理 {$total} 条数据");
    }

    /**
     * 历史记录
     * @return void
     */
    public function movieHistory()
    {
        //保留90天
        $where =[
            'updated_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*90],
        ];

        for($i=0;$i<100;$i++){
            $total = 0;
            MovieHistoryModel::$collection="movie_history_{$i}";
            while (true){
                $rows = MovieHistoryModel::find($where,['_id'],[],0,5000);
                if(empty($rows)){
                    break;
                }
                $ids = array_column($rows,'_id');
                MovieHistoryModel::delete(['_id'=>['$in'=>$ids]]);
                $total+=count($ids);
            }
            LogUtil::info(__CLASS__." movie_history_{$i} 成功清理 {$total} 条数据");
        }
    }


    /**
     * 历史记录
     * @return void
     */
    public function comicsHistory()
    {
        //保留90天
        $where =[
            'updated_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*90],
        ];

        for($i=0;$i<100;$i++){
            $total = 0;
            ComicsHistoryModel::$collection="comics_history_{$i}";
            while (true){
                $rows = ComicsHistoryModel::find($where,['_id'],[],0,5000);
                if(empty($rows)){
                    break;
                }
                $ids = array_column($rows,'_id');
                ComicsHistoryModel::delete(['_id'=>['$in'=>$ids]]);
                $total+=count($ids);
            }
            LogUtil::info(__CLASS__." comics_history_{$i} 成功清理 {$total} 条数据");
        }
    }

    /**
     * 历史记录
     * @return void
     */
    public function audioHistory()
    {
        //保留90天
        $where =[
            'updated_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*90],
        ];

        for($i=0;$i<100;$i++){
            $total = 0;
            AudioHistoryModel::$collection="audio_history_{$i}";
            while (true){
                $rows = AudioHistoryModel::find($where,['_id'],[],0,5000);
                if(empty($rows)){
                    break;
                }
                $ids = array_column($rows,'_id');
                AudioHistoryModel::delete(['_id'=>['$in'=>$ids]]);
                $total+=count($ids);
            }
            LogUtil::info(__CLASS__." audio_history_{$i} 成功清理 {$total} 条数据");
        }
    }
    /**
     * 历史记录
     * @return void
     */
    public function novelHistory()
    {
        //保留90天
        $where =[
            'updated_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*90],
        ];

        for($i=0;$i<100;$i++){
            $total = 0;
            NovelHistoryModel::$collection="novel_history_{$i}";
            while (true){
                $rows = NovelHistoryModel::find($where,['_id'],[],0,5000);
                if(empty($rows)){
                    break;
                }
                $ids = array_column($rows,'_id');
                NovelHistoryModel::delete(['_id'=>['$in'=>$ids]]);
                $total+=count($ids);
            }
            LogUtil::info(__CLASS__." novel_history_{$i} 成功清理 {$total} 条数据");
        }
    }

    /**
     * 历史记录
     * @return void
     */
    public function postHistory()
    {
        //保留90天
        $where =[
            'updated_at'=>['$lte'=>CommonUtil::getTodayZeroTime()-86400*90],
        ];

        for($i=0;$i<100;$i++){
            $total = 0;
            PostHistoryModel::$collection="post_history_{$i}";
            while (true){
                $rows = PostHistoryModel::find($where,['_id'],[],0,5000);
                if(empty($rows)){
                    break;
                }
                $ids = array_column($rows,'_id');
                PostHistoryModel::delete(['_id'=>['$in'=>$ids]]);
                $total+=count($ids);
            }
            LogUtil::info(__CLASS__." post_history_{$i} 成功清理 {$total} 条数据");
        }
    }


    public function success($_id)
    {

    }

    public function error($_id, \Exception $e)
    {

    }

}
