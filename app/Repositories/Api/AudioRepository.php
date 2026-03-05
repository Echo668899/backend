<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Audio\AudioBlockModel;
use App\Models\Audio\AudioModel;
use App\Models\Audio\AudioNavModel;
use App\Models\Audio\AudioTagModel;
use App\Services\Audio\AudioBlockService;
use App\Services\Audio\AudioChapterService;
use App\Services\Audio\AudioFavoriteService;
use App\Services\Audio\AudioHistoryService;
use App\Services\Audio\AudioLoveService;
use App\Services\Audio\AudioService;
use App\Services\Audio\AudioTagService;
use App\Services\Common\AdvService;
use App\Services\Common\ApiService;
use App\Services\Common\CommonService;
use App\Services\Common\IpService;
use App\Services\Common\M3u8Service;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserGroupService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

class AudioRepository extends BaseRepository
{
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
        $navRow = AudioNavModel::findByID($navId);
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
        $blocks = AudioBlockService::get(intval($navId), $page, 6);
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
     * nav下模块,列表模块,带filter
     * @param                             $navId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function navFilter($navId)
    {
        $navId  = intval($navId);
        $navRow = AudioNavModel::findByID($navId);
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
                if ($navRow['style'] == 'audio_2') {
                    return strval('12');
                }
                if ($navRow['style'] == 'audio_3') {
                    return strval('12');
                }
                return strval('30');
            }),
            'filters' => value(function () use ($navRow) {
                $filters = json_decode($navRow['filter'], true);
                // 普通列表
                if ($navRow['style'] == 'audio_2') {
                    return $filters;
                }
                // 带tab的列表
                if ($navRow['style'] == 'audio_3') {
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
     * 筛选页面
     * @return array
     */
    public static function filter()
    {
        /**
         * field提交到搜索接口的字段
         * select仅支持 单选:only 多选:multiple
         */
        $result = [
            // 题材-分类
            'category' => [
                'field'  => 'cat_id',
                'select' => 'only',
                'items'  => value(function () {
                    $rows = [
                        [
                            'name'  => '全部',
                            'value' => ''
                        ]
                    ];
                    foreach (CommonValues::getNovelCategories() as $name) {
                        $rows[] = [
                            'name'  => strval($name),
                            'value' => strval($name),
                        ];
                    }
                    return $rows;
                })
            ],
            'tag' => [
                'field'  => 'tag_id',
                'select' => 'multiple',
                'items'  => value(function () {
                    $rows = [];
                    foreach (AudioTagService::getGroupAttrAll() as $groupName => $items) {
                        $rows[] = [
                            'name'  => strval($groupName),
                            'items' => value(function () use ($items) {
                                $rows = [];
                                foreach ($items as $item) {
                                    $rows[] = [
                                        'name'  => strval($item['name']),
                                        'value' => strval($item['id']),
                                    ];
                                }
                                return $rows;
                            }),
                        ];
                    }
                    return $rows;
                })
            ],

            // 付费,连载
            'pay_type' => [
                'field'  => 'pay_type',
                'select' => 'only',
                'items'  => value(function () {
                    $rows = [
                        [
                            'name'  => '全部',
                            'value' => ''
                        ]
                    ];
                    foreach (CommonValues::getPayTypes() as $code => $name) {
                        $rows[] = [
                            'name'  => strval($name),
                            'value' => strval($code),
                        ];
                    }
                    return $rows;
                })
            ],
            'update_type' => [
                'field'  => 'update_status',
                'select' => 'only',
                'items'  => value(function () {
                    $rows = [
                        [
                            'name'  => '全部',
                            'value' => ''
                        ],
                        [
                            'name'  => '连载',
                            'value' => 'n',
                        ],
                        [
                            'name'  => '完结',
                            'value' => 'y',
                        ],
                    ];
                    return $rows;
                })
            ],

            'sort' => [
                'field'  => 'order',
                'select' => 'only',
                'items'  => value(function () {
                    $rows = [
                        [
                            'name'  => '人气推荐',
                            'value' => 'favorite'
                        ],
                        [
                            'name'  => '最近更新',
                            'value' => 'update_date'
                        ],
                        [
                            'name'  => '最新上架',
                            'value' => 'new'
                        ],
                        [
                            'name'  => '最多阅读',
                            'value' => 'click'
                        ],
                        [
                            'name'  => '最多收藏',
                            'value' => 'favorite'
                        ],
                    ];
                    return $rows;
                })
            ],
        ];
        return $result;
    }

    /**
     * 解锁有声
     * @param                    $userId
     * @param                    $audioId
     * @param                    $chapterId
     * @return true
     * @throws BusinessException
     */
    public static function doBuy($userId, $audioId, $chapterId)
    {
        return AudioService::doBuy($userId, $audioId, $chapterId);
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
        $ids = UserBuyLogService::getIds($userId, 'audio', $page, $pageSize, $cursor);
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
        $query                  = [];
        $query['page']          = self::getRequest($filter, 'page', 'int', 1);
        $query['page_size']     = self::getRequest($filter, 'page_size', 'int', 12);
        $query['keywords']      = self::getRequest($filter, 'keywords', 'string', '');
        $query['icon']          = self::getRequest($filter, 'icon', 'string', '');
        $query['pay_type']      = self::getRequest($filter, 'pay_type', 'string', '');
        $query['cat_id']        = self::getRequest($filter, 'cat_id', 'string', '');
        $query['tag_id']        = self::getRequest($filter, 'tag_id', 'string', '');
        $query['is_hot']        = self::getRequest($filter, 'is_hot', 'string');
        $query['is_new']        = self::getRequest($filter, 'is_new', 'string');
        $query['is_end']        = self::getRequest($filter, 'is_end', 'string');
        $query['ids']           = self::getRequest($filter, 'ids', 'string', '');
        $query['not_ids']       = self::getRequest($filter, 'not_ids', 'string', '');
        $query['order']         = self::getRequest($filter, 'order', 'string', '');
        $query['update_date']   = self::getRequest($filter, 'update_date', 'string');
        $query['update_status'] = self::getRequest($filter, 'update_status', 'string');
        $query['ad_code']       = self::getRequest($filter, 'ad_code', 'string', '');
        $query['language']      = self::getRequest($filter, 'language', 'string', ApiService::getLanguage());
        return AudioService::doSearch($query, $userId);
    }

    /**
     * 模块详情
     * @param                    $blockId
     * @return array
     * @throws BusinessException
     */
    public static function getBlockDetail($blockId)
    {
        $row = AudioBlockModel::findByID(intval($blockId));
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
     * @param                             $audioId
     * @param                             $chapterId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function getDetail($userId, $audioId, $chapterId = null)
    {
        $result   = AudioService::getInfoCache($audioId);
        $userInfo = UserService::getInfoFromCache($userId);

        $lastRead = [];
        if (!empty($userId)) {
            // 上次观看
            $lastRead = AudioHistoryService::getLastRead($userId, $audioId);
        }
        $currentLink = [];
        $chapters    = AudioChapterService::getChapterList($audioId);
        foreach ($chapters as $index => $chapter) {
            $chapter = [
                'id'   => strval($chapter['id']),
                'name' => value(function () use ($chapter) {
                    if (is_numeric($chapter['name'])) {
                        return "第{$chapter['name']}章";
                    }
                    return strval($chapter['name']);
                }),
                'current' => value(function () use ($chapter, $lastRead, $chapterId, $index) {
                    // /优先选中的
                    if (!empty($chapterId)) {
                        return $chapter['id'] == $chapterId ? 'y' : 'n';
                    }
                    if (!empty($lastRead) && $lastRead['chapter_id'] == $chapter['id']) {
                        return 'y';
                    }
                    // 如果没有 lastRead，才默认选中第一集
                    if (empty($lastRead) && $index == 0) {
                        return 'y';
                    }
                    return 'n';
                }),
                'content' => $chapter['content'],
            ];
            if ($chapter['current'] == 'y') {
                $currentLink = [
                    'id'      => $chapter['id'],
                    'name'    => $chapter['name'],
                    'content' => $chapter['content'],
                ];
                $prevLink = $chapters[$index - 1] ?? [];
                $nextLink = $chapters[$index + 1] ?? [];
            }
            unset($chapter['content']);
            $chapters[$index] = $chapter;
        }

        $freeChapters = explode(',', $result['free_chapter']);

        $result = [
            'id'       => strval($result['id']),
            'name'     => strval($result['name']),
            'author'   => strval($result['author']),
            'sub_name' => value(function () use ($result) {
                if ($result['update_status'] == 1) {
                    return '共' . $result['chapter_count'] . '章';
                }
                return '更新' . $result['chapter_count'] . '章';
            }),
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

            'img'         => CommonService::getCdnUrl($result['img_x']),
            'description' => strval($result['description']),
            'score'       => strval($result['score']),
            'category'    => $result['cat_id'] ?? '',
            'click'       => value(function () use ($result) {
                $keyName = 'audio_click_' . $result['id'];
                $real    = CommonService::getRedisCounter($keyName);
                return strval(CommonUtil::formatNum(intval($result['click'] + $real)));
            }),
            'love' => value(function () use ($result) {
                $keyName = 'audio_love_' . $result['id'];
                $real    = CommonService::getRedisCounter($keyName);
                return strval(CommonUtil::formatNum(intval($result['love'] + $real)));
            }),
            'favorite' => value(function () use ($result) {
                $keyName = 'audio_favorite_' . $result['id'];
                $real    = CommonService::getRedisCounter($keyName);
                return strval(CommonUtil::formatNum(intval($result['favorite'] + $real)));
            }),
            'comment' => value(function () use ($result) {
                $ok = CommonService::getRedisCounter("audio_comment_ok_{$result['id']}");
                //                    $no = CommonService::getRedisCounter("audio_comment_no_{$result['id']}");
                return strval(CommonUtil::formatNum(intval($ok)));
            }),
            'has_favorite' => (!empty($userId) && AudioFavoriteService::has($userId, $audioId)) ? 'y' : 'n',
            'tags'         => value(function () use ($result) {
                $rows = [];
                foreach ($result['tags'] as $item) {
                    $rows[] = [
                        'id'   => strval($item['id']),
                        'name' => strval($item['name'])
                    ];
                }
                return $rows;
            }),
            'recommend_items' => value(function () use ($result) {
                $filter = ['page_size' => '6', 'not_ids' => $result['id'], 'order' => 'rand', 'tag_id' => join(',', array_column($result['tags'], 'id'))];
                return self::doSearch($filter)['data'];
            }),
            'seek_time' => value(function () use ($lastRead) {
                if (!empty($lastRead)) {
                    return strval($lastRead['time']);
                }
                return '0';
            }),
            'prev_id' => strval(isset($prevLink) ? $prevLink['id'] : ''),
            'next_id' => strval(isset($nextLink) ? $nextLink['id'] : ''),
            'chapter' => $chapters,

            // 我的信息
            'user' => [
                'id'       => strval($userInfo['id']),
                'username' => strval($userInfo['username']),
                'nickname' => strval($userInfo['nickname']),
                'headico'  => CommonService::getCdnUrl($userInfo['headico']),
                'is_vip'   => strval($userInfo['is_vip']),
                'balance'  => strval($userInfo['balance'])
            ]
        ];

        $hasBuy = value(function () use ($audioId, $freeChapters, $userInfo, $currentLink) {
            // 免费章节
            if (in_array($currentLink['id'], $freeChapters)) {
                return 'y';
            }
            if ($userInfo['group_rate'] == '0') {
                return 'y';
            }
            return UserBuyLogService::has($userInfo['id'], $audioId, 'audio'/* ,$currentLink['id'] */) ? 'y' : 'n';
        });

        // 用户是否购买||用户权限
        if ($hasBuy == 'y' || $result['pay_type'] == 'free') {
            $result['layer_type'] = '';
        } else {
            $vipRights = UserService::getRights($userInfo);
            if ($result['pay_type'] == 'money') {
                $result['layer_type']   = 'money';
                $currentLink['content'] = $currentLink['preview_content'] ?: $currentLink['content'];
            } else {
                // 是否会员
                if (in_array('audio', $vipRights)) {
                    $result['layer_type'] = '';
                } else {
                    /**
                     * 普通用户无权限,需要开通普通vip
                     * ps:因为历史记录表不同板块分开的,如果独立计算增加复杂度,没必要,所以默认需要vip
                     */
                    $result['layer_type']   = 'limit';
                    $currentLink['content'] = $currentLink['preview_content'] ?: $currentLink['content'];
                }
            }
        }

        $isChina              = IpService::isChina(CommonUtil::getClientIp());
        $result['play_links'] = [
            [
                'id'       => $audioId,
                'lid'      => $currentLink['id'],
                'code'     => 'line1',
                'name'     => '线路1',
                'm3u8_url' => M3u8Service::encode($currentLink['content'], $isChina ? 'tencent' : 'aws')
            ],
            [
                'id'       => $audioId,
                'lid'      => $currentLink['id'],
                'code'     => 'line2',
                'name'     => '线路2',
                'm3u8_url' => M3u8Service::encode($currentLink['content'], $isChina ? 'aws' : 'tencent')
            ],
            [
                'id'       => $audioId,
                'lid'      => $currentLink['id'],
                'code'     => 'line3',
                'name'     => '线路3',
                'm3u8_url' => M3u8Service::encode($currentLink['content'], 'free')
            ],
        ];
        return $result;
    }

    /**
     * 标签详情
     * @param                    $tagId
     * @return array
     * @throws BusinessException
     */
    public static function getTagDetail($tagId)
    {
        $row = AudioTagModel::findByID(intval($tagId));
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
     * @param                    $audioId
     * @return bool
     * @throws BusinessException
     */
    public static function doLove($userId, $audioId)
    {
        return AudioLoveService::do($userId, $audioId);
    }

    /**
     * 点赞列表
     * @param        $userId
     * @param        $page
     * @param  mixed $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getLoveList($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        $ids = AudioLoveService::getIds($userId, $page, $pageSize, $cursor);
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
     * @param                    $audioId
     * @return bool
     * @throws BusinessException
     */
    public static function doFavorite($userId, $audioId)
    {
        return AudioFavoriteService::do($userId, $audioId);
    }

    /**
     * 收藏列表
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getFavoriteList($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        $ids = AudioFavoriteService::getIds($userId, $page, $pageSize, $cursor);
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
     * 添加观看记录
     * @param       $userId
     * @param       $audioId
     * @param       $linkId
     * @param       $time
     * @param       $code
     * @return bool
     */
    public static function doHistory($userId, $audioId, $linkId, $time, $code)
    {
        return AudioHistoryService::do($userId, $audioId, $linkId, $time, $code);
    }

    /**
     * 删除历史记录
     * @param             $userId
     * @param             $audioIds
     * @return bool|mixed
     */
    public static function delHistory($userId, $audioIds)
    {
        return AudioHistoryService::delete($userId, $audioIds);
    }

    /**
     * 获取历史记录
     * @param        $userId
     * @param  int   $page
     * @param  int   $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getHistoryList($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        $ids = AudioHistoryService::getIds($userId, $page, $pageSize, $cursor);
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
     * 站点地图
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function sitemap($page = 1, $pageSize = 5000)
    {
        $where = ['status' => 1];
        $items = AudioModel::find($where, ['_id'], ['_id' => -1], ($page - 1) * $pageSize, $pageSize);
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
        $keyName  = "audio_block_{$block['id']}:{$language}";
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
