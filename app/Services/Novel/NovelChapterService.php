<?php

namespace App\Services\Novel;

use App\Core\Services\BaseService;
use App\Models\Novel\NovelChapterModel;

class NovelChapterService extends BaseService
{
    /**
     * 获取小说章节
     * @param        $novelId
     * @param  int   $page
     * @param  int   $pageSize
     * @return array
     */
    public static function getChapterList($novelId, $page = 1, $pageSize = 1000)
    {
        if (empty($novelId)) {
            return [];
        }
        $fields = ['_id', 'name', 'img'];
        $rows   = NovelChapterModel::find(['novel_id' => $novelId], $fields, ['sort' => 1], ($page - 1) * $pageSize, $pageSize);
        foreach ($rows as &$row) {
            $row = [
                'id'   => $row['_id'],
                'name' => $row['name'],
                'img'  => $row['img'],
            ];
            unset($row);
        }
        return $rows;
    }
}
