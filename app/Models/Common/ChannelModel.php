<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 渠道
 * @package App\Models
 * @property string _id 编号
 * @property string code 渠道标识
 * @property string name 渠道名
 * @property string remark
 * @property int is_disabled 是否禁用
 * @property int last_bind
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ChannelModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'channel';
}
