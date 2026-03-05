<?php

declare(strict_types=1);

namespace App\Constants;

class StatusCode
{
    public const SERVER_ERROR        = 5001;
    public const DATA_ERROR          = 2001;
    public const NO_LOGIN_ERROR      = 2002;
    public const NO_PERMISSION_ERROR = 4003;
    public const PARAMETER_ERROR     = 4002;
    public const DB_ERROR            = 5003;
    public const ERRORS              = [
        StatusCode::NO_PERMISSION_ERROR => '无权操作',
        StatusCode::SERVER_ERROR        => '网络异常',
        StatusCode::NO_LOGIN_ERROR      => '请登录后操作',
        StatusCode::DATA_ERROR          => '请求数据错误',
        StatusCode::PARAMETER_ERROR     => '参数错误',
        StatusCode::DB_ERROR            => '数据执行错误!'
    ];
}
