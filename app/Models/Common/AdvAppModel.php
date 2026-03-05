<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 应用中心
 * @package App\Models
 * @property string _id 编号
 * @property string name 名称
 * @property array position 位置
 * @property string image 图片
 * @property string download_url 下载地址
 * @property string download 下载次数,直接填写
 * @property string description 描述
 * @property int sort 排序
 * @property int is_hot 热门
 * @property int is_disabled 是否禁用0 1禁用
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class AdvAppModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'adv_app';
}
