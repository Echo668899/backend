<?php

declare(strict_types=1);

namespace App\Services\Common;

use App\Core\Services\BaseService;
use App\Models\Common\ArticleCategoryModel;
use App\Utils\TreeUtil;

/**
 *  文章分类
 * @package App\Services
 */
class ArticleCategoryService extends BaseService
{
    /**
     * 获取资源树状
     * @return array
     */
    public static function getTree()
    {
        $data     = self::getAll();
        $treeUtil = new TreeUtil($data);
        return $treeUtil->getTree('child');
    }

    /**
     * 获取所有资源
     * @return array
     */
    public static function getAll()
    {
        $query = [];
        $items = ArticleCategoryModel::find($query, [], ['sort' => -1], 0, 1000);
        $data  = [];
        foreach ($items as $item) {
            $data[$item['code']] = [
                'id'        => $item['_id'],
                'code'      => $item['code'],
                'parent_id' => $item['parent_id'],
                'name'      => $item['name'],
                'img'       => $item['img'],
                'sort'      => $item['sort']
            ];
        }
        return $data;
    }

    /**
     * 获取资源树状
     * @return string
     */
    public static function getTreeOptions()
    {
        $data     = self::getAll();
        $treeUtil = new TreeUtil(array_values($data));
        return $treeUtil->getHtmlOptions();
    }

    /**
     * 获取资源树状
     * @return string
     */
    public static function getTreeCodeOptions()
    {
        $data     = self::getAll();
        $treeUtil = new TreeUtil(array_values($data));
        return $treeUtil->getHtmlOptions('', '&nbsp;&nbsp;', false, 'code');
    }
}
