<?php

namespace App\Tasks;

use App\Constants\StatusCode;
use App\Core\BaseTask;
use App\Exception\BusinessException;
use App\Models\Admin\AdminUserModel;
use App\Models\Common\ConfigModel;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\AdminUserService;
use App\Services\Common\ConfigService;
use App\Services\Common\ElasticService;
use App\Services\Common\GoogleService;
use App\Services\Common\IpService;
use App\Utils\CommonUtil;
use App\Utils\DevUtil;
use App\Utils\LogUtil;

class ToolsTask extends BaseTask
{
    /**
     * @param  string $table
     * @return void
     */
    public function modelAction(string $table = '')
    {
        $devUtils = new DevUtil($table);
        $devUtils->autoCreateModels();
    }

    /**
     * 导入ip
     * @throws BusinessException
     */
    public function importIpAction()
    {
        $ipService = new IpService();
        $ipService->init();
    }

    /**
     * 添加管理员ip
     * @param string $action
     * @param string $ip
     */
    public function importAdminIpAction($ip = '', $action = 'add')
    {
        $ips   = ConfigService::getConfig('whitelist_ip');
        $split = CommonUtil::getSplitChar($ips);

        $ips = explode($split, $ips);

        if ($action == 'del') {
            foreach ($ips as $index => $item) {
                if ($item == $ip) {
                    unset($ips[$index]);
                }
            }
        } else {
            $ips[] = $ip;
            foreach ($ips as $index => $item) {
                if (empty($item)) {
                    unset($ips[$index]);
                }
            }
            $ips = array_unique($ips);
        }
        ConfigModel::update(['value' => join("\r\n", $ips)], ['code' => 'whitelist_ip']);
        ConfigService::deleteCache();
        LogUtil::info($ips);
    }

    /**
     * 导入后台用户
     * @return void
     */
    public function importAdminUserAction()
    {
        ini_set('memory_limit', '512M');
        try {
            $adminUserFile = RUNTIME_PATH . '/admin_user.txt';
            if (!file_exists($adminUserFile)) {
                throw new BusinessException(StatusCode::DATA_ERROR, 'no user config!');
            }

            while (true) {
                echo '是否清空admin_user表,是:y 否:n';
                $action = strtolower(trim(fgets(STDIN)));
                if (in_array($action, ['y', 'n'])) {
                    break;
                }
                echo "输入无效,请输入 是:y 否:n\n";
            }

            if ($action == 'y') {
                echo "清理中...\n";
                AdminUserModel::delete();
            }
            $handle = fopen($adminUserFile, 'r+');
            while (($line = fgets($handle)) !== false) {
                $line                                                          = str_replace(["\n", "\r"], '', $line);
                list($id, $roleId, $username, $googleCode, $status, $password) = explode(',', $line);
                if ($id && $username && $googleCode) {
                    $adminUserModel = AdminUserModel::findFirst(['username' => $username]);
                    $slat           = strval(mt_rand(10000, 50000));
                    $password       = $password ?: $username . '123!';
                    $password       = AdminUserService::makePassword($password, $slat);
                    $row            = [
                        '_id'         => intval($id),
                        'username'    => strval($username),
                        'real_name'   => strval($username),
                        'google_code' => strval($googleCode),
                        'role_id'     => intval($adminUserModel['role_id'] ?: $roleId),
                        'is_disabled' => intval($status != 'enabled'),
                        'email'       => '',
                        'password'    => strval($adminUserModel['password'] ?: $password),
                        'slat'        => strval($adminUserModel['slat'] ?: $slat),
                        'login_at'    => intval($adminUserModel['login_at'] ?: 0),
                        'login_ip'    => strval($adminUserModel['login_ip'] ?: ''),
                        'login_num'   => intval($adminUserModel['login_num'] ?: 0),
                        'created_at'  => intval($adminUserModel['created_at'] ?: time()),
                        'updated_at'  => intval($adminUserModel['updated_at'] ?: time()),
                    ];
                    $result = AdminUserModel::findAndModify(['_id' => $row['_id']], $row, [], true);
                    if (empty($result)) {
                        AdminLogService::addLog(-1, '终端', '终端添加管理:' . $row['username'], '127.0.0.1');
                    }
                    LogUtil::info("import user success name:{$username}");
                } elseif (is_numeric($id)) {
                    LogUtil::error("import user error name:{$username}");
                }
            }
            fclose($handle);
        } catch (\Exception $exception) {
            LogUtil::error($exception->getMessage());
        }
    }

    /**
     *  生成谷歌验证码
     * @param $username
     */
    public function createGoogleCodeAction($username)
    {
        if (empty($username)) {
            LogUtil::error('Username is empty!');
            return;
        }
        $secret = '';
        $url    = GoogleService::createSecret($username, 'system@' . $username, $secret);
        LogUtil::info('Username:' . $username);
        LogUtil::info('Google secret:' . $secret);
        LogUtil::info('Google code url:' . $url);
    }

    /**
     * es设置
     * @return void
     */
    public function esAction()
    {
        ElasticService::settings();
    }
}
