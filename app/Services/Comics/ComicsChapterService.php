<?php

namespace App\Services\Comics;

use App\Core\Services\BaseService;
use App\Models\Comics\ComicsChapterModel;

class ComicsChapterService extends BaseService
{
    /**
     * 获取漫画章节
     * @param        $comicsId
     * @param  int   $page
     * @param  int   $pageSize
     * @return array
     */
    public static function getChapterList($comicsId, $page = 1, $pageSize = 1000)
    {
        if (empty($comicsId)) {
            return [];
        }
        $fields = ['_id', 'name', 'img'];
        $rows   = ComicsChapterModel::find(['comics_id' => $comicsId], $fields, ['sort' => 1], ($page - 1) * $pageSize, $pageSize);
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
