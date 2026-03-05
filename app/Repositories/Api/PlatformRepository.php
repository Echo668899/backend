<?php

namespace App\Repositories\Api;

use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Services\Ai\AiGirlService;
use App\Services\Ai\AiToolsService;

/**
 * 第三方平台
 */
class PlatformRepository extends BaseRepository
{
    /**
     * @param                    $userId
     * @param                    $code
     * @return array
     * @throws BusinessException
     */
    public static function enter($userId, $code)
    {
        switch ($code) {
            case 'aigirl':
                $result = AiGirlService::enter($userId);
                break;
            case 'aitools':
                $result = AiToolsService::enter($userId);
                break;
            default:
                throw new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        return [
            'auth_url' => strval($result['auth_url']),
        ];
    }

    /**
     * 退出
     * @param                    $userId
     * @param                    $code
     * @return void
     * @throws BusinessException
     */
    public static function exit($userId, $code)
    {
        switch ($code) {
            case 'aigirl':
                try {
                    AiGirlService::tryExit($userId);
                } catch (\Exception $e) {
                }
                break;
            case 'aitools':
                try {
                    AiToolsService::tryExit($userId);
                } catch (\Exception $e) {
                }
                break;
            default:
                throw new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
    }
}
