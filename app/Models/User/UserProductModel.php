<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Core\Mongodb\MongoModel;

/**
 * 用户产品套餐
 * @package App\Models
 * @property string _id 编号
 * @property string name 套餐名称
 * @property string type 类型 point金币
 * @property string tips 促销提示
 * @property int num 数量
 * @property int gift_num 赠送数量
 * @property int vip_num 赠送vip天数
 * @property float price 价格
 * @property int sort 排序
 * @property string description 描述
 * @property string price_tips 价格提示
 * @property int is_disabled 是否禁用
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class UserProductModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'user_product';
}
