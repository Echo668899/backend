<?php

declare(strict_types=1);

namespace App\Repositories\Backend\Novel;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Novel\NovelBlockModel;
use App\Models\Novel\NovelNavModel;
use App\Services\Novel\NovelNavService;

/**
 * 小说模块管理
 * @package App\Repositories\Backend
 */
class NovelBlockRepository extends BaseRepository
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
        $count  = NovelBlockModel::count($query);
        $items  = NovelBlockModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['created_at']  = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']  = date('Y-m-d H:i', $item['updated_at']);
            $item['is_disabled'] = CommonValues::getIs($item['is_disabled']);
            $item['nav_id']      = value(function () use ($item) {
                $position = self::getPosition($item['nav_id']);
                return $position['id'] . ' | ' . $position['name'];
            });
            $item['route_name'] = $item['route_name'] ?: '-';
            $item['style']      = CommonValues::getNovelBlockStyle($item['style']);
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
     * 返回模块显示位置
     * @param              $id
     * @return array|mixed
     */
    public static function getPosition($id)
    {
        $positions = NovelNavService::getAll();
        foreach ($positions as $position) {
            if ($id == $position['id']) {
                return $position;
            }
        }
        return [];
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
            'route'       => self::getRequest($data, 'route', 'string', ''),
            'route_name'  => self::getRequest($data, 'route_name', 'string', ''),
        ];
        if (empty($row['name']) || empty($row['style']) || empty($row['nav_id']) || empty($row['filter'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $row['filter'] = stripcslashes($row['filter']);
        if (!json_decode($row['filter'], true)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'json格式错误!');
        }

        $navRow = NovelNavModel::findByID($row['nav_id']);
        if (empty($navRow)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '未找到相关菜单!');
        }

        if (in_array($navRow['style'], ['novel_2', 'novel_3'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '该菜单为列表菜单,请直接配置搜索条件,禁止添加模块!');
        }

        if ($row['style'] >= 40 && $row['style'] <= 49) {
            $hasChildFilter = false;
            foreach (json_decode($row['filter'], true) as $index => $item) {
                if (empty($item['name'])) {
                    throw  new BusinessException(StatusCode::PARAMETER_ERROR, CommonValues::getComicsBlockStyle($row['style']) . " 特殊模块,搜索条件配置错误\n可寻求技术指导");
                }
                if (empty($item['filter'])) {
                    throw  new BusinessException(StatusCode::PARAMETER_ERROR, CommonValues::getComicsBlockStyle($row['style']) . " 特殊模块,搜索条件配置错误\n可寻求技术指导");
                }
                $hasChildFilter = true;
            }
            if ($hasChildFilter == false) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, CommonValues::getComicsBlockStyle($row['style']) . " 特殊模块,搜索条件配置错误\n可寻求技术指导");
            }
        }

        if ($data['_id'] > 0) {
            $row['_id'] = self::getRequest($data, '_id', 'int');
        }
        return NovelBlockModel::save($row);
    }

    /**
     * 获取详情
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = NovelBlockModel::findByID(intval($id));
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
        return NovelBlockModel::deleteById(intval($id));
    }
}
