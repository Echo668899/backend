<?php

namespace App\Tasks;

use App\Constants\CommonValues;
use App\Core\BaseTask;
use App\Jobs\Common\ResourceUpdateJob;
use App\Models\Common\ConfigModel;
use App\Services\Audio\AudioService;
use App\Services\Comics\ComicsService;
use App\Services\Common\ConfigService;
use App\Services\Common\JobService;
use App\Services\Movie\MovieService;
use App\Services\Novel\NovelService;
use App\Services\Post\PostService;
use Phalcon\Manager\MediaLSJService;

class MediaTask extends BaseTask
{
    public function movieAction()
    {
        /**
         * 分类编号(cat_id)    名称    分区(position)
         * 13    成人短视频    guochan
         * 12    VR    av
         * 11    电影解说    movie
         * 10    音乐    movie
         * 9    短剧    movie
         * 8    纪录片    movie
         * 7    动漫    movie
         * 6    电影    movie
         * 5    连续剧    movie
         * 4    综艺    movie
         * 3    GC    guochan
         * 2    DM    guochan
         * 1    AV    av
         */
        MovieService::asyncMrsByCat('1');
        //        MovieService::asyncMrsByIds([
        //
        //        ]);
    }

    public function novelAction()
    {
        NovelService::asyncMrsByCat('normal');
        //        NovelService::asyncMrsByIds([
        //
        //        ]);
    }

    public function audioAction()
    {
        AudioService::asyncMrsByCat('audio');
        //        AudioService::asyncMrsByIds([
        //
        //        ]);
    }

    public function comicsAction()
    {
        $categories = CommonValues::getComicsCategories();

        ComicsService::asyncMrsByCat('韩漫');
        //        ComicsService::asyncMrsByIds([
        //
        //        ]);
    }

    public function postAction()
    {
        PostService::asyncMrsByCat('');
        //        PostService::asyncMrsByIds([
        //
        //        ]);
    }

    /**
     * 更新资源
     *
     * @param  string $type movie,comics,novel,audio
     * @return void
     */
    public function resource($type)
    {
        JobService::create(new ResourceUpdateJob($type));
    }

    /**
     * 同步cdn域名 *\/2 * * * *
     * @return void
     */
    public function cdnAction()
    {
        $mediaUrl     = ConfigService::getConfig('media_api');
        $mediaAppid   = ConfigService::getConfig('media_appid');
        $mediaKey     = ConfigService::getConfig('media_key');
        $mediaService = new MediaLSJService($mediaUrl, $mediaKey, $mediaAppid);
        $result       = $mediaService->getCdnDomain();

        $cdnImage = [
            'free'    => $result['image']['free'],
            'aws'     => $result['image']['aws'],
            'tencent' => $result['image']['tencent'],
            'source'  => $result['image']['source'],
        ];
        ConfigModel::update(['value' => value(function () use ($cdnImage) {
            $result = [];
            foreach ($cdnImage as $key => $item) {
                $result[] = $key . '=>' . $item;
            }
            return join("\r\n", $result);
        })], ['code' => 'cdn_image']);
        if (!empty($cdnImage['free'])) {
            ConfigModel::update(['value' => $cdnImage['free']], ['code' => 'media_url']);// 后台回显
        }

        $cdnVideo = [
            'free'    => $result['video']['free'],
            'aws'     => $result['video']['aws'],
            'tencent' => $result['video']['tencent'],
        ];
        ConfigModel::update(['value' => value(function () use ($cdnVideo) {
            $result = [];
            foreach ($cdnVideo as $key => $item) {
                $result[] = $key . '=>' . $item;
            }
            return join("\r\n", $result);
        })], ['code' => 'cdn_video']);
        if (!empty($cdnVideo['free'])) {
            ConfigModel::update(['value' => $cdnVideo['free']], ['code' => 'media_url_video']);// 后台回显
        }

        if (!empty($result['video']['m3u8'])) {
            ConfigModel::update(['value' => $result['video']['m3u8']], ['code' => 'media_url_m3u8']);
        }

        if (!empty($result['upload']['upload_url'])) {
            ConfigModel::update(['value' => $result['upload']['upload_url']], ['code' => 'upload_url']);
        }
        if (!empty($result['upload']['media_dir'])) {
            ConfigModel::update(['value' => $result['upload']['media_dir']], ['code' => 'media_dir']);
        }
        if (!empty($result['upload']['upload_key'])) {
            ConfigModel::update(['value' => $result['upload']['upload_key']], ['code' => 'upload_key']);
        }
        //        if (!empty($result['upload']['user_id'])) {
        //            ConfigModel::update(['value' => $result['upload']['user_id']], ['code' => 'upload_user_id']);
        //        }
        ConfigService::deleteCache();
    }
}
