<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 会话游标
 * @package App\Models
 * @property int _id ID
 * @property string chat_id 会话ID chatid
 * @property string user_id 用户ID(阅读方) 此处用字符串,因为chat_type!=single时,也不能为<=0的数,会导致userService获取错误数据
 * @property int last_read_id 最后阅读到的消息序号(chat_message.seqid)
 * @property int last_read_seqid 最后阅读到的消息ID(chat_message._id)
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ChatReadModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'chat_read';
}
