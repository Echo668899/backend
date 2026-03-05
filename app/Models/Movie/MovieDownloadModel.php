<?php

declare(strict_types=1);

namespace App\Models\Movie;

use App\Core\Mongodb\MongoModel;

/**
 * 视频下载
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property int movie_id 视频编号
 * @property int user_id 用户编号
 * @property string link_ids 视频链接id
 * @property string img 封面图
 * @property string duration 时长
 * @property string label 日期时间
 * @property int status 状态 1正常 -1删除
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class MovieDownloadModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'movie_download';
}
