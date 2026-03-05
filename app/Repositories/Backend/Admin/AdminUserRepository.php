<?php

namespace App\Repositories\Backend\Admin;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Admin\AdminRoleModel;
use App\Models\Admin\AdminUserModel;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\AdminRoleService;
use App\Services\Admin\AdminUserService;
use App\Services\Admin\AuthorityService;
use App\Services\Common\ConfigService;
use App\Services\Common\GoogleService;
use App\Services\Common\TokenService;
use App\Utils\CommonUtil;

class AdminUserRepository extends BaseRepository
{
    /**
     * 登录
     * @param                    $username
     * @param                    $password
     * @param                    $googleCode
     * @return array
     * @throws BusinessException
     */
    public static function login($username, $password, $googleCode)
    {
        $ip      = CommonUtil::getClientIp();
        $configs = ConfigService::getAll();

        if (kProdMode) {
            $ips = $configs['whitelist_ip'];
            if (empty($ips)) {
                throw  new BusinessException(StatusCode::DATA_ERROR, '请联系管理员配置系统白名单!');
            }
            if (strpos($ips, $ip) === false) {
                throw  new BusinessException(StatusCode::DATA_ERROR, '当前ip不在白名单!');
            }
        }

        $adminUser = AdminUserModel::findFirst(['username' => $username]);
        if (empty($adminUser) || $adminUser['is_disabled']) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '用户不存在或已被禁用!');
        }

        if (kProdMode) {
            if (empty($adminUser['google_code'])) {
                throw  new BusinessException(StatusCode::DATA_ERROR, '请联系管理员绑定谷歌验证码!');
            }
            $verifyGoogleCode = self::verifyGoogleCode($googleCode, $adminUser);
            if (!$verifyGoogleCode) {
                throw  new BusinessException(StatusCode::DATA_ERROR, '谷歌验证码错误!');
            }
        }

        $checkPassword = AdminUserService::makePassword($password, $adminUser['slat']);
        if ($checkPassword != $adminUser['password']) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '密码错误!');
        }
        AdminUserModel::updateRaw(['$set' => ['login_at' => time(), 'login_ip' => CommonUtil::getClientIp(), ], '$inc' => ['login_num' => 1]], ['_id' => $adminUser['_id']]);

        $token = TokenService::set($adminUser['_id'], $adminUser['username'], 'admin');
        AdminLogService::do('用户登录');
        return $token;
    }

    /**
     * 校验谷歌验证
     * @param             $googleCode
     * @param  null|array $admin
     * @return bool
     */
    public static function verifyGoogleCode($googleCode, $admin = null)
    {
        if (empty($admin)) {
            $token = AdminUserService::getToken();
            if (empty($token)) {
                return false;
            }
            $admin = AdminUserModel::findByID(intval($token['user_id']));
        }
        return GoogleService::verifyCode($admin['google_code'], $googleCode);
    }

    /**
     * 退出登录
     * @return bool
     */
    public static function logout()
    {
        $userId = AdminUserService::getUserId();
        TokenService::del($userId, 'admin');
        return true;
    }

    /**
     * 获取可操作的菜单
     * @return array
     */
    public static function getMenus()
    {
        $permissions = self::getPermissions();
        foreach ($permissions as $index => $permission) {
            if (!$permission['is_menu']) {
                unset($permissions[$index]);
                continue;
            }
            foreach ($permission['child'] as $key => $child) {
                if (!$child['is_menu']) {
                    unset($permissions[$index]['child'][$key]);
                    continue;
                }
            }
        }
        return array_values($permissions);
    }

    /**
     * 获取所有权限
     * @param  bool|null $isSupperAdmin
     * @return array
     */
    public static function getPermissions(bool &$isSupperAdmin = null)
    {
        $userId = AdminUserService::getUserId();
        if (empty($userId)) {
            return [];
        }
        $adminUser = AdminUserModel::findByID(intval($userId));
        if (empty($adminUser) || $adminUser['is_disabled']) {
            return [];
        }

        $permissions = AuthorityService::getTree();
        if (empty($adminUser['role_id'])) {
            $isSupperAdmin = true;
            return $permissions;
        }

        $adminRole = AdminRoleModel::findByID(intval($adminUser['role_id']));
        if (empty($adminRole) || $adminRole['is_disabled']) {
            return [];
        }

        $rights = $adminRole['rights'];
        if (is_string($rights)) {
            $rights = explode(',', $rights);
        }
        foreach ($permissions as $index => $parent) {
            foreach ($parent['child'] as $key => $resource) {
                if (!in_array($resource['key'], $rights)) {
                    unset($parent['child'][$key]);
                }
            }
            $parent['child'] = array_values($parent['child']);
            if (empty($parent['child'])) {
                unset($permissions[$index]);
            } else {
                $permissions[$index] = $parent;
            }
        }
        return array_values($permissions);
    }

    /**
     * 检查权限
     * @param  string $path
     * @return bool
     */
    public static function checkPermission(string $path = ''): bool
    {
        $isSupperAdmin = false;
        $permissions   = self::getPermissions($isSupperAdmin);
        if ($isSupperAdmin) {
            return true;
        }
        $result = false;
        foreach ($permissions as $permission) {
            if ($permission['key'] == $path) {
                $result = true;
                break;
            }
            foreach ($permission['child'] as $child) {
                if ($child['key'] == $path) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @param       $oldPassword
     * @param       $newPassword
     * @return bool
     */
    public static function changePassword($oldPassword, $newPassword): bool
    {
        $token = AdminUserService::getToken();
        if (empty($token)) {
            throw  new BusinessException(StatusCode::NO_LOGIN_ERROR);
        }
        $adminUser = AdminUserModel::findByID(intval($token['user_id']));
        if (empty($adminUser) || $adminUser['is_disabled']) {
            throw  new BusinessException(StatusCode::NO_LOGIN_ERROR);
        }
        $oldPassword = AdminUserService::makePassword($oldPassword, $adminUser['slat']);
        if ($oldPassword != $adminUser['password']) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '旧密码不正确!');
        }

        $newPassword = AdminUserService::makePassword($newPassword, $adminUser['slat']);
        if ($newPassword == $oldPassword) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '新旧密码一样,无需修改!');
        }

        AdminUserModel::updateById(['password' => $newPassword], intval($adminUser['_id']));
        return true;
    }

    /**
     * 验证是否root用户
     * @param       $userId
     * @return bool
     */
    public static function checkRoot($userId)
    {
        $adminUser = AdminUserModel::findByID(intval($userId));
        if (empty($adminUser) || $adminUser['is_disabled']) {
            return false;
        }
        if (empty($adminUser['role_id'])) {
            return true;
        }
        return false;
    }

    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 10);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['username']) {
            $filter['username'] = self::getRequest($request, 'username');
            $query['username']  = ['$regex' => $filter['username'], '$options' => 'i'];
        }
        if (isset($request['role_id']) && $request['role_id'] !== '') {
            $filter['role_id'] = self::getRequest($request, 'role_id', 'int');
            $query['role_id']  = $filter['role_id'];
        }
        if (isset($request['is_disabled']) && $request['is_disabled'] !== '') {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];

        $count = AdminUserModel::count($query);
        $items = AdminUserModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        $roles = AdminRoleService::getRoles();
        foreach ($items as $index => $item) {
            $item['created_at']       = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']       = date('Y-m-d H:i', $item['updated_at']);
            $item['login_at']         = date('Y-m-d H:i', $item['login_at']);
            $item['login_ip']         = strval($item['login_ip']);
            $item['role_name']        = empty($item['role_id']) ? '超级管理员' : strval($roles[$item['role_id']]['name']);
            $item['bind_google_code'] = empty($item['google_code']) ? 0 : 1;
            $item['is_disabled']      = CommonValues::getIs($item['is_disabled']);
            unset($item['google_code'], $item['password'], $item['slat']);

            $items[$index] = $item;
        }

        return [
            'filter'   => $filter,
            'items'    => empty($items) ? [] : array_values($items),
            'count'    => $count,
            'page'     => $page,
            'pageSize' => $pageSize
        ];
    }

    /**
     * 获取详情
     * @param        $id
     * @return mixed
     */
    public static function getDetail($id)
    {
        $row = AdminUserModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        unset($row['password'], $row['slat']);

        return $row;
    }

    /**
     * 保存数据
     * @param                 $data
     * @return bool|int|mixed
     */
    public static function save($data)
    {
        $username = self::getRequest($data, 'username');
        $password = self::getRequest($data, 'password');
        if (empty($username)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if (empty($data['_id']) && empty($password)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '新增用户密码不能为空!');
        }
        $checkUser = AdminUserModel::findFirst(['username' => $username]);
        if ($checkUser && $checkUser['_id'] != $data['_id']) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '用户名已经存在!');
        }
        $row = [
            'username'    => $username,
            'real_name'   => self::getRequest($data, 'real_name'),
            'google_code' => self::getRequest($data, 'google_code'),
            'role_id'     => self::getRequest($data, 'role_id', 'int', 0),
            'is_disabled' => self::getRequest($data, 'is_disabled', 'int', 0),
        ];
        if ($password) {
            $row['slat']     = strval(mt_rand(10000, 50000));
            $row['password'] = AdminUserService::makePassword($password, $row['slat']);
        }
        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        $result = AdminUserModel::save($row, true);
        AdminLogService::do('操作管理员账号:' . $row['username']);
        return $result;
    }

    public static function doDisable($id)
    {
    }

    /**
     * @param $id
     */
    public static function delete($id)
    {
        return AdminUserModel::deleteById(intval($id));
    }
}
