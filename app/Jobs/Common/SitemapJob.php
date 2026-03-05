<?php

namespace App\Jobs\Common;

use App\Jobs\BaseJob;
use App\Models\Comics\ComicsModel;
use App\Models\Movie\MovieModel;
use App\Models\User\UserUpModel;
use App\Services\Common\ConfigService;
use App\Utils\LogUtil;

class SitemapJob extends BaseJob
{
    protected $baseUrl;
    protected $batchSize = 1000; // 每批处理的数量
    protected $sitemapPath;
    protected $maxUrlsPerFile   = 10000; // 每个文件最大URL数量
    protected $currentFileIndex = 0; // 当前文件索引
    protected $currentUrlCount  = 0; // 当前文件URL计数
    protected $sitemapFiles     = []; // 存储所有生成的sitemap文件
    protected $googleXml;
    protected $baiduXml;

    public function __construct()
    {
        $this->baseUrl     = rtrim(ConfigService::getConfig('site_url'), '/');
        $this->sitemapPath = BASE_PATH . '/public/sitemap/';
        // 初始化 XML 对象
        $this->initSitemapFiles();
    }

    public function handler($_id)
    {
        try {
            // 创建sitemap目录
            if (!is_dir($this->sitemapPath)) {
                if (!mkdir($this->sitemapPath, 0777, true)) {
                    throw new \Exception('无法创建sitemap目录: ' . $this->sitemapPath);
                }
            }

            // 生成视频sitemap
            $this->generateMovieSitemap();

            // 生成漫画sitemap
            $this->generateComicsSitemap();

            // 生成Up sitemap
            $this->generateUpSitemap();

            // 生成sitemap索引文件
            $this->generateSitemapIndex();

            return true;
        } catch (\Exception $e) {
            LogUtil::error('Sitemap生成失败: ' . $e->getMessage());
            throw $e;
        }
    }

    public function error($_id, \Exception $e)
    {
        // TODO: Implement error() method.
    }

    public function success($_id)
    {
        // TODO: Implement success() method.
    }

    protected function initSitemapFiles()
    {
        // 初始化新的sitemap文件
        $this->googleXml       = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
        $this->baiduXml        = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
        $this->currentUrlCount = 0;
    }

    /**
     * 视频
     * @return void
     */
    protected function generateMovieSitemap()
    {
        $where     = ['status' => 1];
        $count     = MovieModel::count($where);
        $totalPage = ceil($count / $this->batchSize);

        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $this->batchSize;
            $movies = MovieModel::find($where, ['_id', 'name'], ['_id' => -1], $skip, $this->batchSize);

            if (empty($movies)) {
                break;
            }

            // 追加到Google sitemap
            $this->appendToGoogleSitemap($movies, 'movie');

            // 追加到百度 sitemap
            $this->appendToBaiduSitemap($movies, 'movie');

            LogUtil::info("处理第Movie {$page} 页，共 {$totalPage} 页");
        }
    }

    protected function appendToGoogleSitemap($items, $type = 'movie')
    {
        if (empty($items)) {
            return;
        }

        foreach ($items as $item) {
            if ($this->currentUrlCount >= $this->maxUrlsPerFile) {
                $this->saveCurrentSitemapFiles();
            }

            $url = $this->googleXml->addChild('url');
            $url->addChild('loc', $this->getLocUrl($item, $type));
            $url->addChild('lastmod', date('Y-m-d'));
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.8');

            $this->currentUrlCount++;
        }
    }

    protected function saveCurrentSitemapFiles()
    {
        if ($this->currentUrlCount == 0) {
            return; // 如果没有URL，不保存文件
        }

        // 如果是第一个文件（前10000条记录），使用 sitemap.xml
        if ($this->currentFileIndex == 0) {
            $googleFile = $this->sitemapPath . 'sitemap.xml';
            $baiduFile  = $this->sitemapPath . 'sitemap_baidu.xml';
        } else {
            $googleFile = $this->sitemapPath . 'sitemap_' . $this->currentFileIndex . '.xml';
            $baiduFile  = $this->sitemapPath . 'sitemap_baidu_' . $this->currentFileIndex . '.xml';
        }

        // 保存Google sitemap
        if (!$this->googleXml->asXML($googleFile)) {
            throw new \Exception('无法保存Google sitemap文件: ' . $googleFile);
        }

        // 保存百度 sitemap
        if (!$this->baiduXml->asXML($baiduFile)) {
            throw new \Exception('无法保存百度 sitemap文件: ' . $baiduFile);
        }

        // 记录生成的文件（只记录非主文件）
        if ($this->currentFileIndex > 0) {
            $this->sitemapFiles[] = [
                'google' => 'sitemap_' . $this->currentFileIndex . '.xml',
                'baidu'  => 'sitemap_baidu_' . $this->currentFileIndex . '.xml'
            ];
        }

        $this->currentFileIndex++;
        $this->initSitemapFiles();
    }

    /**
     * 获取视频url
     * @param mixed $item
     * @param mixed $type
     */
    protected function getLocUrl($item, $type = 'movie')
    {
        url()->setSuffix('.html');
        if ($type == 'movie') {
            return $this->baseUrl . url()->get('movie/detail/' . $item['_id'], null, 'front');
        } elseif ($type == 'up') {
            return $this->baseUrl . url()->get('user/home/' . $item['_id'], null, 'front');
        } elseif ($type == 'comics') {
            return $this->baseUrl . url()->get('comics/detail/' . $item['_id'], null, 'front');
        }
    }

    protected function appendToBaiduSitemap($items, $type = 'movie')
    {
        if (empty($items)) {
            return;
        }

        foreach ($items as $item) {
            $url = $this->baiduXml->addChild('url');
            $url->addChild('loc', $this->getLocUrl($item, $type));
            $url->addChild('lastmod', date('Y-m-d'));
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.8');
        }
    }

    /**
     * 漫画
     * @return void
     */
    protected function generateComicsSitemap()
    {
        $where     = ['status' => 1];
        $count     = ComicsModel::count($where);
        $totalPage = ceil($count / $this->batchSize);

        for ($page = 1; $page <= $totalPage; $page++) {
            $skip   = ($page - 1) * $this->batchSize;
            $comics = ComicsModel::find($where, ['_id', 'name'], ['_id' => -1], $skip, $this->batchSize);

            if (empty($comics)) {
                break;
            }

            // 追加到Google sitemap
            $this->appendToGoogleSitemap($comics, 'comics');

            // 追加到百度 sitemap
            $this->appendToBaiduSitemap($comics, 'comics');

            LogUtil::info("处理Comics 第 {$page} 页，共 {$totalPage} 页");
        }
    }

    /**
     * up主
     * @return void
     */
    protected function generateUpSitemap()
    {
        $where     = [];
        $count     = UserUpModel::count($where);
        $totalPage = ceil($count / $this->batchSize);

        for ($page = 1; $page <= $totalPage; $page++) {
            $skip  = ($page - 1) * $this->batchSize;
            $users = UserUpModel::find($where, ['_id', 'nickname'], ['_id' => -1], $skip, $this->batchSize);

            if (empty($users)) {
                break;
            }

            // 追加到Google sitemap
            $this->appendToGoogleSitemap($users, 'up');

            // 追加到百度 sitemap
            $this->appendToBaiduSitemap($users, 'up');

            LogUtil::info("处理Up 第 {$page} 页，共 {$totalPage} 页");
        }
    }

    /**
     * 生成索引文件
     * @return void
     * @throws \Exception
     */
    protected function generateSitemapIndex()
    {
        // 保存最后一个文件
        if ($this->currentUrlCount > 0) {
            $this->saveCurrentSitemapFiles();
        }

        // 生成Google sitemap索引
        $googleIndex = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');

        // 添加主文件
        $sitemap = $googleIndex->addChild('sitemap');
        $sitemap->addChild('loc', $this->baseUrl . '/sitemap/sitemap.xml');
        $sitemap->addChild('lastmod', date('Y-m-d'));

        // 添加其他文件
        foreach ($this->sitemapFiles as $file) {
            $sitemap = $googleIndex->addChild('sitemap');
            $sitemap->addChild('loc', $this->baseUrl . '/sitemap/' . $file['google']);
            $sitemap->addChild('lastmod', date('Y-m-d'));
        }
        $googleIndex->asXML($this->sitemapPath . 'sitemap_index.xml');

        // 生成百度 sitemap索引
        $baiduIndex = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');

        // 添加主文件
        $sitemap = $baiduIndex->addChild('sitemap');
        $sitemap->addChild('loc', $this->baseUrl . '/sitemap/sitemap_baidu.xml');
        $sitemap->addChild('lastmod', date('Y-m-d'));

        // 添加其他文件
        foreach ($this->sitemapFiles as $file) {
            $sitemap = $baiduIndex->addChild('sitemap');
            $sitemap->addChild('loc', $this->baseUrl . '/sitemap/' . $file['baidu']);
            $sitemap->addChild('lastmod', date('Y-m-d'));
        }
        $baiduIndex->asXML($this->sitemapPath . 'sitemap_baidu_index.xml');

        LogUtil::info('Sitemap索引文件生成成功');
    }
}
