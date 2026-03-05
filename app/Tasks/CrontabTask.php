<?php

namespace App\Tasks;

use App\Core\BaseTask;
use App\Jobs\Common\AiOrderJob;
use App\Jobs\Common\CleanJob;
use App\Jobs\Common\SitemapJob;
use App\Jobs\Common\UploadFindJob;
use App\Jobs\Es\EsAudioJob;
use App\Jobs\Es\EsComicsJob;
use App\Jobs\Es\EsIkJob;
use App\Jobs\Es\EsMovieJob;
use App\Jobs\Es\EsNovelJob;
use App\Jobs\Es\EsPostJob;
use App\Jobs\Es\EsUserJob;
use App\Jobs\Report\ReportAgentJob;
use App\Jobs\Report\ReportAgentV3Job;
use App\Jobs\Report\ReportDomainJob;
use App\Jobs\Report\ReportHourJob;
use App\Jobs\Report\ReportServerJob;
use App\Jobs\Report\ReportUserChannelJob;
use App\Jobs\Stats\StatsAudioJob;
use App\Jobs\Stats\StatsComicsJob;
use App\Jobs\Stats\StatsMovieJob;
use App\Jobs\Stats\StatsNovelJob;
use App\Jobs\Stats\StatsPostJob;
use App\Jobs\Stats\StatsUserUpJob;
use App\Services\Common\JobService;
use App\Services\Common\PaymentService;
use App\Services\Common\QueueService;

/**
 * 系统定时任务
 */
class CrontabTask extends BaseTask
{
    /**
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function queueAction()
    {
        QueueService::run();
    }

    /**
     * @return void
     */
    public function doPaidAction()
    {
        PaymentService::doPaidJob();
    }

    /**
     * 系统统计
     */
    public function reportServerAction()
    {
        JobService::create(new ReportServerJob(), 'sync');
    }

    /**
     * 小时统计
     */
    public function reportServerHourAction()
    {
        JobService::create(new ReportHourJob(date('Y-m-d')), 'sync');
        if (in_array(date('H'), ['01'])) {
            JobService::create(new ReportHourJob(date('Y-m-d', strtotime('-1day'))), 'sync');
        }
    }

    /**
     * 上报代理 *\/10 * * * *
     */
    public function reportAgentAction()
    {
        $startAt = time() - 3600 * 2;
        JobService::create(new ReportAgentJob($startAt), 'sync');
    }

    /**
     * 上报代理V3 *\/10 * * * *
     */
    public function reportAgentV3Action()
    {
        $startAt = time() - 3600 * 2;
        JobService::create(new ReportAgentV3Job($startAt), 'sync');
    }

    /**
     * 上报域名
     * @return void
     */
    public function reportDomainAction()
    {
        JobService::create(new ReportDomainJob(), 'sync');
    }

    /**
     * 用户渠道统计
     */
    public function reportUserChannelAction()
    {
        JobService::create(new ReportUserChannelJob(), 'sync');
    }

    /**========================业务相关任务-公共===========================**/
    /**
     * 生成词库
     * @return void
     */
    public function esAnalyzerAction()
    {
        JobService::create(new EsIkJob());
    }

    /**
     * 生成sitemap
     * @return void
     */
    public function sitemapAction()
    {
        // /该类禁止序列化
        (new SitemapJob())->handler(uniqid());
    }

    /**
     * 数据清理
     * @param  mixed $table
     * @return void
     */
    public function cleanAction($table)
    {
        JobService::create(new CleanJob($table));
    }

    /**========================业务相关任务-ES===========================**/

    /**
     * 同步视频到es
     * @return void
     */
    public function asyncMovieAction()
    {
        JobService::create(new EsMovieJob());
    }

    /**
     * 同步漫画到es
     * @return void
     */
    public function asyncComicsAction()
    {
        JobService::create(new EsComicsJob());
    }

    /**
     * 同步有声到es
     * @return void
     */
    public function asyncAudioAction()
    {
        JobService::create(new EsAudioJob());
    }

    /**
     * 同步小说到es
     * @return void
     */
    public function asyncNovelAction()
    {
        JobService::create(new EsNovelJob());
    }

    /**
     * 同步帖子到es
     * @return void
     */
    public function asyncPostAction()
    {
        JobService::create(new EsPostJob());
    }

    /**
     * 同步用户到es
     * @return void
     */
    public function asyncUserAction()
    {
        JobService::create(new EsUserJob());
    }

    /**========================业务相关任务-统计===========================**/

    /**
     * 视频统计
     * @return void
     */
    public function statsMovieAction()
    {
        JobService::create(new StatsMovieJob());
    }

    /**
     * 漫画统计
     * @return void
     */
    public function statsComicsAction()
    {
        JobService::create(new StatsComicsJob());
    }

    /**
     * 有声统计
     * @return void
     */
    public function statsAudioAction()
    {
        JobService::create(new StatsAudioJob());
    }

    /**
     * 小说统计
     * @return void
     */
    public function statsNovelAction()
    {
        JobService::create(new StatsNovelJob());
    }

    /**
     * 帖子统计
     * @return void
     */
    public function statsPostAction()
    {
        JobService::create(new StatsPostJob());
    }

    /**
     * UP统计
     * @return void
     */
    public function statsUpAction()
    {
        JobService::create(new StatsUserUpJob());
    }

    /**
     * Ai订单处理
     * @return void
     */
    public function aiAction()
    {
        JobService::create(new AiOrderJob());
    }

    /**
     * movie和post上传后查询
     * @return void
     */
    public function uploadFindAction()
    {
        JobService::create(new UploadFindJob());
    }
}
