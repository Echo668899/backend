<?php

declare(strict_types=1);

namespace App\Models\Common;

use App\Core\Mongodb\MongoModel;

/**
 * 会话管理
 * @package App\Models
 * @property string _id 会话ID chatid_{from or to}
 * @property string chat_id 会话ID chatid
 * @property string chat_type 会话类型,实体来源 single私聊 system系统 service官方号 (group群聊板块独立,单独创建chat_group表)
 * @property string from_id 发送人 此处用字符串,因为chat_type!=single时,也不能为<=0的数,会导致userService获取错误数据
 * @property string to_id 接收人 此处用字符串,因为chat_type!=single时,也不能为<=0的数,会导致userService获取错误数据
 * @property int last_msg_id 最新消息ID
 * @property int last_msg_seqid 最新消息序号（chat_message.seqid）
 * @property string last_msg_preview 最新消息预览
 * @property string last_msg_role 最后消息发送方 user或service,主要用于chat_type=service,后台判断消息是否处理,last_msg_role==user就是未处理
 * @property string inbox 会话收件箱,用户视角下的归类 main主收件箱 request消息请求箱(粉丝或陌生人消息) system(系统推送消息) service(品牌、机器人、客服)
 * @property int top 是否置顶 0否 1是
 * @property int unread_count 未读计数(缓存,每次发送消息后计算,chat_message.seqid-chat_read.last_msg_seqid)
 * @property int created_at 创建时间
 * @property int updated_at 更新时间
 */
class ChatModel extends MongoModel
{
    public static $connection = 'default';
    public static $collection = 'chat';
}
