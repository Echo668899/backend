<?php

namespace App\Jobs\Event;

use App\Jobs\BaseJob;
use App\Jobs\Center\CenterDataJob;
use App\Jobs\Event\Payload\Ai\AIChangeDressBarePayload;
use App\Jobs\Event\Payload\Ai\AIChangeDressPayload;
use App\Jobs\Event\Payload\Ai\AIChangeFaceImagePayload;
use App\Jobs\Event\Payload\Ai\AIChangeFaceVideoPayload;
use App\Jobs\Event\Payload\Ai\AiImageToVideoPayload;
use App\Jobs\Event\Payload\Ai\AiNovelPayload;
use App\Jobs\Event\Payload\Ai\AiTextToImagePayload;
use App\Jobs\Event\Payload\Ai\AiTextToVoicePayload;
use App\Jobs\Event\Payload\Audio\AudioBuyPayload;
use App\Jobs\Event\Payload\Comics\ComicsBuyPayload;
use App\Jobs\Event\Payload\Common\AdvClickPayload;
use App\Jobs\Event\Payload\Common\AdvShowPayload;
use App\Jobs\Event\Payload\Common\CommentPayload;
use App\Jobs\Event\Payload\Movie\MovieBuyPayload;
use App\Jobs\Event\Payload\Movie\MovieFavoritePayload;
use App\Jobs\Event\Payload\Movie\MovieLovePayload;
use App\Jobs\Event\Payload\Movie\MovieSearchKeywordPayload;
use App\Jobs\Event\Payload\Movie\MovieViewCompletePayload;
use App\Jobs\Event\Payload\Movie\MovieViewPayload;
use App\Jobs\Event\Payload\Novel\NovelBuyPayload;
use App\Jobs\Event\Payload\Post\PostBuyPayload;
use App\Jobs\Event\Payload\User\UserDoRechargePayload;
use App\Jobs\Event\Payload\User\UserDoRechargeSuccessPayload;
use App\Jobs\Event\Payload\User\UserDoVipPayload;
use App\Jobs\Event\Payload\User\UserDoVipSuccessPayload;
use App\Jobs\Event\Payload\User\UserLoginPayload;
use App\Jobs\Event\Payload\User\UserRegisterPayload;
use App\Models\Ai\AiOrderModel;
use App\Models\Audio\AudioModel;
use App\Models\Comics\ComicsModel;
use App\Models\Common\AdvAppModel;
use App\Models\Common\AdvModel;
use App\Models\Movie\MovieModel;
use App\Models\Novel\NovelModel;
use App\Models\Post\PostModel;
use App\Models\User\UserModel;
use App\Models\User\UserOrderModel;
use App\Models\User\UserRechargeModel;
use App\Services\Ai\AiGirlService;
use App\Services\Ai\AiToolsService;
use App\Services\Movie\MovieService;
use App\Utils\LogUtil;
use Phalcon\Manager\Center\CenterDataService;

/**
 * 平台事件总线
 * 用于各种任务,上报,埋点分发
 * 采用同步阻塞调用的方式,传递到当前Job
 */
class EventBusJob extends BaseJob
{
    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function handler($_id)
    {
        try {
            // / 提交给活动任务
            $this->activityTask();
        } catch (\Exception $e) {
            LogUtil::error(sprintf(__CLASS__ . ' %s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        try {
            $this->aiTask();
        } catch (\Exception $e) {
            LogUtil::error(sprintf(__CLASS__ . ' %s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
        try {
            // / 提交给数据中心
            $this->centerData();
        } catch (\Exception $e) {
            LogUtil::error(sprintf(__CLASS__ . ' %s in %s line %s', $e->getMessage(), $e->getFile(), $e->getLine()));
        }
    }

    /**
     * 活动任务
     * @return void
     */
    public function activityTask()
    {
        // 遍历所有活动,判断有无匹配
        //        dd($this->payload);
    }

    /**
     * 数据中心
     * @return void
     */
    public function centerData()
    {
        $payload   = $this->payload;
        $className = get_class($payload);
        $configs   = CenterDataJob::getCenterConfig('data');

        switch ($className) {
            // 用户注册
            case UserRegisterPayload::class:
                # 数据中心初始化-由于注册是在ApiService之后,无法拿到基础信息,但是事件是在注册之后,所以需要重新设置
                CenterDataService::setUserId($payload->userId);
                CenterDataService::setChannelCode($payload->channelCode);
                CenterDataService::doRegister($payload->accountType, uniqid(), $payload->registerAt);
                break;
                // 用户登录
            case UserLoginPayload::class:
                CenterDataService::doLogin($payload->accountType);
                break;
                // 订单创建
            case UserDoVipPayload::class:
                $orderRow = UserOrderModel::findByID(intval($payload->orderId));
                CenterDataService::doVipOrder($payload->orderId, $payload->groupId, $orderRow['group_name'], $orderRow['price'], $orderRow['created_at'], 'vip', '会员中心', );
                break;
            case UserDoRechargePayload::class:
                $orderRow = UserRechargeModel::findByID(intval($payload->orderId));
                CenterDataService::doRechargeOrder($payload->orderId, $payload->groupId, $orderRow['num'] . '金币', $orderRow['amount'], $orderRow['created_at'], 'recharge', '金币充值');
                break;
                // 订单支付成功
            case UserDoVipSuccessPayload::class:
                $orderRow = UserOrderModel::findByID(intval($payload->orderId));
                # 数据中心初始化-由于支付是异步回调,所以需要单独设置
                $userRow = UserModel::findByID(intval($payload->userId));
                CenterDataService::setRedis(redis());
                CenterDataService::setSessionId();
                CenterDataService::setDeviceType($userRow['device_type']);
                CenterDataService::setDeviceId($userRow['device_id']);
                CenterDataService::setClientIp($orderRow['created_ip']);
                CenterDataService::setAppid($configs['appid']);
                CenterDataService::setUserId($payload->userId);
                CenterDataService::setUserAgent('');
                CenterDataService::setChannelCode($userRow['channel_name'] ?? '');
                CenterDataService::doVipOrderPay($payload->orderId, $payload->groupId, $orderRow['day_num'], $orderRow['price'], $orderRow['pay_name'], $orderRow['trade_sn'], $orderRow['pay_at']);
                break;
            case UserDoRechargeSuccessPayload::class:
                $orderRow = UserRechargeModel::findByID(intval($payload->orderId));
                # 数据中心初始化
                $userRow = UserModel::findByID(intval($payload->userId));
                CenterDataService::setRedis(redis());
                CenterDataService::setSessionId();
                CenterDataService::setDeviceType($userRow['device_type']);
                CenterDataService::setDeviceId($userRow['device_id']);
                CenterDataService::setClientIp($orderRow['created_ip']);
                CenterDataService::setAppid($configs['appid']);
                CenterDataService::setUserId($payload->userId);
                CenterDataService::setUserAgent('');
                CenterDataService::setChannelCode($userRow['channel_name'] ?? '');
                CenterDataService::doRechargeOrderPay($payload->orderId, $payload->groupId, $orderRow['num'], $orderRow['amount'], $orderRow['pay_name'], $orderRow['trade_sn'], $orderRow['pay_at']);
                break;
                // 金币消耗
            case AIChangeDressPayload::class:
            case AIChangeDressBarePayload::class:
            case AIChangeFaceImagePayload::class:
            case AIChangeFaceVideoPayload::class:
            case AiImageToVideoPayload::class:
            case AiTextToImagePayload::class:
            case AiTextToVoicePayload::class:
            case AiNovelPayload::class:
                $orderRow = AiOrderModel::findByID(intval($payload->orderId));
                CenterDataService::doReduceBalance($payload->type, $payload->getDescription(), $payload->num, $payload->oldMoney, $payload->newMoney, 'content_purchase', $orderRow['created_at']);
                break;
            case AudioBuyPayload::class:
                $audioRow = AudioModel::findByID(strval($payload->audioId));
                CenterDataService::doReduceBalance($payload->audioId, $audioRow['name'], $payload->num, $payload->oldMoney, $payload->newMoney, 'content_purchase', $audioRow['created_at']);
                break;
            case ComicsBuyPayload::class:
                $comicsRow = ComicsModel::findByID(strval($payload->comicsId));
                CenterDataService::doReduceBalance($payload->comicsId, $comicsRow['name'], $payload->num, $payload->oldMoney, $payload->newMoney, 'content_purchase', $comicsRow['created_at']);
                break;
            case MovieBuyPayload::class:
                $movieRow = MovieModel::findByID(strval($payload->movieId));
                CenterDataService::doReduceBalance($payload->movieId, $movieRow['name'], $payload->num, $payload->oldMoney, $payload->newMoney, 'video_unlock', $movieRow['created_at']);
                CenterDataService::doMovieBuy($payload->movieId, $movieRow['name'], $movieRow['categories'], '', $payload->orderSn, $payload->num);
                break;
            case NovelBuyPayload::class:
                $novelRow = NovelModel::findByID(strval($payload->novelId));
                CenterDataService::doReduceBalance($payload->novelId, $novelRow['name'], $payload->num, $payload->oldMoney, $payload->newMoney, 'content_purchase', $novelRow['created_at']);
                break;
            case PostBuyPayload::class:
                $postRow = PostModel::findByID(strval($payload->postId));
                CenterDataService::doReduceBalance($payload->postId, $postRow['title'], $payload->num, $payload->oldMoney, $payload->newMoney, 'content_purchase', $postRow['created_at']);
                break;
                // 视频观看
            case MovieViewCompletePayload::class:
                $movieInfo = MovieService::getInfoCache($payload->movieId);
                CenterDataService::doMoviePlayEvent(
                    $payload->movieId,
                    $movieInfo['name'],
                    $movieInfo['categories']['id'],
                    $movieInfo['categories']['name'],
                    array_column($movieInfo['tags'], 'id'),
                    array_column($movieInfo['tags'], 'name'),
                    $movieInfo['duration'],
                    strval($payload->playTime),
                    'video_complete',
                    strval($payload->viewTime),
                );
                break;
            case MovieViewPayload::class:
                $movieInfo = MovieService::getInfoCache($payload->movieId);
                CenterDataService::doMoviePlayEvent(
                    $payload->movieId,
                    $movieInfo['name'],
                    $movieInfo['categories']['id'],
                    $movieInfo['categories']['name'],
                    array_column($movieInfo['tags'], 'id'),
                    array_column($movieInfo['tags'], 'name'),
                    $movieInfo['duration'],
                    strval($payload->playTime),
                    'video_view',
                    strval($payload->viewTime),
                );
                break;
                // 视频点赞
            case MovieLovePayload::class:
                $movieRow = MovieModel::findByID(strval($payload->movieId));
                CenterDataService::doMovieLove($payload->movieId, $movieRow['name'], $movieRow['categories'], '', $payload->status);
                break;
                // 视频评论
            case CommentPayload::class:
                if ($payload->objectType == 'movie') {
                    $movieRow = MovieModel::findByID(strval($payload->movieId));
                    CenterDataService::doMovieComment($payload->movieId, $movieRow['name'], $movieRow['categories'], '', $payload->content);
                }
                break;
                // 视频收藏
            case MovieFavoritePayload::class:
                $movieRow = MovieModel::findByID(strval($payload->movieId));
                CenterDataService::doMovieFavorite($payload->movieId, $movieRow['name'], $movieRow['categories'], '', $payload->status);
                break;
                // 搜索关键词
            case MovieSearchKeywordPayload::class:
                CenterDataService::doKeywordSearch($payload->keywords, $payload->resultCount);
                break;
                // 广告点击
            case AdvClickPayload::class:
                if ($payload->objectType == 'adv') {
                    $advRow = AdvModel::findByID(strval($payload->advId));
                    CenterDataService::doAdvClick($payload->advId, $advRow['position_code'], $advRow['position_code']);
                } elseif ($payload->objectType == 'adv-app') {
                    $advRow = AdvAppModel::findByID(strval($payload->advId));
                    CenterDataService::doAdvAppClick($payload->advId, $advRow['position'][0] ?? '', $advRow['position'][0] ?? '');
                }
                break;
                // 广告展示
            case AdvShowPayload::class:
                break;
        }
    }

    /**
     * Ai任务
     * @return void
     */
    public function aiTask()
    {
        $payload   = $this->payload;
        $className = get_class($payload);

        switch ($className) {
            // 用户注册
            case UserRegisterPayload::class:
                // 用户登录
            case UserLoginPayload::class:
                // 订单创建
            case UserDoVipPayload::class:
                // 订单创建
            case UserDoRechargePayload::class:
                // 这些行为说明用户已经没在第三方平台,所以进行带出
                try {
                    AiGirlService::tryExit($payload->userId);
                } catch (\Exception $e) {
                }
                try {
                    AiToolsService::tryExit($payload->userId);
                } catch (\Exception $e) {
                }
                break;
        }
    }

    public function success($_id)
    {
    }

    public function error($_id, \Exception $e)
    {
    }
}
