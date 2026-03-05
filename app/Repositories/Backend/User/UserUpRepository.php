<?php

declare(strict_types=1);

namespace App\Repositories\Backend\User;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\User\UserModel;
use App\Models\User\UserUpModel;
use App\Services\User\UserUpService;
use App\Utils\CommonUtil;

/**
 * 用户up
 * @package App\Repositories\Backend
 */
class UserUpRepository extends BaseRepository
{
    /**
     * 获取列表
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);
        $sort     = self::getRequest($request, 'sort', 'string', '_id');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['_id']) {
            $filter['_id'] = self::getRequest($request, '_id', 'int');
            $query['_id']  = $filter['_id'];
        }
        if ($request['nickname']) {
            $filter['nickname'] = self::getRequest($request, 'nickname', 'string');
            $query['nickname']  = ['$regex' => $filter['nickname'], '$options' => 'i'];
        }
        if ($request['categories']) {
            $filter['categories'] = self::getRequest($request, 'categories', 'string');
            $query['categories']  = $filter['categories'];
        }
        if ($request['is_hot'] !== '') {
            $filter['is_hot'] = self::getRequest($request, 'is_hot', 'int');
            $query['is_hot']  = $filter['is_hot'];
        }
        $skip   = ($page - 1) * $pageSize;
        $fields = [];

        $count = UserUpModel::count($query);
        $items = UserUpModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['categories'] = CommonValues::getUpCategories($item['categories']);
            $items[$index]      = $item;
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
     * 保存数据
     * @param                    $data
     * @return bool|int|mixed
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            '_id'        => self::getRequest($data, '_id', 'int'),
            'categories' => self::getRequest($data, 'categories', 'string'),
            'cup'        => self::getRequest($data, 'cup', 'string'),
            'is_hot'     => self::getRequest($data, 'is_hot', 'int', 0),
            'sort'       => self::getRequest($data, 'sort', 'int', 0),
            'birthday'   => self::getRequest($data, 'birthday', 'int', 0),
        ];
        if (empty($row['_id'])) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '参数错误!');
        }
        $userRow = UserModel::findByID(intval($data['_id']));
        if (empty($userRow)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '用户不存在!');
        }
        $row['username'] = $userRow['username'];
        $row['nickname'] = $userRow['nickname'];
        $row['headico']  = $userRow['headico'];
        if (empty($row['first_letter'])) {
            $row['first_letter'] = strtoupper(substr(CommonUtil::pinyin($userRow['nickname'], true), 0, 1));
        }
        if (UserUpModel::count(['_id' => intval($row['_id'])]) > 0) {
            return UserUpModel::updateById($row, $row['_id']);
        }
        return UserUpService::do($row['_id'], $row['nickname'], $row['headico'], $row['categories'], true);
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = UserUpModel::findByID(intval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return $row;
    }

    /**
     * 删除
     * @param        $id
     * @return mixed
     */
    public static function delete($id)
    {
        return UserUpModel::deleteById(intval($id));
    }
}
