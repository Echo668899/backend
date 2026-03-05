<?php

declare(strict_types=1);

namespace App\Controller\Swagger;

use App\Controller\BaseApiController;

class PingSwagger extends BaseApiController
{
    /**
     * ping检测
     * @OA\Post(
     *     path="/ping",
     *     summary="ping检测",
     *      tags={"Ping"},
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
    public function indexAction()
    {
    }
}
