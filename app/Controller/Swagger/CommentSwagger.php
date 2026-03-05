<?php

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

/**
 * Class CommentController
 * @package App\Controller\Swagger
 *
 * @OA\Tag(
 *     name="Comment",
 *     description="评论相关接口"
 * )
 */
class CommentSwagger extends BaseApiController
{
    /**
     * 发表评论
     *
     * @OA\Post(
     *     path="/comment/doComment",
     *     tags={"Comment"},
     *     summary="发表评论",
     *     description="对影片/有声等对象发表评论",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id","content"},
     *             @OA\Property(property="id", type="string", description="对象ID"),
     *             @OA\Property(property="content", type="string", description="评论内容"),
     *             @OA\Property(property="time", type="string", description="播放时间点"),
     *             @OA\Property(property="type", type="string", default="movie", description="评论对象类型"),
     *             @OA\Property(property="comment_type", type="string", default="text", description="评论类型")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     )
     * )
     *
     * @throws \App\Exception\BusinessException
     */
    public function doCommentAction()
    {
    }

    /**
     * 评论列表
     *
     * @OA\Post(
     *     path="/comment/list",
     *     tags={"Comment"},
     *     summary="评论列表",
     *     description="获取对象的评论列表",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="对象ID"
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(type="integer", default=1),
     *         description="页码"
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         @OA\Schema(type="string", default="movie"),
     *         description="评论对象类型"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     )
     * )
     *
     * @throws \App\Exception\BusinessException
     */
    public function listAction()
    {
    }

    /**
     * 回复列表
     *
     * @OA\Post(
     *     path="/comment/replyList",
     *     tags={"Comment"},
     *     summary="回复列表",
     *     description="获取某条评论的回复列表",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="评论ID"
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(type="integer", default=1),
     *         description="页码"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="成功"
     *     )
     * )
     */
    public function replyListAction()
    {
    }

    /**
     * 评论点赞
     *
     * @OA\Post(
     *     path="/comment/doLove",
     *     tags={"Comment"},
     *     summary="评论点赞",
     *     description="对评论进行点赞或取消点赞",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="string", description="评论ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="返回 y 或 n"
     *     )
     * )
     *
     * @throws \App\Exception\BusinessException
     */
    public function doLoveAction()
    {
    }
}
