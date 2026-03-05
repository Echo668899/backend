<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 自增ID生成
 * @package App\Models
 * @property int _id 编号
 * @property string name 名称
 * @property string id 值
 */
class CollectionIdsModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'collection_ids';
}
