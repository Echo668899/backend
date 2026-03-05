<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Ai;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Ai\AiBlockModel;
use App\Models\Ai\AiNavModel;
use App\Services\Ai\AiNavService;

/**
 * 视频模块管理
 * @package App\Repositories\Backend
 */
class AiBlockRepository extends BaseRepository
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

        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }
        if ($request['nav_id']) {
            $filter['nav_id'] = self::getRequest($request, 'nav_id', 'int');
            $query['nav_id']  = $filter['nav_id'];
        }
        if (!empty($request['is_disabled'])) {
            $filter['is_disabled'] = self::getRequest($request, 'is_disabled', 'int');
            $query['is_disabled']  = $filter['is_disabled'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = AiBlockModel::count($query);
        $items  = AiBlockModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $item['nav_id']      = value(function () use ($item) {
                $position = self::getPosition($item['nav_id']);
                return $position['id'] . ' | ' . $position['name'];
            });
            $item['style'] = CommonValues::getAiBlockStyle($item['style']);
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
     * 保存数据
     * @param                    $data
     * @return bool|int|mixed
     * @throws BusinessException
     */
    public static function save($data)
    {
        $row = [
            'name'        => self::getRequest($data, 'name'),
            'sub_name'    => self::getRequest($data, 'sub_name', 'string', ''),
            'style'       => self::getRequest($data, 'style', 'int'),
            'sort'        => self::getRequest($data, 'sort', 'int', 0),
            'filter'      => self::getRequest($data, 'filter'),
            'num'         => self::getRequest($data, 'num', 'int', 0),
            'nav_id'      => self::getRequest($data, 'nav_id', 'int', ''),
            'is_disabled' => self::getRequest($data, 'is_disabled', 'int', 0),
            'icon'        => self::getRequest($data, 'icon', 'string', ''),
        ];
        if (empty($row['name']) || empty($row['style']) || empty($row['nav_id']) || empty($row['filter'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $navRow = AiNavModel::findByID($row['nav_id']);
        if (empty($navRow)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '未找到相关菜单!');
        }

        if (in_array($navRow['style'], ['ai_2', 'ai_3'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '该菜单为列表菜单,请直接配置搜索条件,禁止添加模块!');
        }

        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        return AiBlockModel::save($row);
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = AiBlockModel::findByID(intval($id));
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
        return AiBlockModel::deleteById(intval($id));
    }

    /**
     * 返回模块显示位置
     * @param              $id
     * @return array|mixed
     */
    public static function getPosition($id)
    {
        $positions = AiNavService::getAll();
        foreach ($positions as $position) {
            if ($id == $position['id']) {
                return $position;
            }
        }
        return [];
    }
}
