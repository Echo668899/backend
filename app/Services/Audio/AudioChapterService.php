<?php

namespace App\Services\Audio;

use App\Core\Services\BaseService;
use App\Models\Audio\AudioChapterModel;

class AudioChapterService extends BaseService
{
    /**
     * 获取小说章节
     * @param        $audioId
     * @param  int   $page
     * @param  int   $pageSize
     * @return array
     */
    public static function getChapterList($audioId, $page = 1, $pageSize = 1000)
    {
        if (empty($audioId)) {
            return [];
        }
        $fields = ['_id', 'name', 'img', 'content'];
        $rows   = AudioChapterModel::find(['audio_id' => $audioId], $fields, ['sort' => 1], ($page - 1) * $pageSize, $pageSize);
        foreach ($rows as &$row) {
            $row = [
                'id'      => $row['_id'],
                'name'    => $row['name'],
                'img'     => $row['img'],
                'content' => $row['content'],
            ];
            unset($row);
        }
        return $rows;
    }
}
