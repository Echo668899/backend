<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 系统配置
 * @package App\Models
 * @property string _id 编号
 * @property string code 配置code
 * @property string name 配置名称
 * @property int type 类型1文本框,2多行,3单选,4下拉,5富文本 6上传文件
 * @property string value 配置值
 * @property string values 配置值选型
 * @property string group 分组key,base,other,app
 * @property int sort 配置排序
 * @property string help 帮助信息
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ConfigModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'config';
}
