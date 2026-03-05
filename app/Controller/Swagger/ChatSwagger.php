<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * @OA\Tag(name="聊天模块", description="聊天相关接口")
 */
class ChatSwagger extends BaseApiController
{
    /**
     * 用户资料
     * 由于消息没返回用户信息,所以需要客户端单独调用
     *
     * @OA\Post(
     *     path="/chat/profile",
     *     summary="批量获取用户资料",
     *      tags={"聊天模块"},
     *     description="由于消息没返回用户信息,所以需要客户端单独调用",
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="string", description="用户ID，多个用逗号分割", example="60b4d2a3e4b0e2a3b4c5d6e7,60b4d2a3e4b0e2a3b4c5d6e8")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     * @return void
     */
    public function profileAction()
    {
    }

    /**
     * 会话列表
     *
     * @OA\Post(
     *     path="/chat/list",
     *     summary="获取会话列表",
     *      tags={"聊天模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"page", "inbox"},
     *             @OA\Property(property="page", type="integer", description="页码", default=1),
     *             @OA\Property(property="inbox", type="string", description="收件箱类型", default="all", enum={"all", "read", "unread"})
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     * @return void
     */
    public function chatAction()
    {
    }

    /**
     * 消息列表
     *
     * @OA\Post(
     *     path="/chat/message",
     *     summary="获取消息列表",
     *      tags={"聊天模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"to_id"},
     *             @OA\Property(property="to_id", type="string", description="对方用户ID"),
     *             @OA\Property(property="seqid", type="integer", description="起始消息序号(用于分页)", default=0),
     *             @OA\Property(property="direction", type="string", description="拉取方向", default="before", enum={"before", "after"}),
     *             @OA\Property(property="limit", type="integer", description="拉取数量", default=50)
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     * @return void
     */
    public function messageAction()
    {
    }

    /**
     * 发送私聊消息
     *
     * @OA\Post(
     *     path="/chat/sendMessage",
     *     summary="发送私聊消息",
     *      tags={"聊天模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"to_id", "client_msg_id", "msg_type", "msg_body"},
     *             @OA\Property(property="to_id", type="string", description="接收方用户ID"),
     *             @OA\Property(property="client_msg_id", type="string", description="客户端生成的消息ID，用于去重"),
     *             @OA\Property(property="msg_type", type="string", description="消息类型 (e.g., text, image)"),
     *             @OA\Property(property="msg_body", type="object", description="消息内容体，结构随msg_type变化")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     * @return void
     * @throws \App\Exception\BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public function sendMessageAction()
    {
    }

    /**
     * 删除会话
     *
     * @OA\Post(
     *     path="/chat/doDelChat",
     *     summary="删除会话",
     *      tags={"聊天模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="string", description="会话ID，多个用逗号分割")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     */
    public function doDelChatAction()
    {
    }

    /**
     * 删除消息
     *
     * @OA\Post(
     *     path="/chat/doDelMessage",
     *     summary="删除消息",
     *      tags={"聊天模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"ids"},
     *             @OA\Property(property="ids", type="string", description="消息ID，多个用逗号分割")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     * @return void
     */
    public function doDelMessageAction()
    {
    }

    /**
     * 撤回消息
     *
     * @OA\Post(
     *     path="/chat/doRecallMessage",
     *     summary="撤回消息",
     *      tags={"聊天模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"msg_id"},
     *             @OA\Property(property="msg_id", type="integer", description="消息ID")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     * @return void
     * @throws \App\Exception\BusinessException
     */
    public function doRecallMessageAction()
    {
    }

    /**
     * 阅读消息
     *
     * @OA\Post(
     *     path="/chat/doReadMessage",
     *     summary="标记消息已读",
     *      tags={"聊天模块"},
     *     @OA\Parameter(ref="#/components/parameters/HeaderVersion"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceType"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderTime"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDebugKey"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderRequestId"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderToken"),
     *     @OA\Parameter(ref="#/components/parameters/HeaderDeviceId"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="JSON格式数据",
     *         @OA\JsonContent(
     *             required={"chat_id", "msg_id"},
     *             @OA\Property(property="chat_id", type="string", description="会话ID"),
     *             @OA\Property(property="msg_id", type="integer", description="已读的最后一条消息ID")
     *         )
     *     ),
     *     @OA\Response(
     *      response=200,
     *      description="success",
     *      @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponseEmpty")
     *          }
     *      )
     *   )
     * )
     * @return void
     */
    public function doReadMessageAction()
    {
    }
}
