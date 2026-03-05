# 生成 swagger 接口文档

## 安装

```bash
composer require zircote/swagger-php doctrine/annotations --dev
```

## composer.json

```json
{
    "scripts": {
        "swagger": "openapi app/ -o public/swagger.json"
    },
    "scripts-descriptions": {
        "swagger": "接口文档生成"
    }
}
```

## Step1

```bash
vim app/WebApplication.php

<?php

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="My API",
 *         version="1.0.0",
 *         description="API documentation"
 *     )
 * )
 */
```

## Step2

```bash
<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\BaseApiController;

class PingController extends BaseApiController
{
    /**
     * ping检测
     * @OA\Post(
     *     path="/ping",
     *     summary="ping检测",
     *     tags={"Ping"},
     *     @OA\Response(
     *         response=200,
     *         description="ok"
     *     )
     * )
     */
    public function indexAction()
    {
        $this->sendSuccessResult();
    }
}
```
