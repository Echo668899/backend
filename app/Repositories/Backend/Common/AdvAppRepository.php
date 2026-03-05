<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Common;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Common\AdvAppModel;
use App\Services\Admin\AdminLogService;
use App\Services\Common\AdvAppService;

/**
 * 应用中心
 */
class AdvAppRepository extends BaseRepository
{
    /**
     * @param        $request
     * @return array
     */
    public static function getList($request)
    {
        $page     = self::getRequest($request, 'page', 'int', 1);
        $pageSize = self::getRequest($request, 'pageSize', 'int', 15);

        $query  = [];
        $filter = [];
        if ($request['_id']) {
            $filter['_id'] = self::getRequest($request, '_id');
            $query['_id']  = $filter['_id'];
        }
        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }
        if ($request['position']) {
            $filter['position'] = self::getRequest($request, 'position');
            $query['position']  = $filter['position'];
        }
        if ($request['is_hot'] !== '') {
            $filter['is_hot'] = self::getRequest($request, 'is_hot', 'int');
            $query['is_hot']  = $filter['is_hot'];
        }

        if ($request['is_disabled'] !== '') {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AdvAppModel::count($query);
        $items  = AdvAppModel::find($query, $fields, ['created_at' => -1], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);
            $item['position']   = value(function () use ($item) {
                $catArr = [];
                foreach ($item['position'] as $value) {
                    $catArr[] = $value . ' | ' . AdvAppService::$position[$value];
                }
                return implode('</br>', $catArr);
            });
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $items[$index]       = $item;
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
            'name'         => self::getRequest($data, 'name'),
            'position'     => self::getRequest($data, 'position'),
            'image'        => self::getRequest($data, 'image', 'string', ''),
            'download_url' => self::getRequest($data, 'download_url', 'string', ''),
            'download'     => self::getRequest($data, 'download', 'string', ''),
            'description'  => self::getRequest($data, 'description', 'string', ''),
            'sort'         => self::getRequest($data, 'sort', 'int', 0),
            'is_hot'       => self::getRequest($data, 'is_hot', 'int', 0),
            'is_disabled'  => self::getRequest($data, 'is_disabled', 'int', 0),
        ];

        if (empty($row['name']) || empty($row['image']) || empty($row['download_url'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if (!empty($row['position'])) {
            $row['position'] = array_values($row['position']);
        }

        if ($data['_id'] !== '') {
            $row['_id'] = self::getRequest($data, '_id', 'string');
        }
        $result = AdvAppModel::save($row, false);
        AdminLogService::do(sprintf('操作应用中心:名称%s,ios链接%s,android链接%s', $row['name'], $row['ios_url'], $row['android_url']));
        AdvAppService::deleteCache();
        return $result;
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = AdvAppModel::findByID(strval($id));
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return $row;
    }

    /**
     * 删除
     * @param           $id
     * @return bool|int
     */
    public static function delete($id)
    {
        $result = AdvAppModel::deleteByID(strval($id));
        AdminLogService::do(sprintf('删除应用中心:编号%s', $id));
        return $result;
    }
}
