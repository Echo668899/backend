<?php

namespace App\Repositories\Backend\Comics;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Comics\ComicsChapterModel;
use App\Models\Comics\ComicsModel;
use App\Services\Comics\ComicsService;
use App\Services\Comics\ComicsTagService;

class ComicsRepository extends BaseRepository
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

        $query  = [];
        $filter = [];

        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['$or']   = [
                ['name' => ['$regex' => $filter['name'], '$options' => 'i']],
                ['alias_name' => ['$regex' => $filter['name'], '$options' => 'i']]
            ];
        }
        if ($request['tags']) {
            $filter['tags'] = self::getRequest($request, 'tags', 'int');
            $query['tags']  = ['$in' => [$filter['tags']]];
        }
        if ($request['status'] !== '' && $request['status'] !== null) {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }
        if ($request['update_status'] !== '' && $request['update_status'] !== null) {
            $filter['update_status'] = self::getRequest($request, 'update_status', 'int');
            $query['update_status']  = $filter['update_status'];
        }
        if ($request['cat_id']) {
            $filter['cat_id'] = self::getRequest($request, 'cat_id');
            $query['cat_id']  = $filter['cat_id'];
        }
        if ($request['pay_type']) {
            $filter['pay_type'] = self::getRequest($request, 'pay_type');
            $query['pay_type']  = $filter['pay_type'];
        }
        if ($request['update_date']) {
            $filter['update_date'] = self::getRequest($request, 'update_date');
            $query['update_date']  = $filter['update_date'];
        }
        if ($request['_id']) {
            $filter['_id'] = self::getRequest($request, '_id');
            $query['_id']  = $filter['_id'];
        }
        if (isset($request['clickMinSort']) && $request['clickMinSort'] !== '') {
            $filter['clickMinSort']      = self::getRequest($request, 'clickMinSort', 'int');
            $query['real_click']['$gte'] = $filter['clickMinSort'];
        }
        if (isset($request['clickMaxSort']) && $request['clickMaxSort'] !== '') {
            $filter['clickMaxSort']      = self::getRequest($request, 'clickMaxSort', 'int');
            $query['real_click']['$lte'] = $filter['clickMaxSort'];
        }

        if (isset($request['favMinSort']) && $request['favMinSort'] !== '') {
            $filter['favMinSort']           = self::getRequest($request, 'favMinSort', 'int');
            $query['favorite_rate']['$gte'] = $filter['favMinSort'];
        }
        if (isset($request['favMaxSort']) && $request['favMaxSort'] !== '') {
            $filter['favMaxSort']           = self::getRequest($request, 'favMaxSort', 'int');
            $query['favorite_rate']['$lte'] = $filter['favMaxSort'];
        }

        if (isset($request['minSort']) && $request['minSort'] !== '') {
            $filter['minSort']     = self::getRequest($request, 'minSort', 'int');
            $query['sort']['$gte'] = $filter['minSort'];
        }
        if (isset($request['maxSort']) && $request['maxSort'] !== '') {
            $filter['maxSort']     = self::getRequest($request, 'maxSort', 'int');
            $query['sort']['$lte'] = $filter['maxSort'];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];
        $count  = ComicsModel::count($query);
        $items  = ComicsModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['tags'] = value(function () use ($item) {
                $tags = ComicsTagService::getByIds($item['tags']);
                return $tags ? join(',', array_column($tags, 'name')) : '-';
            });
            $item['created_at']    = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']    = date('Y-m-d H:i', $item['updated_at']);
            $item['show_at']       = date('Y-m-d H:i', $item['show_at']);
            $item['last_update']   = date('Y-m-d', $item['last_update']);
            $item['status']        = CommonValues::getComicsStatus($item['status'] * 1);
            $item['update_status'] = CommonValues::getComicsUpdateStatus($item['update_status'] * 1);
            $item['update_date']   = $item['update_date'] ?: '-';
            $items[$index]         = $item;
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
     * @param                    $id
     * @return mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = ComicsModel::findByID($id);
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $row['show_at']     = $row['show_at'] ? date('Y-m-d H:i:s', $row['show_at']) : '';
        $row['last_update'] = $row['last_update'] ? date('Y-m-d H:i:s', $row['last_update']) : '';
        $row['sort']        = $row['sort'] ? date('Y-m-d H:i:s', $row['sort']) : '';
        return $row;
    }

    /**
     * 章节详情
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getChapterDetail($id)
    {
        $row = ComicsChapterModel::findByID($id);
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        return $row;
    }

    /**
     * 保存数据
     * @param                 $data
     * @return bool|int|mixed
     */
    public static function save($data)
    {
        $row = [
            'name'         => self::getRequest($data, 'name'),
            'alias_name'   => self::getRequest($data, 'alias_name', 'string'),
            'cat_id'       => self::getRequest($data, 'cat_id', 'string'),
            'img_x'        => self::getRequest($data, 'img_x', 'string'),
            'img_y'        => self::getRequest($data, 'img_y', 'string'),
            'click'        => self::getRequest($data, 'click', 'int', 0),
            'love'         => self::getRequest($data, 'love', 'int', 0),
            'favorite'     => self::getRequest($data, 'favorite', 'int', 0),
            'money'        => self::getRequest($data, 'money', 'int', 0),
            'score'        => self::getRequest($data, 'score', 'int', 80),
            'free_chapter' => self::getRequest($data, 'free_chapter', 'string'),
            'description'  => self::getRequest($data, 'description', 'string', ''),
            'icon'         => self::getRequest($data, 'icon', 'string'),

            'sort'        => self::getRequest($data, 'sort', 'int', 0),
            'status'      => self::getRequest($data, 'status', 'int', 0),
            'update_date' => self::getRequest($data, 'update_date', 'string'),
            'last_update' => self::getRequest($data, 'last_update', 'string'),
            'show_at'     => self::getRequest($data, 'show_at', 'string'),
        ];
        $row['pay_type']    = CommonValues::getPayTypeByMoney($row['money']);
        $row['name']        = stripslashes($row['name']);
        $row['sort']        = $row['sort'] ? strtotime($row['sort']) : 0;
        $row['description'] = stripslashes($row['description']);

        if (empty($row['name']) || empty($row['img_x'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if ($data['_id']) {
            $row['_id'] = self::getRequest($data, '_id', 'string');
        }
        if ($row['show_at']) {
            $row['show_at'] = strtotime($row['show_at']);
        }
        if ($row['last_update']) {
            $row['last_update'] = strtotime($row['last_update']);
        }

        $tags = [];
        foreach ($data['tags'] as $tag) {
            $tags[] = intval($tag);
        }
        if (empty($tags)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请设置标签!');
        }
        $row['tags'] = $tags;

        $freeIds = [];
        foreach ($data['free_ids'] as $freeId) {
            $freeIds[] = $freeId;
        }
        $row['free_chapter'] = join(',', $freeIds);

        $result = ComicsModel::save($row);
        if ($row['_id']) {
            ComicsService::asyncEs($row['_id']);
        }
        return $result;
    }

    /**
     * 批量修改
     * @param                             $data
     * @return true
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function update($data)
    {
        $ids      = $data['ids'];
        $tags     = $data['tags'];
        $catId    = self::getRequest($data, 'cat_id', 'string', '');
        $click    = self::getRequest($data, 'click', 'string', '');
        $love     = self::getRequest($data, 'love', 'string', '');
        $favorite = self::getRequest($data, 'favorite', 'string', '');
        $sort     = self::getRequest($data, 'sort', 'string', '');
        $money    = self::getRequest($data, 'money', 'string', '');
        $showAt   = self::getRequest($data, 'show_at', 'string', '');

        $status = self::getRequest($data, 'status', 'string', '');
        $icon   = self::getRequest($data, 'icon', 'string', null);

        $ids = explode(',', $ids);

        if ($catId) {
            $update['cat_id'] = strval($catId);
        }
        if ($tags) {
            $update['tags'] = [];
            foreach ($tags as $tag) {
                if (empty($tag)) {
                    continue;
                }
                $update['tags'][] = intval($tag);
            }
        }
        if ($click) {
            $update['click'] = intval($click);
        }
        if ($love) {
            $update['love'] = intval($love);
        }
        if ($favorite) {
            $update['favorite'] = intval($favorite);
        }
        if ($sort) {
            $update['sort'] = intval($sort);
        }
        if ($money) {
            $update['money']    = intval($money);
            $update['pay_type'] = CommonValues::getPayTypeByMoney($money);
        }
        if ($showAt) {
            $update['show_at'] = strtotime($showAt);
        }
        if ($status) {
            $update['status'] = intval($status);
        }
        if ($icon) {
            $update['icon'] = strval($icon);
        }

        if (empty($update)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '请输入您要修改的内容!');
        }
        foreach ($ids as $id) {
            if (!empty($update)) {
                ComicsModel::updateById($update, $id);
                ComicsService::asyncEs($id);
                ComicsService::delCache($id);
            }
        }
        return true;
    }

    /**
     * 批量修改（叠加）
     * @param             $data
     * @return true
     * @throws \Exception
     */
    public static function updateOverlay($data)
    {
        $ids  = $data['ids'];
        $tags = $data['tags'];

        $ids = explode(',', $ids);

        $update['tags'] = [];
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $update['tags'][] = intval($tag);
            }
        }

        if (empty($update)) {
            throw new \Exception('请输入您要修改的内容!');
        }
        foreach ($ids as $id) {
            $comicsInfo = ComicsModel::findByID($id);
            $updateTags = array_merge($comicsInfo['tags'] ?: [], $update['tags']);
            $updateTags = array_values(array_unique($updateTags));

            ComicsModel::updateRaw(['$set' => ['tags' => $updateTags]], ['_id' => $id]);
            ComicsService::asyncEs($id);
            ComicsService::delCache($id);
        }
        return true;
    }

    /**
     * 批量修改（移除）
     * @param                             $data
     * @return true
     * @throws \Phalcon\Storage\Exception
     */
    public static function updateRemove($data)
    {
        $ids  = $data['ids'];
        $tags = $data['tags'];

        $ids = explode(',', $ids);

        $update['tags'] = [];
        foreach ($tags as $tag) {
            if (!empty($tag)) {
                $update['tags'][] = intval($tag);
            }
        }

        if (empty($update)) {
            throw new \Exception('请输入您要修改的内容!');
        }
        foreach ($ids as $id) {
            $comicsInfo = ComicsModel::findByID($id);
            foreach ($comicsInfo['tags'] as $i => $tId) {
                if (in_array($tId, $update['tags'])) {
                    unset($comicsInfo['tags'][$i]);
                }
            }
            $updateTags = array_values($comicsInfo['tags']);

            ComicsModel::updateRaw(['$set' => ['tags' => $updateTags]], ['_id' => $id]);
            ComicsService::asyncEs($id);
            ComicsService::delCache($id);
        }
        return true;
    }

    /**
     * @param       $source
     * @param       $idStr
     * @return true
     */
    public static function asyncMrs($source, $idStr)
    {
        $idsArr = explode(',', $idStr);
        return ComicsService::asyncMrsByIds($idsArr, $source);
    }
}
