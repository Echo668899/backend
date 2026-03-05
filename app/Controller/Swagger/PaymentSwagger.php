<?php

declare(strict_types=1);

namespace App\Controller\Swagger;

use App\Core\Controller\BaseController;

/**
 * Class MovieController
 * @package App\Controller\Swagger
 */
class PaymentSwagger extends BaseController
{
    /**
     * 支付通知
     * @param $paymentId
     * @param $orderId
     * @param $type
     *
     * @OA\Post(
     *     path="/payment/notify",
     *     summary="支付通知",
     *     tags={"支付模块"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="paymentId", type="string", description="支付ID"),
     *             @OA\Property(property="orderId", type="integer", description="订单ID"),
     *             @OA\Property(property="type", type="string", description="支付类型")
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
    public function notifyAction($paymentId, $orderId, $type)
    {
    }

    /**
     * 订单回退
     * 取消订单拉黑用户
     *
     * @OA\Post(
     *     path="/payment/doBack",
     *     summary="订单回退，取消订单拉黑用户",
     *     tags={"支付模块"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="order_sn", type="string", description="订单编号")
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
    public function doBackAction()
    {
    }

    /**
     * 返回
     *
     * @OA\Post(
     *     path="/payment/return",
     *     summary="返回支付页面",
     *     tags={"支付模块"},
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
    public function returnAction()
    {
    }
}
