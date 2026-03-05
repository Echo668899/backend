<?php

namespace App\Services\Common;

use App\Constants\CacheKey;
use App\Core\Services\BaseService;
use App\Models\Common\ArticleModel;
use App\Utils\CommonUtil;

class ArticleService extends BaseService
{
    /**
     * 获取通知
     * @return string|null
     */
    public static function getAnnouncement()
    {
        $items = self::getArticleList('announcement', 1, 1);
        if (empty($items['data'])) {
            return null;
        }
        return strval($items['data'][0]['content']);
    }

    /**
     * 获取文章
     * @param                             $category_code
     * @param                             $page
     * @param                             $pageSize
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function getArticleList($category_code, $page = 1, $pageSize = 10)
    {
        $keyName = CacheKey::ARTICLE;
        $result  = cache()->get($keyName);
        if (is_null($result)) {
            $result = ArticleModel::find([], [], ['sort' => -1], 0, 1000);
            cache()->set($keyName, $result, 300);
        }
        $rows = [];
        foreach ($result as $item) {
            if ($category_code != $item['category_code']) {
                continue;
            }
            $rows[] = [
                'id'      => strval($item['_id']),
                'img'     => strval($item['img']),
                'title'   => strval($item['title']),
                'content' => strval($item['content']),
                'label'   => date('Y-m-d H:i:s', $item['created_at'])
            ];
        }
        $count = count($rows);
        $data  = [
            'data'         => CommonUtil::arrayPage($rows, $page, $pageSize),
            'total'        => $count,
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
        ];
        $data['last_page'] = strval(ceil($count / $pageSize));
        return $data;
    }
}
