<?php

namespace App\Repositories\Backend\Post;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Post\PostModel;
use App\Models\User\UserModel;
use App\Services\Post\PostService;
use App\Services\Post\PostTagService;
use App\Services\User\UserService;
use App\Services\User\UserUpService;

class PostRepository extends BaseRepository
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
        if ($request['title'] != '') {
            $filter['title'] = self::getRequest($request, 'title');
            $query['title']  = ['$regex' => $filter['title'], '$options' => 'i'];
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

        if ($request['global_top'] !== '') {
            $filter['global_top'] = self::getRequest($request, 'global_top', 'int');
            $query['global_top']  = $filter['global_top'];
        }

        if ($request['permission'] !== '') {
            $filter['permission'] = self::getRequest($request, 'permission', 'string');
            $query['permission']  = $filter['permission'];
        }

        if ($request['status'] !== '') {
            $filter['status'] = self::getRequest($request, 'status', 'int');
            $query['status']  = $filter['status'];
        }

        if ($request['x_filter'] !== '') {
            $filter['x_filter'] = self::getRequest($request, 'x_filter');
            $query['x_filter']  = $filter['x_filter'];
        }
        if ($request['pay_type'] !== '') {
            $filter['pay_type'] = self::getRequest($request, 'pay_type');
            $query['pay_type']  = $filter['pay_type'];
        }
        if ($request['clickMinSort'] !== '') {
            $filter['clickMinSort']      = self::getRequest($request, 'clickMinSort', 'int');
            $query['real_click']['$gte'] = $filter['clickMinSort'];
        }
        if ($request['clickMaxSort'] !== '') {
            $filter['clickMaxSort']      = self::getRequest($request, 'clickMaxSort', 'int');
            $query['real_click']['$lte'] = $filter['clickMaxSort'];
        }

        if ($request['favMinSort'] !== '') {
            $filter['favMinSort']           = self::getRequest($request, 'favMinSort', 'int');
            $query['favorite_rate']['$gte'] = $filter['favMinSort'];
        }
        if ($request['favMaxSort'] !== '') {
            $filter['favMaxSort']           = self::getRequest($request, 'favMaxSort', 'int');
            $query['favorite_rate']['$lte'] = $filter['favMaxSort'];
        }

        if ($request['minSort'] !== '') {
            $filter['minSort']     = self::getRequest($request, 'minSort', 'int');
            $query['sort']['$gte'] = $filter['minSort'];
        }
        if ($request['maxSort'] !== '') {
            $filter['maxSort']     = self::getRequest($request, 'maxSort', 'int');
            $query['sort']['$lte'] = $filter['maxSort'];
        }
        if ($request['start_time'] !== '') {
            $filter['start_time'] = self::getRequest($request, 'start_time', 'string', '');
            $query['created_at']  = ['$gte' => intval(strtotime($filter['start_time']))];
        }
        if ($request['end_time'] !== '') {
            $filter['end_time']  = self::getRequest($request, 'end_time', 'string', '');
            $query['created_at'] = ['$lte' => intval(strtotime($filter['end_time']))];
        }

        $skip   = ($page - 1) * $pageSize;
        $fields = [];

        $count = PostModel::count($query);
        $items = PostModel::find($query, $fields, [$sort => $order], $skip, $pageSize);
        foreach ($items as $index => $item) {
            $user = UserModel::findByID(intval($item['user_id']));

            $item['user'] = [
                '_id'         => $user['_id'],
                'username'    => $user['username'],
                'nickname'    => $user['nickname'],
                'headico'     => $user['headico'],
                'is_vip'      => UserService::isVip($item),
                'lang'        => $user['lang'],
                'sex'         => $user['sex'],
                'is_up'       => UserUpService::has($user['_id']),
                'is_disabled' => $user['is_disabled'],
            ];
            $item['user']['group_name'] = $item['is_vip'] ? $item['group_name'] : '否';
            $item['content']            = mb_substr($item['content'], 0, 30, 'utf-8');
            $item['videos']             = count($item['videos']);

            $item['global_top'] = CommonValues::getIs($item['global_top']);
            $item['home_top']   = CommonValues::getIs($item['home_top']);
            $item['pay_type']   = CommonValues::getPayTypes($item['pay_type']);
            $item['status']     = CommonValues::getPostStatus($item['status']);
            $item['permission'] = CommonValues::getPostPermission($item['permission']);
            $item['show_at']    = date('Y-m-d H:i', $item['show_at']);
            $item['created_at'] = date('Y-m-d H:i', $item['created_at']);
            $item['updated_at'] = date('Y-m-d H:i', $item['updated_at']);

            $item['tags'] = value(function () use ($item) {
                $tags = PostTagService::getByIds($item['tags']);
                return $tags ? join(',', array_column($tags, 'name')) : '-';
            });
            $item['position'] = value(function () use ($item) {
                if (!empty($item['position'])) {
                    $result = CommonValues::getPostPosition($item['position']);
                    if (empty($result)) {
                        return $item['position'];
                    }
                    return $result;
                }
                return '-';
            });
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
            'user_id' => self::getRequest($data, 'user_id', 'int'),
            'title'   => self::getRequest($data, 'title', 'string'),
            'content' => self::getRequest($data, 'content', 'string'),
            'images'  => value(function () use ($data) {
                $images = $_REQUEST['images'];
                $result = [];
                foreach ($images as $image) {
                    if (empty($image)) {
                        continue;
                    }
                    $result[] = [
                        'url' => strval($image)
                    ];
                }
                return $result;
            }),
            'videos' => value(function () use ($data) {
                $videos = $_REQUEST['videos'];
                $result = [];
                foreach ($videos as $video) {
                    if (empty($video)) {
                        continue;
                    }
                    $result[] = [
                        'img' => strval($video['img']),
                        'url' => strval($video['url'])
                    ];
                }
                return $result;
            }),

            'favorite' => self::getRequest($data, 'favorite', 'int'),
            'click'    => self::getRequest($data, 'click', 'int'),
            'love'     => self::getRequest($data, 'love', 'int'),
            'money'    => self::getRequest($data, 'money', 'int'),
            'sort'     => self::getRequest($data, 'sort', 'int', 0),

            'permission' => self::getRequest($data, 'permission', 'string'),
            'position'   => self::getRequest($data, 'position', 'string'),
            'status'     => self::getRequest($data, 'status', 'int'),
            'tags'       => value(function () use ($data) {
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
            'deny_msg' => self::getRequest($data, 'deny_msg', 'string'),
        ];
        $row['pay_type'] = CommonValues::getPayTypeByMoney($row['money']);

        if (empty($row['title']) || empty($row['position'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '名称、分区不能为空!');
        }
        if (empty($row['user_id'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '用户编号为空!');
        }
        $userRow = UserModel::findByID($row['user_id']);
        if (empty($userRow)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, "用户{$row['user_id']}不存在!");
        }
        if (empty($row['_id'])) {
            $row['real_click']    = 0;
            $row['real_love']     = 0;
            $row['real_favorite'] = 0;
            $row['comment']       = 0;
            $row['status']        = 0;
            $row['source']        = 'self';
            PostModel::insert($row, false);
        } else {
            PostModel::updateById($row, $row['_id']);
            PostService::asyncEs($row['_id']);
            PostService::delCache($row['_id']);
        }
        return true;
    }

    /**
     * @param                    $id
     * @return array|mixed
     * @throws BusinessException
     */
    public static function getDetail($id)
    {
        $row = PostModel::findByID($id);
        if (empty($row)) {
            throw  new BusinessException(StatusCode::DATA_ERROR, '数据不存在!');
        }
        $row['created_at'] = date('Y-m-d H:i:s', $row['created_at']);
        $row['updated_at'] = date('Y-m-d H:i:s', $row['updated_at']);
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
        $isHot    = self::getRequest($data, 'is_hot', 'string', null);
        $isNew    = self::getRequest($data, 'is_new', 'string', null);
        $position = self::getRequest($data, 'position', 'string', null);
        $icon     = self::getRequest($data, 'icon', 'string', null);
        $xFilter  = self::getRequest($data, 'x_filter', 'string', null);

        $ids     = explode(',', $ids);
        $userIds = explode(',', $userIds);

        if ($catId) {
            $update['categories'] = intval($catId);
        }
        if ($userIds) {
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
        if ($isHot) {
            $update['is_hot'] = intval($isHot);
        }
        if ($isNew) {
            $update['is_new'] = intval($isNew);
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
                PostModel::updateById($update, $id);
                PostService::asyncEs($id);
                PostService::delCache($id);
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
            }
            $tags[$key] = intval($tag);
        }
        foreach ($userIds as $key => $userId) {
            if (empty($userId)) {
                unset($userIds[$key]);
            }
            $userIds[$key] = intval($userId);
        }

        if (empty($tags) && empty($userIds)) {
            throw new \Exception('请输入您要修改的内容!');
        }
        foreach ($ids as $id) {
            $movieInfo = PostModel::findByID($id);
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
                PostModel::updateRaw(['$set' => $update], ['_id' => $id]);
            }

            PostService::asyncEs($id);
            PostService::delCache($id);
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
            }
            $tags[$key] = intval($tag);
        }
        foreach ($userIds as $key => $userId) {
            if (empty($userId)) {
                unset($userIds[$key]);
            }
            $userIds[$key] = intval($userId);
        }

        if (empty($tags) && empty($userIds)) {
            throw new \Exception('请输入您要修改的内容!');
        }

        foreach ($ids as $id) {
            $movieInfo = PostModel::findByID($id);
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
                PostModel::updateRaw(['$set' => $update], ['_id' => $id]);
            }

            PostService::asyncEs($id);
            PostService::delCache($id);
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
        return PostService::asyncMrsByIds($idsArr, $source);
    }
}
