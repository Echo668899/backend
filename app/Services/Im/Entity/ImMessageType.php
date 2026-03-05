<?php

namespace App\Services\Im\Entity;

class ImMessageType
{
    /**=======================内容消息（由 msg_type 决定具体结构）=====================**/

    /*
     * 私聊消息
     * text|image|video|transfer|location
     */
    public const CHAT_MESSAGE = 'chat.message';       // ChatMessageData
    // 对“已存在消息”的状态变更事件
    public const CHAT_RECALL = 'chat.message.recall'; // ChatRecallData      // 撤回某条消息
    public const CHAT_DELETE = 'chat.message.delete'; // ChatDeleteData      //（单向）删除某条消息

    /**=======================会话内的状态事件（不是内容消息）=====================**/
    public const CHAT_READ   = 'chat.message.read';   // ChatReadData      // 已读游标更新
    public const CHAT_TYPING = 'chat.message.typing';  // ChatTypingData     // 正在输入/停止输入

    /**=======================系统层通知=====================**/

    /*
     * 系统广播/通知
     * 账户与安全类 account.xxx 异地登录
     * 奖励与活动类 activity.xxx 任务系统执行奖励,拉新奖励通知
     * 审核与内容管理类 content.xxx 内容审核通过,内容违规等
     * 资金与交易类 funds.xxx 充值,提现
     */
    public const SYSTEM_NOTIFY = 'system.notify';   // ChatSystemNotifyData

    /**=======================交互层通知=====================**/

    /*
     * 用户交互通知
     * 评论 comment
     * 点赞 like
     * @我  mention
     * 关注 follow
     */
    public const INTERACT_NOTIFY = 'interact.notify';   // ChatInteractNotifyData

    private function __construct()
    {
    } // 防止被实例化
}
