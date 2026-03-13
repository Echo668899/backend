<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Post\PostBlockModel;
use App\Models\Post\PostModel;
use App\Models\Post\PostNavModel;
use App\Models\Post\PostTagModel;
use App\Services\Common\AdvService;
use App\Services\Common\ApiService;
use App\Services\Common\CommonService;
use App\Services\Common\IpService;
use App\Services\Common\M3u8Service;
use App\Services\Post\PostBlockService;
use App\Services\Post\PostFavoriteService;
use App\Services\Post\PostHistoryService;
use App\Services\Post\PostLoveService;
use App\Services\Post\PostNavService;
use App\Services\Post\PostService;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserFansService;
use App\Services\User\UserGroupService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

/**
 * 帖子
 */
class PostRepository extends BaseRepository
{

    /**
     * 获取顶部菜单列表
     * @return array
     */
    public static function navList()
    {
        $row = PostNavService::getAll();
        if (empty($row)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '未找到相关列表!');
        }

        $filter = ['love' => '热度最高', 'new' => '最新上架', 'click' => '观看最多', 'favorite' => '收藏最多', 'buy' => '购买最多'];
        $res = ['nav'=>$row, 'filter'=>$filter];
        return $res;
    }
    
    /**
     * nav下模块
     * @param                             $navId
     * @param  mixed                      $page
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function navBlock($navId, $page = 1)
    {
        $navId  = intval($navId);
        $navRow = PostNavModel::findByID($navId);
        if (empty($navRow)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '板块不存在');
        }
        $result = [];
        if ($page == 1) {
            $result[] = [
                'id'         => '-1',
                'name'       => '广告',
                'style'      => '-1',
                'filter'     => [],
                'route'      => '',
                'route_name' => '',
                'items'      => AdvService::getAll('app_banner')
            ];
        }
        $blocks = PostBlockService::get(intval($navId), $page, 6);
        $ad     = AdvService::getAll('app_block_list');
        foreach ($blocks as $block) {
            $result[] = [
                'id'     => strval($block['id']),
                'name'   => strval($block['name']),
                'style'  => strval($block['style']),
                'filter' => value(function () use ($block) {
                    if ($block['style'] >= 40 && $block['style'] <= 49) {
                        return [];
                    }
                    return $block['filter'];
                }),
                'route'      => strval($block['route']),
                'route_name' => strval($block['route_name'] ?: '更多'),
                'items'      => self::getBlockItems($block)
            ];
            if (count($ad)) {
                // 一个模块一个广告
                $result[] = [
                    'id'         => '-1',
                    'name'       => 'ad',
                    'style'      => '-1',
                    'filter'     => [],
                    'route'      => '',
                    'route_name' => '',
                    'items'      => [array_shift($ad)]
                ];
            }
        }
        return $result;
    }

    /**
     * nav下模块
     * @param                             $navId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function navFilter($navId)
    {
        $navId  = intval($navId);
        $navRow = PostNavModel::findByID($navId);
        if (empty($navRow)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '板块不存在');
        }
        $result = [
            'banner' => [
                'style' => '-2',
                'items' => AdvService::getAll('app_banner')
            ],
            'style' => value(function () use ($navRow) {
                // 具体显示样式,根据业务自行选择
                if ($navRow['style'] == 'post_2') {
                    return strval('12');
                }
                if ($navRow['style'] == 'post_3') {
                    return strval('12');
                }
                return strval('30');
            }),
            'filters' => value(function () use ($navRow) {
                $filters = json_decode($navRow['filter'], true);
                // 普通列表
                if ($navRow['style'] == 'post_2') {
                    return $filters;
                }
                // 带tab的列表
                if ($navRow['style'] == 'post_3') {
                    $result = [];
                    foreach ($filters as $filter) {
                        $result[] = [
                            'name'  => strval($filter['name']),
                            'style' => value(function () use ($filter) {
                                if ($filter['style'] < 10) {
                                    return strval(4);
                                } elseif ($filter['style'] < 20) {
                                    return strval(12);
                                }
                                return strval(30);
                            }),
                            'filter' => value(function () use ($filter) {
                                if (empty($filter['filter']['ad_code'])) {
                                    $filter['filter']['ad_code'] = 'app_data_list';
                                }
                                return $filter['filter'];
                            }),
                        ];
                    }
                    return $result;
                }
                return [];
            })
        ];
        return $result;
    }

    /**
     * 模块详情
     * @param                    $blockId
     * @return array
     * @throws BusinessException
     */
    public static function getBlockDetail($blockId)
    {
        $row = PostBlockModel::findByID(intval($blockId));
        if (empty($row)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '未找到相关模块!');
        }
        $filter = json_decode($row['filter'], true);
        // 特殊样式
        if ($row['style'] >= 40 && $row['style'] <= 49) {
            return [
                'id'      => strval($row['_id']),
                'name'    => strval($row['name']),
                'style'   => strval($row['style']),
                'filters' => value(function () use ($filter) {
                    $rows = [];
                    foreach ($filter as $item) {
                        $item['page']      = 1;
                        $item['page_size'] = 6 * 4;
                        $item['ad_code']   = 'app_data_list';
                        $rows[]            = [
                            'name'   => $item['name'],
                            'filter' => $item['filter'],
                        ];
                    }
                    return $rows;
                })
            ];
        } else {
            $filter['page']      = 1;
            $filter['page_size'] = 6 * 4;
            $filter['ad_code']   = 'app_data_list';
            return [
                'id'    => strval($row['_id']),
                'name'  => strval($row['name']),
                'style' => value(function () use ($row) {
                    if ($row['style'] < 10) {
                        return strval(4);
                    } elseif ($row['style'] < 20) {
                        return strval(12);
                    }
                    return strval(30);
                }),
                'filters' => [
                    ['name' => '近期最佳', 'filter' => value(function () use ($filter) {
                        $filter['order'] = '';
                        return $filter;
                    })],
                    ['name' => '最近更新', 'filter' => value(function () use ($filter) {
                        $filter['order'] = 'new';
                        return $filter;
                    })],
                    ['name' => '最多观看', 'filter' => value(function () use ($filter) {
                        $filter['order'] = 'click';
                        return $filter;
                    })],
                    ['name' => '最多收藏', 'filter' => value(function () use ($filter) {
                        $filter['order'] = 'favorite';
                        return $filter;
                    })],
                ]
            ];
        }
    }

    /**
     * 详情
     * @param                             $userId
     * @param                             $postId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function getDetail($userId, $postId)
    {
        $result = PostService::getInfoCache($postId);

        $userInfo = UserService::getInfoFromCache($userId);
        UserService::checkDisabled($userInfo);

        $isChina = IpService::isChina(CommonUtil::getClientIp());

        $result = [
            'id'      => strval($result['id']),
            'title'   => strval($result['title']),
            'content' => strval($result['content']),
            'actor'   => value(function () use ($result, $userId) {
                $userInfo = UserService::getInfoFromCache($result['user_id']['id']);
                if (empty($userInfo)) {
                    $userInfo['id']         = strval($result['user_id']['id']);
                    $userInfo['nickname']   = '用户已注销';
                    $userInfo['post_total'] = '0';
                }
                return [
                    'id'         => strval($userInfo['id']),
                    'nickname'   => strval($userInfo['nickname']),
                    'username'   => strval($userInfo['username']),
                    'headico'    => CommonService::getCdnUrl($userInfo['headico']),
                    'fans'       => CommonUtil::formatNum($userInfo['fans']),
                    'post_total' => strval($userInfo['creator']['post_total']),
                    'has_follow' => UserFansService::has($userId, $userInfo['id']) ? 'y' : 'n',
                ];
            }),
            'tags' => value(function () use ($result) {
                if (empty($result['tags'])) {
                    return [];
                }
                $tags = [];
                foreach ($result['tags'] as $tag) {
                    $tags[] = [
                        'id'   => strval($tag['id']),
                        'name' => strval($tag['name']),
                    ];
                }
                return $tags;
            }),
            'images' => value(function () use ($result) {
                $rows = [];
                foreach ($result['images'] as $image) {
                    $rows[] = [
                        'url' => CommonService::getCdnUrl($image['url']),
                    ];
                }
                return $rows;
            }),
            'videos' => value(function () use ($result, $isChina) {
                $rows = [];
                foreach ($result['videos'] as $video) {
                    $rows[] = [
                        'img' => CommonService::getCdnUrl($video['img'], 'image'),
                        'url' => M3u8Service::encode($video['url'], $isChina ? 'tencent' : 'aws'),
                    ];
                }
                return $rows;
            }),
            'has_images' => count($result['images']) ? 'y' : 'n',
            'has_videos' => count($result['videos']) ? 'y' : 'n',
            'click'      => value(function () use ($result) {
                $real = CommonService::getRedisCounter("post_click_{$result['id']}");
                return strval((intval($result['click'] + $real)));
            }),
            'love' => value(function () use ($result) {
                $real = CommonService::getRedisCounter('post_love_' . $result['id']);
                return strval($result['love'] + $real);
            }),
            'favorite' => value(function () use ($result) {
                $keyName = 'post_favorite_' . $result['id'];
                $real    = CommonService::getRedisCounter($keyName);
                return CommonUtil::formatNum(intval($result['favorite'] + $real));
            }),
            'comment' => value(function () use ($result) {
                $real = CommonService::getRedisCounter("post_comment_ok_{$result['id']}");
                return strval((intval($real)));
            }),

            'has_love'       => PostLoveService::has($userId, $postId) ? 'y' : 'n',
            'has_favorite'   => PostFavoriteService::has($userId, $postId) ? 'y' : 'n',
            'content_length' => strval(mb_strlen($result['content'])), // 字数

            'pay_type' => strval($result['pay_type']),
            'money'    => value(function () use ($userInfo, $result) {
                if ($result['money'] <= 0) {
                    return '';
                }
                // 现价
                if ($userInfo['group_rate'] != 100) {
                    return strval(round($result['money'] * $userInfo['group_rate'] / 100, 0));
                }
                return strval($result['money']);
            }),
            // 原价
            'old_money' => value(function () use ($userInfo, $result) {
                if ($result['money'] <= 0) {
                    return '';
                }
                if ($userInfo['is_vip'] == 'y') {
                    return '';
                }
                // 这里用最大会员折扣
                $group = UserGroupService::getMaxRateGroup();
                return strval(round($result['money'] * $group['rate'] / 100, 0));
            }),
            'layer_type' => 'limit', // 默认都显示次数
            // 我的信息
            'user' => [
                'id'       => strval($userInfo['id']),
                'username' => strval($userInfo['username']),
                'nickname' => strval($userInfo['nickname']),
                'headico'  => CommonService::getCdnUrl($userInfo['headico']),
                'is_vip'   => strval($userInfo['is_vip']),
                'balance'  => strval($userInfo['balance'])
            ],
        ];

        $hasBuy = value(function () use ($result, $userInfo) {
            if ($userInfo['id'] == $result['user_id']['id']) {
                return 'y';
            }
            if ($userInfo['group_rate'] == '0') {
                return 'y';
            }
            return UserBuyLogService::has($userInfo['id'], $result['id'], 'post') ? 'y' : 'n';
        });
        if ($hasBuy == 'y' || $result['pay_type'] == 'free') {
            $result['layer_type'] = '';
        } else {
            $vipRights = UserService::getRights($userInfo);
            if ($result['pay_type'] == 'money') {
                $result['layer_type'] = 'money';
                $result['videos']     = [];
            } else {
                if (in_array('post', $vipRights)) {
                    $result['layer_type'] = '';
                } else {
                    $result['layer_type'] = 'limit';
                    $result['videos']     = [];
                }
            }
        }
        PostHistoryService::do($userId, $postId);
        return $result;
    }

    /**
     * @param                    $tagId
     * @return array
     * @throws BusinessException
     */
    public static function getTagDetail($tagId)
    {
        $row = PostTagModel::findByID(intval($tagId));
        if (empty($row)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '未找到相关标签!');
        }

        $filter = [
            'tag_id'    => $tagId,
            'page_size' => '20',
        ];

        return [
            'id'      => strval($row['_id']),
            'name'    => strval($row['name']),
            'filters' => [
                ['name' => '近期最佳', 'filter' => value(function () use ($filter) {
                    $filter['order'] = '';
                    return $filter;
                })],
                ['name' => '最近更新', 'filter' => value(function () use ($filter) {
                    $filter['order'] = 'new';
                    return $filter;
                })],
                ['name' => '最多观看', 'filter' => value(function () use ($filter) {
                    $filter['order'] = 'click';
                    return $filter;
                })],
                ['name' => '最多收藏', 'filter' => value(function () use ($filter) {
                    $filter['order'] = 'favorite';
                    return $filter;
                })],
            ]
        ];
    }

    /**
     * 去点赞
     * @param                    $userId
     * @param                    $comicsId
     * @return bool
     * @throws BusinessException
     */
    public static function doLove($userId, $comicsId)
    {
        return PostLoveService::do($userId, $comicsId);
    }

    /**
     * 点赞列表
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getLoveList($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        $ids = PostLoveService::getIds($userId, $page, $pageSize, $cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }
        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }

        return [
            'data'         => $data,
            'total'        => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size'    => strval($ids['page_size']),
            'last_page'    => strval($ids['last_page']),
            'cursor'       => strval($ids['cursor']),
        ];
    }

    /**
     * 去收藏
     * @param                    $userId
     * @param                    $postId
     * @return bool
     * @throws BusinessException
     */
    public static function doFavorite($userId, $postId)
    {
        return PostFavoriteService::do($userId, $postId);
    }

    /**
     * 收藏的帖子列表
     * @param        $userId
     * @param        $page
     * @param  mixed $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getFavoriteList($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        $ids = PostFavoriteService::getIds($userId, $page, $pageSize, $cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }
        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }
        return [
            'data'         => $data,
            'total'        => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size'    => strval($ids['page_size']),
            'last_page'    => strval($ids['last_page']),
            'cursor'       => strval($ids['cursor']),
        ];
    }

    /**
     * 删除历史记录
     * @param             $userId
     * @param             $audioIds
     * @return bool|mixed
     */
    public static function delHistory($userId, $audioIds)
    {
        return PostHistoryService::delete($userId, $audioIds);
    }

    /**
     * 获取历史记录
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param        $cursor
     * @return array
     */
    public static function getHistoryList($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        $ids = PostHistoryService::getIds($userId, $page, $pageSize, $cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }
        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }

        return [
            'data'         => $data,
            'total'        => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size'    => strval($ids['page_size']),
            'last_page'    => strval($ids['last_page']),
            'cursor'       => strval($ids['cursor']),
        ];
    }

    /**
     * 购买帖子
     * @param                    $userId
     * @param                    $postId
     * @return true
     * @throws BusinessException
     */
    public static function doBuy($userId, $postId)
    {
        return PostService::doBuy($userId, $postId);
    }

    /**
     * 购买记录
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param        $cursor
     * @return array
     */
    public static function getBuyLogList($userId, $page = 1, $pageSize = 20, $cursor = null)
    {
        $ids = UserBuyLogService::getIds($userId, 'post', $page, $pageSize, $cursor);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }

        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])])['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }
        return [
            'data'         => $data,
            'total'        => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size'    => strval($ids['page_size']),
            'last_page'    => strval($ids['last_page']),
            'cursor'       => strval($ids['cursor']),
        ];
    }

    /**
     * 搜索
     * @param  array      $filter
     * @param  null|mixed $userId
     * @return array
     */
    public static function doSearch(array $filter = [], $userId = null)
    {
        $query               = [];
        $query['page']       = self::getRequest($filter, 'page', 'int', 1);
        $query['page_size']  = self::getRequest($filter, 'page_size', 'int', 12);
        $query['keywords']   = self::getRequest($filter, 'keywords', 'string', '');
        $query['tag_id']     = self::getRequest($filter, 'tag_id', 'string', '');
        $query['global_top'] = self::getRequest($filter, 'global_top', 'string', '');
        $query['home_top']   = self::getRequest($filter, 'home_top', 'string', '');
        $query['ids']        = self::getRequest($filter, 'ids', 'string', '');
        $query['not_ids']    = self::getRequest($filter, 'not_ids', 'string', '');
        $query['home_id']    = self::getRequest($filter, 'home_id', 'string', '');
        $query['home_ids']   = self::getRequest($filter, 'home_ids', 'string', '');
        $query['position']   = self::getRequest($filter, 'position', 'string', '');
        $query['order']      = self::getRequest($filter, 'order', 'string', '');
        $query['status']     = self::getRequest($filter, 'status', 'string', '');

        $query['type']     = self::getRequest($filter, 'type', 'string', '');
        $query['pay_type'] = self::getRequest($filter, 'pay_type', 'string', '');
        $query['ad_code']  = self::getRequest($filter, 'ad_code', 'string', '');
        $query['language'] = self::getRequest($filter, 'language', 'string', ApiService::getLanguage());
        return PostService::doSearch($query, $userId);
    }

    /**
     * 发布信息
     * @param        $userId
     * @return array
     */
    public static function getCreateInfo($userId)
    {
        $result = [
            'tags' => value(function () {
                $result = [];
                $items  = PostTagModel::find([], [], [], 0, 1000);
                foreach ($items as $item) {
                    if ($item['is_show_upload'] == 0) {
                        continue;
                    }
                    $result[] = [
                        'id'       => strval($item['_id']),
                        'name'     => strval($item['name']),
                        'click'    => strval(CommonUtil::formatNum($item['click'] ?? 0) . '次播放'),
                        'favorite' => strval(CommonUtil::formatNum($item['favorite'] ?? 0) . '次收藏'),
                        'follow'   => strval(CommonUtil::formatNum($item['follow'] ?: 0) . '人参与'),
                        // 关注的用户
                        'follow_user' => value(function () use ($item) {
                            $num    = max(4, $item['follow'] ?? 0);
                            $result = [];
                            for ($n = 1;$n <= $num;$n++) {
                                // 随机生成头像
                                $userRow  = UserService::getDefaultUserRow(null);
                                $result[] = CommonService::getCdnUrl($userRow['headico']);
                            }
                            return $result;
                        }),
                    ];
                }
                return $result;
            }),
            'tags_limit' => '2',
        ];
        return $result;
    }

    /**
     * 发帖
     * @param                    $userId
     * @param                    $request
     * @return bool
     * @throws BusinessException
     */
    public static function doCreate($userId, $request)
    {
        $tagIds = value(function () use ($request) {
            $tagIds = self::getRequest($request, 'tags', 'string');
            $tagIds = explode(',', $tagIds);
            $result = [];
            foreach ($tagIds as $tagId) {
                if (empty($tagId)) {
                    continue;
                }
                $result[] = intval($tagId);
            }
            return $result;
        });
        $images = value(function () use ($request) {
            $images = $request['images'];
            $result = [];
            foreach ($images as $image) {
                $result[] = [
                    'url'    => strval($image['url']),
                    'width'  => intval($image['width']),
                    'height' => intval($image['height']),
                ];
            }
            return $result;
        });
        $videos = value(function () use ($request) {
            $videos = $request['videos'];
            $result = [];
            foreach ($videos as $video) {
                $result[] = [
                    'upload_id' => strval('upload_' . $video['upload_id']),
                    'img'       => '',
                    'url'       => '',
                ];
            }
            return $result;
        });
        $money      = self::getRequest($request, 'money', 'int', 0);
        $title      = self::getRequest($request, 'title', 'string');
        $content    = self::getRequest($request, 'content', 'string');
        $permission = self::getRequest($request, 'permission', 'string', 'public');

        $userInfo = UserService::getInfoFromCache($userId);

        if (!in_array('do_post', UserService::getRights($userInfo))) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '您没用发布文章的权限!');
        }
        // 判断今日发布次数
        $count = PostModel::count(['user_id' => $userId, 'created_at' => ['$gte' => strtotime(date('Y-m-d')), '$lte' => strtotime(date('Y-m-d 23:59:59'))]]);
        if ($count >= $userInfo['creator']['post_upload_num']) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '今日发布次数不足');
        }
        if (count($images) > 9) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '最多只能上传9个图片');
        }
        if (empty($tagIds)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '请至少选择一个文章标签');
        }
        if (count($tagIds) > 2) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '最多只能选择两个标签');
        }
        if (empty($title)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '请输入文章标题');
        }
        if (empty($content)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '请输入文章内容');
        }
        $ip       = CommonUtil::getClientIp();
        $saveData = [
            'user_id'        => intval($userId),
            'source'         => 'upload',
            'title'          => strval($title),
            'content'        => strval($content),
            'tags'           => $tagIds,
            'at_users'       => [],
            'images'         => $images,
            'videos'         => $videos,
            'files'          => [],
            'click'          => 0,
            'real_click'     => 0,
            'love'           => 0,
            'real_love'      => 0,
            'favorite'       => 0,
            'real_favorite'  => 0,
            'favorite_rate'  => 0,
            'hot_rate'       => 0,
            'click_total'    => 0,
            'love_total'     => 0,
            'favorite_total' => 0,
            'comment'        => 0,
            'last_comment'   => 0,
            'permission'     => strval($permission),

            'money'      => 0,
            'pay_type'   => CommonValues::getPayTypeByMoney($money),
            'position'   => 'normal',
            'global_top' => 0,
            'home_top'   => 0,
            'ip'         => strval($ip),
            'pos_info'   => value(function () use ($ip) {
                $result = IpService::parse($ip);
                return $result['country'] . '-' . $result['province'] . '-' . $result['city'];
            }),
            'sort'     => 0,
            'status'   => -1,
            'deny_msg' => ''
        ];

        $result = PostModel::insert($saveData, false);
        return boolval($result);
    }

    /**
     * 站点地图
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function sitemap($page = 1, $pageSize = 5000)
    {
        $where = ['status' => 1];
        $items = PostModel::find($where, ['_id'], ['_id' => -1], ($page - 1) * $pageSize, $pageSize);
        return array_column($items, '_id');
    }

    /**
     * @param  array                      $block
     * @return mixed|null
     * @throws \Phalcon\Storage\Exception
     */
    private static function getBlockItems(array $block)
    {
        $language = ApiService::getLanguage();
        $keyName  = "post_block_{$block['id']}:{$language}";
        $result   = cache()->get($keyName);
        if (is_null($result)) {
            // 特殊样式
            if ($block['style'] >= 40 && $block['style'] <= 49) {
                $result = [];
                foreach ($block['filter'] as $item) {
                    $filter              = $item['filter'];
                    $filter['page_size'] = $filter['page_size'] ?: $block['num'];

                    $result[] = [
                        'name'  => strval($item['name']),
                        'items' => self::doSearch($filter)['data'],
                    ];
                }
            } else {
                $filter              = $block['filter'];
                $filter['page_size'] = $filter['page_size'] ?: $block['num'];
                $result              = self::doSearch($filter)['data'];
            }
            cache()->set($keyName, $result, 300);
        }
        return $result;
    }
}
