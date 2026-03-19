<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Novel\NovelBlockModel;
use App\Models\Novel\NovelChapterModel;
use App\Models\Novel\NovelModel;
use App\Models\Novel\NovelNavModel;
use App\Models\Novel\NovelTagModel;
use App\Services\Common\AdvService;
use App\Services\Common\ApiService;
use App\Services\Common\CommonService;
use App\Services\Novel\NovelBlockService;
use App\Services\Novel\NovelChapterService;
use App\Services\Novel\NovelDisLoveService;
use App\Services\Novel\NovelFavoriteService;
use App\Services\Novel\NovelHistoryService;
use App\Services\Novel\NovelLoveService;
use App\Services\Novel\NovelService;
use App\Services\Novel\NovelTagService;
use App\Services\User\UserBuyLogService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;

class NovelRepository extends BaseRepository
{
    /**
     * nav下模块,常规模块,带items
     * @param                             $navId
     * @param  mixed                      $page
     * @return array[]
     * @throws \Phalcon\Storage\Exception
     */
    public static function navBlock($navId, $page = 1)
    {
        $navId  = intval($navId);
        $navRow = NovelNavModel::findByID($navId);
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
        $blocks = NovelBlockService::get(intval($navId), $page, 6);
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
        $navRow = NovelNavModel::findByID($navId);
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
                if ($navRow['style'] == 'novel_2') {
                    return strval('12');
                }
                if ($navRow['style'] == 'novel_3') {
                    return strval('12');
                }
                return strval('30');
            }),
            'filters' => value(function () use ($navRow) {
                $filters = json_decode($navRow['filter'], true);
                // 普通列表
                if ($navRow['style'] == 'novel_2') {
                    return $filters;
                }
                // 带tab的列表
                if ($navRow['style'] == 'novel_3') {
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
                    foreach (NovelTagService::getGroupAttrAll() as $groupName => $items) {
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
     * @param                    $novelId
     * @param                    $chapterId
     * @return true
     * @throws BusinessException
     */
    public static function doBuy($userId, $novelId, $chapterId)
    {
        return NovelService::doBuy($userId, $novelId, $chapterId);
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
        $ids = UserBuyLogService::getIds($userId, 'novel', $page, $pageSize, $cursor);
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
        return NovelService::doSearch($query, $userId);
    }

    /**
     * 模块详情
     * @param                    $blockId
     * @return array
     * @throws BusinessException
     */
    public static function getBlockDetail($blockId)
    {
        $row = NovelBlockModel::findByID(intval($blockId));
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
     * @param                             $novelId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function getDetail($userId, $novelId)
    {
        $result   = NovelService::getInfoCache($novelId);
        $lastRead = [];
        if (!empty($userId)) {
            // 上次观看
            $lastRead = NovelHistoryService::getLastRead($userId, $novelId);
        }
        $chapters = NovelChapterService::getChapterList($novelId);

        foreach ($chapters as $index => $chapter) {
            $chapter = [
                'id'   => strval($chapter['id']),
                'name' => value(function () use ($chapter) {
                    if (is_numeric($chapter['name'])) {
                        return "第{$chapter['name']}章";
                    }
                    return strval($chapter['name']);
                }),
                'img'     => CommonService::getCdnUrl($chapter['img'] ?: $result['img']),
                'current' => 'n',
            ];
            if (empty($lastRead) && $index == 0) {
                $lastRead = [
                    'novel_id'     => strval($novelId),
                    'chapter_id'   => strval($chapter['id']),
                    'chapter_name' => strval($chapter['name']),
                    'index'        => strval(0),
                ];
            }
            if ($lastRead['chapter_id'] == $chapter['id']) {
                $chapter['current'] = 'y';
            }
            $chapters[$index] = $chapter;
        }

        $userInfo = [];
        if (!empty($userId)) {
            $userInfo = UserService::getInfoFromCache($userId);
        }
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
            'img'         => CommonService::getCdnUrl($result['img_x']),
            'description' => strval($result['description']),
            'score'       => strval($result['score']),
            'category'    => $result['cat_id'] ?? '',
            'click'       => value(function () use ($result) {
                $keyName = 'novel_click_' . $result['id'];
                $real    = CommonService::getRedisCounter($keyName);
                return strval(CommonUtil::formatNum(intval($result['click'] + $real)));
            }),
            'love' => value(function () use ($result) {
                $keyName = 'novel_love_' . $result['id'];
                $real    = CommonService::getRedisCounter($keyName);
                return strval(CommonUtil::formatNum(intval($result['love'] + $real)));
            }),
            'favorite' => value(function () use ($result) {
                $keyName = 'novel_favorite_' . $result['id'];
                $real    = CommonService::getRedisCounter($keyName);
                return strval(CommonUtil::formatNum(intval($result['favorite'] + $real)));
            }),
            'comment' => value(function () use ($result) {
                $ok = CommonService::getRedisCounter("novel_comment_ok_{$result['id']}");
                //                    $no = CommonService::getRedisCounter("novel_comment_no_{$result['id']}");
                return strval(CommonUtil::formatNum(intval($ok)));
            }),
            'has_favorite' => (!empty($userId) && NovelFavoriteService::has($userId, $novelId)) ? 'y' : 'n',
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
            'user' => value(function () use ($userInfo) {
                return [
                    'user_id'  => strval($userInfo['id']),
                    'username' => strval($userInfo['username']),
                    'nickname' => strval($userInfo['nickname']),
                    'is_vip'   => strval($userInfo['is_vip']),
                    'balance'  => strval($userInfo['balance'])
                ];
            }),
            'recommend_items' => value(function () use ($result) {
                $filter = ['page_size' => '6', 'not_ids' => $result['id'], 'order' => 'rand', 'tag_id' => join(',', array_column($result['tags'], 'id'))];
                return self::doSearch($filter)['data'];
            }),
            'current_chapter' => $lastRead,
            'chapter'         => $chapters
        ];

        return $result;
    }

    /**
     * 章节详情
     * @param                             $userId
     * @param                             $chapterId
     * @return array
     * @throws BusinessException
     * @throws \Phalcon\Storage\Exception
     */
    public static function getChapterDetail($userId, $chapterId)
    {
        $chapterId  = strval($chapterId);
        $userInfo   = UserService::getInfoFromCache($userId);
        $chapterRow = NovelChapterModel::findByID($chapterId);
        if (empty($chapterRow)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '章节不存在!');
        }
        $chapterRow['name'] = value(function () use ($chapterRow) {
            if (is_numeric($chapterRow['name'])) {
                return "第{$chapterRow['name']}章";
            }
            return strval($chapterRow['name']);
        });
        $novelId   = $chapterRow['novel_id'];
        $novelInfo = NovelService::getInfoCache($novelId);
        $chapters  = NovelChapterService::getChapterList($novelId);
        foreach ($chapters as $index => $chapter) {
            $chapter = [
                'id'   => strval($chapter['id']),
                'name' => value(function () use ($chapter) {
                    if (is_numeric($chapter['name'])) {
                        return "第{$chapter['name']}章";
                    }
                    return strval($chapter['name']);
                }),
                'img'     => CommonService::getCdnUrl($chapter['img'] ?: $novelInfo['img']),
                'current' => $chapterId == $chapter['id'] ? 'y' : 'n'
            ];
            if ($chapter['current'] == 'y') {
                $prevChapter = $chapters[$index - 1] ?? [];
                $nextChapter = $chapters[$index + 1] ?? [];
            }
            $chapters[$index] = $chapter;
        }

        if (!empty($userId)) {
            // /添加观看记录
            NovelHistoryService::do($userId, $chapterRow['novel_id'], $chapterRow);
        }

        $result = [
            'novel_id'   => strval($novelId),
            'novel_name' => strval($novelInfo['name']),
            'pay_type'   => $novelInfo['pay_type'],
            'money'      => strval($novelInfo['money']),
            'name'       => strval($chapterRow['name']),
            'sub_name'   => value(function () use ($novelInfo) {
                if ($novelInfo['update_status'] == 1) {
                    return '共' . $novelInfo['chapter_count'] . '章';
                }
                return '更新' . $novelInfo['chapter_count'] . '章';
            }),
            'chapter' => $chapters,
            'tags'    => value(function () use ($novelInfo) {
                $rows = [];
                foreach ($novelInfo['tags'] as $tag) {
                    $rows[] = [
                        'id'   => strval($tag['id']),
                        'name' => strval($tag['name'])
                    ];
                }
                return $rows;
            }),
            'chapter_id'   => strval($chapterId),
            'has_favorite' => (!empty($userId) && NovelFavoriteService::has($userId, $novelId)) ? 'y' : 'n',
            'content'      => CommonService::getCdnUrl($chapterRow['content'], 'image'),
            // 上一章
            'prev_id' => strval(!empty($prevChapter) ? $prevChapter['id'] : ''),
            // 下一章
            'next_id' => strval(!empty($nextChapter) ? $nextChapter['id'] : ''),

            'layer_type' => 'limit', // 默认都显示次数
        ];

        // 付费逻辑
        $hasBuy = value(function () use ($novelId, $chapterId, $novelInfo, $userInfo) {
            // 免费章节
            $freeChapters = explode(',', $novelInfo['free_chapter']);
            if (in_array($chapterId, $freeChapters)) {
                return 'y';
            }
            if ($userInfo['group_rate'] == '0') {
                return 'y';
            }
            return UserBuyLogService::has($userInfo['id'], $novelId, 'novel'/* ,$chapterId */) ? 'y' : 'n';
        });

        if ($hasBuy == 'y' || $novelInfo['pay_type'] == 'free') {
            $result['layer_type'] = '';
        } else {
            $vipRights = UserService::getRights($userInfo);
            if ($result['pay_type'] == 'money') {
                $result['layer_type'] = 'money';
            } elseif (in_array('novel', $vipRights)) {
                /**
                 * 普通用户无权限,需要开通普通vip
                 * ps:因为历史记录表不同板块分开的,如果独立计算增加复杂度,没必要,所以默认需要vip
                 */
                $result['layer_type'] = '';
            }
        }

        // 二次过滤内容
        if ($result['layer_type'] != '') {
            $result['content'] = '';// 图片设置为空 客户端判断,layer_type!=""&&当前url为空,则展示对应购买弹窗
        }

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
        $row = NovelTagModel::findByID(intval($tagId));
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
     * @param                                   $userId
     * @param                                   $novelId
     * @return bool
     * @throws \App\Exception\BusinessException
     */
    public static function doLove($userId, $novelId)
    {
        return NovelLoveService::do($userId, $novelId);
    }

    /**
     * 去点踩
     * @param $userId
     * @param $novelId
     * @return bool
     * @throws BusinessException
     */
    public static function doDisLove($userId,$novelId)
    {
        return NovelDisLoveService::do($userId, $novelId);
    }

    /**
     * 是否踩
     * @param $userId
     * @param $novelId
     * @return bool
     * @throws BusinessException
     */
    public static function isDisLove($userId,$novelId)
    {
        return NovelDisLoveService::has($userId, $novelId);
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
        $ids = NovelLoveService::getIds($userId, $page, $pageSize, $cursor);
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
     * @param                                   $userId
     * @param                                   $novelId
     * @return bool
     * @throws \App\Exception\BusinessException
     */
    public static function doFavorite($userId, $novelId)
    {
        return NovelFavoriteService::do($userId, $novelId);
    }

    /**
     * 收藏的漫画列表
     * @param        $userId
     * @param        $page
     * @param  mixed $pageSize
     * @param  mixed $cursor
     * @return array
     */
    public static function getFavoriteList($userId, $page = 1, $pageSize = 12, $cursor = '')
    {
        $ids = NovelFavoriteService::getIds($userId, $page, $pageSize, $cursor);
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
     * 上报观看记录
     * @param                    $userId
     * @param                    $novelId
     * @param                    $chapterId
     * @param                    $index
     * @return bool|int|null
     * @throws BusinessException
     */
    public static function doHistory($userId, $novelId, $chapterId, $index)
    {
        $chapterRow = NovelChapterModel::findFirst(['_id' => strval($chapterId)]);
        if (empty($chapterRow)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '章节不存在!');
        }
        return NovelHistoryService::do($userId, $novelId, $chapterRow, $index);
    }

    /**
     * 删除历史记录
     * @param             $userId
     * @param             $novelIds
     * @return bool|mixed
     */
    public static function delHistory($userId, $novelIds)
    {
        return NovelHistoryService::delete($userId, $novelIds);
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
        $ids = NovelHistoryService::getIds($userId, $page, $pageSize, $cursor);
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
        $items = NovelModel::find($where, ['_id'], ['_id' => -1], ($page - 1) * $pageSize, $pageSize);
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
        $keyName  = "novel_block_{$block['id']}:{$language}";
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
