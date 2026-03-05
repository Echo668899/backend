<?php

namespace App\Repositories\Backend\Movie;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Movie\MovieModel;
use App\Models\User\UserModel;
use App\Services\Movie\MovieCategoryService;
use App\Services\Movie\MovieService;
use App\Services\Movie\MovieTagService;
use App\Utils\CommonUtil;

class MovieRepository extends BaseRepository
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
        $sort     = self::getRequest($request, 'sort', 'string', 'created_at');
        $order    = self::getRequest($request, 'order', 'int', -1);
        $query    = [];
        $filter   = [];

        if ($request['_id']) {
            $filter['_id'] = self::getRequest($request, '_id');
            $query['_id']  = $filter['_id'];
        }
        if ($request['user_id']) {
            if ($request['user_id'] == '-') {
                $filter['user_id'] = [];
            } else {
                $filter['user_id'] = self::getRequest($request, 'user_id', 'int');
            }
            $query['user_id'] = $filter['user_id'];
        }
        if ($request['mid']) {
            $filter['mid'] = self::getRequest($request, 'mid', 'string');
            $query['mid']  = $filter['mid'];
        }
        if ($request['name']) {
            $filter['name'] = self::getRequest($request, 'name');
            $query['name']  = ['$regex' => $filter['name'], '$options' => 'i'];
        }
        if ($request['number']) {
            $filter['number'] = self::getRequest($request, 'number');
            $query['number']  = $filter['number'];
        }
        if ($request['publisher']) {
            $filter['publisher'] = self::getRequest($request, 'publisher');
            $query['publisher']  = $filter['publisher'];
        }
        if ($request['categories']) {
            if ($request['categories'] == '-') {
                $filter['categories'] = '';
            } else {
                $filter['categories'] = self::getRequest($request, 'categories', 'int');
            }
            $query['categories'] = $filter['categories'];
        }
        if ($request['position']) {
            if ($request['position'] == '-') {
                $filter['position'] = '';
            } else {
                $filter['position'] = self::getRequest($request, 'position', 'string');
            }
            $query['position'] = $filter['position'];
        }
        if ($request['tags']) {
            $filter['tags'] = self::getRequest($request, 'tags', 'int');
            $query['tags']  = ['$in' => [$filter['tags']]];
        }

        if (isset($request['status']) && $request['status'] !== '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }
        if (isset($request['canvas']) && $request['canvas'] !== '') {
            $filter['canvas'] = self::getRequest($request, 'canvas');
            $query['canvas']  = $filter['canvas'];
        }
        if (isset($request['icon']) && $request['icon'] !== '') {
            $filter['icon'] = self::getRequest($request, 'icon');
            $query['icon']  = $filter['icon'];
        }
        if (isset($request['img_type']) && $request['img_type'] !== '') {
            $filter['img_type'] = self::getRequest($request, 'img_type');
            $query['img_type']  = $filter['img_type'];
        }
        if (isset($request['x_filter']) && $request['x_filter'] !== '') {
            $filter['x_filter'] = self::getRequest($request, 'x_filter');
            $query['x_filter']  = $filter['x_filter'];
        }
        if (isset($request['pay_type']) && $request['pay_type'] !== '') {
            $filter['pay_type'] = self::getRequest($request, 'pay_type');
            $query['pay_type']  = $filter['pay_type'];
        }
        if (isset($request['is_more_link']) && $request['is_more_link'] !== '') {
            if ($request['is_more_link'] == 1) {
                $query['$expr']['$gte'] = [['$size' => '$links'], 2];
            } else {
                $query['$expr']['$lte'] = [['$size' => '$links'], 1];
            }
            $filter['is_more_link'] = $request['is_more_link'];
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

        $count = MovieModel::count($query);
        $items = MovieModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $item['categories'] = value(function () use ($item) {
                $category = MovieCategoryService::getById($item['categories']);
                return $category ? $category['name'] : '-';
            });
            $item['tags'] = value(function () use ($item) {
                $tags = MovieTagService::getByIds($item['tags']);
                return $tags ? join(',', array_column($tags, 'name')) : '-';
            });
            $item['user_id']       = $item['user_id'] ? join(',', $item['user_id']) : '-';
            $item['status']        = CommonValues::getMovieStatus($item['status']);
            $item['canvas']        = CommonValues::getMovieCanvas($item['canvas']);
            $item['img_type']      = CommonValues::getMovieCanvas($item['img_type']);
            $item['duration']      = CommonUtil::formatSecond($item['links'][0]['duration'] ?? '0');
            $item['favorite_rate'] = $item['favorite_rate'] . '%';
            $item['number']        = $item['number'] ?: '-';
            $item['icon']          = $item['icon'] ?: '-';
            $item['show_at']       = $item['show_at'] ? date('y-m-d H:i', $item['show_at']) : '-';
            $item['last_at']       = $item['last_at'] ? date('y-m-d H:i', $item['last_at']) : '-';
            $item['created_at']    = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at']    = date('Y-m-d H:i', $item['updated_at']);
            $item['position']      = value(function () use ($item) {
                if (!empty($item['position'])) {
                    $result = CommonValues::getMoviePosition($item['position']);
                    if (empty($result)) {
                        return $item['position'];
                    }
                    return $result;
                }
                return '-';
            });
            $item['links'] = [];
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

    public static function save($data)
    {
        $row = [
            '_id'     => self::getRequest($data, '_id', 'string'),
            'name'    => self::getRequest($data, 'name', 'string'),
            'user_id' => value(function () use ($data) {
                $ids    = explode(',', $_REQUEST['user_id']);
                $result = [];
                foreach ($ids as $id) {
                    if (empty($id)) {
                        continue;
                    }
                    $result[] = intval($id);
                }
                return $result;
            }),
            'number' => self::getRequest($data, 'number', 'string'),
            'sort'   => self::getRequest($data, 'sort', 'int'),

            'img_x'    => self::getRequest($data, 'img_x', 'string'),
            'img_y'    => self::getRequest($data, 'img_y', 'string'),
            'favorite' => self::getRequest($data, 'favorite', 'int'),
            'click'    => self::getRequest($data, 'click', 'int'),
            'love'     => self::getRequest($data, 'love', 'int'),
            'money'    => self::getRequest($data, 'money', 'int'),
            'status'   => self::getRequest($data, 'status', 'int'),

            'position' => self::getRequest($data, 'position', 'string'),

            //            'm3u8_url'     => self::getRequest($data, 'm3u8_url','string'),
            //            'preview_m3u8_url'      => self::getRequest($data, 'preview_m3u8_url','string'),
            'duration'    => self::getRequest($data, 'duration', 'int'),
            'description' => self::getRequest($data, 'description', 'string'),
            'icon'        => self::getRequest($data, 'icon', 'string'),
            'show_at'     => self::getRequest($data, 'show_at', 'string'),
            'publisher'   => self::getRequest($data, 'publisher', 'string'),
            'issue_date'  => self::getRequest($data, 'issue_date', 'string'),
            'x_filter'    => self::getRequest($data, 'x_filter', 'string'),
            'score'       => self::getRequest($data, 'score', 'int'),
            'categories'  => value(function () use ($data) {
                if (!empty($data['categories'])) {
                    return self::getRequest($data, 'categories', 'int');
                }
                return null;
            }),
            'tags' => value(function () use ($data) {
                $tagIds = $_REQUEST['tags'];
                $result = [];
                foreach ($tagIds as $tagId) {
                    if (empty($tagId)) {
                        continue;
                    }
                    $result[] = intval($tagId);
                }
                return $result;
            }),
        ];
        $row['show_at']  = $row['show_at'] ? strtotime($row['show_at']) : 0;
        $row['pay_type'] = CommonValues::getPayTypeByMoney($row['money']);
        if (empty($row['_id'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请选择视频');
        }
        if (empty($row['name']) || empty($row['img_x']) || empty($row['position'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '名称、封面图、分区不能为空!');
        }
        if (empty($row['user_id'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '用户编号为空!');
        }
        foreach ($row['user_id'] as $userId) {
            $userRow = UserModel::findByID($userId);
            if (empty($userRow)) {
                throw  new BusinessException(StatusCode::DATA_ERROR, "用户{$userId}不存在!");
            }
        }

        MovieModel::updateById($row, $row['_id']);
        MovieService::asyncEs($row['_id']);
        MovieService::delCache($row['_id']);
        return true;
    }

    /**
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = MovieModel::findByID($id);
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $row['show_at'] = $row['show_at'] ? date('Y-m-d H:i:s', $row['show_at']) : '';
        return $row;
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
        $userIds  = $data['user_id'];
        $click    = self::getRequest($data, 'click', 'string', '');
        $love     = self::getRequest($data, 'love', 'string', '');
        $favorite = self::getRequest($data, 'favorite', 'string', '');
        $sort     = self::getRequest($data, 'sort', 'string', '');
        $money    = self::getRequest($data, 'money', 'string', '');
        $showAt   = self::getRequest($data, 'show_at', 'string', '');

        $status   = self::getRequest($data, 'status', 'string', '');
        $position = self::getRequest($data, 'position', 'string', null);
        $icon     = self::getRequest($data, 'icon', 'string', null);
        $xFilter  = self::getRequest($data, 'x_filter', 'string', null);

        $ids = explode(',', $ids);

        if ($catId) {
            $update['categories'] = intval($catId);
        }
        if ($userIds) {
            $userIds           = explode(',', $userIds);
            $update['user_id'] = [];
            foreach ($userIds as $userId) {
                if (empty($userId)) {
                    continue;
                }
                $update['user_id'][] = intval($userId);
            }
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
        if ($position) {
            $update['position'] = strval($position);
        }
        if ($icon) {
            $update['icon'] = strval($icon);
        }
        if ($xFilter) {
            $update['x_filter'] = strval($xFilter);
        }

        if (empty($update)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '请输入您要修改的内容!');
        }
        foreach ($ids as $id) {
            if (!empty($update)) {
                MovieModel::updateById($update, $id);
                MovieService::asyncEs($id);
                MovieService::delCache($id);
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
        $ids     = $data['ids'];
        $tags    = $data['tags'];
        $userIds = $data['user_id'];

        $ids     = explode(',', $ids);
        $userIds = explode(',', $userIds);

        foreach ($tags as $key => $tag) {
            if (empty($tag)) {
                unset($tags[$key]);
                continue;
            }
            $tags[$key] = intval($tag);
        }
        foreach ($userIds as $key => $userId) {
            if (empty($userId)) {
                unset($userIds[$key]);
                continue;
            }
            $userIds[$key] = intval($userId);
        }

        if (empty($tags) && empty($userIds)) {
            throw new \Exception('请输入您要修改的内容!');
        }
        foreach ($ids as $id) {
            $movieInfo = MovieModel::findByID($id);
            $update    = [];
            if (!empty($tags)) {
                $updateTags     = array_merge($movieInfo['tags'] ?: [], $tags);
                $updateTags     = array_values(array_unique($updateTags));
                $update['tags'] = $updateTags;
            }

            if (!empty($userIds)) {
                $updateUserId      = array_merge($movieInfo['user_id'] ?: [], $userIds);
                $updateUserId      = array_values(array_unique($updateUserId));
                $update['user_id'] = $updateUserId;
            }

            if (!empty($update)) {
                MovieModel::updateRaw(['$set' => $update], ['_id' => $id]);
            }

            MovieService::asyncEs($id);
            MovieService::delCache($id);
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
        $ids     = $data['ids'];
        $tags    = $data['tags'];
        $userIds = $data['user_id'];

        $ids     = explode(',', $ids);
        $userIds = explode(',', $userIds);

        foreach ($tags as $key => $tag) {
            if (empty($tag)) {
                unset($tags[$key]);
                continue;
            }
            $tags[$key] = intval($tag);
        }
        foreach ($userIds as $key => $userId) {
            if (empty($userId)) {
                unset($userIds[$key]);
                continue;
            }
            $userIds[$key] = intval($userId);
        }

        if (empty($tags) && empty($userIds)) {
            throw new \Exception('请输入您要修改的内容!');
        }

        foreach ($ids as $id) {
            $movieInfo = MovieModel::findByID($id);
            foreach ($movieInfo['tags'] as $i => $tId) {
                if (in_array($tId, $tags)) {
                    unset($movieInfo['tags'][$i]);
                }
            }
            foreach ($movieInfo['user_id'] as $i => $uId) {
                if (in_array($uId, $userIds)) {
                    unset($movieInfo['user_id'][$i]);
                }
            }

            $updateTags   = array_values($movieInfo['tags']);
            $updateUserId = array_values($movieInfo['user_id']);

            $update = [];
            if ($updateTags) {
                $update['tags'] = $updateTags;
            }
            if ($updateUserId) {
                $update['user_id'] = $updateUserId;
            }

            if (!empty($update)) {
                MovieModel::updateRaw(['$set' => $update], ['_id' => $id]);
            }

            MovieService::asyncEs($id);
            MovieService::delCache($id);
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
        return MovieService::asyncMrsByIds($idsArr, $source);
    }
}
