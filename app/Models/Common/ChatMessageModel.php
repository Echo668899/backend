<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 消息管理
 * @package App\Models
 * @property int _id 自增ID
 * @property string chat_id 会话ID from_to
 * @property string from_id 发送人 此处用字符串,因为chat_type!=single时,也不能为<=0的数,会导致userService获取错误数据
 * @property string to_id 接收人 此处用字符串,因为chat_type!=single时,也不能为<=0的数,会导致userService获取错误数据
 * @property int seqid 会话内自增序号
 * @property string msg_type 消息类型 text image voice video location notify tips
 * @property object msg_body 消息体
 * @property string ext 扩展
 * @property array deleted_by 删除的用户
 * @property int is_recalled 消息级状态 0未撤回 1已撤回
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ChatMessageModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'chat_message';
}
