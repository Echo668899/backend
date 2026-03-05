<?php

namespace App\Services\Admin;

use App\Core\Services\BaseService;
use App\Models\Admin\AuthorityModel;
use App\Utils\TreeUtil;

class AuthorityService extends BaseService
{
    /**
     * 获取html
     * @return string
     */
    public static function getTreeOptions()
    {
        $data     = self::getAll();
        $treeUtil = new TreeUtil($data, 'id', 'name', 'parent_id');
        return $treeUtil->getHtmlOptions('', '&nbsp;&nbsp;&nbsp;');
    }

    /**
     * 获取所有资源
     * @return array
     */
    public static function getAll()
    {
        $query = [];
        $items = AuthorityModel::find($query, [], ['sort' => -1], 0, 1000);
        $data  = [];
        foreach ($items as $item) {
            $data[] = [
                'id'         => $item['_id'],
                'name'       => $item['name'],
                'parent_id'  => $item['parent_id'],
                'key'        => $item['key'],
                'class_name' => $item['class_name'],
                'is_menu'    => $item['is_menu'],
                'link'       => $item['link']
            ];
        }
        return $data;
    }

    /**
     * 获取树形结构权限
     * @return array
     */
    public static function getTree()
    {
        $data     = self::getAll();
        $treeUtil = new TreeUtil($data, 'id', 'name', 'parent_id');
        return $treeUtil->getTree();
    }
}
