<?php

namespace App\Repositories\Api;

use App\Constants\CommonValues;
use App\Constants\StatusCode;
use App\Core\Repositories\BaseRepository;
use App\Exception\BusinessException;
use App\Models\Ai\AiOrderModel;
use App\Models\Ai\AiTplModel;
use App\Services\Ai\AiFavoriteService;
use App\Services\Ai\AiLoveService;
use App\Services\Ai\AiService;
use App\Services\Ai\AiTagService;
use App\Services\Ai\AiTplService;
use App\Services\Common\CommonService;
use App\Services\Common\ConfigService;
use App\Services\Report\ReportAiTplLogService;
use App\Services\User\UserFansService;
use App\Services\User\UserService;
use App\Utils\CommonUtil;
use App\Utils\FileUtil;

class AiRepository extends BaseRepository
{
    /**
     * 获取配置
     * @return array
     */
    public static function getConfig()
    {
        $configs = ConfigService::getAll();
        return [
            // 换脸
            'ai_change_face_status' => $configs['ai_change_face_status'],

            // 换装
            'ai_change_dress_status' => $configs['ai_change_dress_status'],

            // 脱衣
            'ai_change_dress_bare_status' => $configs['ai_change_dress_bare_status'],

            // 小说
            'ai_novel_status' => $configs['ai_novel_status'],

            // 文转语音
            'ai_text_to_voice_status' => $configs['ai_text_to_voice_status'],

            // 图生视频
            'ai_image_to_video_status' => $configs['ai_image_to_video_status'],

            // 文生图片
            'ai_text_to_image_status' => $configs['ai_text_to_image_status'],

            // 支持类型
            'types' => value(function () {
                $result = [];
                foreach (CommonValues::getAiTplType() as $code => $name) {
                    $result[] = [
                        'code' => strval($code),
                        'name' => strval($name),
                    ];
                }
                return $result;
            }),
        ];
    }

    /**
     * 获取标签
     * @param                             $type
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public static function navFilter($type)
    {
        $rows   = AiTagService::getAll($type);
        $result = [
            'banner'  => [],
            'filters' => value(function () use ($rows) {
                $result = [
                    [
                        'name'   => '热门推荐',
                        'filter' => [
                            'order' => 'hot'
                        ]
                    ],
                    [
                        'name'   => '最近更新',
                        'filter' => [
                            'order' => 'new'
                        ]
                    ]
                ];

                foreach ($rows as $row) {
                    $result[] = [
                        'name'   => strval($row['name']),
                        'filter' => [
                            'tag_id' => strval($row['id'])
                        ]
                    ];
                }
                return $result;
            }),
        ];
        return $result;
    }

    /**
     * 获取模板列表
     * @param        $type
     * @param  mixed $filter
     * @return array
     */
    public static function getTpl($type, $filter = [])
    {
        $query    = [];
        $page     = self::getRequest($filter, 'page', 'int', 1);
        $pageSize = self::getRequest($filter, 'page_size', 'int', 20);
        $order    = self::getRequest($filter, 'order', 'string', '');
        $tagId    = self::getRequest($filter, 'tag_id', 'string', '');

        $rows = AiTplService::getAll($type);

        if (!empty($filter)) {
            if (!empty($tagId)) {
                foreach ($rows as $index => $row) {
                    if (!in_array($tagId, $row['tags'])) {
                        unset($rows[$index]);
                    }
                }
            }
            // 排序
            if ($order == 'hot') {
                // 热门模板
                $ids  = ReportAiTplLogService::getIds('buy', 'week', 0, count($rows), $type);
                $rows = CommonUtil::arraySort($rows, 'id', $ids);
            }
            if ($order == 'new') {
                // 最新模板
                array_multisort(array_column($rows, 'id'), SORT_DESC, $rows);
            }
            $rows = array_values($rows);
        }

        $total = count($rows);
        return [
            'data'         => CommonUtil::arrayPage($rows, $page, $pageSize),
            'total'        => strval($total),
            'current_page' => strval($page),
            'page_size'    => strval($pageSize),
            'last_page'    => strval(ceil($total / $pageSize)),
        ];
    }

    /**
     * 提示词
     * @param          $type
     * @return array[]
     */
    public static function getTips($type)
    {
        if ($type == 'text_to_image') {
            $rows = [
                '头部' => [
                    [
                        'name' => '黑色头发',
                        'key'  => 'black hair',
                    ],
                    [
                        'name' => '金色头发',
                        'key'  => 'blonde hair',
                    ],
                    [
                        'name' => '红色头发',
                        'key'  => 'red hair',
                    ],
                    [
                        'name' => '白色头发',
                        'key'  => 'white hair',
                    ],
                    [
                        'name' => '黄色头发',
                        'key'  => 'yellow hair',
                    ],
                    [
                        'name' => '粉色头发',
                        'key'  => 'pink hair',
                    ],
                    [
                        'name' => '短头发',
                        'key'  => 'short hair',
                    ],
                    [
                        'name' => '双马尾发型',
                        'key'  => 'twintails',
                    ],
                    [
                        'name' => '精灵耳朵',
                        'key'  => 'elf ears',
                    ],
                    [
                        'name' => '猫耳朵',
                        'key'  => 'cat ears',
                    ],
                    [
                        'name' => '狗耳朵',
                        'key'  => 'dog ears',
                    ],
                    [
                        'name' => '兔耳朵',
                        'key'  => 'bunny ears',
                    ],
                    [
                        'name' => '闭上眼睛',
                        'key'  => 'closed eyes',
                    ],
                    [
                        'name' => '翻白眼',
                        'key'  => 'rolling eyes',
                    ],
                    [
                        'name' => '闪闪发光的眼睛',
                        'key'  => 'sparkling eyes',
                    ],
                    [
                        'name' => '蒙住眼睛',
                        'key'  => 'covered eyes',
                    ],
                    [
                        'name' => '张嘴',
                        'key'  => 'open mouth',
                    ],
                    [
                        'name' => '咬紧牙关',
                        'key'  => 'clenched teeth',
                    ],
                    [
                        'name' => '伸出舌头',
                        'key'  => 'tongue out',
                    ],
                    [
                        'name' => '唾液',
                        'key'  => 'saliva',
                    ],
                    [
                        'name' => '诱人的微笑',
                        'key'  => 'seductive smile',
                    ],
                    [
                        'name' => '兴奋的',
                        'key'  => 'excited',
                    ],
                    [
                        'name' => '脸红',
                        'key'  => 'blush',
                    ],
                    [
                        'name' => '困',
                        'key'  => 'sleepy',
                    ],
                    [
                        'name' => '醉',
                        'key'  => 'drunk',
                    ],
                    [
                        'name' => '伤心',
                        'key'  => 'sad',
                    ],
                    [
                        'name' => '生气',
                        'key'  => 'angry',
                    ],
                    [
                        'name' => '皱眉',
                        'key'  => 'scowl',
                    ],
                    [
                        'name' => '凌乱的头发',
                        'key'  => 'disheveled hair',
                    ],
                    [
                        'name' => '卷发',
                        'key'  => 'wavy hair',
                    ],
                    [
                        'name' => '长发',
                        'key'  => 'long hair',
                    ],
                    [
                        'name' => '呻吟',
                        'key'  => 'moaning',
                    ],
                    [
                        'name' => '性高潮',
                        'key'  => 'orgasm,fucked_silly,uneven_eyes,rolling_eyes,',
                    ],
                    [
                        'name' => '异瞳',
                        'key'  => 'heterochromia',
                    ],
                ],
                '身体' => [
                    [
                        'name' => '平胸',
                        'key'  => 'Flat chest',
                    ],
                    [
                        'name' => '苗条',
                        'key'  => 'slim',
                    ],
                    [
                        'name' => '魅力',
                        'key'  => 'charm',
                    ],
                    [
                        'name' => '丰满',
                        'key'  => 'fat',
                    ],
                    [
                        'name' => '小乳房',
                        'key'  => 'small breasts',
                    ],
                    [
                        'name' => '中等乳房',
                        'key'  => 'medium breasts',
                    ],
                    [
                        'name' => '大乳房',
                        'key'  => 'large breasts',
                    ],
                    [
                        'name' => '巨大的乳房',
                        'key'  => 'huge breasts',
                    ],
                    [
                        'name' => '下垂的乳房',
                        'key'  => 'hanging breasts',
                    ],
                    [
                        'name' => '弹跳的乳房',
                        'key'  => 'bouncing breasts',
                    ],
                    [
                        'name' => '隆胸',
                        'key'  => 'breast implants',
                    ],
                    [
                        'name' => '假胸',
                        'key'  => 'fake tits',
                    ],
                    [
                        'name' => '不对称的乳房',
                        'key'  => 'unaligned breasts',
                    ],
                    [
                        'name' => '完美圆润的乳房',
                        'key'  => 'perfectly round breasts',
                    ],
                    [
                        'name' => '乳头',
                        'key'  => 'nipples',
                    ],
                    [
                        'name' => '屁股特写',
                        'key'  => 'close up ass',
                    ],
                    [
                        'name' => '巨大的屁股',
                        'key'  => 'huge ass',
                    ],
                    [
                        'name' => '阴部',
                        'key'  => 'detailed pussy',
                    ],
                    [
                        'name' => '阴蒂',
                        'key'  => 'clitoris',
                    ],
                    [
                        'name' => '阴道',
                        'key'  => 'vaginal',
                    ],
                    [
                        'name' => '肛门',
                        'key'  => 'anus',
                    ],
                    [
                        'name' => '阴道分泌物',
                        'key'  => 'pussy_juice',
                    ],
                    [
                        'name' => '阴毛',
                        'key'  => 'pubic hair',
                    ],
                    [
                        'name' => '内阴',
                        'key'  => 'innie pussy',
                    ],
                    [
                        'name' => '阴茎',
                        'key'  => 'detailed penis',
                    ],
                    [
                        'name' => '大阴茎',
                        'key'  => 'large detailed penis',
                    ],
                    [
                        'name' => '睾丸',
                        'key'  => 'testicles',
                    ],
                    [
                        'name' => '射精',
                        'key'  => 'ejaculation',
                    ],
                    [
                        'name' => '勃起',
                        'key'  => 'erection',
                    ],
                    [
                        'name' => '假阳具',
                        'key'  => 'dildo_riding',
                    ],
                    [
                        'name' => '湿润',
                        'key'  => 'wet',
                    ],
                    [
                        'name' => '汗水',
                        'key'  => 'sweat',
                    ],
                ],
                '场景' => [
                    [
                        'name' => '酒店',
                        'key'  => 'in the hotel',
                    ],
                    [
                        'name' => '酒吧',
                        'key'  => 'bar',
                    ],
                    [
                        'name' => '办公室',
                        'key'  => 'in the office room',
                    ],
                    [
                        'name' => '公园',
                        'key'  => 'park',
                    ],
                    [
                        'name' => '厕所',
                        'key'  => 'bathroom',
                    ],
                    [
                        'name' => '商场',
                        'key'  => 'shopping center',
                    ],
                    [
                        'name' => '电影院',
                        'key'  => 'Cinema',
                    ],
                    [
                        'name' => '医院',
                        'key'  => 'Hospital',
                    ],
                    [
                        'name' => '健身房',
                        'key'  => 'Gym',
                    ],
                    [
                        'name' => '车内',
                        'key'  => 'in the car',
                    ],
                    [
                        'name' => '火车',
                        'key'  => 'train',
                    ],
                    [
                        'name' => '飞机',
                        'key'  => 'on the plane',
                    ],
                    [
                        'name' => '公交车',
                        'key'  => 'in the bus',
                    ],
                    [
                        'name' => '试衣间',
                        'key'  => 'fitting room',
                    ],
                    [
                        'name' => '厨房',
                        'key'  => 'kitchen',
                    ],
                    [
                        'name' => '浴室',
                        'key'  => 'shower room',
                    ],
                    [
                        'name' => '街头',
                        'key'  => 'in the cyberpunk city',
                    ],
                    [
                        'name' => '在地板上',
                        'key'  => 'on the floor',
                    ],
                    [
                        'name' => '在沙发上',
                        'key'  => 'on the sofa',
                    ],
                    [
                        'name' => '丛林',
                        'key'  => 'grove',
                    ],
                    [
                        'name' => '海滩',
                        'key'  => 'sea beach',
                    ],
                    [
                        'name' => '洞穴',
                        'key'  => 'in cave',
                    ],
                    [
                        'name' => '森林',
                        'key'  => 'forest',
                    ],
                    [
                        'name' => '草地',
                        'key'  => 'grassland',
                    ],
                    [
                        'name' => '卧室',
                        'key'  => 'in the bedroom',
                    ],
                    [
                        'name' => '客厅',
                        'key'  => 'in the living room',
                    ],
                    [
                        'name' => '更衣室',
                        'key'  => 'in the locker room',
                    ],
                    [
                        'name' => '阳台',
                        'key'  => 'in the recreation room',
                    ],
                    [
                        'name' => '咖啡店',
                        'key'  => 'cafe',
                    ],
                    [
                        'name' => '图书馆',
                        'key'  => 'library',
                    ],
                    [
                        'name' => '课堂',
                        'key'  => 'classroom',
                    ],
                ],
                '动作' => [
                    [
                        'name' => '内射',
                        'key'  => 'cum in pussy,one boy,large penis,testicles,detailed penis,huge cum,vaginal penetration,covered in semen,detailed pussy,pussy_juice,vaginal,anus,clitoris,pussy close-up',
                    ],
                    [
                        'name' => '后入',
                        'key'  => 'detailed vagina,bent over,ass view,ass focus,from behind,large penis,detailed penis,sex,saliva on penis,twitching penis,vaginal penetration,Naked male body,The man\\\'s penis is entering the woman\\\'s vagina',
                    ],
                    [
                        'name' => '乳交',
                        'key'  => 'wet skin,blush, breasts,breasts squeezed together,Penis between breasts,large penis,cum,ejaculation,hetero,huge breasts,nipples,open mouth,topless female,Naked male',
                    ],
                    [
                        'name' => '足交',
                        'key'  => 'fellatio,oral,one boy,penis,detailed penis,kissing penis shaft,saliva on penis,twitching penis,pre cum,testicles、close-up',
                    ],
                    [
                        'name' => '打飞机',
                        'key'  => 'hand penis,large penis, cum,起erection,testicles,bend over,opened mouth,blush,one boy',
                    ],
                    [
                        'name' => '自慰',
                        'key'  => 'legs spread,pussy,rubbing clitoris,pussy_juice,detailed pussy,vaginal,anus,dynamic pose,',
                    ],
                    [
                        'name' => '颜射',
                        'key'  => 'cum on face,one boy,open mouth,large penis,testicles,huge cum,covered in semen,face close-up',
                    ],
                    [
                        'name' => '女上位',
                        'key'  => 'cowgirl position,cowgirl position,nipples,penis,pussy,sex, cum,cum in pussy,deep penetration,vaginal penetration,ejaculation,erection,female focus,internal cumshot,nude,pussy juice,sitting on Naked male,riding,shaking,vaginal,wet，',
                    ],
                    [
                        'name' => '舔逼',
                        'key'  => 'Naked male,male tongue,licking pussy,cunnilingus,close up,side view,wet,orgasm,,fingering,fingering anus,detailed pussy,pussy_juice,',
                    ],
                    [
                        'name' => '口交',
                        'key'  => 'fellatio,oral,penis,detailed penis,kissing penis shaft,saliva on penis,twitching penis,pre cum,testicles,Naked male body',
                    ],
                    [
                        'name' => '站着',
                        'key'  => 'standing',
                    ],
                    [
                        'name' => '趴着',
                        'key'  => 'Lying down',
                    ],
                    [
                        'name' => '手放在自己胸口',
                        'key'  => 'hand on own chest',
                    ],
                    [
                        'name' => '双臂交叉',
                        'key'  => 'arms crossed',
                    ],
                    [
                        'name' => '张开双臂',
                        'key'  => 'spread arms',
                    ],
                    [
                        'name' => '双手叉腰',
                        'key'  => 'hands on hip',
                    ],
                    [
                        'name' => '嘘',
                        'key'  => 'shushing',
                    ],
                    [
                        'name' => '张开双腿',
                        'key'  => 'spread legs',
                    ],
                    [
                        'name' => '高抬腿',
                        'key'  => 'leg lift',
                    ],
                    [
                        'name' => '抱着腿',
                        'key'  => 'leg hug',
                    ],
                    [
                        'name' => '跨坐',
                        'key'  => 'straddling',
                    ],
                    [
                        'name' => '跪下',
                        'key'  => 'kneeling',
                    ],
                    [
                        'name' => '吸烟',
                        'key'  => 'smoking',
                    ],
                    [
                        'name' => '性爱',
                        'key'  => 'sex',
                    ],
                    [
                        'name' => '射在身上',
                        'key'  => 'cum on body,one boy,large penis,testicles,detailed penis,huge cum,covered in semen',
                    ],
                    [
                        'name' => '射在胸上',
                        'key'  => 'cum on breasts,one boy,large penis,testicles,detailed penis,huge cum,covered in semen,perfectly round breasts,nipples,Chest close-up',
                    ],
                ],
                '人物' => [
                    [
                        'name' => '女高中生',
                        'key'  => 'high school girl',
                    ],
                    [
                        'name' => '青春少女',
                        'key'  => 'young girl',
                    ],
                    [
                        'name' => '小女孩',
                        'key'  => 'a little girl',
                    ],
                    [
                        'name' => '性感的女人',
                        'key'  => 'sexy girl',
                    ],
                    [
                        'name' => '孕妇',
                        'key'  => 'pregnant woman',
                    ],
                    [
                        'name' => '成熟的女人',
                        'key'  => 'mature woman',
                    ],
                    [
                        'name' => '中年女人',
                        'key'  => 'middle aged woman',
                    ],
                    [
                        'name' => '学生',
                        'key'  => 'student',
                    ],
                    [
                        'name' => '萝莉',
                        'key'  => 'loli',
                    ],
                    [
                        'name' => '辣妹',
                        'key'  => 'gyaru',
                    ],
                    [
                        'name' => '大小姐',
                        'key'  => 'ojousama',
                    ],
                    [
                        'name' => '护士',
                        'key'  => 'nurse',
                    ],
                    [
                        'name' => '空姐',
                        'key'  => 'stewardess',
                    ],
                    [
                        'name' => '医生',
                        'key'  => 'doctor',
                    ],
                    [
                        'name' => '警察',
                        'key'  => 'police',
                    ],
                    [
                        'name' => '舞女',
                        'key'  => 'alme',
                    ],
                    [
                        'name' => '巫女',
                        'key'  => 'miko',
                    ],
                    [
                        'name' => '女佣',
                        'key'  => 'housemaid',
                    ],
                    [
                        'name' => '女王',
                        'key'  => 'queen',
                    ],
                    [
                        'name' => '天使',
                        'key'  => 'angel',
                    ],
                    [
                        'name' => '吸血鬼',
                        'key'  => 'vampire',
                    ],
                    [
                        'name' => '骑士',
                        'key'  => 'knight',
                    ],
                    [
                        'name' => '魔法女孩',
                        'key'  => 'magical girl',
                    ],
                    [
                        'name' => '兽耳女孩',
                        'key'  => 'kemonomimi gril',
                    ],
                    [
                        'name' => '美人鱼',
                        'key'  => 'mermaid',
                    ],
                    [
                        'name' => '精灵',
                        'key'  => 'elf gril',
                    ],
                    [
                        'name' => '仙女',
                        'key'  => 'fairy',
                    ],
                    [
                        'name' => '尼姑',
                        'key'  => 'nun',
                    ],
                ],
                '服饰' => [
                    [
                        'name' => '洛丽塔裙子',
                        'key'  => 'Lolita uniform',
                    ],
                    [
                        'name' => '全裸',
                        'key'  => 'completely nude',
                    ],
                    [
                        'name' => '比基尼',
                        'key'  => 'bikini',
                    ],
                    [
                        'name' => '情趣内衣',
                        'key'  => 'Sexy lingerie',
                    ],
                    [
                        'name' => 'cos服装',
                        'key'  => 'cosplay uniform',
                    ],
                    [
                        'name' => '护士服装',
                        'key'  => 'nurse uniform',
                    ],
                    [
                        'name' => '透明的衣服',
                        'key'  => 'transparent clothes',
                    ],
                    [
                        'name' => '女仆装',
                        'key'  => 'maid outfit',
                    ],
                    [
                        'name' => '水手服',
                        'key'  => 'Sailor dress',
                    ],
                    [
                        'name' => '旗袍',
                        'key'  => 'cheongsam',
                    ],
                    [
                        'name' => '晚礼服',
                        'key'  => 'evening dress',
                    ],
                    [
                        'name' => '白色连衣裙',
                        'key'  => 'white dress',
                    ],
                    [
                        'name' => '校服',
                        'key'  => 'school uniform',
                    ],
                    [
                        'name' => '婚纱',
                        'key'  => 'wedding dress',
                    ],
                    [
                        'name' => '泳装',
                        'key'  => 'swimsuit',
                    ],
                    [
                        'name' => '连体泳衣',
                        'key'  => 'one-piece swimsuit',
                    ],
                    [
                        'name' => '超短裙',
                        'key'  => 'miniskirt',
                    ],
                    [
                        'name' => '黑色运动胸罩',
                        'key'  => 'black sports bra',
                    ],
                    [
                        'name' => '睡衣',
                        'key'  => 'pajamas',
                    ],
                    [
                        'name' => '短裤',
                        'key'  => 'short shorts',
                    ],
                    [
                        'name' => '百褶裙',
                        'key'  => 'pleated skirt',
                    ],
                    [
                        'name' => '吊带比基尼',
                        'key'  => 'sling bikini',
                    ],
                    [
                        'name' => '性感内衣',
                        'key'  => 'sexy lingerie',
                    ],
                    [
                        'name' => '透明内衣',
                        'key'  => 'transparent underwear',
                    ],
                    [
                        'name' => '没有内裤',
                        'key'  => 'no panties',
                    ],
                    [
                        'name' => '细带内裤',
                        'key'  => 'string panties',
                    ],
                    [
                        'name' => '丁字裤',
                        'key'  => 'thong',
                    ],
                    [
                        'name' => '湿衣服',
                        'key'  => 'wet clothes',
                    ],
                    [
                        'name' => '暴露的衣服',
                        'key'  => 'revealing dress',
                    ],
                    [
                        'name' => '粉色透明连衣裙',
                        'key'  => 'pink lucency full dress',
                    ],
                    [
                        'name' => '露乳连衣裙',
                        'key'  => 'cleavage dress',
                    ],
                    [
                        'name' => 'jk',
                        'key'  => 'JK',
                    ],
                ],
            ];
        } elseif ($type == 'text_to_voice') {
            $rows = [
                'all' => [
                    [
                        'name' => '不要嘛，快点插……再深一点，再深一点……对，就是那里，啊……美死我了，美死了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊！住手！你竟敢，你这个大变态！呜……啊……混蛋……放……放开我……快停下……我……啊！',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊啊啊！！！别咬人家……那里啊……啊啊啊！……用力……再用力……啊……',
                        'key'  => '',
                    ],
                    [
                        'name' => '哟，那么快就要封我的嘴吗？我们还没怎么相互了解呢！算了，今天也累了，记得明天再来好好地‘蹂躏’我哦！不许偷懒……',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊……真是变态呢，居然要我穿这个……啊！……好挤……哈……真够爽……再……再用力点儿……就好了呢！',
                        'key'  => '',
                    ],
                    [
                        'name' => '别……别接……啊啊……继续……妈妈快……快到了！啊啊啊……啊啊……好……好……不要停……用力一点……干死我……干死妈妈！',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊…. 姊夫的鸡鸡真好吃…..啊…. 我已经不能忍耐了…… 快在我这里插进来吧……',
                        'key'  => '',
                    ],
                    [
                        'name' => '我说，我说，求主……人快…插进我………我我奴隶…张美娴的淫洞……替我开苞破处',
                        'key'  => '',
                    ],
                    [
                        'name' => '求求你，这么粗大的阳具，一定会插死我的，我……我，请你轻力一点，慢一点',
                        'key'  => '',
                    ],
                    [
                        'name' => '请主人放过我吧，性奴一生一世都服侍你，我什么都听，求求你不要再插了，好痛啊！',
                        'key'  => '',
                    ],
                    [
                        'name' => '嗯哼……爽………好爽………再舔……再……啊……进去………我……快飞了………哎呀……我快死了………真的……死了………',
                        'key'  => '',
                    ],
                    [
                        'name' => '哦！唷……唉……停止……止吧……我……我的阴户……。将…将要爆开了…哎呀……我……我……快……要丢了…',
                        'key'  => '',
                    ],
                    [
                        'name' => '亲鸡巴，我，我忍不住了，动吧！动吧！再用力点，好……好使阳具插进去挖我的花心……哎，哎，哎……',
                        'key'  => '',
                    ],
                    [
                        'name' => '我……我亲爱的宝贝哥哥……好哥哥呀……快，快，快把我的屁股抱高一点呀！哎呀！我这肥穴痛快死囉！宝贝哥……亲哥哥……顶……顶……快把你的大鸡巴插进点呀',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊……好宝贝……亲哥哥……用力、用力、用力、再用力、再用力，啊……快顶、快顶，再用力顶一点，顶重一点',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊……啊……啊……顶住我的最深层处呀……哎哟！哎哟！哎哟……伟明哥哥……我的好鸡巴……我又丢啦……丢啦……丢啦……啦！',
                        'key'  => '',
                    ],
                    [
                        'name' => '好、好、好舒服了，要我的命，真要我的命呀！太好了，太好了，太好了…………',
                        'key'  => '',
                    ],
                    [
                        'name' => '哥哥……亲哥哥……亲鸡巴……你不要离开我，我们永远在一块儿，现在要你用劲干我',
                        'key'  => '',
                    ],
                    [
                        'name' => '有点！有点！就这样别再乱来啊！好麻！好痒！好………我说不出来',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊！我痒死了！死鬼！你………你拱到那儿去了？啊！死鬼！轻点不行吗！我的心给你捣碎了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '嗯！亲哥！你………真好！你搞死我了！被你撞垮了，捣破了，我……我又流了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊！好涨！怎么你………的………更大了？啊！痛快！痛快！嗯……我完了！完……完了',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊天！我流了，泄了！我………我里面难受死了，小罗亲爱的！亲爹！你……你快………快点肏……啊！我！我没命了………',
                        'key'  => '',
                    ],
                    [
                        'name' => '嗯！好！好！够刺激！够痛快！啊！你也真够劲！我从没………从没遇过这大家伙，噢！舒服！真舒服！',
                        'key'  => '',
                    ],
                    [
                        'name' => '想要……好想要……姊姊的淫穴……要用大肉棒子插进姊姊的淫穴里面……',
                        'key'  => '',
                    ],
                    [
                        'name' => '妳的怎么又……变大了……姊姊快被妳……撑坏了啊……姊姊，好舒服……姊姊的里面……人家要把姊姊插……插死……啊……',
                        'key'  => '',
                    ],
                    [
                        'name' => '嗯……哎呀……你怎么这样？叫我……哦！……以后怎么做人啊……你流氓，你这个坏老头……亏我这么相信你……呜……',
                        'key'  => '',
                    ],
                    [
                        'name' => '别……别这么用力，我……我好痛……你……你温柔些，我……我那里好像要裂开了',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊！这么深，你要人家的小命了，人家今天把一切都给你了，哦，爽死了',
                        'key'  => '',
                    ],
                    [
                        'name' => '人家的小乳头，给你逗得好痒，好硬了……嗯，不要嘛，人家受不了了',
                        'key'  => '',
                    ],
                    [
                        'name' => '不可以的，你不能动那里，那是人家的禁区，啊……爽死了！我流了！老公，我流了……',
                        'key'  => '',
                    ],
                    [
                        'name' => '不要，不要动人家的小豆豆，人家老公都没这么玩过，爽死了！快点，快点动，我要死了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '进去了，人家是你的人了！你的大鸡巴，这么硬，这么粗，这么烫！',
                        'key'  => '',
                    ],
                    [
                        'name' => '好深哦！老公，亲哥哥，我的小亲哥哥，我要给你捅死了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '我感觉到了，你……的精液……都射进来了，散到我的花心里了……哦……好爽喔！',
                        'key'  => '',
                    ],
                    [
                        'name' => '再深一点，啊……人家花心都被你捅乱了，要死了！射进去吧，有劲，啊……',
                        'key'  => '',
                    ],
                    [
                        'name' => '哦，我的小花瓣已经张开了，人家开始被他弄流水了，嗯……我求求你，好舒服',
                        'key'  => '',
                    ],
                    [
                        'name' => '亲老公，亲爸爸，你为什么老在逗弄人家的小阴蒂啊，人家的浪水都流到大腿根了。',
                        'key'  => '',
                    ],
                    [
                        'name' => '就喜欢用你的大肉棒直接地插啊，插啊，插得我都……要死了，顶到人家花心里了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '嗯，我里面……好烫，好舒服的，老色狼，你就尽情地玩我吧！',
                        'key'  => '',
                    ],
                    [
                        'name' => '我……我受不了了，我……我要丢了，你射吧，我已经到了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊……接着射，我子宫里都能感觉到了，你的精液好烫，好多！哦……我又来了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '嗯，坏死了，一边干着人家，还一边摸人家的小阴蒂，哦……爽死了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '哦……你的龟头，顶到我的花心里了。哦……就在那里，别动……哦……磨死我了，天！',
                        'key'  => '',
                    ],
                    [
                        'name' => '我要来了……我……深点，使劲动！我的天，好舒服啊！都射进去吧！啊……',
                        'key'  => '',
                    ],
                    [
                        'name' => '亲哥，哥哥……呜……我要死了，我的里面……出了好多水了，我要来了！快，再深点！',
                        'key'  => '',
                    ],
                    [
                        'name' => '哦……爽死了！你怎么能舔……哦……别舔了，我求求你了！我受不了了！',
                        'key'  => '',
                    ],
                    [
                        'name' => '哦……我丢了……我要丢了……我的乳头太痒了……你插到人家子宫里了……再深点！',
                        'key'  => '',
                    ],
                    [
                        'name' => '哦….很棒….爽死了！而且一点也不痛….哦….我有一股很奇妙的感觉呢……',
                        'key'  => '',
                    ],
                    [
                        'name' => '喂….哥….帮我看一下，我的花心快溶化了哟！快….快嘛！帮人家看看嘛！',
                        'key'  => '',
                    ],
                    [
                        'name' => '啊….好舒服….哥哥….前面啦…..下面啦….快..快干我…..我..我受不了了……',
                        'key'  => '',
                    ]
                ]
            ];
        }

        foreach ($rows as $group => $items) {
            foreach ($items as $index => $item) {
                $rows[$group][$index]['id'] = $group . '_' . $index;
            }
        }
        return $rows;
    }

    /**
     * 图片|视频 换脸
     * @param                    $userId
     * @param                    $request
     * @return true
     * @throws BusinessException
     */
    public static function doChangeFace($userId, $request = [])
    {
        if (ConfigService::getConfig('ai_change_face_status') != 'y') {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'AI换脸服务目前关闭中!');
        }

        $tplId      = self::getRequest($request, 'tpl_id');// 模版id
        $sourcePath = self::getRequest($request, 'source_path');// 脸部源,图片地址
        $type       = self::getRequest($request, 'type');// 类型,image video
        if (empty($sourcePath)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if (!in_array($type, ['video', 'image'])) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '类型错误!');
        }
        if (!empty($tplId)) {
            $tplRow = AiTplModel::findByID($tplId);
            if (empty($tplRow) || $tplRow['is_disabled'] != 0) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, '模版异常!');
            }
            $money = $tplRow['money'];
            if ($tplRow['type'] == 'change_face_image') {
                $targetPath = $tplRow['config']['img'];
            } else {
                $targetPath = $tplRow['config']['m3u8_url'];
            }
        } else {
            $money      = 0;
            $targetPath = self::getRequest($request, 'target_path');// 需要处理的视频或者图片  图片可以支持多个用,分开  视频支持mp4 m3u8
            // 这里为上传的视频id,因为上传后没有立即返回m3u8地址
            if (empty($targetPath)) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请上传处理的视频或者图片!');
            }
        }

        return AiService::doSave($userId, [
            'remark' => $type == 'image' ? 'AI图片换脸' : 'AI视频换脸',
            'type'   => "change_face_{$type}",
            'money'  => intval($money),
            'num'    => 1,
            'tpl_id' => $tplId,
            'extra'  => [
                'source_path' => $sourcePath,
                'target_path' => $targetPath,
            ]
        ]);
    }

    /**
     * 图生视频
     * @param                    $userId
     * @param                    $request
     * @return true
     * @throws BusinessException
     */
    public static function doImageToVideo($userId, $request = [])
    {
        if (ConfigService::getConfig('ai_image_to_video_status') != 'y') {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'AI图生视频服务目前关闭中!');
        }

        $sourcePath = self::getRequest($request, 'source_path');// 单张图片,第三方接口只支持单张
        $tplId      = self::getRequest($request, 'tpl_id');// 模版id

        if (empty($tplId) || empty($sourcePath)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $tplRow = AiTplModel::findByID($tplId);
        if (empty($tplRow) || $tplRow['is_disabled'] != 0) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '模版异常!');
        }
        $money  = $tplRow['money'];
        $method = $tplRow['config']['code'];
        return AiService::doSave($userId, [
            'remark' => 'AI图生视频',
            'type'   => 'image_to_video',
            'money'  => intval($money),
            'num'    => 1,
            'tpl_id' => $tplId,
            'extra'  => [
                'source_path' => $sourcePath,
                'method'      => $method,
            ]
        ]);
    }

    /**
     * 文生语音
     * @param                    $userId
     * @param                    $request
     * @return true
     * @throws BusinessException
     */
    public static function doTextToVoice($userId, $request = [])
    {
        if (ConfigService::getConfig('ai_text_to_voice_status') != 'y') {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'AI文转语音服务目前关闭中!');
        }

        $content = self::getRequest($request, 'content');// 需要转化的文本
        $tplId   = self::getRequest($request, 'tpl_id');// 模版id

        if (empty($content)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if (mb_strlen($content) > 200) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '文字限制200字以内!');
        }
        if (!empty($tplId)) {
            $tplRow = AiTplModel::findByID($tplId);
            if (empty($tplRow) || $tplRow['is_disabled'] != 0) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, '模版异常!');
            }
            $money      = $tplRow['money'];
            $sourcePath = $tplRow['config']['m3u8_url'];
            $method     = $tplRow['config']['code'];
        } else {
            $money      = 10;
            $method     = 'method_1';
            $sourcePath = self::getRequest($request, 'source_path');// 语音源 可以是模板 也可以是用户自己的 可以是m3u8 也可
            // 这里为上传的视频id,因为上传后没有立即返回m3u8地址
            if (empty($sourcePath)) {
                throw  new BusinessException(StatusCode::PARAMETER_ERROR, '请上传语音模板!');
            }
        }

        return AiService::doSave($userId, [
            'remark' => 'AI文转语音',
            'type'   => 'text_to_voice',
            'money'  => intval($money),
            'num'    => 1,
            'tpl_id' => $tplId,
            'extra'  => [
                'content'     => $content,
                'source_path' => $sourcePath,
                'method'      => $method,
            ]
        ]);
    }

    /**
     * 文生图片
     * @param                    $userId
     * @param                    $request
     * @return true
     * @throws BusinessException
     */
    public static function doTextToImage($userId, $request = [])
    {
        if (ConfigService::getConfig('ai_text_to_image_status') != 'y') {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'AI文字生成图片服务目前关闭中!');
        }
        $prompt     = self::getRequest($request, 'prompt');// 提示词
        $size       = self::getRequest($request, 'size');// 尺寸
        $sourcePath = self::getRequest($request, 'source_path');// 参考图片
        $tplId      = self::getRequest($request, 'tpl_id');// 模版id
        $batchCount = self::getRequest($request, 'batch_count');// 生成组数
        $batchSize  = self::getRequest($request, 'batch_size');// 每组生成张数

        if (empty($tplId) || empty($prompt)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        if (mb_strlen($prompt) > 200) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '文字限制200字以内!');
        }
        $tplRow = AiTplModel::findByID($tplId);
        if (empty($tplRow) || $tplRow['is_disabled'] != 0) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '模版异常!');
        }
        $money  = $tplRow['money'];
        $method = $tplRow['config']['code'];

        return AiService::doSave($userId, [
            'remark' => 'AI文生图片',
            'type'   => 'text_to_image',
            'money'  => intval($money),
            'num'    => intval($batchCount),
            'tpl_id' => $tplId,
            'extra'  => [
                'method'      => $method,
                'size'        => $size,
                'prompt'      => $prompt,
                'source_path' => $sourcePath,
                'batch_count' => $batchCount,
                'batch_size'  => $batchSize,
            ]
        ]);
    }

    /**
     * 小说
     * @param                    $userId
     * @param                    $request
     * @return true
     * @throws BusinessException
     */
    public static function doNovel($userId, $request = [])
    {
        if (ConfigService::getConfig('ai_novel_status') != 'y') {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'AI小说生成服务目前关闭中!');
        }
        $background  = self::getRequest($request, 'background');// 人物设定
        $scene       = self::getRequest($request, 'scene');// 场景地点
        $story       = self::getRequest($request, 'story');// 故事情节
        $description = self::getRequest($request, 'description');// 细节说明
        $tplId       = self::getRequest($request, 'tpl_id');// 模板

        if (empty($description) || empty($story) || empty($tplId)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $tplRow = AiTplModel::findByID($tplId);
        if (empty($tplRow) || $tplRow['is_disabled'] != 0) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '模版异常!');
        }
        $money  = $tplRow['money'];
        $method = $tplRow['config']['code'];

        return AiService::doSave($userId, [
            'remark' => 'AI小说',
            'type'   => 'novel',
            'money'  => intval($money),
            'num'    => 1,
            'tpl_id' => $tplId,
            'extra'  => [
                'background'  => $background,
                'scene'       => $scene,
                'story'       => $story,
                'description' => $description,
                'method'      => $method,
            ]
        ]);
    }

    /**
     * 换装
     * @param                    $userId
     * @param                    $request
     * @return true
     * @throws BusinessException
     */
    public static function doChangeDress($userId, $request = [])
    {
        if (ConfigService::getConfig('ai_change_dress_status') != 'y') {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'AI换装服务目前关闭中!');
        }
        $sourcePath = self::getRequest($request, 'source_path');// 图片地址
        $tplId      = self::getRequest($request, 'tpl_id');// 模版id
        if (empty($sourcePath) || empty($tplId)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }

        $tplRow = AiTplModel::findByID($tplId);
        if (empty($tplRow) || $tplRow['is_disabled'] != 0) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '模版异常!');
        }
        $money      = $tplRow['money'];
        $method     = $tplRow['config']['code'];
        $targetPath = $tplRow['img'];

        return AiService::doSave($userId, [
            'remark' => 'AI换装',
            'type'   => 'change_dress',
            'money'  => intval($money),
            'num'    => 1,
            'tpl_id' => $tplId,
            'extra'  => [
                'source_path' => $sourcePath,
                'target_path' => $targetPath,
                'method'      => $method,
            ]
        ]);
    }
    /**
     * 去衣
     * @param                    $userId
     * @param                    $request
     * @return true
     * @throws BusinessException
     */
    public static function doChangeDressBare($userId, $request = [])
    {
        if (ConfigService::getConfig('ai_change_dress_bare_status') != 'y') {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, 'AI脱衣服务目前关闭中!');
        }
        $sourcePath = self::getRequest($request, 'source_path');// 图片地址
        $tplId      = self::getRequest($request, 'tpl_id');// 模版id
        if (empty($sourcePath)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }

        $tplRow = AiTplModel::findByID($tplId);
        if (empty($tplRow) || $tplRow['is_disabled'] != 0) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '模版异常!');
        }
        $money  = $tplRow['money'];
        $method = $tplRow['config']['code'];

        return AiService::doSave($userId, [
            'remark' => 'AI去衣',
            'type'   => 'change_dress_bare',
            'money'  => intval($money),
            'num'    => 1,
            'tpl_id' => $tplId,
            'extra'  => [
                'source_path' => $sourcePath,
                'method'      => $method,
            ]
        ]);
    }

    /**
     * 获取任务列表
     * @param        $userId
     * @param        $type
     * @param        $page
     * @param        $pageSize
     * @param  mixed $homeId
     * @return array
     */
    public static function getTaskList($userId, $homeId, $type = '', $page = 1, $pageSize = 20)
    {
        $where = ['home_id' => intval($homeId ?: $userId)];
        if (!empty($type)) {
            $where['order_type'] = $type;
        }
        // 如果是查看他人的作品，仅完成
        if (!empty($homeId)) {
            $where['status'] = 2;  // 仅完成
        } else {
            $where['status_all'] = 1;  // 全部状态
        }

        $where['page']      = $page;
        $where['page_size'] = $pageSize;
        return self::doSearch($where, $userId);
    }

    /**
     * 获取任务详情
     * @param                    $userId
     * @param                    $orderId
     * @return array|string[]
     * @throws BusinessException
     */
    public static function getTaskInfo($userId, $orderId)
    {
        $query = [
            '_id'     => intval($orderId),
            'user_id' => intval($userId),
        ];
        $orderRow = AiOrderModel::findFirst($query);
        if (empty($orderRow)) {
            throw new BusinessException(StatusCode::PARAMETER_ERROR, '任务不存在');
        }
        if (in_array($orderRow['status'], [0])) {
            return [
                'status' => '1',  // 正在处理中 2 已完成
                'msg'    => '正在处理中...'
            ];
        } elseif (in_array($orderRow['status'], [-1])) {
            return [
                'status' => '-1',  // -1 失败  退款
                'msg'    => '订单取消，已退款'
            ];
        }
        return [
            'id'          => strval($orderRow['_id']),
            'order_sn'    => strval($orderRow['order_sn']),
            'status'      => strval($orderRow['status']),
            'order_type'  => strval($orderRow['order_type']),
            'tpl_id'      => strval($orderRow['tpl_id']),
            'out_data'    => AiService::parseOutData($orderRow),
            'real_amount' => strval($orderRow['real_amount']),
        ];
    }

    /**
     * 详情
     * @param                    $userId
     * @param                    $orderId
     * @return array
     * @throws BusinessException
     */
    public static function getDetail($userId, $orderId)
    {
        if (empty($orderId)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '参数错误!');
        }
        $orderRow = AiOrderModel::findByID(intval($orderId));
        if (empty($orderRow)) {
            throw  new BusinessException(StatusCode::PARAMETER_ERROR, '当前作品不存在!');
        }

        $result = [
            'id'   => strval($orderRow['_id']),
            'name' => strval($orderRow['name']),
            'user' => value(function () use ($orderRow, $orderId, $userId) {
                $userInfo = UserService::getInfoFromCache($orderRow['user_id']);
                $rows     = [
                    'id'         => strval($userInfo['id']),
                    'nickname'   => strval($userInfo['nickname']),
                    'username'   => strval($userInfo['username']),
                    'headico'    => CommonService::getCdnUrl($userInfo['headico']),
                    'has_follow' => UserFansService::has($userId, $orderRow['user_id']) ? 'y' : 'n'
                ];

                return $rows;
            }),
            'click' => value(function () use ($orderRow) {
                $real = CommonService::getRedisCounter("ai_order_click_{$orderRow['_id']}");
                return strval((intval($orderRow['click'] + $real)));
            }),
            'love' => value(function () use ($orderRow) {
                $real = CommonService::getRedisCounter('ai_order_love_' . $orderRow['_id']);
                return strval($orderRow['love'] + $real);
            }),
            'favorite' => value(function () use ($orderRow) {
                $real = CommonService::getRedisCounter('ai_order_favorite_' . $orderRow['_id']);
                return strval($orderRow['favorite'] + $real);
            }),
            'comment' => value(function () use ($orderRow) {
                $real = CommonService::getRedisCounter('ai_comment_ok' . $orderRow['_id']);
                return strval($real);
            }),
            'has_love'     => AiLoveService::has($userId, $orderId) ? 'y' : 'n',
            'has_favorite' => AiFavoriteService::has($userId, $orderId) ? 'y' : 'n',
            'status'       => strval($orderRow['status']),
            'order_type'   => strval($orderRow['order_type']),
            'tpl_id'       => strval($orderRow['tpl_id']),
            'username'     => strval($orderRow['username']),
            'extra'        => value(function () use ($orderRow) {
                $extra = $orderRow['extra'];
                $ext   = FileUtil::getFileExt($extra['source_path']);
                if ($ext == 'png' || $ext == 'jpg') {
                    $extra['source_path'] = CommonService::getCdnUrl($extra['source_path']);
                }
                $targetExt = FileUtil::getFileExt($extra['target_path']);
                if (in_array($targetExt, ['png', 'jpg', 'm3u8'])) {
                    $extra['target_path'] = CommonService::getCdnUrl($extra['target_path']);
                }
                return $extra;
            }),
            'out_data' => AiService::parseOutData($orderRow),
            'amount'   => value(function () use ($orderRow, $userId) {
                if ($userId == $orderRow['user_id']) {
                    return strval($orderRow['amount']);
                }
                return strval(0);
            }),
            'show_at' => date('Y-m-d H:i', $orderRow['show_at'] ?: $orderRow['created_at'])
        ];
        return $result;
    }

    /**
     * 去点赞
     * @param                                   $userId
     * @param                                   $movieId
     * @return bool
     * @throws \App\Exception\BusinessException
     */
    public static function doLove($userId, $movieId)
    {
        return AiLoveService::do($userId, $movieId);
    }

    /**
     * 点赞列表
     * @param        $userId
     * @param  int   $page
     * @param  int   $pageSize
     * @return array
     */
    public static function getLoveList($userId, $page = 1, $pageSize = 20)
    {
        $ids = AiLoveService::getIds($userId, $page, $pageSize);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }

        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])], $userId)['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }

        return [
            'data'         => $data,
            'total'        => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size'    => strval($ids['page_size']),
            'last_page'    => strval($ids['last_page']),
        ];
    }

    /**
     * 去收藏 AI 作品
     * @param                                   $userId
     * @param                                   $movieId
     * @return bool
     * @throws \App\Exception\BusinessException
     */
    public static function doFavorite($userId, $movieId)
    {
        return AiFavoriteService::do($userId, $movieId);
    }

    /**
     * 收藏的作品列表
     * @param        $userId
     * @param        $page
     * @param        $pageSize
     * @return array
     */
    public static function getFavoriteList($userId, $page = 1, $pageSize = 20)
    {
        $ids = AiFavoriteService::getIds($userId, $page, $pageSize);
        foreach ($ids['ids'] as $key => $id) {
            if (empty($id)) {
                unset($ids['ids'][$key]);
            } else {
                $ids['ids'][$key] = strval($id);
            }
        }

        $data = [];
        if (!empty($ids['ids'])) {
            $data = self::doSearch(['ids' => join(',', $ids['ids']), 'page_size' => count($ids['ids'])], $userId)['data'];
            $data = CommonUtil::arraySort($data, 'id', $ids['ids']);
        }

        return [
            'data'         => $data,
            'total'        => strval($ids['total']),
            'current_page' => strval($ids['current_page']),
            'page_size'    => strval($ids['page_size']),
            'last_page'    => strval($ids['last_page']),
        ];
    }

    /**
     * 搜索
     * @param mixed      $filter
     * @param null|mixed $userId
     */
    public static function doSearch($filter = [], $userId = null)
    {
        $query               = [];
        $query['page']       = self::getRequest($filter, 'page', 'int', 1);
        $query['page_size']  = self::getRequest($filter, 'page_size', 'int', 12);
        $query['order_type'] = self::getRequest($filter, 'order_type', 'string', '');
        $query['home_id']    = self::getRequest($filter, 'home_id', 'string', '');
        $query['order']      = self::getRequest($filter, 'order', 'string', '');
        $query['status_all'] = self::getRequest($filter, 'status_all', 'int', 0);
        $query['status']     = self::getRequest($filter, 'status', 'int', 2);
        $query['keywords']   = self::getRequest($filter, 'keywords', 'string', '');
        $query['ids']        = self::getRequest($filter, 'ids', 'string', '');
        return AiService::doSearch($query, $userId);
    }

    /**
     * 删除订单
     * @param       $userId
     * @param       $orderIds
     * @return true
     */
    public static function delOrder($userId, $orderIds)
    {
        $userId = intval($userId);
        if ($orderIds == 'all') {
            AiOrderModel::update(['is_delete' => 1], ['user_id' => $userId]);
        } else {
            $ids = explode(',', $orderIds);
            foreach ($ids as $key => $id) {
                if (empty($id)) {
                    unset($ids[$key]);
                    continue;
                }
                $ids[$key] = intval($id);
            }
            $ids = array_values($ids);
            if (!empty($ids)) {
                AiOrderModel::update(['is_delete' => 1], ['_id' => ['$in' => $ids], 'user_id' => $userId]);
            }
        }
        return true;
    }
}
