<?php

namespace App\Services\Admin;

use App\Core\Services\BaseService;
use App\Models\Admin\AdminLogModel;
use App\Utils\CommonUtil;

class AdminLogService extends BaseService
{
    /**
     * @param       $content
     * @return void
     */
    public static function do($content)
    {
        $token = AdminUserService::getToken();
        self::addLog($token['user_id'], $token['username'], $content);
    }

    /**
     * 保存管理员日志
     * @param         $adminId
     * @param         $adminName
     * @param         $content
     * @param  string $ip
     * @return bool
     */
    public static function addLog($adminId, $adminName, $content, $ip = '')
    {
        AdminLogModel::insert([
            'admin_id'   => intval($adminId),
            'admin_name' => $adminName,
            'content'    => $content,
            'date_time'  => CommonUtil::getTodayZeroTime(),
            'ip'         => !empty($ip) ? $ip : CommonUtil::getClientIp()
        ], true);
        return true;
    }
}
