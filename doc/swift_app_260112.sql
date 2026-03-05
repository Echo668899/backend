/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1_27017
 Source Server Type    : MongoDB
 Source Server Version : 60016
 Source Host           : 127.0.0.1:27017
 Source Schema         : swift_app_init

 Target Server Type    : MongoDB
 Target Server Version : 60016
 File Encoding         : 65001

 Date: 12/01/2026 21:26:58
*/


// ----------------------------
// Collection structure for account_log
// ----------------------------
db.getCollection("account_log").drop();
db.createCollection("account_log");
db.getCollection("account_log").createIndex({
    "order_sn": NumberInt("1")
}, {
    name: "index_order_sn"
});
db.getCollection("account_log").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("account_log").createIndex({
    username: NumberInt("1")
}, {
    name: "index_username"
});
db.getCollection("account_log").createIndex({
    "balance_field": NumberInt("1")
}, {
    name: "index_balance_field"
});

// ----------------------------
// Documents of account_log
// ----------------------------

// ----------------------------
// Collection structure for activity
// ----------------------------
db.getCollection("activity").drop();
db.createCollection("activity");
db.getCollection("activity").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of activity
// ----------------------------
db.getCollection("activity").insert([ {
    _id: "693750da84ce8ac17f05c4f2",
    name: "元旦元旦",
    description: "元旦元旦元旦元旦元旦元旦",
    "img_x": "/hc237/uploads/default/other/2025-12-09/b8fcb83ec41ba62fb3f17fa2ca343a9f.jpg",
    "start_time": NumberInt("1765209600"),
    "end_time": NumberInt("1766764800"),
    "tpl_id": "lottery",
    "tpl_config": {
        prizes: [
            {
                name: "会员1天",
                num: "1",
                rate: "40",
                image: "",
                type: "vip"
            },
            {
                name: "金币10个",
                num: "10",
                rate: "40",
                image: "",
                type: "point"
            },
            {
                name: "谢谢",
                num: "1",
                rate: "20",
                image: "",
                type: "none"
            },
            {
                name: "不谢谢",
                num: "1",
                rate: "1",
                image: "",
                type: "point"
            }
        ],
        "max_times_per_day": "1",
        mode: "chance",
        "mode_value": "1"
    },
    sort: NumberInt("0"),
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1765232858"),
    "updated_at": NumberInt("1767900833"),
    right: "all"
} ]);
db.getCollection("activity").insert([ {
    _id: "6951986350454127640d7442",
    name: "普通活动",
    description: "",
    "img_x": "",
    "start_time": NumberInt("1766937600"),
    "end_time": NumberInt("1769788800"),
    "tpl_id": "countdown",
    "tpl_config": {
        "is_show_time": "y",
        link: "buyvip://",
        "wait_time": "24",
        "show_time": "144",
        time: ""
    },
    sort: NumberInt("0"),
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1766955107"),
    "updated_at": NumberInt("1767900285"),
    right: "normal"
} ]);
db.getCollection("activity").insert([ {
    _id: "6960016150454127640d7443",
    name: "会员活动",
    description: "",
    "img_x": "",
    "start_time": NumberInt("1767888000"),
    "end_time": NumberInt("1770393600"),
    "tpl_id": "countdown",
    "tpl_config": {
        "is_show_time": "y",
        link: "inner://vip",
        "wait_time": "",
        "show_time": "",
        "end_type": "lt",
        time: "10"
    },
    sort: NumberInt("0"),
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1767899489"),
    "updated_at": NumberInt("1767900340"),
    right: "vip"
} ]);
db.getCollection("activity").insert([ {
    _id: "6960022f50454127640d7444",
    name: "全部用户活动",
    description: "",
    "img_x": "",
    "start_time": NumberInt("1767888000"),
    "end_time": NumberInt("1775836800"),
    "tpl_id": "countdown",
    "tpl_config": {
        "is_show_time": "y",
        link: "inner://vip",
        "wait_time": "",
        "show_time": "",
        time: ""
    },
    sort: NumberInt("0"),
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1767899695"),
    "updated_at": NumberInt("1767900361"),
    right: "all"
} ]);

// ----------------------------
// Collection structure for activity_lottery_chance
// ----------------------------
db.getCollection("activity_lottery_chance").drop();
db.createCollection("activity_lottery_chance");
db.getCollection("activity_lottery_chance").createIndex({
    "activity_id": NumberInt("1")
}, {
    name: "index_activity_id"
});
db.getCollection("activity_lottery_chance").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});

// ----------------------------
// Documents of activity_lottery_chance
// ----------------------------

// ----------------------------
// Collection structure for activity_lottery_log
// ----------------------------
db.getCollection("activity_lottery_log").drop();
db.createCollection("activity_lottery_log");
db.getCollection("activity_lottery_log").createIndex({
    "activity_id": NumberInt("1")
}, {
    name: "index_activity_id"
});
db.getCollection("activity_lottery_log").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("activity_lottery_log").createIndex({
    username: NumberInt("1")
}, {
    name: "index_username"
});
db.getCollection("activity_lottery_log").createIndex({
    "order_sn": NumberInt("1")
}, {
    name: "index_order_sn",
    unique: true
});
db.getCollection("activity_lottery_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of activity_lottery_log
// ----------------------------

// ----------------------------
// Collection structure for admin_log
// ----------------------------
db.getCollection("admin_log").drop();
db.createCollection("admin_log");
db.getCollection("admin_log").createIndex({
    "admin_id": NumberInt("1")
}, {
    name: "index_admin_id"
});
db.getCollection("admin_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of admin_log
// ----------------------------

// ----------------------------
// Collection structure for admin_role
// ----------------------------
db.getCollection("admin_role").drop();
db.createCollection("admin_role");

// ----------------------------
// Documents of admin_role
// ----------------------------
db.getCollection("admin_role").insert([ {
    _id: NumberInt("10"),
    name: "运营-管理",
    description: "所有权限,仅次管理员,不开技术相关配置,如广告位,后台用户,角色,权限,日志,高级设置",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763675556"),
    rights: [
        "/systemMain",
        "/systemHome",
        "/systemHour",
        "/systemAdv",
        "/systemAdvApp",
        "/systemChannel",
        "/systemMovie",
        "/systemComics",
        "/systemPost",
        "/systemNovel",
        "/systemAudio",
        "/systemFake",
        "/userMain",
        "/user",
        "/userUp",
        "/account",
        "/userGroup",
        "/userProduct",
        "/orderBuy",
        "/orderVip",
        "/orderPoint",
        "/orderCollection",
        "/userCode",
        "/userCodeLog",
        "/chatFeedback",
        "/quickReply",
        "/userWithdraw",
        "/movieMain",
        "/movie",
        "/movieWarehouse",
        "/movieCategory",
        "/movieTag",
        "/movieNav",
        "/movieBlock",
        "/movieSpecial",
        "/commentMovie",
        "/danmaku",
        "/movieKeywords",
        "/comicsMain",
        "/comics",
        "/comicsWarehouse",
        "/comicsTag",
        "/comicsNav",
        "/comicsBlock",
        "/commentComics",
        "/comicsKeywords",
        "/postMain",
        "/post",
        "/postWarehouse",
        "/postTag",
        "/postNav",
        "/postBlock",
        "/commentPost",
        "/postKeywords",
        "/novelMain",
        "/novel",
        "/novelWarehouse",
        "/novelTag",
        "/novelNav",
        "/novelBlock",
        "/commentNovel",
        "/novelKeywords",
        "/audioMain",
        "/audio",
        "/audioWarehouse",
        "/audioTag",
        "/audioNav",
        "/audioBlock",
        "/commentAudio",
        "/audioKeywords",
        "/aiMain",
        "/aiOrder",
        "/aiTpl",
        "/aiTag",
        "/aiNav",
        "/aiBlock",
        "/configAi",
        "/adv",
        "/advApp",
        "/article",
        "/configBase",
        "/configApp",
        "/domain",
        "/channelApk",
        "/logsSms",
        "/logsEmail"
    ]
} ]);
db.getCollection("admin_role").insert([ {
    _id: NumberInt("11"),
    name: "运营-高级",
    description: "仅次运营-管理,没有广告,设置APK,设置CDN权限",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763675577"),
    rights: [
        "/systemMain",
        "/systemHome",
        "/systemHour",
        "/systemAdv",
        "/systemAdvApp",
        "/systemChannel",
        "/systemMovie",
        "/systemComics",
        "/systemPost",
        "/systemNovel",
        "/systemAudio",
        "/systemFake",
        "/userMain",
        "/user",
        "/userUp",
        "/account",
        "/userGroup",
        "/userProduct",
        "/orderBuy",
        "/orderVip",
        "/orderPoint",
        "/orderCollection",
        "/userCode",
        "/userCodeLog",
        "/chatFeedback",
        "/quickReply",
        "/userWithdraw",
        "/movieMain",
        "/movie",
        "/movieWarehouse",
        "/movieCategory",
        "/movieTag",
        "/movieNav",
        "/movieBlock",
        "/movieSpecial",
        "/commentMovie",
        "/danmaku",
        "/movieKeywords",
        "/comicsMain",
        "/comics",
        "/comicsWarehouse",
        "/comicsTag",
        "/comicsNav",
        "/comicsBlock",
        "/commentComics",
        "/comicsKeywords",
        "/postMain",
        "/post",
        "/postWarehouse",
        "/postTag",
        "/postNav",
        "/postBlock",
        "/commentPost",
        "/postKeywords",
        "/novelMain",
        "/novel",
        "/novelWarehouse",
        "/novelTag",
        "/novelNav",
        "/novelBlock",
        "/commentNovel",
        "/novelKeywords",
        "/audioMain",
        "/audio",
        "/audioWarehouse",
        "/audioTag",
        "/audioNav",
        "/audioBlock",
        "/commentAudio",
        "/audioKeywords",
        "/aiMain",
        "/aiOrder",
        "/aiTpl",
        "/aiTag",
        "/aiNav",
        "/aiBlock",
        "/configAi",
        "/article"
    ]
} ]);
db.getCollection("admin_role").insert([ {
    _id: NumberInt("12"),
    name: "运营-普通",
    description: "仅次运营-高级,没有系统统计,会员套餐,金币套餐权限",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760983259"),
    rights: [
        "/user",
        "/userUp",
        "/account",
        "/orderBuy",
        "/orderVip",
        "/orderPoint",
        "/orderCollection",
        "/userCode",
        "/userCodeLog",
        "/chatFeedback",
        "/quickReply",
        "/movie",
        "/movieWarehouse",
        "/movieCategory",
        "/movieTag",
        "/movieSpecial",
        "/commentMovie",
        "/danmaku",
        "/movieKeywords",
        "/comics",
        "/comicsWarehouse",
        "/comicsTag",
        "/commentComics",
        "/comicsKeywords",
        "/post",
        "/postWarehouse",
        "/postTag",
        "/commentPost",
        "/postKeywords",
        "/novel",
        "/novelWarehouse",
        "/novelTag",
        "/commentNovel",
        "/novelKeywords",
        "/audio",
        "/audioWarehouse",
        "/audioTag",
        "/commentAudio",
        "/audioKeywords",
        "/aiOrder",
        "/aiTpl",
        "/aiTag",
        "/configAi"
    ]
} ]);
db.getCollection("admin_role").insert([ {
    _id: NumberInt("13"),
    name: "运营-基础",
    description: "仅次运营-普通,只有内容管理权限",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760983390"),
    rights: [
        "/movie",
        "/movieWarehouse",
        "/comics",
        "/comicsWarehouse",
        "/post",
        "/postWarehouse",
        "/novel",
        "/novelWarehouse",
        "/audio",
        "/audioWarehouse",
        "/aiOrder",
        "/aiTpl"
    ]
} ]);
db.getCollection("admin_role").insert([ {
    _id: NumberInt("20"),
    name: "渠道",
    description: "仅渠道统计,时段统计,日活统计",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760983466"),
    rights: [
        "/systemHour",
        "/systemDau",
        "/systemChannel",
        "/systemFake"
    ]
} ]);
db.getCollection("admin_role").insert([ {
    _id: NumberInt("30"),
    name: "商务",
    description: "仅授权营销管理",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760983554"),
    rights: [
        "/systemHome",
        "/systemDau",
        "/systemChannel",
        "/adv",
        "/advApp"
    ]
} ]);
db.getCollection("admin_role").insert([ {
    _id: NumberInt("40"),
    name: "客服",
    description: "仅授权用户列表,兑换码,兑换码记录,评论,弹幕",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761015191"),
    rights: [
        "/systemHour",
        "/user",
        "/account",
        "/orderBuy",
        "/orderVip",
        "/orderPoint",
        "/userCode",
        "/userCodeLog",
        "/chatFeedback",
        "/commentMovie",
        "/danmaku",
        "/commentComics",
        "/post",
        "/postWarehouse",
        "/commentPost",
        "/commentNovel",
        "/commentAudio",
        "/aiOrder"
    ]
} ]);
db.getCollection("admin_role").insert([ {
    _id: NumberInt("50"),
    name: "审计",
    "created_at": NumberInt("1735664400"),
    "is_disabled": NumberInt("0"),
    "updated_at": NumberInt("1763675658"),
    description: "仅渠道统计,时段统计,日活统计,广告统计",
    rights: [
        "/systemChannel",
        "/account",
        "/orderBuy",
        "/orderVip",
        "/orderPoint",
        "/userCodeLog",
        "/chatFeedback",
        "/userWithdraw"
    ]
} ]);
db.getCollection("admin_role").insert([ {
    _id: NumberInt("60"),
    name: "运营-数据",
    description: "仅数据查看权限",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761784154"),
    rights: [
        "/systemHome",
        "/systemHour",
        "/systemAdv",
        "/systemAdvApp",
        "/systemChannel"
    ]
} ]);

// ----------------------------
// Collection structure for admin_user
// ----------------------------
db.getCollection("admin_user").drop();
db.createCollection("admin_user");
db.getCollection("admin_user").createIndex({
    username: NumberInt("1")
}, {
    name: "index_username",
    unique: true
});

// ----------------------------
// Documents of admin_user
// ----------------------------
db.getCollection("admin_user").insert([ {
    _id: NumberInt("1"),
    username: "test",
    "real_name": "test",
    "google_code": "",
    "role_id": NumberInt("0"),
    "is_disabled": NumberInt("0"),
    email: "",
    password: "e61bc85bdaed8afb1c63d7109ac2281f",
    slat: "48523",
    "login_at": NumberInt("1767798755"),
    "login_ip": "172.238.15.194",
    "login_num": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("admin_user").insert([ {
    _id: NumberInt("2"),
    username: "test1",
    "real_name": "test1",
    "google_code": "",
    "role_id": NumberInt("0"),
    "is_disabled": NumberInt("0"),
    email: "",
    password: "e61bc85bdaed8afb1c63d7109ac2281f",
    slat: "48523",
    "login_at": NumberInt("1767798755"),
    "login_ip": "172.238.15.194",
    "login_num": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("admin_user").insert([ {
    _id: NumberInt("3"),
    username: "test2",
    "real_name": "test2",
    "google_code": "",
    "role_id": NumberInt("0"),
    "is_disabled": NumberInt("0"),
    email: "",
    password: "e61bc85bdaed8afb1c63d7109ac2281f",
    slat: "48523",
    "login_at": NumberInt("1767798755"),
    "login_ip": "172.238.15.194",
    "login_num": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("admin_user").insert([ {
    _id: NumberInt("4"),
    username: "test3",
    "real_name": "test3",
    "google_code": "",
    "role_id": NumberInt("0"),
    "is_disabled": NumberInt("0"),
    email: "",
    password: "e61bc85bdaed8afb1c63d7109ac2281f",
    slat: "48523",
    "login_at": NumberInt("1767798755"),
    "login_ip": "172.238.15.194",
    "login_num": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("admin_user").insert([ {
    _id: 5,
    username: "test4",
    "real_name": "test4",
    "google_code": "",
    "role_id": NumberInt("0"),
    "is_disabled": NumberInt("0"),
    email: "",
    password: "e61bc85bdaed8afb1c63d7109ac2281f",
    slat: "48523",
    "login_at": NumberInt("1767798755"),
    "login_ip": "172.238.15.194",
    "login_num": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);

// ----------------------------
// Collection structure for adv
// ----------------------------
db.getCollection("adv").drop();
db.createCollection("adv");
db.getCollection("adv").createIndex({
    "position_code": NumberInt("1")
}, {
    name: "index_position_code"
});

// ----------------------------
// Documents of adv
// ----------------------------
db.getCollection("adv").insert([ {
    _id: NumberInt("1"),
    name: "品质成就未来",
    description: "",
    "position_code": "app_data_list",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/ec7dec5eaf78e66b19a28a2572dc0bec.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "http://baidu.com",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760730807"),
    "updated_at": NumberInt("1760730807")
} ]);
db.getCollection("adv").insert([ {
    _id: NumberInt("2"),
    name: "生活更精彩",
    description: "",
    "position_code": "app_start",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/fe57aef58346637db0bb9044dafe1645.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "http://baidu.com",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760742624"),
    "updated_at": NumberInt("1760742624")
} ]);
db.getCollection("adv").insert([ {
    _id: NumberInt("3"),
    name: "真心不打折",
    description: "",
    "position_code": "app_start",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/803da2604185957af352da4a5084017a.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "http://a.com",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760742810"),
    "updated_at": NumberInt("1760742810")
} ]);
db.getCollection("adv").insert([ {
    _id: NumberInt("4"),
    name: "只要有梦想，就能闪闪发光！",
    description: "",
    "position_code": "app_layer",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/f9d011724d21a17a170ef3f7a5cf31cb.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "http://baidu.com",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760742872"),
    "updated_at": NumberInt("1760742872")
} ]);
db.getCollection("adv").insert([ {
    _id: NumberInt("5"),
    name: "今天也要元气满满地出发！",
    description: "",
    "position_code": "app_layer",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/a6877c9a191714ec1d6db87383b2f834.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "http://baidu.com",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760742912"),
    "updated_at": NumberInt("1760742912")
} ]);
db.getCollection("adv").insert([ {
    _id: NumberInt("6"),
    name: "喧嚣退散，静寂才是力量。",
    description: "",
    "position_code": "app_banner",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/6e130acee02256c4328ee2dd2e2df804.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "http://baidu.com",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760742943"),
    "updated_at": NumberInt("1760742943")
} ]);
db.getCollection("adv").insert([ {
    _id: NumberInt("7"),
    name: "相信魔法，相信自己！",
    description: "",
    "position_code": "app_banner",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/1856c94e83df139efd91315c9b8de3a9.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "https://www.youtube.com/watch?v=U_qbwdKQQ4Q&list=RDU_qbwdKQQ4Q&start_radio=1",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760742988"),
    "updated_at": NumberInt("1760742988")
} ]);
db.getCollection("adv").insert([ {
    _id: NumberInt("8"),
    name: "哼！才、才不是为你准备的呢",
    description: "",
    "position_code": "app_banner",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/cf0f0ef33710bc45641b2d595574f10a.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "http://baidu.com",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760743195"),
    "updated_at": NumberInt("1760743195")
} ]);
db.getCollection("adv").insert([ {
    _id: NumberInt("9"),
    name: "你要买就快点啦！我才不是在等你决定",
    description: "",
    "position_code": "app_banner",
    type: "image",
    right: "all",
    content: "/hc237/uploads/default/other/2025-10-18/5bfed64280f1e173332f3a033fa0b381.jpg",
    "start_time": NumberInt("1760716800"),
    "end_time": NumberInt("1792252800"),
    link: "inner://a/s",
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "show_time": NumberInt("0"),
    "created_at": NumberInt("1760743255"),
    "updated_at": NumberInt("1760743255")
} ]);

// ----------------------------
// Collection structure for adv_app
// ----------------------------
db.getCollection("adv_app").drop();
db.createCollection("adv_app");
db.getCollection("adv_app").createIndex({
    "is_hot": NumberInt("1")
}, {
    name: "index_is_hot"
});

// ----------------------------
// Documents of adv_app
// ----------------------------
db.getCollection("adv_app").insert([ {
    _id: NumberInt("18"),
    name: "珍珠直播",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-06-16/650f912059db57eb41dbdeafc64d0185.gif",
    "download_url": "https://74806.579ggtz26.com:8206/aq.html?q119",
    download: "4325345",
    description: "珍珠直播",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704015"),
    "updated_at": NumberInt("1764789674")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("19"),
    name: "免费黄游",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-06-09/8ae792b56b04f2dcd0506efac4042c77.gif",
    "download_url": "https://jytfhdgrsfdzd.com/1001",
    download: "2514211",
    description: "免费黄游",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704015"),
    "updated_at": NumberInt("1764789670")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("20"),
    name: "抖阴直播",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-05-23/7959129c21bda9522d66db655f0093fc.gif",
    "download_url": "https://oltk00cdy.dnxkgy64119.vip:39006/3_dydp/dydp2/houzi1.htm?kmz1595",
    download: "125411",
    description: "抖阴直播",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704015"),
    "updated_at": NumberInt("1764789666")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("21"),
    name: "放置传说",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-05-14/4976fc60cb2bd568172c53bbcd456079.jpg",
    "download_url": "https://m.xrapk123.com/dl.php?a=75",
    download: NumberInt("0"),
    description: "放置传说",
    sort: NumberInt("0"),
    "is_hot": NumberInt("0"),
    "created_at": NumberInt("1764704016"),
    "updated_at": NumberInt("1764704016")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("22"),
    name: "火影忍者",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-05-14/45525f1d3ab08edec7bddc88ed302d42.gif",
    "download_url": "https://ent.qpcvypbm.shop/?n=1903",
    download: NumberInt("2651422"),
    description: "火影忍者",
    sort: NumberInt("0"),
    "is_hot": NumberInt("0"),
    "created_at": NumberInt("1764704016"),
    "updated_at": NumberInt("1764704016")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("23"),
    name: "海角社区",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-06-19/f67ee65cc03e7b6997e14f8ca155540e.gif",
    "download_url": "https://gg.morkodruwemvrxufvzormbek.com/yj/33129/zjxhj539",
    download: NumberInt("2514221"),
    description: "海角社区",
    sort: NumberInt("0"),
    "is_hot": NumberInt("0"),
    "created_at": NumberInt("1764704016"),
    "updated_at": NumberInt("1764704016")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("24"),
    name: "91暗网",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-04-25/a73e54a21243c207941027d099dcd597.gif",
    "download_url": "https://gg.jevrodrevryc6sltothronex.com/hy/21391/jh91aw808",
    download: NumberInt("221122"),
    description: "91暗网",
    sort: NumberInt("0"),
    "is_hot": NumberInt("0"),
    "created_at": NumberInt("1764704016"),
    "updated_at": NumberInt("1764704016")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("25"),
    name: "半次元",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-04-25/a4302fab4e55db19c27e422337082d59.gif",
    "download_url": "https://gg.zantrimelfrovrxufvbnarnar.com/bk/33113/zjbcy1559",
    download: "25112",
    description: "半次元",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704016"),
    "updated_at": NumberInt("1764789679")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("26"),
    name: "樱花动漫",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-04-25/731a82b5278d85937977891cc4a18c03.gif",
    "download_url": "https://yhmh.zolvrxufvtrikjevropronkaz.com/yj/33105/jhyhmh1103",
    download: "221411",
    description: "樱花动漫",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704017"),
    "updated_at": NumberInt("1764789643")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("27"),
    name: "中文x站",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-07-08/a43da2419be02d19890c3716640fae7e.gif",
    "download_url": "https://gg.jevrodrevryc6sltothronex.com/bk/33141/jhawn810",
    download: "2554522",
    description: "X站中文版",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704017"),
    "updated_at": NumberInt("1764789647")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("28"),
    name: "涩漫天堂",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-11-07/508c194dbc70b4d944b5e5d2307ef3fc.jpg",
    "download_url": "https://d2f25zk34pau42.cloudfront.net?dc=lh123",
    download: "254556",
    description: "少女日记",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704017"),
    "updated_at": NumberInt("1764789652")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("29"),
    name: "海王视频",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-04-12/dc457595583ff098e0626496f3679292.gif",
    "download_url": "https://gg.jevrodrevryc6sltothronex.com/yj/43285/jhxgys2111z",
    download: "0",
    description: "海王",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704017"),
    "updated_at": NumberInt("1764789656")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("30"),
    name: "次元对决",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-01-28/7ccc3a4bd16c30b27c050a8076f76d0f.gif",
    "download_url": "https://ent.lybfmzar.shop/?n=1905",
    download: "0",
    description: "次元对决",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704017"),
    "updated_at": NumberInt("1764789661")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("31"),
    name: "红莲社区",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-07-08/3f179234f549ab485216fdddda13f757.gif",
    "download_url": "https://gg.fldrovrxufvphinovrabluvon.com/yj/7729/jhhlsqx",
    download: "0",
    description: "红莲",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704018"),
    "updated_at": NumberInt("1764789628")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("32"),
    name: "77直播",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/download/af/af6a4abaa63927a0096531bd2f3cdd14.gif",
    "download_url": "https://ytedr1217.fqropamours.com:51777/5f05.html",
    download: "254225",
    description: "77直播",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704018"),
    "updated_at": NumberInt("1764789632")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("33"),
    name: "51直播",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/download/5d/5d2438f15e466b870e8004ffcc90bf30.gif",
    "download_url": "https://iwgr587.p4768fp48kr.com:25118/c7f5.html",
    download: "2511232",
    description: "最潮",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704018"),
    "updated_at": NumberInt("1764789636")
} ]);
db.getCollection("adv_app").insert([ {
    _id: NumberInt("34"),
    name: "萝莉呦呦",
    position: [
        "recommend"
    ],
    image: "/hc237/g1-old/uploads/default/other/2025-07-10/c20befddb85dbb0ef3f82346924e913a.gif",
    "download_url": "https://gg.korzakkrutalqorvrxufvvim.com/hy/34797/zjmzsp43",
    download: "3648135",
    description: "UU视频",
    sort: NumberInt("0"),
    "is_hot": NumberInt("1"),
    "created_at": NumberInt("1764704018"),
    "updated_at": NumberInt("1764789639")
} ]);

// ----------------------------
// Collection structure for adv_pos
// ----------------------------
db.getCollection("adv_pos").drop();
db.createCollection("adv_pos");
db.getCollection("adv_pos").createIndex({
    code: NumberInt("1")
}, {
    name: "index_code",
    unique: true
});

// ----------------------------
// Documents of adv_pos
// ----------------------------
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("1"),
    code: "app_start",
    name: "App-启动页",
    "is_disabled": NumberInt("0"),
    height: NumberInt("1334"),
    width: NumberInt("750"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("2"),
    code: "app_layer",
    name: "App-首页弹窗",
    "is_disabled": NumberInt("0"),
    height: NumberInt("550"),
    width: NumberInt("550"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("3"),
    code: "app_banner",
    name: "App-首页-Banner",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("4"),
    code: "app_float_left",
    name: "App-页面浮动-左",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("5"),
    code: "app_float_right",
    name: "App-页面浮动-右",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("6"),
    code: "app_float_bottom",
    name: "App-页面浮动-底",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("7"),
    code: "app_float_bottom_left",
    name: "App-页面浮动-底左",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("8"),
    code: "app_float_bottom_right",
    name: "App-页面浮动-底右",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("9"),
    code: "app_block_list",
    name: "App-模块间广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("10"),
    code: "app_data_list",
    name: "App-数据流广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("11"),
    code: "app_info_top",
    name: "App-所有详情-顶部广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("12"),
    code: "app_info_left",
    name: "App-所有详情-左部广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("13"),
    code: "app_info_right",
    name: "App-所有详情-右部广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("14"),
    code: "app_info_bottom",
    name: "App-所有详情-底部广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("100"),
    code: "movie_info_play",
    name: "App-视频详情-播放器广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("101"),
    code: "comics_info_read",
    name: "App-漫画详情-阅读广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("adv_pos").insert([ {
    _id: NumberInt("102"),
    code: "novel_info_read",
    name: "App-小说详情-阅读广告",
    "is_disabled": NumberInt("0"),
    height: NumberInt("0"),
    width: NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);

// ----------------------------
// Collection structure for ai_block
// ----------------------------
db.getCollection("ai_block").drop();
db.createCollection("ai_block");
db.getCollection("ai_block").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of ai_block
// ----------------------------

// ----------------------------
// Collection structure for ai_favorite
// ----------------------------
db.getCollection("ai_favorite").drop();
db.createCollection("ai_favorite");
db.getCollection("ai_favorite").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("ai_favorite").createIndex({
    "folder_id": NumberInt("1")
}, {
    name: "index_folder_id"
});

// ----------------------------
// Documents of ai_favorite
// ----------------------------

// ----------------------------
// Collection structure for ai_keywords
// ----------------------------
db.getCollection("ai_keywords").drop();
db.createCollection("ai_keywords");
db.getCollection("ai_keywords").createIndex({
    "is_hot": NumberInt("1")
}, {
    name: "index_is_hot"
});

// ----------------------------
// Documents of ai_keywords
// ----------------------------

// ----------------------------
// Collection structure for ai_love
// ----------------------------
db.getCollection("ai_love").drop();
db.createCollection("ai_love");
db.getCollection("ai_love").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});

// ----------------------------
// Documents of ai_love
// ----------------------------

// ----------------------------
// Collection structure for ai_nav
// ----------------------------
db.getCollection("ai_nav").drop();
db.createCollection("ai_nav");
db.getCollection("ai_nav").createIndex({
    position: NumberInt("1")
}, {
    name: "index_position"
});

// ----------------------------
// Documents of ai_nav
// ----------------------------

// ----------------------------
// Collection structure for ai_order
// ----------------------------
db.getCollection("ai_order").drop();
db.createCollection("ai_order");
db.getCollection("ai_order").createIndex({
    "order_sn": NumberInt("1")
}, {
    name: "index_order_sn",
    unique: true
});
db.getCollection("ai_order").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("ai_order").createIndex({
    "order_type": NumberInt("1")
}, {
    name: "index_order_type"
});
db.getCollection("ai_order").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("ai_order").createIndex({
    "register_at": NumberInt("1")
}, {
    name: "index_register_at"
});
db.getCollection("ai_order").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("ai_order").createIndex({
    "tpl_id": NumberInt("1"),
    label: NumberInt("1")
}, {
    name: "index_tpl_id_label"
});
db.getCollection("ai_order").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});
db.getCollection("ai_order").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of ai_order
// ----------------------------

// ----------------------------
// Collection structure for ai_tag
// ----------------------------
db.getCollection("ai_tag").drop();
db.createCollection("ai_tag");

// ----------------------------
// Documents of ai_tag
// ----------------------------

// ----------------------------
// Collection structure for ai_tpl
// ----------------------------
db.getCollection("ai_tpl").drop();
db.createCollection("ai_tpl");
db.getCollection("ai_tpl").createIndex({
    type: NumberInt("1")
}, {
    name: "index_type"
});

// ----------------------------
// Documents of ai_tpl
// ----------------------------
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_01",
    adult: NumberInt("1"),
    config: {
        code: "01",
        img: "/md-204/common/78/7831965eaa21422651732258fc878c9e.jpg",
        "m3u8_url": "/md-204/m3u8-download/718/71843ecd7b629dc0e434df50cb26553b/121a24-m.m3u8"
    },
    "created_at": NumberInt("1763669309"),
    description: "",
    img: "/md-204/common/78/7831965eaa21422651732258fc878c9e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "01",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670385")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_02",
    adult: NumberInt("1"),
    config: {
        code: "02",
        img: "/md-204/common/b9/b92496faeb4bc9db3a7f858e46b19551.jpg",
        "m3u8_url": "/md-204/m3u8-download/4e1/4e19dbc33fbb37e7a8c5b116fe955968/1919db-m.m3u8"
    },
    "created_at": NumberInt("1763669309"),
    description: "",
    img: "/md-204/common/b9/b92496faeb4bc9db3a7f858e46b19551.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "02",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670385")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_03",
    adult: NumberInt("1"),
    config: {
        code: "03",
        img: "/md-204/common/46/4690d05b1d788aa8b95b291ee31986fb.jpg",
        "m3u8_url": "/md-204/m3u8-download/d79/d795d3f45b470b2f918c2c6aa1f81ee6/695037-m.m3u8"
    },
    "created_at": NumberInt("1763669309"),
    description: "",
    img: "/md-204/common/46/4690d05b1d788aa8b95b291ee31986fb.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "03",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670386")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_04",
    adult: NumberInt("1"),
    config: {
        code: "04",
        img: "/md-204/common/3e/3ee8d9845beeb56722de0d56acab6b9b.jpg",
        "m3u8_url": "/md-204/m3u8-download/9dc/9dc8bd310a27f63791e2d03ef9ec87bb/ebe523-m.m3u8"
    },
    "created_at": NumberInt("1763669309"),
    description: "",
    img: "/md-204/common/3e/3ee8d9845beeb56722de0d56acab6b9b.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "04",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670386")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_05",
    adult: NumberInt("1"),
    config: {
        code: "05",
        img: "/md-204/common/40/40e8346a863f729046ff286e4d9c9bdd.jpg",
        "m3u8_url": "/md-204/m3u8-download/e6f/e6f190fceaef7b7630a4dccd44174e02/18d701-m.m3u8"
    },
    "created_at": NumberInt("1763669309"),
    description: "",
    img: "/md-204/common/40/40e8346a863f729046ff286e4d9c9bdd.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "05",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670386")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_06",
    adult: NumberInt("1"),
    config: {
        code: "06",
        img: "/md-204/common/36/360b810b35caaa18682338f00c8dc53c.jpg",
        "m3u8_url": "/md-204/m3u8-download/de3/de394955081d83222b0746fb417455a0/3fc728-m.m3u8"
    },
    "created_at": NumberInt("1763669309"),
    description: "",
    img: "/md-204/common/36/360b810b35caaa18682338f00c8dc53c.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "06",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670386")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_07",
    adult: NumberInt("1"),
    config: {
        code: "07",
        img: "/md-204/common/64/64ad19865a785ffc77bbf99cc9cb6fe3.jpg",
        "m3u8_url": "/md-204/m3u8-download/10c/10c23fa332269dc3bc8ca68a24fa3ced/cbe816-m.m3u8"
    },
    "created_at": NumberInt("1763669310"),
    description: "",
    img: "/md-204/common/64/64ad19865a785ffc77bbf99cc9cb6fe3.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "07",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670386")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_08",
    adult: NumberInt("1"),
    config: {
        code: "08",
        img: "/md-204/common/13/131070d89a6fbb0976c7d42add9bbab6.jpg",
        "m3u8_url": "/md-204/m3u8-download/840/840b65919ee4280a891b3c41d32cd707/a9a021-m.m3u8"
    },
    "created_at": NumberInt("1763669310"),
    description: "",
    img: "/md-204/common/13/131070d89a6fbb0976c7d42add9bbab6.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "08",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670386")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_09",
    adult: NumberInt("1"),
    config: {
        code: "09",
        img: "/md-204/common/70/70536672f062f909352379684bbc9840.jpg",
        "m3u8_url": "/md-204/m3u8-download/4e4/4e47e151c949ab0f3466dd97c6b0fb1a/998cfd-m.m3u8"
    },
    "created_at": NumberInt("1763669310"),
    description: "",
    img: "/md-204/common/70/70536672f062f909352379684bbc9840.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "09",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670387")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_10",
    adult: NumberInt("1"),
    config: {
        code: "10",
        img: "/md-204/common/3b/3b688603adf7836bfcfe741f107a3303.jpg",
        "m3u8_url": "/md-204/m3u8-download/1d2/1d209722717cd70e67eeb7a25c667aef/02aff8-m.m3u8"
    },
    "created_at": NumberInt("1763669310"),
    description: "",
    img: "/md-204/common/3b/3b688603adf7836bfcfe741f107a3303.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "10",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670387")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_11",
    adult: NumberInt("1"),
    config: {
        code: "11",
        img: "/md-204/common/ad/ad1339d101828b37d8e374d9f0ade5c7.jpg",
        "m3u8_url": "/md-204/m3u8-download/b01/b01ab61fbaf9757d981a33e37fa84a8f/887673-m.m3u8"
    },
    "created_at": NumberInt("1763669310"),
    description: "",
    img: "/md-204/common/ad/ad1339d101828b37d8e374d9f0ade5c7.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "11",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670387")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_12",
    adult: NumberInt("1"),
    config: {
        code: "12",
        img: "/md-204/common/01/01ada7863f6e3e51156513beae1a050a.jpg",
        "m3u8_url": "/md-204/m3u8-download/267/26795816665351b3603b9ce82b10e3c3/772f2d-m.m3u8"
    },
    "created_at": NumberInt("1763669310"),
    description: "",
    img: "/md-204/common/01/01ada7863f6e3e51156513beae1a050a.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "12",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670387")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_13",
    adult: NumberInt("1"),
    config: {
        code: "13",
        img: "/md-204/common/4e/4ea5a8fae26f9b3b0dec3aadbb4baaf3.jpg",
        "m3u8_url": "/md-204/m3u8-download/947/9472a2af206741a7bc0d74bc24bc27b0/efc707-m.m3u8"
    },
    "created_at": NumberInt("1763669311"),
    description: "",
    img: "/md-204/common/4e/4ea5a8fae26f9b3b0dec3aadbb4baaf3.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "13",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670387")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_14",
    adult: NumberInt("1"),
    config: {
        code: "14",
        img: "/md-204/common/91/91bcffe44752b4f752770bfd2a49e55b.jpg",
        "m3u8_url": "/md-204/m3u8-download/23a/23a334a5bac68152d84c43d8932e74e7/9fad54-m.m3u8"
    },
    "created_at": NumberInt("1763669311"),
    description: "",
    img: "/md-204/common/91/91bcffe44752b4f752770bfd2a49e55b.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "14",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670388")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_15",
    adult: NumberInt("1"),
    config: {
        code: "15",
        img: "/md-204/common/bc/bce4a280131f646aa019f370612e1854.jpg",
        "m3u8_url": "/md-204/m3u8-download/e2f/e2f884d10c3d5269291611793d16ffc2/d70d26-m.m3u8"
    },
    "created_at": NumberInt("1763669311"),
    description: "",
    img: "/md-204/common/bc/bce4a280131f646aa019f370612e1854.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "15",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670388")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_16",
    adult: NumberInt("1"),
    config: {
        code: "16",
        img: "/md-204/common/76/76e847ee9f67f1f6fcd5f343bcefe064.jpg",
        "m3u8_url": "/md-204/m3u8-download/ac7/ac75a7defd66242c2d0239132e49ab27/4bc3a2-m.m3u8"
    },
    "created_at": NumberInt("1763669311"),
    description: "",
    img: "/md-204/common/76/76e847ee9f67f1f6fcd5f343bcefe064.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "16",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670388")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_17",
    adult: NumberInt("1"),
    config: {
        code: "17",
        img: "/md-204/common/07/077b298d9e8aaa2ecb89313393d2e39b.jpg",
        "m3u8_url": "/md-204/m3u8-download/e74/e74deba40f86a508458ed59c45cf9471/0c7c53-m.m3u8"
    },
    "created_at": NumberInt("1763669311"),
    description: "",
    img: "/md-204/common/07/077b298d9e8aaa2ecb89313393d2e39b.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "17",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670388")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_18",
    adult: NumberInt("1"),
    config: {
        code: "18",
        img: "/md-204/common/93/93c6ee02b1bffa25f5fce7abd22d3dd5.jpg",
        "m3u8_url": "/md-204/m3u8-download/d7b/d7ba6088a2c1cfdb8437dc5edc8a04cb/ad068f-m.m3u8"
    },
    "created_at": NumberInt("1763669312"),
    description: "",
    img: "/md-204/common/93/93c6ee02b1bffa25f5fce7abd22d3dd5.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "18",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670388")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_19",
    adult: NumberInt("1"),
    config: {
        code: "19",
        img: "/md-204/common/39/399c229040b6ec1558108f2e010393d7.jpg",
        "m3u8_url": "/md-204/m3u8-download/ef5/ef5e1bccc21ad76ebc0ce2b4b8932672/a6b8a3-m.m3u8"
    },
    "created_at": NumberInt("1763669312"),
    description: "",
    img: "/md-204/common/39/399c229040b6ec1558108f2e010393d7.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "19",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670389")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_20",
    adult: NumberInt("1"),
    config: {
        code: "20",
        img: "/md-204/common/a1/a1853a6de66cf81c462445a97a611744.jpg",
        "m3u8_url": "/md-204/m3u8-download/5e5/5e524292a0f882aebb3488a78dba75c3/da50b3-m.m3u8"
    },
    "created_at": NumberInt("1763669312"),
    description: "",
    img: "/md-204/common/a1/a1853a6de66cf81c462445a97a611744.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "20",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670389")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_21",
    adult: NumberInt("1"),
    config: {
        code: "21",
        img: "/md-204/common/65/65b60ba4080b52f9c0dd912663375715.jpg",
        "m3u8_url": "/md-204/m3u8-download/3ef/3efcc4e5d08eb3d791a27248e8694b2e/fa9fda-m.m3u8"
    },
    "created_at": NumberInt("1763669312"),
    description: "",
    img: "/md-204/common/65/65b60ba4080b52f9c0dd912663375715.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "21",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670389")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_22",
    adult: NumberInt("1"),
    config: {
        code: "22",
        img: "/md-204/common/8c/8c0a99f03a8b0dc193edd632ba9096a9.jpg",
        "m3u8_url": "/md-204/m3u8-download/ce1/ce1bdc0350a6bdb13d086163d97bba34/a23b59-m.m3u8"
    },
    "created_at": NumberInt("1763669312"),
    description: "",
    img: "/md-204/common/8c/8c0a99f03a8b0dc193edd632ba9096a9.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "22",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670389")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_23",
    adult: NumberInt("1"),
    config: {
        code: "23",
        img: "/md-204/common/e6/e60ed3894153bbb31d6b6b215b880ed6.jpg",
        "m3u8_url": "/md-204/m3u8-download/bec/bec1bbd03d7db3d4be8f6fea424fe8ce/706905-m.m3u8"
    },
    "created_at": NumberInt("1763669312"),
    description: "",
    img: "/md-204/common/e6/e60ed3894153bbb31d6b6b215b880ed6.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "23",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670389")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_24",
    adult: NumberInt("1"),
    config: {
        code: "24",
        img: "/md-204/common/dc/dc36724c197a68716049ca625a5f78a8.jpg",
        "m3u8_url": "/md-204/m3u8-download/d29/d2996b306540c8c804c88dfe6f979a00/bf2040-m.m3u8"
    },
    "created_at": NumberInt("1763669313"),
    description: "",
    img: "/md-204/common/dc/dc36724c197a68716049ca625a5f78a8.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "24",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670390")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_25",
    adult: NumberInt("1"),
    config: {
        code: "25",
        img: "/md-204/common/d6/d618da38d315746521118fd7cccbd4f4.jpg",
        "m3u8_url": "/md-204/m3u8-download/382/382342577aa87b6da418c3669fe5fe42/86f3f4-m.m3u8"
    },
    "created_at": NumberInt("1763669313"),
    description: "",
    img: "/md-204/common/d6/d618da38d315746521118fd7cccbd4f4.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "25",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670390")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_26",
    adult: NumberInt("1"),
    config: {
        code: "26",
        img: "/md-204/common/db/db7a3269caa30e1e57cabacd430096fc.jpg",
        "m3u8_url": "/md-204/m3u8-download/d66/d66f80c990e5c16afd2eddab142b1e35/93516f-m.m3u8"
    },
    "created_at": NumberInt("1763669313"),
    description: "",
    img: "/md-204/common/db/db7a3269caa30e1e57cabacd430096fc.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "26",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670390")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_27",
    adult: NumberInt("1"),
    config: {
        code: "27",
        img: "/md-204/common/71/7107220ea5672b4c24fbc421b95c5320.jpg",
        "m3u8_url": "/md-204/m3u8-download/37c/37ce0d4443c7a73480e8e6a08d13fd45/a8652b-m.m3u8"
    },
    "created_at": NumberInt("1763669313"),
    description: "",
    img: "/md-204/common/71/7107220ea5672b4c24fbc421b95c5320.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "27",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670390")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_28",
    adult: NumberInt("1"),
    config: {
        code: "28",
        img: "/md-204/common/cd/cd1aefced8b89a121f55cc5d2af3db7d.jpg",
        "m3u8_url": "/md-204/m3u8-download/d68/d68c1fb1d4d3a39f4465d8e535e37aa2/fddcea-m.m3u8"
    },
    "created_at": NumberInt("1763670297"),
    description: "",
    img: "/md-204/common/cd/cd1aefced8b89a121f55cc5d2af3db7d.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "28",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670390")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_29",
    adult: NumberInt("1"),
    config: {
        code: "29",
        img: "/md-204/common/90/90828f781078ed976bbc83537080a4c4.jpg",
        "m3u8_url": "/md-204/m3u8-download/029/0298c74753ce34ece2f0b74969bf6d9b/be4450-m.m3u8"
    },
    "created_at": NumberInt("1763670297"),
    description: "",
    img: "/md-204/common/90/90828f781078ed976bbc83537080a4c4.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "29",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670391")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_30",
    adult: NumberInt("1"),
    config: {
        code: "30",
        img: "/md-204/common/8a/8a91ce3846f96b012764f7eff71be297.jpg",
        "m3u8_url": "/md-204/m3u8-download/ec9/ec91f29c462ec3dc3233980bc3930e0f/b722db-m.m3u8"
    },
    "created_at": NumberInt("1763670297"),
    description: "",
    img: "/md-204/common/8a/8a91ce3846f96b012764f7eff71be297.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "30",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670391")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_31",
    adult: NumberInt("1"),
    config: {
        code: "31",
        img: "/md-204/common/c9/c9cc744cdd570e1e23425b40e0b1f214.jpg",
        "m3u8_url": "/md-204/m3u8-download/102/1024c63cb7348f239651a51e3ef63a5b/9dc87e-m.m3u8"
    },
    "created_at": NumberInt("1763670297"),
    description: "",
    img: "/md-204/common/c9/c9cc744cdd570e1e23425b40e0b1f214.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "31",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670391")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_32",
    adult: NumberInt("1"),
    config: {
        code: "32",
        img: "/md-204/common/c1/c1da4fd670b23bd2e8863210014779fe.jpg",
        "m3u8_url": "/md-204/m3u8-download/095/095217e3c17a325cfc4568ab90a1387b/e319f8-m.m3u8"
    },
    "created_at": NumberInt("1763670321"),
    description: "",
    img: "/md-204/common/c1/c1da4fd670b23bd2e8863210014779fe.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "32",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670391")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_33",
    adult: NumberInt("1"),
    config: {
        code: "33",
        img: "/md-204/common/07/07a22cd070bc34eb4ebb8e4df0822819.jpg",
        "m3u8_url": "/md-204/m3u8-download/37c/37c0e727ffb792566ba4effa5c0abec2/1bd1cf-m.m3u8"
    },
    "created_at": NumberInt("1763670321"),
    description: "",
    img: "/md-204/common/07/07a22cd070bc34eb4ebb8e4df0822819.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "33",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670391")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_34",
    adult: NumberInt("1"),
    config: {
        code: "34",
        img: "/md-204/common/5f/5f263660c52a51a9c2b998ee446fdd4a.jpg",
        "m3u8_url": "/md-204/m3u8-download/2b5/2b52c881ca10c31865fbc9a6589423ac/1c542e-m.m3u8"
    },
    "created_at": NumberInt("1763670321"),
    description: "",
    img: "/md-204/common/5f/5f263660c52a51a9c2b998ee446fdd4a.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "34",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670392")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_35",
    adult: NumberInt("1"),
    config: {
        code: "35",
        img: "/md-204/common/ed/ed931bf10196892cafca5ecbae1e1b4e.jpg",
        "m3u8_url": "/md-204/m3u8-download/c5c/c5cdad158f009de451ea538dd2d4adaa/8dbe67-m.m3u8"
    },
    "created_at": NumberInt("1763670321"),
    description: "",
    img: "/md-204/common/ed/ed931bf10196892cafca5ecbae1e1b4e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "35",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670392")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_36",
    adult: NumberInt("1"),
    config: {
        code: "36",
        img: "/md-204/common/c6/c66bce746dd45d088481bab4b63d835a.jpg",
        "m3u8_url": "/md-204/m3u8-download/b2a/b2a43c05e41b99c172fe589edf4c2c95/4c88d9-m.m3u8"
    },
    "created_at": NumberInt("1763670321"),
    description: "",
    img: "/md-204/common/c6/c66bce746dd45d088481bab4b63d835a.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "36",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670392")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_37",
    adult: NumberInt("1"),
    config: {
        code: "37",
        img: "/md-204/common/e9/e9fbc6f2890733b7cf70cc8ac8e8b4d3.jpg",
        "m3u8_url": "/md-204/m3u8-download/dd0/dd0a4261e31be475795648fcf791a296/91052a-m.m3u8"
    },
    "created_at": NumberInt("1763670322"),
    description: "",
    img: "/md-204/common/e9/e9fbc6f2890733b7cf70cc8ac8e8b4d3.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "37",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670392")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_38",
    adult: NumberInt("1"),
    config: {
        code: "38",
        img: "/md-204/common/bb/bbca68da9febd69b8f6644176f2046fb.jpg",
        "m3u8_url": "/md-204/m3u8-download/108/108b0866682f8c8ef1ac98329024d7b7/591033-m.m3u8"
    },
    "created_at": NumberInt("1763670322"),
    description: "",
    img: "/md-204/common/bb/bbca68da9febd69b8f6644176f2046fb.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "38",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670393")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_39",
    adult: NumberInt("1"),
    config: {
        code: "39",
        img: "/md-204/common/ff/ffe191de9cf1bd2f3c4f66576b3ea707.jpg",
        "m3u8_url": "/md-204/m3u8-download/c0a/c0a01bd87fda08400025858896a45a77/e36450-m.m3u8"
    },
    "created_at": NumberInt("1763670322"),
    description: "",
    img: "/md-204/common/ff/ffe191de9cf1bd2f3c4f66576b3ea707.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "39",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670393")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_40",
    adult: NumberInt("1"),
    config: {
        code: "40",
        img: "/md-204/common/fd/fdbdba5034ebffc0e1ab3e338a2fbdf5.jpg",
        "m3u8_url": "/md-204/m3u8-download/7a5/7a599db92326a6f43e0de738b84e97c8/231004-m.m3u8"
    },
    "created_at": NumberInt("1763670322"),
    description: "",
    img: "/md-204/common/fd/fdbdba5034ebffc0e1ab3e338a2fbdf5.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "40",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670394")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_41",
    adult: NumberInt("1"),
    config: {
        code: "41",
        img: "/md-204/common/de/de3a3cc84360401904ad5d4a9fd21666.jpg",
        "m3u8_url": "/md-204/m3u8-download/b23/b2377e07e6d5e3841e24c370f7884454/c644d4-m.m3u8"
    },
    "created_at": NumberInt("1763670322"),
    description: "",
    img: "/md-204/common/de/de3a3cc84360401904ad5d4a9fd21666.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "41",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670394")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_face_video_42",
    adult: NumberInt("1"),
    config: {
        code: "42",
        img: "/md-204/common/bb/bbdf9e87508b6e2d4c6c2dfa1c5933b7.jpg",
        "m3u8_url": "/md-204/m3u8-download/e0b/e0bec54bc8be06424c45d2f1fc079fb4/712ec0-m.m3u8"
    },
    "created_at": NumberInt("1763670322"),
    description: "",
    img: "/md-204/common/bb/bbdf9e87508b6e2d4c6c2dfa1c5933b7.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "42",
    sort: NumberInt("0"),
    type: "change_face_video",
    "updated_at": NumberInt("1763670394")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_rip_clothes",
    adult: NumberInt("1"),
    config: {
        code: "rip_clothes",
        "m3u8_url": "/md-204/m3u8-download/5de/5def9b939d9b0f8aa041531953da7050/d34570-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670322"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照，全身照",
    img: "/md-204/common/a9/a91595781c71c5c74b6f20d57e4f70c9.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "撕衣服",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670394")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_fuck_pussy",
    adult: NumberInt("1"),
    config: {
        code: "fuck_pussy",
        "m3u8_url": "/md-204/m3u8-download/819/8194b7edba1af2c703e1d404977646c8/cf65bb-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670323"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/68/68220173b46017ba30c3408b09f40dff.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "美鲍抽插",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670395")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_tittydrop",
    adult: NumberInt("1"),
    config: {
        code: "tittydrop",
        "m3u8_url": "/md-204/m3u8-download/13b/13b55ae0fbb90daf40f9d480801632a9/d7fc77-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670323"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照，全身照",
    img: "/md-204/common/7d/7ddd77224e8a6191366ab26994dab750.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "掀衣漏奶",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670395")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_tittysuck",
    adult: NumberInt("1"),
    config: {
        code: "tittysuck",
        "m3u8_url": "/md-204/m3u8-download/6a2/6a28e5ca47998307be6db959437e821e/3ca6f7-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670323"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照,裸体图片",
    img: "/md-204/common/d2/d299c5512892a7ecc8348a52c14b35ad.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "自吸奶",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670395")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_deep_blowjob",
    adult: NumberInt("1"),
    config: {
        code: "deep_blowjob",
        "m3u8_url": "/md-204/m3u8-download/546/5464ea62b30b34bdebf8ebad0c70cde6/df94db-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670323"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/3e/3e4f70aba0df2f900b3e3c4459bed92e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "深喉口交",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670395")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_shot_mouth",
    adult: NumberInt("1"),
    config: {
        code: "shot_mouth",
        "m3u8_url": "/md-204/m3u8-download/0c2/0c26e52f28a3eab3486ff507fd7a4bed/bbac5c-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670323"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/78/787743b0de1d8451ef428045c3f6a75f.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "口爆吞精",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670395")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_finger_fucking",
    adult: NumberInt("1"),
    config: {
        code: "finger_fucking",
        "m3u8_url": "/md-204/m3u8-download/701/701f17ae7db4e683422513c6d3686660/e5495a-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670323"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/29/296969aaa938a27a6fd87e0bf4f8c531.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "手指操逼",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670396")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_ahegao",
    adult: NumberInt("1"),
    config: {
        code: "ahegao",
        "m3u8_url": "/md-204/m3u8-download/2f7/2f7495727ab165e8c01c914b1d299373/34768d-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670324"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/ce/cef2c23ed95553b97fb07945325ff80f.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "啊嘿颜",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670396")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_cumshot",
    adult: NumberInt("1"),
    config: {
        code: "cumshot",
        "m3u8_url": "/md-204/m3u8-download/5ec/5ec736972dca6597332700b3702a55cd/f48edc-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670324"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/59/59174a7b5399c759782f6fb2ebe6805f.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "屌射",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670397")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_kissing",
    adult: NumberInt("1"),
    config: {
        code: "kissing",
        "m3u8_url": "/md-204/m3u8-download/937/937145091e22789d999809a6fffd2074/14fb7a-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670324"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/81/813ad3ca95e83c3422e716f0e55b4a4e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "舌吻",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670397")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_milk_spray",
    adult: NumberInt("1"),
    config: {
        code: "milk_spray",
        "m3u8_url": "/md-204/m3u8-download/918/9180bc99a0a174113a10dff6018c83f2/ea4c4c-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670324"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照,裸体图片",
    img: "/md-204/common/58/58885a2a9c6e86e148c07f27c1cb1ee6.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "喷奶",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670397")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_long_skirt_off_expose_buttocks",
    adult: NumberInt("1"),
    config: {
        code: "long_skirt_off_expose_buttocks",
        "m3u8_url": "/md-204/m3u8-download/5ba/5ba3046258d62667670502d5d4fde591/d707bd-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670324"),
    description: "图片 没有遮挡, 全身照，背身照片",
    img: "/md-204/common/9f/9f69eba67ed43bb71782b5f001fe4307.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "掀长裙露臀",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670397")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_two_men_tear_skirts",
    adult: NumberInt("1"),
    config: {
        code: "two_men_tear_skirts",
        "m3u8_url": "/md-204/m3u8-download/755/755df338d499840bc0db7a3a1d752250/633f77-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670324"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照，全身照",
    img: "/md-204/common/e9/e9554925a79bcd958b900c6160fef55a.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "双人撕裙",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670398")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_pants_off_expose_ass",
    adult: NumberInt("1"),
    config: {
        code: "pants_off_expose_ass",
        "m3u8_url": "/md-204/m3u8-download/210/210313c1ffebd08e5cb336106a7a85f6/e1c72d-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670324"),
    description: "图片 没有遮挡, 全身照，背身照片",
    img: "/md-204/common/4d/4dc638302030b1bde8c33b0242ec16fd.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "掀裤露臀",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670398")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_shapeshifting_mermaid",
    adult: NumberInt("1"),
    config: {
        code: "shapeshifting_mermaid",
        "m3u8_url": "/md-204/m3u8-download/ec7/ec7802001c6a715026e73cd5ed0b9695/81787f-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670325"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照，全身照",
    img: "/md-204/common/f4/f459ae6692df40ea85a61afc71674b09.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "变身美人鱼",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670399")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_groping_breasts",
    adult: NumberInt("1"),
    config: {
        code: "groping_breasts",
        "m3u8_url": "/md-204/m3u8-download/3ac/3ac8353186487f07ecf7115fd13aa726/b6dbdc-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670325"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照，全身照",
    img: "/md-204/common/85/8501f227f8ff1e65de1c8ad04fafa822.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "男人摸胸",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670399")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_cowgirl_sex",
    adult: NumberInt("1"),
    config: {
        code: "cowgirl_sex",
        "m3u8_url": "/md-204/m3u8-download/0a5/0a5fff12c197dd956fb4c475880d20c8/b54fb3-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670325"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/f1/f1e239f08343db0d6492f00070191b41.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "牛仔式做爱",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670399")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_cum_in_mouth",
    adult: NumberInt("1"),
    config: {
        code: "cum_in_mouth",
        "m3u8_url": "/md-204/m3u8-download/cd2/cd2f0bfcaa856c48828187aa59af1329/f9e1dc-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670325"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/17/17083330341955c2479bc75ee3c73575.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "颜射口爆",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670399")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_self_orgasm",
    adult: NumberInt("1"),
    config: {
        code: "self_orgasm",
        "m3u8_url": "/md-204/m3u8-download/6dc/6dce0707995373decd45fbd38b6e59ec/f66fee-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670325"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/53/5352a67a0ae72bda8bca724e0ba18908.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "自慰高潮",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670399")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_overlay_fuck",
    adult: NumberInt("1"),
    config: {
        code: "overlay_fuck",
        "m3u8_url": "/md-204/m3u8-download/40b/40b75328cc2ba451d703c4cbf032484f/7944ef-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670325"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/0a/0a67ad8e08797949b2b6816a2fe860e4.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "叠躺后入",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670400")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_breasts_fuck",
    adult: NumberInt("1"),
    config: {
        code: "breasts_fuck",
        "m3u8_url": "/md-204/m3u8-download/e12/e12839d952a9e3aea2f085157b20655c/46925a-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670326"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/72/72009e3aaa5e4b6c3fe6734a54badaad.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "巨乳胸推",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670401")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_footjob",
    adult: NumberInt("1"),
    config: {
        code: "footjob",
        "m3u8_url": "/md-204/m3u8-download/8d6/8d6c9a8fa8d3797a7876f05c1cd59305/5b6993-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670326"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/42/42615fe99308ff1b7a2602646c5dd698.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "美女足交",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670401")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_venom_transform",
    adult: NumberInt("1"),
    config: {
        code: "venom_transform",
        "m3u8_url": "/md-204/m3u8-download/b9a/b9a09ab7e5a36dcfa5b2d61ac3171c25/7f9c1d-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670326"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照，全身照",
    img: "/md-204/common/5b/5b289fe044e9f06c310197b1d2c60ae9.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "毒液变身",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670401")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_Split_legs",
    adult: NumberInt("1"),
    config: {
        code: "Split_legs",
        "m3u8_url": "/md-204/m3u8-download/547/5478c2353a562046b057b94ca2bc136b/69e955-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670326"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照，全身照",
    img: "/md-204/common/3a/3a7d0a4a3d00c1e7918581d43cd950f8.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "抬腿一字马看B",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670401")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_chest_ejaculation",
    adult: NumberInt("1"),
    config: {
        code: "chest_ejaculation",
        "m3u8_url": "/md-204/m3u8-download/b8b/b8b41a750fa50942722b9e3a09962241/0be073-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670326"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/e8/e8ba1c55c35f86cb065e75747ff72b95.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "胸推射精",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670402")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_sit_pos_dildo",
    adult: NumberInt("1"),
    config: {
        code: "sit_pos_dildo",
        "m3u8_url": "/md-204/m3u8-download/d08/d089498fa4589318ef5d22a7901c5901/8c32c6-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670326"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/23/23233dc01705ea0241d6b5989d595f1e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "坐姿假阳具",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670402")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "image_to_video_POV_ejaculation",
    adult: NumberInt("1"),
    config: {
        code: "POV_ejaculation",
        "m3u8_url": "/md-204/m3u8-download/dfa/dfa639c5ea41c8a1b998db5c075f7e5e/b319c4-m.m3u8",
        "img_num": "1"
    },
    "created_at": NumberInt("1763670326"),
    description: "图片和五官清晰, 没有遮挡, 正面图, 半身照",
    img: "/md-204/common/cb/cb9ee5cd4fa04d70da5b07bc3f5a1dd6.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "POV视角射精",
    sort: NumberInt("0"),
    type: "image_to_video",
    "updated_at": NumberInt("1763670402")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_Angelababy",
    adult: NumberInt("1"),
    config: {
        code: "Angelababy",
        "m3u8_url": "/md-204/m3u8-download/b91/b914afedbead35ae6182cba0617eed41/9f24ec-m.m3u8"
    },
    "created_at": NumberInt("1763670327"),
    description: "",
    img: "/md-204/common/dd/dd11d0491e6d3767c168d3534daee773.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "Angelababy",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670402")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_佟丽娅",
    adult: NumberInt("1"),
    config: {
        code: "佟丽娅",
        "m3u8_url": "/md-204/m3u8-download/af0/af0bd01af3cf80e150e79186237f7008/59d1ec-m.m3u8"
    },
    "created_at": NumberInt("1763670327"),
    description: "",
    img: "/md-204/common/ed/ed39737fdd392729ec2ba750ab9e10c2.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "佟丽娅",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670402")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_关晓彤",
    adult: NumberInt("1"),
    config: {
        code: "关晓彤",
        "m3u8_url": "/md-204/m3u8-download/2f3/2f3025ae32a8493e13ad80b739950fdd/549f20-m.m3u8"
    },
    "created_at": NumberInt("1763670327"),
    description: "",
    img: "/md-204/common/ad/ad9bcb57973d19e6aabd86f0d0a8ac29.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "关晓彤",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670403")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_刘德华",
    adult: NumberInt("1"),
    config: {
        code: "刘德华",
        "m3u8_url": "/md-204/m3u8-download/3ef/3ef478fb2a8f1e34a58845a5dec8f443/eb4848-m.m3u8"
    },
    "created_at": NumberInt("1763670327"),
    description: "",
    img: "/md-204/common/b0/b05755d947cf2da89c75dde9fd6d60c1.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "刘德华",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670403")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_刘诗诗",
    adult: NumberInt("1"),
    config: {
        code: "刘诗诗",
        "m3u8_url": "/md-204/m3u8-download/e19/e19edf1040a6dfd6588505ca7b503f52/93fbdf-m.m3u8"
    },
    "created_at": NumberInt("1763670327"),
    description: "",
    img: "/md-204/common/e9/e9b95893aa591589aeafd0cf09fbc301.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "刘诗诗",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670403")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_古力娜扎",
    adult: NumberInt("1"),
    config: {
        code: "古力娜扎",
        "m3u8_url": "/md-204/m3u8-download/8b2/8b2e3df31a93e63f943ca47ee95e861f/b0d38b-m.m3u8"
    },
    "created_at": NumberInt("1763670327"),
    description: "",
    img: "/md-204/common/39/39a8dd8139247e12df4017a74331d9c0.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "古力娜扎",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670404")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_吴彦祖",
    adult: NumberInt("1"),
    config: {
        code: "吴彦祖",
        "m3u8_url": "/md-204/m3u8-download/ed2/ed21979acee376d890628ec94faf226f/b3ccb6-m.m3u8"
    },
    "created_at": NumberInt("1763670328"),
    description: "",
    img: "/md-204/common/93/938ee9f409b0e622f12c30c8526c65d0.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "吴彦祖",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670404")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_吴梦梦",
    adult: NumberInt("1"),
    config: {
        code: "吴梦梦",
        "m3u8_url": "/md-204/m3u8-download/b19/b19ad82cb636bc57c8fe4bc98823f556/9a7689-m.m3u8"
    },
    "created_at": NumberInt("1763670328"),
    description: "",
    img: "/md-204/common/65/65e943a7515bba1f2396336f7cdb2ed2.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "吴梦梦",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670404")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_周星驰",
    adult: NumberInt("1"),
    config: {
        code: "周星驰",
        "m3u8_url": "/md-204/m3u8-download/5d8/5d81d32fcd78209bac37bf974c7cfb50/40ebe4-m.m3u8"
    },
    "created_at": NumberInt("1763670328"),
    description: "",
    img: "/md-204/common/e2/e21a23310100418038eb5be97b120856.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "周星驰",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670404")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_周杰伦",
    adult: NumberInt("1"),
    config: {
        code: "周杰伦",
        "m3u8_url": "/md-204/m3u8-download/0da/0da060cda37faabb926eaf70c88bec15/18871e-m.m3u8"
    },
    "created_at": NumberInt("1763670328"),
    description: "",
    img: "/md-204/common/ad/ad51b4ef42b16c9a3e6b15d716922b1e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "周杰伦",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670404")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_周雨彤",
    adult: NumberInt("1"),
    config: {
        code: "周雨彤",
        "m3u8_url": "/md-204/m3u8-download/263/2637daf41a01f5b16a6c07d253298c71/40ad2e-m.m3u8"
    },
    "created_at": NumberInt("1763670328"),
    description: "",
    img: "/md-204/common/a8/a80ca171b71821fffe1e0709343517a0.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "周雨彤",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670405")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_夏晴子",
    adult: NumberInt("1"),
    config: {
        code: "夏晴子",
        "m3u8_url": "/md-204/m3u8-download/8b8/8b8ee6f13bd6e35cdec14b50d50b901f/73b28f-m.m3u8"
    },
    "created_at": NumberInt("1763670328"),
    description: "",
    img: "/md-204/common/df/dfde1b5cf8dfc7c73ea2242ff78d97c2.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "夏晴子",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670405")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_太乙真人",
    adult: NumberInt("1"),
    config: {
        code: "太乙真人",
        "m3u8_url": "/md-204/m3u8-download/a7d/a7d78635e82b0c064282ae9afc7a5447/0125d5-m.m3u8"
    },
    "created_at": NumberInt("1763670328"),
    description: "",
    img: "/md-204/common/e6/e6ab8e1f294be8403efac324d4fa2c75.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "太乙真人",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670405")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_奶龙",
    adult: NumberInt("1"),
    config: {
        code: "奶龙",
        "m3u8_url": "/md-204/m3u8-download/7f7/7f735971eeb55d2b8e08cf86de4dc00d/deee2b-m.m3u8"
    },
    "created_at": NumberInt("1763670329"),
    description: "",
    img: "/md-204/common/ec/ecdda00c3c6bc7a2e6d0e55255c4a775.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "奶龙",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670405")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_孟若羽",
    adult: NumberInt("1"),
    config: {
        code: "孟若羽",
        "m3u8_url": "/md-204/m3u8-download/c6f/c6f5307de79d3c5a04dcde9b5ab95e88/c8de0f-m.m3u8"
    },
    "created_at": NumberInt("1763670329"),
    description: "",
    img: "/md-204/common/c0/c0fa48b72abf2037cfb5520d2f8a4235.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "孟若羽",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670405")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_张学友",
    adult: NumberInt("1"),
    config: {
        code: "张学友",
        "m3u8_url": "/md-204/m3u8-download/011/0116c96f4cfbbebc07661ab02ee49048/7ac3c9-m.m3u8"
    },
    "created_at": NumberInt("1763670329"),
    description: "",
    img: "/md-204/common/2f/2f9bcfd2a86573fe307cc86543e5dc5f.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "张学友",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670406")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_张家辉",
    adult: NumberInt("1"),
    config: {
        code: "张家辉",
        "m3u8_url": "/md-204/m3u8-download/99f/99ff2e9328a03da1c59d8483bdcc9d84/ff49c9-m.m3u8"
    },
    "created_at": NumberInt("1763670329"),
    description: "",
    img: "/md-204/common/51/519b07ca257a7aec572f1c325fc6e0b1.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "张家辉",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670406")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_御梦子",
    adult: NumberInt("1"),
    config: {
        code: "御梦子",
        "m3u8_url": "/md-204/m3u8-download/909/909a66f05ed069240d4efd7227609ca4/028ea7-m.m3u8"
    },
    "created_at": NumberInt("1763670329"),
    description: "",
    img: "/md-204/common/e4/e48e1b3213667664d3e4841d0e7e386e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "御梦子",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670406")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_杨幂",
    adult: NumberInt("1"),
    config: {
        code: "杨幂",
        "m3u8_url": "/md-204/m3u8-download/da5/da5f15c1bca81e2fdcf9b67304b7898f/5fa276-m.m3u8"
    },
    "created_at": NumberInt("1763670329"),
    description: "",
    img: "/md-204/common/b0/b02770a6271efcca478f3fb98308bdf4.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "杨幂",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670407")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_杨紫",
    adult: NumberInt("1"),
    config: {
        code: "杨紫",
        "m3u8_url": "/md-204/m3u8-download/dcd/dcd409a65435094f57405c90ec7f89a0/b102bd-m.m3u8"
    },
    "created_at": NumberInt("1763670330"),
    description: "",
    img: "/md-204/common/6b/6b2a93f7ec89825cc0b5dd8fa61faa3a.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "杨紫",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670407")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_林俊杰",
    adult: NumberInt("1"),
    config: {
        code: "林俊杰",
        "m3u8_url": "/md-204/m3u8-download/313/3135f76a27912aaae45d644c0fe11f81/4aeedb-m.m3u8"
    },
    "created_at": NumberInt("1763670330"),
    description: "",
    img: "/md-204/common/e6/e66b352f8111002813ebd81f887a3065.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "林俊杰",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670407")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_林志玲",
    adult: NumberInt("1"),
    config: {
        code: "林志玲",
        "m3u8_url": "/md-204/m3u8-download/39c/39c5cc6e1aabddd423e2b95535076d3c/d930d3-m.m3u8"
    },
    "created_at": NumberInt("1763670330"),
    description: "",
    img: "/md-204/common/f9/f9186a815fdf8ee5c953313cb64258c1.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "林志玲",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670407")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_江疏影",
    adult: NumberInt("1"),
    config: {
        code: "江疏影",
        "m3u8_url": "/md-204/m3u8-download/181/1818ee9adf16f93f0d1a8525eb08d09b/9020e0-m.m3u8"
    },
    "created_at": NumberInt("1763670330"),
    description: "",
    img: "/md-204/common/75/75fbab04e33170c20666f4b6ea2cf71e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "江疏影",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670407")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_沈腾",
    adult: NumberInt("1"),
    config: {
        code: "沈腾",
        "m3u8_url": "/md-204/m3u8-download/8a2/8a2181224bb8a3e8e6fd24a2c89e182d/a3b223-m.m3u8"
    },
    "created_at": NumberInt("1763670330"),
    description: "",
    img: "/md-204/common/2b/2bca40310581571ad447c2ba0d5f96af.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "沈腾",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670408")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_王心凌",
    adult: NumberInt("1"),
    config: {
        code: "王心凌",
        "m3u8_url": "/md-204/m3u8-download/1fa/1fa65e318dae1bbb5a772d22935f8780/a2228e-m.m3u8"
    },
    "created_at": NumberInt("1763670330"),
    description: "",
    img: "/md-204/common/4a/4add3bd5c6e8fbf52d5715281bd8fa01.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "王心凌",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670408")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_玩偶姐姐",
    adult: NumberInt("1"),
    config: {
        code: "玩偶姐姐",
        "m3u8_url": "/md-204/m3u8-download/796/7961c19b550ecae15a63d11166fe2380/d3348d-m.m3u8"
    },
    "created_at": NumberInt("1763670330"),
    description: "",
    img: "/md-204/common/d9/d92c4df3b99bd786c2497f90659921ef.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "玩偶姐姐",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670408")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_甄子丹",
    adult: NumberInt("1"),
    config: {
        code: "甄子丹",
        "m3u8_url": "/md-204/m3u8-download/341/341e47fa248ec619d4bf98138328072b/d5e256-m.m3u8"
    },
    "created_at": NumberInt("1763670331"),
    description: "",
    img: "/md-204/common/81/815146c3d74d8423acbaca102477128c.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "甄子丹",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670408")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_舌尖上的中国",
    adult: NumberInt("1"),
    config: {
        code: "舌尖上的中国",
        "m3u8_url": "/md-204/m3u8-download/a74/a74036ce31b8ddc30daeafff9f9cae1f/f12b3e-m.m3u8"
    },
    "created_at": NumberInt("1763670331"),
    description: "",
    img: "/md-204/common/c3/c3f5388a292064fe7aa222b3b64d9832.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "舌尖上的中国",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670408")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_苏畅",
    adult: NumberInt("1"),
    config: {
        code: "苏畅",
        "m3u8_url": "/md-204/m3u8-download/874/8741c7ee3f8a012e6fd90fdc40887655/ed5e52-m.m3u8"
    },
    "created_at": NumberInt("1763670331"),
    description: "",
    img: "/md-204/common/ca/ca0caabdb2bf4b8e70af8be1eea4e6d0.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "苏畅",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670409")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_范冰冰",
    adult: NumberInt("1"),
    config: {
        code: "范冰冰",
        "m3u8_url": "/md-204/m3u8-download/03d/03d1f8225f10297459112f272a069947/e58510-m.m3u8"
    },
    "created_at": NumberInt("1763670331"),
    description: "",
    img: "/md-204/common/fa/fa11de969121f5d222b7a8f84e7cdbf3.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "范冰冰",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670409")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_蒋介石",
    adult: NumberInt("1"),
    config: {
        code: "蒋介石",
        "m3u8_url": "/md-204/m3u8-download/67f/67f3b8760d49927ada6cbb0615711adb/be4e69-m.m3u8"
    },
    "created_at": NumberInt("1763670331"),
    description: "",
    img: "/md-204/common/44/447bbea1a292ad00033c46cbaa932dc9.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "蒋介石",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670409")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_谢霆锋",
    adult: NumberInt("1"),
    config: {
        code: "谢霆锋",
        "m3u8_url": "/md-204/m3u8-download/77c/77c89f0c179b55caea737c34616ecff2/682e92-m.m3u8"
    },
    "created_at": NumberInt("1763670331"),
    description: "",
    img: "/md-204/common/19/19efbd1c6b422c36db4165f601cbe2a9.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "谢霆锋",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670409")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_赵丽颖",
    adult: NumberInt("1"),
    config: {
        code: "赵丽颖",
        "m3u8_url": "/md-204/m3u8-download/f2a/f2aa4d8ec3882d5792532b6c80058be4/e26a54-m.m3u8"
    },
    "created_at": NumberInt("1763670332"),
    description: "",
    img: "/md-204/common/d0/d02e9b672f1ef155b542b9b8039c38c8.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "赵丽颖",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670410")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_赵本山",
    adult: NumberInt("1"),
    config: {
        code: "赵本山",
        "m3u8_url": "/md-204/m3u8-download/5a3/5a3b6facc58c5ebe057fdd498e21e388/142c65-m.m3u8"
    },
    "created_at": NumberInt("1763670332"),
    description: "",
    img: "/md-204/common/17/177284fef6473223ff39750dee6ff97f.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "赵本山",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670410")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_迪丽热巴",
    adult: NumberInt("1"),
    config: {
        code: "迪丽热巴",
        "m3u8_url": "/md-204/m3u8-download/2f8/2f8d5083473979f503cd3991bb8df3ef/9272bf-m.m3u8"
    },
    "created_at": NumberInt("1763670332"),
    description: "",
    img: "/md-204/common/e8/e89942b0934a281b2f2a57c9f132f6b4.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "迪丽热巴",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670411")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_邓紫棋",
    adult: NumberInt("1"),
    config: {
        code: "邓紫棋",
        "m3u8_url": "/md-204/m3u8-download/f02/f029c06ef9c370b392c80062b998d7bb/0ea213-m.m3u8"
    },
    "created_at": NumberInt("1763670332"),
    description: "",
    img: "/md-204/common/a5/a565c14c5236f04c32187b6df5f852ca.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "邓紫棋",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670411")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_邱淑贞",
    adult: NumberInt("1"),
    config: {
        code: "邱淑贞",
        "m3u8_url": "/md-204/m3u8-download/f73/f73bc409fdb2b8a50d2b37e39bb2c376/1dadd3-m.m3u8"
    },
    "created_at": NumberInt("1763670332"),
    description: "",
    img: "/md-204/common/bb/bb84e28c0c088ebb3f37d7abbf81ffce.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "邱淑贞",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670411")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_郭德纲",
    adult: NumberInt("1"),
    config: {
        code: "郭德纲",
        "m3u8_url": "/md-204/m3u8-download/81a/81aaa50778f1729a543ac3dfde2b2a0f/41414a-m.m3u8"
    },
    "created_at": NumberInt("1763670332"),
    description: "",
    img: "/md-204/common/9b/9b7dd777e7780b7e689d02fd160c12cd.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "郭德纲",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670411")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_陈冠希",
    adult: NumberInt("1"),
    config: {
        code: "陈冠希",
        "m3u8_url": "/md-204/m3u8-download/9d1/9d1bf2e6a472776c11dbd05d55e9a403/17349e-m.m3u8"
    },
    "created_at": NumberInt("1763670333"),
    description: "",
    img: "/md-204/common/7a/7a2814404436c64a0c66576b6de66db1.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "陈冠希",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670411")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_雷军",
    adult: NumberInt("1"),
    config: {
        code: "雷军",
        "m3u8_url": "/md-204/m3u8-download/e1d/e1de8d2f85b81f061daada62bfba7e72/7cf875-m.m3u8"
    },
    "created_at": NumberInt("1763670333"),
    description: "",
    img: "/md-204/common/10/10ab1916d1cb95c81fb9b563d28e80dc.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "雷军",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670412")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_太乙真人2",
    adult: NumberInt("1"),
    config: {
        code: "太乙真人2",
        "m3u8_url": "/md-204/m3u8-download/c3a/c3afde3b44de4d2bd95787c3da6bfabe/6ca4a2-m.m3u8"
    },
    "created_at": NumberInt("1763670333"),
    description: "",
    img: "/md-204/common/dd/ddf1ec21fc2691039d766e658b09c29a.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "太乙真人2",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670412")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_AD学姐",
    adult: NumberInt("1"),
    config: {
        code: "AD学姐",
        "m3u8_url": "/md-204/m3u8-download/e48/e4876cb6528f06ee5e351a02622e6e1e/baee40-m.m3u8"
    },
    "created_at": NumberInt("1763670333"),
    description: "",
    img: "/md-204/common/9f/9fb706f7b7e67b311ccb4379ede789a7.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "AD学姐",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670412")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_Q弹一只菊",
    adult: NumberInt("1"),
    config: {
        code: "Q弹一只菊",
        "m3u8_url": "/md-204/m3u8-download/476/476893e6fccff2f928ae8364713751d7/b889e2-m.m3u8"
    },
    "created_at": NumberInt("1763670333"),
    description: "",
    img: "/md-204/common/80/808cb4a9232dc742d4ec7d64927f7318.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "Q弹一只菊",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670412")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_七重极乐",
    adult: NumberInt("1"),
    config: {
        code: "七重极乐",
        "m3u8_url": "/md-204/m3u8-download/286/28616850cfc6f9d2b6c0985e3ad5ab4d/951eec-m.m3u8"
    },
    "created_at": NumberInt("1763670333"),
    description: "",
    img: "/md-204/common/df/df6ae4e5184e10427744bad32d821494.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "七重极乐",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670412")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_冰糖IO",
    adult: NumberInt("1"),
    config: {
        code: "冰糖IO",
        "m3u8_url": "/md-204/m3u8-download/049/0497886c792ef1d73bcb4900dea5e34f/e41205-m.m3u8"
    },
    "created_at": NumberInt("1763670334"),
    description: "",
    img: "/md-204/common/5c/5c60870fa43838edc787da83e1e02922.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "冰糖IO",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670413")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_南晚姐姐",
    adult: NumberInt("1"),
    config: {
        code: "南晚姐姐",
        "m3u8_url": "/md-204/m3u8-download/280/280269316e6a4de67c9bf2200703f0c0/450fa1-m.m3u8"
    },
    "created_at": NumberInt("1763670334"),
    description: "",
    img: "/md-204/common/8f/8f2bc4c58650bc4f8da85054c7fd6ad1.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "南晚姐姐",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670413")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_夜小雨",
    adult: NumberInt("1"),
    config: {
        code: "夜小雨",
        "m3u8_url": "/md-204/m3u8-download/638/6387915680228af64b56d8be38ab9585/03e183-m.m3u8"
    },
    "created_at": NumberInt("1763670334"),
    description: "",
    img: "/md-204/common/5a/5aae083625b36a03914d761ca505e43c.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "夜小雨",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670413")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_奶斯姐姐",
    adult: NumberInt("1"),
    config: {
        code: "奶斯姐姐",
        "m3u8_url": "/md-204/m3u8-download/bed/bedb354a98095d5f86295bcc310d3a21/cd7482-m.m3u8"
    },
    "created_at": NumberInt("1763670334"),
    description: "",
    img: "/md-204/common/76/76502f3032f977483ab79031139de775.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "奶斯姐姐",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670414")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_妹妹头",
    adult: NumberInt("1"),
    config: {
        code: "妹妹头",
        "m3u8_url": "/md-204/m3u8-download/bfb/bfb4163d53bf09c74309cb620854464c/0e838a-m.m3u8"
    },
    "created_at": NumberInt("1763670334"),
    description: "",
    img: "/md-204/common/95/95013a201e592290def9275ad663807d.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "妹妹头",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670414")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_少妇白洁叶倩彤",
    adult: NumberInt("1"),
    config: {
        code: "少妇白洁叶倩彤",
        "m3u8_url": "/md-204/m3u8-download/a80/a80f2c9980ca73112ffdac4f147649f2/ca4867-m.m3u8"
    },
    "created_at": NumberInt("1763670334"),
    description: "",
    img: "/md-204/common/cf/cfb2ce90b40093f1a574f41a638e8987.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "少妇白洁叶倩彤",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670415")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_voice_番茄酱",
    adult: NumberInt("1"),
    config: {
        code: "番茄酱",
        "m3u8_url": "/md-204/m3u8-download/f9b/f9b05fe5ae6f62bcaac06bc947bdaa14/05e1ac-m.m3u8"
    },
    "created_at": NumberInt("1763670335"),
    description: "",
    img: "/md-204/common/ae/ae7d28d9089f5a28c67728a25b91cd9e.jpg",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "番茄酱",
    sort: NumberInt("0"),
    type: "text_to_voice",
    "updated_at": NumberInt("1763670415")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_1",
    adult: NumberInt("1"),
    config: {
        code: "1",
        type: "real_person",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670335"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "真人风1",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670415")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_2",
    adult: NumberInt("1"),
    config: {
        code: "2",
        type: "real_person",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670335"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "真人风2",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670415")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_3",
    adult: NumberInt("1"),
    config: {
        code: "3",
        type: "real_person",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670335"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "真人风3",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670415")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_4",
    adult: NumberInt("1"),
    config: {
        code: "4",
        type: "real_person",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670335"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "真人风4",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670416")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_5",
    adult: NumberInt("1"),
    config: {
        code: "5",
        type: "real_person",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670335"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "韩国男风",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670416")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_6",
    adult: NumberInt("1"),
    config: {
        code: "6",
        type: "real_person",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670336"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "亚洲男风",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670416")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_7",
    adult: NumberInt("1"),
    config: {
        code: "7",
        type: "real_person",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670336"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "硬汉风",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670417")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_8",
    adult: NumberInt("1"),
    config: {
        code: "8",
        type: "real_person",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670336"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "真人风5",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670417")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_9",
    adult: NumberInt("0"),
    config: {
        code: "9",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670336"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "真人风6",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670417")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_10",
    adult: NumberInt("0"),
    config: {
        code: "10",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670336"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "真人风7",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670417")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_11",
    adult: NumberInt("1"),
    config: {
        code: "11",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670336"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风1",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670417")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_12",
    adult: NumberInt("1"),
    config: {
        code: "12",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670337"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风2",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670418")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_13",
    adult: NumberInt("1"),
    config: {
        code: "13",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670337"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风3",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670418")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_14",
    adult: NumberInt("1"),
    config: {
        code: "14",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670337"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风4",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670418")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_15",
    adult: NumberInt("1"),
    config: {
        code: "15",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670337"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风5",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670418")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_16",
    adult: NumberInt("1"),
    config: {
        code: "16",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670337"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画2.5D",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670419")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_17",
    adult: NumberInt("1"),
    config: {
        code: "17",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670337"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画男风",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670419")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_18",
    adult: NumberInt("1"),
    config: {
        code: "18",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670337"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风6",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670419")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_19",
    adult: NumberInt("1"),
    config: {
        code: "19",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670338"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风3D",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670419")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_20",
    adult: NumberInt("1"),
    config: {
        code: "20",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670338"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风7",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670420")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_21",
    adult: NumberInt("1"),
    config: {
        code: "21",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670338"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风8",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670420")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_22",
    adult: NumberInt("1"),
    config: {
        code: "22",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670338"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风9",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670420")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_23",
    adult: NumberInt("1"),
    config: {
        code: "23",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670338"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风10",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670420")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_24",
    adult: NumberInt("1"),
    config: {
        code: "24",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670338"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风12",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670420")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_25",
    adult: NumberInt("1"),
    config: {
        code: "25",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670339"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "漫画风13",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670421")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_26",
    adult: NumberInt("1"),
    config: {
        code: "26",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670339"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "日系女孩",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670421")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_27",
    adult: NumberInt("1"),
    config: {
        code: "27",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670339"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "亚洲女孩",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670421")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_28",
    adult: NumberInt("0"),
    config: {
        code: "28",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670339"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "写真风1",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670421")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "text_to_image_29",
    adult: NumberInt("0"),
    config: {
        code: "29",
        type: "acg",
        size: [
            {
                id: "720X720",
                name: "1:1"
            },
            {
                id: "720X1280",
                name: "16:9"
            },
            {
                id: "720X960",
                name: "4:3"
            }
        ]
    },
    "created_at": NumberInt("1763670339"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "写真风2",
    sort: NumberInt("0"),
    type: "text_to_image",
    "updated_at": NumberInt("1763670421")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "novel_method_1",
    adult: NumberInt("0"),
    config: {
        code: "method_1"
    },
    "created_at": NumberInt("1763670339"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "Ai作者(小萌)",
    sort: NumberInt("0"),
    type: "novel",
    "updated_at": NumberInt("1763670422")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "novel_method_2",
    adult: NumberInt("0"),
    config: {
        code: "method_2"
    },
    "created_at": NumberInt("1763670339"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "Ai作者(小艺)",
    sort: NumberInt("0"),
    type: "novel",
    "updated_at": NumberInt("1763670422")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_bikini",
    adult: NumberInt("0"),
    config: {
        code: "bikini"
    },
    "created_at": NumberInt("1763670340"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "比基尼",
    sort: NumberInt("0"),
    type: "change_dress",
    "updated_at": NumberInt("1763670422")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_sm",
    adult: NumberInt("0"),
    config: {
        code: "sm"
    },
    "created_at": NumberInt("1763670340"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "SM装",
    sort: NumberInt("0"),
    type: "change_dress",
    "updated_at": NumberInt("1763670423")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_sexy",
    adult: NumberInt("0"),
    config: {
        code: "sexy"
    },
    "created_at": NumberInt("1763670340"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "性感内衣",
    sort: NumberInt("0"),
    type: "change_dress",
    "updated_at": NumberInt("1763670423")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_tattoo",
    adult: NumberInt("0"),
    config: {
        code: "tattoo"
    },
    "created_at": NumberInt("1763670340"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "去衣纹身",
    sort: NumberInt("0"),
    type: "change_dress",
    "updated_at": NumberInt("1763670423")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_wedding",
    adult: NumberInt("0"),
    config: {
        code: "wedding"
    },
    "created_at": NumberInt("1763670340"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "婚纱照",
    sort: NumberInt("0"),
    type: "change_dress",
    "updated_at": NumberInt("1763670423")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_nurse",
    adult: NumberInt("0"),
    config: {
        code: "nurse"
    },
    "created_at": NumberInt("1763670340"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "护士装",
    sort: NumberInt("0"),
    type: "change_dress",
    "updated_at": NumberInt("1763670424")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_cartoon",
    adult: NumberInt("0"),
    config: {
        code: "cartoon"
    },
    "created_at": NumberInt("1763670340"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "人物转卡通",
    sort: NumberInt("0"),
    type: "change_dress",
    "updated_at": NumberInt("1763670425")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_lolita",
    adult: NumberInt("0"),
    config: {
        code: "lolita"
    },
    "created_at": NumberInt("1763670341"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "萝莉塔(需全身)",
    sort: NumberInt("0"),
    type: "change_dress",
    "updated_at": NumberInt("1763670425")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_bare_method_1",
    adult: NumberInt("0"),
    config: {
        code: "method_1"
    },
    "created_at": NumberInt("1763670341"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "模式一",
    sort: NumberInt("0"),
    type: "change_dress_bare",
    "updated_at": NumberInt("1763670425")
} ]);
db.getCollection("ai_tpl").insert([ {
    _id: "change_dress_bare_method_2",
    adult: NumberInt("0"),
    config: {
        code: "method_2"
    },
    "created_at": NumberInt("1763670341"),
    description: "",
    img: "",
    "is_disabled": NumberInt("0"),
    money: NumberInt("0"),
    name: "模式二",
    sort: NumberInt("0"),
    type: "change_dress_bare",
    "updated_at": NumberInt("1763670425")
} ]);

// ----------------------------
// Collection structure for app_log
// ----------------------------
db.getCollection("app_log").drop();
db.createCollection("app_log");
db.getCollection("app_log").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("app_log").createIndex({
    date: NumberInt("1")
}, {
    name: "index_date"
});
db.getCollection("app_log").createIndex({
    month: NumberInt("1")
}, {
    name: "index_month"
});
db.getCollection("app_log").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("app_log").createIndex({
    "jet_lag": NumberInt("1")
}, {
    name: "index_jet_lag"
});
db.getCollection("app_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});
db.getCollection("app_log").createIndex({
    "register_date": NumberInt("1")
}, {
    name: "index_register_date"
});

// ----------------------------
// Documents of app_log
// ----------------------------

// ----------------------------
// Collection structure for article
// ----------------------------
db.getCollection("article").drop();
db.createCollection("article");
db.getCollection("article").createIndex({
    "category_code": NumberInt("1")
}, {
    name: "index_category_code"
});

// ----------------------------
// Documents of article
// ----------------------------
db.getCollection("article").insert([ {
    _id: NumberInt("1"),
    title: "公告",
    content: "<p>公告</p><p><br/></p><p><a href=\\\"http://baidu.com\\\" target=\\\"_blank\\\">baidu</a></p>",
    img: null,
    "seo_keywords": null,
    "seo_description": null,
    url: "",
    "category_code": "announcement",
    "is_recommend": NumberInt("0"),
    sort: NumberInt("0"),
    click: NumberInt("0"),
    "created_at": NumberInt("1754526991"),
    "updated_at": NumberInt("1754700269")
} ]);

// ----------------------------
// Collection structure for article_category
// ----------------------------
db.getCollection("article_category").drop();
db.createCollection("article_category");
db.getCollection("article_category").createIndex({
    code: NumberInt("1")
}, {
    name: "index_code",
    unique: true
});

// ----------------------------
// Documents of article_category
// ----------------------------
db.getCollection("article_category").insert([ {
    _id: NumberInt("1"),
    name: "系统公告",
    code: "announcement",
    img: null,
    sort: NumberInt("0"),
    "parent_id": NumberInt("0"),
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);

// ----------------------------
// Collection structure for audio
// ----------------------------
db.getCollection("audio").drop();
db.createCollection("audio");
db.getCollection("audio").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});

// ----------------------------
// Documents of audio
// ----------------------------

// ----------------------------
// Collection structure for audio_block
// ----------------------------
db.getCollection("audio_block").drop();
db.createCollection("audio_block");
db.getCollection("audio_block").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of audio_block
// ----------------------------

// ----------------------------
// Collection structure for audio_chapter
// ----------------------------
db.getCollection("audio_chapter").drop();
db.createCollection("audio_chapter");
db.getCollection("audio_chapter").createIndex({
    "audio_id": NumberInt("1")
}, {
    name: "index_audio_id"
});

// ----------------------------
// Documents of audio_chapter
// ----------------------------

// ----------------------------
// Collection structure for audio_favorite
// ----------------------------
db.getCollection("audio_favorite").drop();
db.createCollection("audio_favorite");
db.getCollection("audio_favorite").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("audio_favorite").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of audio_favorite
// ----------------------------

// ----------------------------
// Collection structure for audio_keywords
// ----------------------------
db.getCollection("audio_keywords").drop();
db.createCollection("audio_keywords");
db.getCollection("audio_keywords").createIndex({
    "is_hot": NumberInt("1")
}, {
    name: "index_is_hot"
});

// ----------------------------
// Documents of audio_keywords
// ----------------------------

// ----------------------------
// Collection structure for audio_love
// ----------------------------
db.getCollection("audio_love").drop();
db.createCollection("audio_love");
db.getCollection("audio_love").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("audio_love").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of audio_love
// ----------------------------

// ----------------------------
// Collection structure for audio_nav
// ----------------------------
db.getCollection("audio_nav").drop();
db.createCollection("audio_nav");
db.getCollection("audio_nav").createIndex({
    position: NumberInt("1")
}, {
    name: "index_position"
});

// ----------------------------
// Documents of audio_nav
// ----------------------------

// ----------------------------
// Collection structure for audio_tag
// ----------------------------
db.getCollection("audio_tag").drop();
db.createCollection("audio_tag");

// ----------------------------
// Documents of audio_tag
// ----------------------------

// ----------------------------
// Collection structure for authority
// ----------------------------
db.getCollection("authority").drop();
db.createCollection("authority");
db.getCollection("authority").createIndex({
    key: NumberInt("1")
}, {
    name: "index_key",
    unique: true
});

// ----------------------------
// Documents of authority
// ----------------------------
db.getCollection("authority").insert([ {
    _id: NumberInt("1"),
    name: "系统统计",
    key: "/systemMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("1000"),
    "class_name": "layui-icon-home",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2"),
    name: "用户管理",
    key: "/userMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("990"),
    "class_name": "layui-icon-user",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("3"),
    name: "视频管理",
    key: "/movieMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("980"),
    "class_name": "layui-icon-video",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("4"),
    name: "漫画管理",
    key: "/comicsMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("970"),
    "class_name": "layui-icon-read",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("5"),
    name: "帖子管理",
    key: "/postMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("960"),
    "class_name": "layui-icon-list",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("6"),
    name: "小说管理",
    key: "/novelMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("950"),
    "class_name": "layui-icon-read",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("7"),
    name: "有声管理",
    key: "/audioMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("940"),
    "class_name": "layui-icon-music",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("8"),
    name: "AI管理",
    key: "/aiMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("930"),
    "class_name": "layui-icon-component",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("20"),
    name: "营销管理",
    key: "/advMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("920"),
    "class_name": "layui-icon-website",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("21"),
    name: "文章管理",
    key: "/articleMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("910"),
    "class_name": "layui-icon-app",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("22"),
    name: "系统用户",
    key: "/adminUserMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("900"),
    "class_name": "layui-icon-friends",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("23"),
    name: "系统设置",
    key: "/configMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("890"),
    "class_name": "layui-icon-set-fill",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("24"),
    name: "日志记录",
    key: "/logMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("880"),
    "class_name": "layui-icon-about",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("100"),
    name: "系统统计",
    key: "/systemHome",
    "parent_id": NumberInt("1"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/home",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("101"),
    name: "时段统计",
    key: "/systemHour",
    "parent_id": NumberInt("1"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/hour",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("102"),
    name: "广告统计",
    key: "/systemAdv",
    "parent_id": NumberInt("1"),
    sort: NumberInt("980"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/adv",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("103"),
    name: "应用广告统计",
    key: "/systemAdvApp",
    "parent_id": NumberInt("1"),
    sort: NumberInt("970"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/advApp",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("104"),
    name: "渠道统计",
    key: "/systemChannel",
    "parent_id": NumberInt("1"),
    sort: NumberInt("960"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/channel",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("105"),
    name: "视频统计",
    key: "/systemMovie",
    "parent_id": NumberInt("1"),
    sort: NumberInt("950"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/movie",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("106"),
    name: "漫画统计",
    key: "/systemComics",
    "parent_id": NumberInt("1"),
    sort: NumberInt("940"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/comics",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("107"),
    name: "帖子统计",
    key: "/systemPost",
    "parent_id": NumberInt("1"),
    sort: NumberInt("930"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/post",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("108"),
    name: "小说统计",
    key: "/systemNovel",
    "parent_id": NumberInt("1"),
    sort: NumberInt("920"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/novel",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("110"),
    name: "有声统计",
    key: "/systemAudio",
    "parent_id": NumberInt("1"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/system/audio",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("200"),
    name: "用户列表",
    key: "/user",
    "parent_id": NumberInt("2"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/user/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("201"),
    name: "UP主列表",
    key: "/userUp",
    "parent_id": NumberInt("2"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/userUp/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("202"),
    name: "余额日志",
    key: "/account",
    "parent_id": NumberInt("2"),
    sort: NumberInt("980"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/account/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("203"),
    name: "会员套餐",
    key: "/userGroup",
    "parent_id": NumberInt("2"),
    sort: NumberInt("970"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/userGroup/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("204"),
    name: "金币套餐",
    key: "/userProduct",
    "parent_id": NumberInt("2"),
    sort: NumberInt("960"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/userProduct/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("205"),
    name: "购买订单",
    key: "/orderBuy",
    "parent_id": NumberInt("2"),
    sort: NumberInt("950"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/order/buy",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("206"),
    name: "会员订单",
    key: "/orderVip",
    "parent_id": NumberInt("2"),
    sort: NumberInt("940"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/order/vip",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("207"),
    name: "金币订单",
    key: "/orderPoint",
    "parent_id": NumberInt("2"),
    sort: NumberInt("930"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/order/point",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("208"),
    name: "收款记录",
    key: "/orderCollection",
    "parent_id": NumberInt("2"),
    sort: NumberInt("920"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/order/collection",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("209"),
    name: "兑换码",
    key: "/userCode",
    "parent_id": NumberInt("2"),
    sort: NumberInt("200"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/userCode/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("210"),
    name: "兑换码记录",
    key: "/userCodeLog",
    "parent_id": NumberInt("2"),
    sort: NumberInt("190"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/userCodeLog/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("211"),
    name: "用户反馈",
    key: "/chatFeedback",
    "parent_id": NumberInt("2"),
    sort: NumberInt("100"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/chat/feedback",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("212"),
    name: "快捷回复",
    key: "/quickReply",
    "parent_id": NumberInt("2"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/quickReply/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("300"),
    name: "视频列表",
    key: "/movie",
    "parent_id": NumberInt("3"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/movie/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("301"),
    name: "视频仓库",
    key: "/movieWarehouse",
    "parent_id": NumberInt("3"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/movie/warehouse",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("302"),
    name: "分类管理",
    key: "/movieCategory",
    "parent_id": NumberInt("3"),
    sort: NumberInt("980"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/movieCategory/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("303"),
    name: "标签管理",
    key: "/movieTag",
    "parent_id": NumberInt("3"),
    sort: NumberInt("970"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/movieTag/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("304"),
    name: "顶部菜单",
    key: "/movieNav",
    "parent_id": NumberInt("3"),
    sort: NumberInt("960"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/movieNav/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("305"),
    name: "模块管理",
    key: "/movieBlock",
    "parent_id": NumberInt("3"),
    sort: NumberInt("950"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/movieBlock/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("306"),
    name: "专题管理",
    key: "/movieSpecial",
    "parent_id": NumberInt("3"),
    sort: NumberInt("940"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/movieSpecial/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("307"),
    name: "评论列表",
    key: "/commentMovie",
    "parent_id": NumberInt("3"),
    sort: NumberInt("930"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comment/list?object_type=movie",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("308"),
    name: "弹幕列表",
    key: "/danmaku",
    "parent_id": NumberInt("3"),
    sort: NumberInt("920"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/danmaku/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("309"),
    name: "关键字",
    key: "/movieKeywords",
    "parent_id": NumberInt("3"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/movieKeywords/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("400"),
    name: "漫画列表",
    key: "/comics",
    "parent_id": NumberInt("4"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comics/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("401"),
    name: "漫画仓库",
    key: "/comicsWarehouse",
    "parent_id": NumberInt("4"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comics/warehouse",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("402"),
    name: "标签管理",
    key: "/comicsTag",
    "parent_id": NumberInt("4"),
    sort: NumberInt("980"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comicsTag/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("403"),
    name: "顶部菜单",
    key: "/comicsNav",
    "parent_id": NumberInt("4"),
    sort: NumberInt("970"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comicsNav/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("404"),
    name: "模块管理",
    key: "/comicsBlock",
    "parent_id": NumberInt("4"),
    sort: NumberInt("960"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comicsBlock/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("405"),
    name: "评论列表",
    key: "/commentComics",
    "parent_id": NumberInt("4"),
    sort: NumberInt("950"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comment/list?object_type=comics",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("406"),
    name: "关键字",
    key: "/comicsKeywords",
    "parent_id": NumberInt("4"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comicsKeywords/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("500"),
    name: "帖子列表",
    key: "/post",
    "parent_id": NumberInt("5"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/post/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("501"),
    name: "帖子仓库",
    key: "/postWarehouse",
    "parent_id": NumberInt("5"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/post/warehouse",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("502"),
    name: "标签管理",
    key: "/postTag",
    "parent_id": NumberInt("5"),
    sort: NumberInt("980"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/postTag/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("503"),
    name: "顶部菜单",
    key: "/postNav",
    "parent_id": NumberInt("5"),
    sort: NumberInt("970"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/postNav/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("504"),
    name: "模块管理",
    key: "/postBlock",
    "parent_id": NumberInt("5"),
    sort: NumberInt("960"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/postBlock/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("505"),
    name: "评论列表",
    key: "/commentPost",
    "parent_id": NumberInt("5"),
    sort: NumberInt("950"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comment/list?object_type=post",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("506"),
    name: "关键字",
    key: "/postKeywords",
    "parent_id": NumberInt("5"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/postKeywords/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1762832738")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("600"),
    name: "小说列表",
    key: "/novel",
    "parent_id": NumberInt("6"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/novel/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("601"),
    name: "小说仓库",
    key: "/novelWarehouse",
    "parent_id": NumberInt("6"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/novel/warehouse",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("602"),
    name: "标签管理",
    key: "/novelTag",
    "parent_id": NumberInt("6"),
    sort: NumberInt("980"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/novelTag/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("603"),
    name: "顶部菜单",
    key: "/novelNav",
    "parent_id": NumberInt("6"),
    sort: NumberInt("970"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/novelNav/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("604"),
    name: "模块管理",
    key: "/novelBlock",
    "parent_id": NumberInt("6"),
    sort: NumberInt("960"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/novelBlock/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("605"),
    name: "评论列表",
    key: "/commentNovel",
    "parent_id": NumberInt("6"),
    sort: NumberInt("950"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comment/list?object_type=novel",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("606"),
    name: "关键字",
    key: "/novelKeywords",
    "parent_id": NumberInt("6"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/novelKeywords/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("700"),
    name: "有声列表",
    key: "/audio",
    "parent_id": NumberInt("7"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/audio/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("701"),
    name: "有声仓库",
    key: "/audioWarehouse",
    "parent_id": NumberInt("7"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/audio/warehouse",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("702"),
    name: "标签管理",
    key: "/audioTag",
    "parent_id": NumberInt("7"),
    sort: NumberInt("980"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/audioTag/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("703"),
    name: "顶部菜单",
    key: "/audioNav",
    "parent_id": NumberInt("7"),
    sort: NumberInt("970"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/audioNav/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("704"),
    name: "模块管理",
    key: "/audioBlock",
    "parent_id": NumberInt("7"),
    sort: NumberInt("960"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/audioBlock/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("705"),
    name: "评论列表",
    key: "/commentAudio",
    "parent_id": NumberInt("7"),
    sort: NumberInt("950"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/comment/list?object_type=audio",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("706"),
    name: "关键字",
    key: "/audioKeywords",
    "parent_id": NumberInt("7"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/audioKeywords/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("800"),
    name: "订单列表",
    key: "/aiOrder",
    "parent_id": NumberInt("8"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/aiOrder/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("801"),
    name: "模板管理",
    key: "/aiTpl",
    "parent_id": NumberInt("8"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/aiTpl/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("802"),
    name: "模板标签",
    key: "/aiTag",
    "parent_id": NumberInt("8"),
    sort: NumberInt("980"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/aiTag/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("803"),
    name: "顶部菜单",
    key: "/aiNav",
    "parent_id": NumberInt("8"),
    sort: NumberInt("970"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/aiNav/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("804"),
    name: "模块管理",
    key: "/aiBlock",
    "parent_id": NumberInt("8"),
    sort: NumberInt("960"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/aiBlock/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("805"),
    name: "基础设置",
    key: "/configAi",
    "parent_id": NumberInt("8"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/config/ai",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2000"),
    name: "广告列表",
    key: "/adv",
    "parent_id": NumberInt("20"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/adv/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2001"),
    name: "广告位",
    key: "/advPos",
    "parent_id": NumberInt("20"),
    sort: NumberInt("900"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/advPos/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2002"),
    name: "应用中心",
    key: "/advApp",
    "parent_id": NumberInt("20"),
    sort: NumberInt("800"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/advApp/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2100"),
    name: "文章列表",
    key: "/article",
    "parent_id": NumberInt("21"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/article/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2101"),
    name: "文章分类",
    key: "/articleCategory",
    "parent_id": NumberInt("21"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/articleCategory/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2200"),
    name: "用户列表",
    key: "/adminUser",
    "parent_id": NumberInt("22"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/adminUser/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2201"),
    name: "角色列表",
    key: "/adminRole",
    "parent_id": NumberInt("22"),
    sort: NumberInt("900"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/adminRole/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2202"),
    name: "系统资源",
    key: "/authority",
    "parent_id": NumberInt("22"),
    sort: NumberInt("800"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/authority/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2300"),
    name: "基础设置",
    key: "/configBase",
    "parent_id": NumberInt("23"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/config/base",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2301"),
    name: "高级设置",
    key: "/configOther",
    "parent_id": NumberInt("23"),
    sort: NumberInt("950"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/config/other",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2302"),
    name: "APP设置",
    key: "/configApp",
    "parent_id": NumberInt("23"),
    sort: NumberInt("900"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/config/app",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2303"),
    name: "APK设置",
    key: "/configApk",
    "parent_id": NumberInt("23"),
    sort: NumberInt("800"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/config/apk",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2304"),
    name: "CDN设置",
    key: "/configCdn",
    "parent_id": NumberInt("23"),
    sort: NumberInt("700"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/config/cdn",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2305"),
    name: "域名管理",
    key: "/domain",
    "parent_id": NumberInt("23"),
    sort: NumberInt("100"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/domain/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2306"),
    name: "渠道包管理",
    key: "/channelApk",
    "parent_id": NumberInt("23"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/channelApk/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2400"),
    name: "操作日志",
    key: "/logsAdmin",
    "parent_id": NumberInt("24"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/logs/admin",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2401"),
    name: "短信日志",
    key: "/logsSms",
    "parent_id": NumberInt("24"),
    sort: NumberInt("900"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/logs/sms",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2402"),
    name: "邮箱日志",
    key: "/logsEmail",
    "parent_id": NumberInt("24"),
    sort: NumberInt("800"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/logs/email",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("109"),
    name: "系统统计",
    key: "/systemFake",
    "parent_id": 1,
    sort: 0,
    "class_name": "",
    "is_menu": 1,
    link: "/system/fake",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("213"),
    name: "用户提现",
    key: "/userWithdraw",
    "parent_id": NumberInt("2"),
    sort: NumberInt("0"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/userWithdraw/list",
    "created_at": NumberInt("1763655202"),
    "updated_at": NumberInt("1763655202")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("2307"),
    name: "中心配置",
    key: "/configCenter",
    "parent_id": NumberInt("23"),
    sort: NumberInt("600"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/config/center",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("9"),
    name: "活动管理",
    key: "/activityMain",
    "parent_id": NumberInt("0"),
    sort: NumberInt("925"),
    "class_name": "layui-icon-gift",
    "is_menu": NumberInt("1"),
    link: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("901"),
    name: "活动管理",
    key: "/activity",
    "parent_id": NumberInt("9"),
    sort: NumberInt("1000"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/activity/list",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);
db.getCollection("authority").insert([ {
    _id: NumberInt("902"),
    name: "抽奖活动记录",
    key: "/activityLog",
    "parent_id": NumberInt("9"),
    sort: NumberInt("990"),
    "class_name": "",
    "is_menu": NumberInt("1"),
    link: "/activityLog/lottery",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1735664400")
} ]);

// ----------------------------
// Collection structure for channel
// ----------------------------
db.getCollection("channel").drop();
db.createCollection("channel");
db.getCollection("channel").createIndex({
    code: NumberInt("1")
}, {
    name: "index_code",
    unique: true
});
db.getCollection("channel").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of channel
// ----------------------------

// ----------------------------
// Collection structure for channel_apk
// ----------------------------
db.getCollection("channel_apk").drop();
db.createCollection("channel_apk");
db.getCollection("channel_apk").createIndex({
    code: NumberInt("1")
}, {
    name: "index_code",
    unique: true
});
db.getCollection("channel_apk").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of channel_apk
// ----------------------------

// ----------------------------
// Collection structure for chat
// ----------------------------
db.getCollection("chat").drop();
db.createCollection("chat");
db.getCollection("chat").createIndex({
    "from_id": NumberInt("1")
}, {
    name: "index_from_id"
});
db.getCollection("chat").createIndex({
    "to_id": NumberInt("1")
}, {
    name: "index_to_id"
});
db.getCollection("chat").createIndex({
    "chat_type": NumberInt("1")
}, {
    name: "index_chat_type"
});
db.getCollection("chat").createIndex({
    inbox: NumberInt("1")
}, {
    name: "index_inbox"
});
db.getCollection("chat").createIndex({
    "from_id": NumberInt("1"),
    "last_msg_role": NumberInt("1")
}, {
    name: "index_from_id_and_last_msg_role"
});
db.getCollection("chat").createIndex({
    "updated_at": NumberInt("1")
}, {
    name: "index_updated_at"
});

// ----------------------------
// Documents of chat
// ----------------------------

// ----------------------------
// Collection structure for chat_message
// ----------------------------
db.getCollection("chat_message").drop();
db.createCollection("chat_message");
db.getCollection("chat_message").createIndex({
    "chat_id": NumberInt("1")
}, {
    name: "index_chat_id"
});

// ----------------------------
// Documents of chat_message
// ----------------------------

// ----------------------------
// Collection structure for chat_read
// ----------------------------
db.getCollection("chat_read").drop();
db.createCollection("chat_read");
db.getCollection("chat_read").createIndex({
    "chat_id": NumberInt("1")
}, {
    name: "index_chat_id"
});
db.getCollection("chat_read").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});

// ----------------------------
// Documents of chat_read
// ----------------------------

// ----------------------------
// Collection structure for collection_ids
// ----------------------------
db.getCollection("collection_ids").drop();
db.createCollection("collection_ids");
db.getCollection("collection_ids").createIndex({
    name: NumberInt("1")
}, {
    name: "index_name",
    unique: true
});

// ----------------------------
// Documents of collection_ids
// ----------------------------
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6475c26b31d77c10a0f3448c"),
    name: "authority",
    id: NumberInt("3001")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6893e8ea8f450000ab000745"),
    name: "config",
    id: NumberInt("1000")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6893ec529b2d452c43c9a6bd"),
    name: "article_category",
    id: NumberInt("10")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6893f4bd8f450000ab000746"),
    name: "adv_pos",
    id: NumberInt("221")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6893f50f9b2d452c43c9b851"),
    name: "article",
    id: NumberInt("10")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6893f6128f450000ab000747"),
    name: "user",
    id: NumberInt("500000")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6893f6378f450000ab000748"),
    name: "admin_role",
    id: NumberInt("100")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6893f7619b2d452c43c9bc69"),
    name: "user_group",
    id: NumberInt("100")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6893f7a59b2d452c43c9bcba"),
    name: "user_product",
    id: NumberInt("100")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68ed97e42ea7819c3c5897aa"),
    name: "admin_log",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68ef4fbe36553867e21423d3"),
    name: "report_server_log",
    id: NumberInt("3")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68ef71a536553867e2143ff3"),
    name: "comics_nav",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68ef739a36553867e2144142"),
    name: "comics_block",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68ef7e9a36553867e2144480"),
    name: "movie_category",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68f29eb748cb30a73171997b"),
    name: "adv",
    id: NumberInt("163")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68f8825b48cb30a731737e21"),
    name: "movie_nav",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68f88d3748cb30a7317385e6"),
    name: "movie_block",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68fb838b48cb30a73174721d"),
    name: "user_order",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68fb8b0a48cb30a73174760c"),
    name: "user_recharge",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68fc448a7f4b00008b0058fb"),
    name: "novel_nav",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68fc44927f4b00008b0058fc"),
    name: "novel_block",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68fc44e27f4b00008b0058fd"),
    name: "audio_nav",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68fc44e97f4b00008b0058fe"),
    name: "audio_block",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68fc4fda48cb30a73174e620"),
    name: "chat_message",
    id: NumberInt("1155")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68ff77b448cb30a73175c64d"),
    name: "post_nav",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68ff79fb48cb30a73175c943"),
    name: "post_block",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("690e4383db462a2c4b3e5ac8"),
    name: "danmaku",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("691173dadb462a2c4b404da5"),
    name: "account_log",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("691f37b9db462a2c4b485ced"),
    name: "withdraw",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("6931eaccdb462a2c4b4f2f4a"),
    name: "adv_app",
    id: NumberInt("105")
} ]);

// ----------------------------
// Collection structure for collections
// ----------------------------
db.getCollection("collections").drop();
db.createCollection("collections");
db.getCollection("collections").createIndex({
    "order_sn": NumberInt("1")
}, {
    name: "index_order_sn",
    unique: true
});
db.getCollection("collections").createIndex({
    "trade_sn": NumberInt("1")
}, {
    name: "index_trade_sn"
});
db.getCollection("collections").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("collections").createIndex({
    "record_type": NumberInt("1")
}, {
    name: "index_record_type"
});
db.getCollection("collections").createIndex({
    "pay_date": NumberInt("1")
}, {
    name: "index_pay_date"
});
db.getCollection("collections").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("collections").createIndex({
    "register_at": NumberInt("1")
}, {
    name: "index_register_at"
});
db.getCollection("collections").createIndex({
    "order_at": NumberInt("1")
}, {
    name: "index_order_at"
});
db.getCollection("collections").createIndex({
    "jet_lag": NumberInt("1")
}, {
    name: "index_jet_lag"
});
db.getCollection("collections").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of collections
// ----------------------------

// ----------------------------
// Collection structure for comics
// ----------------------------
db.getCollection("comics").drop();
db.createCollection("comics");
db.getCollection("comics").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});

// ----------------------------
// Documents of comics
// ----------------------------

// ----------------------------
// Collection structure for comics_block
// ----------------------------
db.getCollection("comics_block").drop();
db.createCollection("comics_block");
db.getCollection("comics_block").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of comics_block
// ----------------------------

// ----------------------------
// Collection structure for comics_chapter
// ----------------------------
db.getCollection("comics_chapter").drop();
db.createCollection("comics_chapter");
db.getCollection("comics_chapter").createIndex({
    "comics_id": NumberInt("1")
}, {
    name: "index_comics_id"
});

// ----------------------------
// Documents of comics_chapter
// ----------------------------

// ----------------------------
// Collection structure for comics_favorite
// ----------------------------
db.getCollection("comics_favorite").drop();
db.createCollection("comics_favorite");
db.getCollection("comics_favorite").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("comics_favorite").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of comics_favorite
// ----------------------------

// ----------------------------
// Collection structure for comics_keywords
// ----------------------------
db.getCollection("comics_keywords").drop();
db.createCollection("comics_keywords");
db.getCollection("comics_keywords").createIndex({
    "is_hot": NumberInt("1")
}, {
    name: "index_is_hot"
});

// ----------------------------
// Documents of comics_keywords
// ----------------------------

// ----------------------------
// Collection structure for comics_love
// ----------------------------
db.getCollection("comics_love").drop();
db.createCollection("comics_love");
db.getCollection("comics_love").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("comics_love").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of comics_love
// ----------------------------

// ----------------------------
// Collection structure for comics_nav
// ----------------------------
db.getCollection("comics_nav").drop();
db.createCollection("comics_nav");
db.getCollection("comics_nav").createIndex({
    position: NumberInt("1")
}, {
    name: "index_position"
});

// ----------------------------
// Documents of comics_nav
// ----------------------------

// ----------------------------
// Collection structure for comics_tag
// ----------------------------
db.getCollection("comics_tag").drop();
db.createCollection("comics_tag");

// ----------------------------
// Documents of comics_tag
// ----------------------------

// ----------------------------
// Collection structure for comment
// ----------------------------
db.getCollection("comment").drop();
db.createCollection("comment");
db.getCollection("comment").createIndex({
    "object_id": NumberInt("1")
}, {
    name: "index_object_id"
});
db.getCollection("comment").createIndex({
    "parent_id": NumberInt("1")
}, {
    name: "index_parent_id"
});
db.getCollection("comment").createIndex({
    "comment_id": NumberInt("1")
}, {
    name: "index_comment_id"
});
db.getCollection("comment").createIndex({
    "from_uid": NumberInt("1")
}, {
    name: "index_from_uid"
});
db.getCollection("comment").createIndex({
    "to_uid": NumberInt("1")
}, {
    name: "index_to_uid"
});
db.getCollection("comment").createIndex({
    "object_type": NumberInt("1")
}, {
    name: "index_object_type"
});
db.getCollection("comment").createIndex({
    "comment_type": NumberInt("1")
}, {
    name: "index_comment_type"
});

// ----------------------------
// Documents of comment
// ----------------------------

// ----------------------------
// Collection structure for comment_love
// ----------------------------
db.getCollection("comment_love").drop();
db.createCollection("comment_love");
db.getCollection("comment_love").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});

// ----------------------------
// Documents of comment_love
// ----------------------------

// ----------------------------
// Collection structure for config
// ----------------------------
db.getCollection("config").drop();
db.createCollection("config");
db.getCollection("config").createIndex({
    code: NumberInt("1")
}, {
    name: "index_code",
    unique: true
});
db.getCollection("config").createIndex({
    group: NumberInt("1")
}, {
    name: "index_group"
});

// ----------------------------
// Documents of config
// ----------------------------
db.getCollection("config").insert([ {
    _id: NumberInt("1"),
    code: "site_title",
    name: "首页标题",
    type: NumberInt("1"),
    value: "Swift-APP框架",
    values: "",
    group: "base",
    sort: NumberInt("1000"),
    help: "设定后尽量不要去修改,修改后会影响SEO效果",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("2"),
    code: "keywords",
    name: "关键字",
    type: NumberInt("1"),
    value: "Swift-APP框架",
    values: "",
    group: "base",
    sort: NumberInt("990"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("3"),
    code: "description",
    name: "描述",
    type: NumberInt("2"),
    value: "Swift-APP框架",
    values: "",
    group: "base",
    sort: NumberInt("980"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("4"),
    code: "group_link",
    name: "官方社区",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("900"),
    help: "官方社区",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("5"),
    code: "service_link",
    name: "客服链接",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("890"),
    help: "可配置tg和土豆链接",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("6"),
    code: "service_email",
    name: "官方邮箱",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("880"),
    help: "客服邮箱",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("7"),
    code: "permanent_url",
    name: "永久网址",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("870"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("8"),
    code: "business_cooperation",
    name: "商务合作",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("860"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("9"),
    code: "channel_cooperation",
    name: "渠道合作",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("850"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("10"),
    code: "place_ad",
    name: "投放广告",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("840"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("11"),
    code: "contact_us",
    name: "联系我们",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("830"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("12"),
    code: "backup_url",
    name: "备用网址",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "base",
    sort: NumberInt("100"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401625")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("101"),
    code: "system_name",
    name: "管理系统名称",
    type: NumberInt("1"),
    value: "Swift-APP框架",
    values: "",
    group: "other",
    sort: NumberInt("1000"),
    help: "设置管理系统名称",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("102"),
    code: "site_url",
    name: "最新网址",
    type: NumberInt("1"),
    value: "https://www.swift.com",
    values: "",
    group: "other",
    sort: NumberInt("990"),
    help: "格式为：http://www.xxx.com",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("103"),
    code: "domains",
    name: "落地页最新域名",
    type: NumberInt("2"),
    value: "www.baidu.com\nwww.google.com\nwww.baidu.com\nwww.google.com\nwww.baidu.com\nwww.google.com",
    values: "",
    group: "other",
    sort: NumberInt("980"),
    help: "一行一个",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("104"),
    code: "static_version",
    name: "静态资源版本号",
    type: NumberInt("1"),
    value: "20250101",
    values: "",
    group: "other",
    sort: NumberInt("970"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("105"),
    code: "channel_domains",
    name: "渠道最新域名",
    type: NumberInt("2"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("960"),
    help: "一行一个",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("106"),
    code: "private_url",
    name: "保护域名",
    type: NumberInt("2"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("950"),
    help: "受保护的域名,只是用于做跳转,每行为一个",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("107"),
    code: "video_header_domain",
    name: "防盗链域名",
    type: NumberInt("1"),
    value: "http://www.qq.com",
    values: "",
    group: "other",
    sort: NumberInt("940"),
    help: "设置后自动在header中加入,防盗链使用",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("108"),
    code: "pay_notice_url",
    name: "支付通知",
    type: NumberInt("1"),
    value: "http://ip:8088",
    values: "",
    group: "other",
    sort: NumberInt("930"),
    help: "支付通知url",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("109"),
    code: "mms_url",
    name: "管理系统-地址",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("800"),
    help: "管理系统地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("110"),
    code: "mms_appid",
    name: "管理系统-AppId",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("790"),
    help: "管理系统appid",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("111"),
    code: "mms_appkey",
    name: "管理系统-AppKey",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("780"),
    help: "管理系统appkey",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("112"),
    code: "agent_url",
    name: "代理系统V2-地址",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("770"),
    help: "代理系统V2-地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("113"),
    code: "agent_appid",
    name: "代理系统V2-AppId",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("760"),
    help: "代理系统V2-AppId",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("114"),
    code: "agent_appkey",
    name: "代理系统V2-AppKey",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("750"),
    help: "代理系统V2-AppKey",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("115"),
    code: "channel_system_url",
    name: "代理系统V3-地址",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("740"),
    help: "代理系统V3-地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("116"),
    code: "channel_system_app_id",
    name: "代理系统V3-AppId",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("730"),
    help: "代理系统V3-AppId",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("117"),
    code: "channel_system_app_key",
    name: "代理系统V3-AppKey",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("720"),
    help: "代理系统V3-AppKey",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("118"),
    code: "wss_url",
    name: "分析系统-地址",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("700"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("119"),
    code: "wss_app_id",
    name: "分析系统-Appid",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("690"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("120"),
    code: "wss_app_key",
    name: "分析系统-Appkey",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("680"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("121"),
    code: "wss_app_common_key",
    name: "分析系统-公共key",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("670"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("123"),
    code: "count_code",
    name: "统计代码",
    type: NumberInt("2"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("0"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("124"),
    code: "project_group_id",
    name: "维护群ID",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("0"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("125"),
    code: "project_bot_token",
    name: "机器人token",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "other",
    sort: NumberInt("0"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("126"),
    code: "whitelist_ip",
    name: "后台白名单",
    type: NumberInt("2"),
    value: "127.0.0.1",
    values: "",
    group: "other",
    sort: NumberInt("0"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1761743400")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("200"),
    code: "cdn_drive_image",
    name: "图片CDN驱动",
    type: NumberInt("3"),
    value: "free",
    values: "tencent|腾讯;kingshan|金山;aws|AWS;free|Free",
    group: "cdn",
    sort: NumberInt("1000"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401934")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("201"),
    code: "cdn_drive_video",
    name: "视频CDN驱动",
    type: NumberInt("3"),
    value: "tencent",
    values: "tencent|腾讯;kingshan|金山;aws|AWS;free|Free",
    group: "cdn",
    sort: NumberInt("990"),
    help: "图片CDN域名",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401934")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("202"),
    code: "cdn_image",
    name: "CDN-图片域名",
    type: NumberInt("2"),
    value: "free=>https://cdn.mfield6.com\r\naws=>https://zhlsaj.zrr4ij4.site\r\ntencent=>https://ksj1224.hynxyx.com\r\nsource=>https://jajgl.nbpvuhvv.com",
    values: "",
    group: "cdn",
    sort: NumberInt("900"),
    help: "一行一个,格式: {drive}=>{url}",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1767141228")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("203"),
    code: "cdn_video",
    name: "CDN-视频域名",
    type: NumberInt("2"),
    value: "free=>https://cdn.mfield6.com/m3f\r\naws=>https://zhlsaj.zrr4ij4.site/m3v\r\ntencent=>https://zhlsaj.zrr4ij4.site/m3c\r\nsource=>https://zhlsaj.zrr4ij4.site/m3m",
    values: "",
    group: "cdn",
    sort: NumberInt("800"),
    help: "一行一个,格式: {drive}=>{url}",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1767141229")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("204"),
    code: "cdn_referer",
    name: "CDN Referer",
    type: NumberInt("1"),
    value: "https://www.baidu.com",
    values: "",
    group: "cdn",
    sort: NumberInt("300"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401934")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("205"),
    code: "media_url",
    name: "媒资链接-图片",
    type: NumberInt("1"),
    value: "https://cdn.mfield6.com",
    values: "",
    group: "cdn",
    sort: NumberInt("250"),
    help: "媒资库链接(回显)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1767141229")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("206"),
    code: "media_url_video",
    name: "媒资链接-视频",
    type: NumberInt("1"),
    value: "https://cdn.mfield6.com/m3f",
    values: "",
    group: "cdn",
    sort: NumberInt("246"),
    help: "媒资库链接(回显)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1767141229")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("207"),
    code: "media_api",
    name: "媒资链接-接口地址",
    type: NumberInt("1"),
    value: "https://zhlsaj.zrr4ij4.site",
    values: "",
    group: "cdn",
    sort: NumberInt("245"),
    help: "媒资库接口地址(拉数据)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401935")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("208"),
    code: "media_key",
    name: "媒资链接-接口Key",
    type: NumberInt("1"),
    value: "6780185a19c37c00c97e303cf31ba524",
    values: "",
    group: "cdn",
    sort: NumberInt("240"),
    help: "媒资库Key",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401935")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("209"),
    code: "media_encode_key",
    name: "图片加密Key",
    type: NumberInt("1"),
    value: "525202f9149e061d",
    values: "",
    group: "cdn",
    sort: NumberInt("230"),
    help: "图片加密解密的Key",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401935")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("210"),
    code: "upload_url",
    name: "媒资链接-上传地址",
    type: NumberInt("1"),
    value: "https://upload-lh.ye5weelawj.com/cxapi",
    values: "",
    group: "cdn",
    sort: NumberInt("200"),
    help: "媒资库上传地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1767141230")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("211"),
    code: "upload_key",
    name: "媒资链接-上传Key",
    type: NumberInt("1"),
    value: "7ff6105b86ed4e1a09ac49c023c932ab",
    values: "",
    group: "cdn",
    sort: NumberInt("190"),
    help: "媒资库上传地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1767141230")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("213"),
    code: "media_url_m3u8",
    name: "M3U8回源域名",
    type: NumberInt("1"),
    value: "https://zhlsaj.zrr4ij4.site/m3m",
    values: "",
    group: "cdn",
    sort: NumberInt("0"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1767141229")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("214"),
    code: "media_dir",
    name: "媒资文件夹",
    type: NumberInt("1"),
    value: "/hc237",
    values: "",
    group: "cdn",
    sort: NumberInt("0"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1767141230")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("215"),
    code: "media_appid",
    name: "媒资链接-接口AppId",
    type: NumberInt("1"),
    value: "430691",
    values: "",
    group: "cdn",
    sort: NumberInt("244"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401935")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("300"),
    code: "app_store",
    name: "应用中心",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "app",
    sort: NumberInt("1000"),
    help: "应用中心链接",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763653336")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("301"),
    code: "welcome_msg",
    name: "欢迎消息",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "app",
    sort: NumberInt("990"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763653336")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("302"),
    code: "can_play_num",
    name: "每天能播放次数",
    type: NumberInt("1"),
    value: "0",
    values: "",
    group: "app",
    sort: NumberInt("980"),
    help: "每天能播放次数",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763653336")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("303"),
    code: "withdraw_fee",
    name: "提现费率",
    type: NumberInt("1"),
    value: "20",
    values: "",
    group: "app",
    sort: NumberInt("970"),
    help: "提现费率(%)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763653336")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("304"),
    code: "withdraw_min",
    name: "最低提现",
    type: NumberInt("1"),
    value: "1000",
    values: "",
    group: "app",
    sort: NumberInt("960"),
    help: "最低提现金币",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763653336")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("305"),
    code: "system_user_headico",
    name: "系统用户头像",
    type: NumberInt("6"),
    value: "/media2/uploads-images/default/other/2023-05-27/c725492064614ee83541164b39a8dc96.png",
    values: "",
    group: "app",
    sort: NumberInt("0"),
    help: "消息等使用的头像",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763653336")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("400"),
    code: "android_url",
    name: "android下载地址-默认",
    type: NumberInt("1"),
    value: "https://www.1.com",
    values: "",
    group: "apk",
    sort: NumberInt("1000"),
    help: "android下载地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("401"),
    code: "android_version",
    name: "android最新版本",
    type: NumberInt("1"),
    value: "1.0.0",
    values: "",
    group: "apk",
    sort: NumberInt("990"),
    help: "android版本",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("402"),
    code: "android_min_version",
    name: "android最低版本",
    type: NumberInt("1"),
    value: "1.0.0",
    values: "",
    group: "apk",
    sort: NumberInt("980"),
    help: "低于该版本将强制更新,如果升级时候就需要,则和最新版本填写一样",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("403"),
    code: "android_version_desc",
    name: "android版本描述",
    type: NumberInt("2"),
    value: "1.0.0升级公告",
    values: "",
    group: "apk",
    sort: NumberInt("970"),
    help: "android版本升级描述",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("404"),
    code: "android_apks",
    name: "安卓下载地址列表",
    type: NumberInt("2"),
    value: "",
    values: "",
    group: "apk",
    sort: NumberInt("960"),
    help: "根据IP取模,可配置多条 china_apk_{n}=>{线路}=>{apk路径}",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("405"),
    code: "ios_tf_url",
    name: "ios下载地址-TF",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "apk",
    sort: NumberInt("950"),
    help: "TF下载地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("406"),
    code: "ios_store_url",
    name: "ios下载地址-AppStore",
    type: NumberInt("1"),
    value: "appstore://sxxxxx",
    values: "",
    group: "apk",
    sort: NumberInt("940"),
    help: "AppStore地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("407"),
    code: "ios_qy",
    name: "ios下载地址-企业签",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "apk",
    sort: NumberInt("930"),
    help: "企业签地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("408"),
    code: "ios_qlb",
    name: "ios下载地址-轻量版",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "apk",
    sort: NumberInt("920"),
    help: "轻量版-壳下载链接",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("409"),
    code: "ios_h5k",
    name: "ios下载地址-H5(壳)",
    type: NumberInt("2"),
    value: "",
    values: "",
    group: "apk",
    sort: NumberInt("910"),
    help: "IOS下载地址轻量版壳",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("410"),
    code: "ios_h5",
    name: "ios下载地址-H5线路",
    type: NumberInt("2"),
    value: "",
    values: "",
    group: "apk",
    sort: NumberInt("900"),
    help: "格式 名称=>https链接",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("411"),
    code: "ios_version",
    name: "ios最新版本",
    type: NumberInt("1"),
    value: "1.0.0",
    values: "",
    group: "apk",
    sort: NumberInt("890"),
    help: "ios版本",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("412"),
    code: "ios_min_version",
    name: "ios最低版本",
    type: NumberInt("1"),
    value: "1.0.0",
    values: "",
    group: "apk",
    sort: NumberInt("880"),
    help: "低于该版本将强制更新,如果升级时候就需要,则和最新版本填写一样",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("413"),
    code: "ios_version_desc",
    name: "ios版本描述",
    type: NumberInt("2"),
    value: "1.0.0升级公告",
    values: "",
    group: "apk",
    sort: NumberInt("870"),
    help: "ios版本升级描述",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("414"),
    code: "ios_description",
    name: "IOS描述",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "apk",
    sort: NumberInt("860"),
    help: "IOS下载页面描述",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760782330")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("500"),
    code: "ai_tpl_id",
    name: "AI模板资源",
    type: NumberInt("2"),
    value: "face_tpl_id=>1\nimg_to_video_tpl_id=>1\ntext_to_voice_tpl_id=>1",
    values: "",
    group: "ai",
    help: "格式 类型=>id 一行一个 face_tpl_id(换脸模板编号) img_to_video_tpl_id(图生视频模板) text_to_voice_tpl_id(文生语音模板)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763670222"),
    sort: NumberInt("1000")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("501"),
    code: "ai_change_face_status",
    name: "AI换脸开关",
    type: NumberInt("3"),
    value: "y",
    values: "y|开启;n|关闭",
    group: "ai",
    help: "一般不用设置,自动同步接口",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763670425"),
    sort: NumberInt("990")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("502"),
    code: "ai_change_dress_bare_status",
    name: "AI脱衣开关",
    type: NumberInt("3"),
    value: "y",
    values: "y|开启;n|关闭",
    group: "ai",
    help: "一般不用设置,自动同步接口",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763670223"),
    sort: NumberInt("980")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("503"),
    code: "ai_novel_status",
    name: "AI小说开关",
    type: NumberInt("3"),
    value: "y",
    values: "y|开启;n|关闭",
    group: "ai",
    help: "一般不用设置,自动同步接口",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763670223"),
    sort: NumberInt("970")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("504"),
    code: "ai_text_to_voice_status",
    name: "AI文字转语音开关",
    type: NumberInt("3"),
    value: "y",
    values: "y|开启;n|关闭",
    group: "ai",
    help: "一般不用设置,自动同步接口",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763670426"),
    sort: NumberInt("960")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("505"),
    code: "ai_image_to_video_status",
    name: "AI文生图开关",
    type: NumberInt("3"),
    value: "y",
    values: "y|开启;n|关闭",
    group: "ai",
    help: "一般不用设置,自动同步接口",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763670426"),
    sort: NumberInt("950")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("506"),
    code: "ai_change_dress_status",
    name: "AI换装开关",
    type: NumberInt("3"),
    value: "y",
    values: "y|开启;n|关闭",
    group: "ai",
    help: "一般不用设置,自动同步接口",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763670223"),
    sort: NumberInt("940")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("507"),
    code: "ai_text_to_image_status",
    name: "AI文生图开关",
    type: NumberInt("3"),
    value: "y",
    values: "y|开启;n|关闭",
    group: "ai",
    help: "一般不用设置,自动同步接口",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763670223"),
    sort: NumberInt("930")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("216"),
    code: "xiaozu_media_api",
    name: "媒资链接-小组库-接口地址",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("239"),
    help: "媒资库接口地址(拉数据)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401935")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("217"),
    code: "xiaozu_media_key",
    name: "媒资链接-小组库-接口Key",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("238"),
    help: "媒资库Key",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401935")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("218"),
    code: "tangxin_media_api",
    name: "媒资链接-糖心库-接口地址",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("235"),
    help: "媒资库接口地址(拉数据)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401935")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("219"),
    code: "tangxin_media_key",
    name: "媒资链接-糖心库-接口key",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("234"),
    help: "媒资库Key",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1760401935")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("600"),
    code: "center_adv",
    name: "广告中心",
    type: NumberInt("2"),
    value: "pull_url=>https://adopenapi.adcs01.top\npush_url=>https://adadmin.adcs01.top\nmerid=>yc886699\ndeptid=>1\nappid=>yc_app_test\nappkey=>uQnR8weQSuRzQuz8lgBwkGQQOMxl+X/l+dFQkImSEBE",
    values: "",
    group: "center",
    sort: NumberInt("990"),
    help: "一行一个,pull_url拉广告接口;push_url推送接口;merid商户CODE;deptid部门CODE;appid应用CODE",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1764819429")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("601"),
    code: "center_data",
    name: "数据中心",
    value: "pull_url=>\npush_url=>\nmerid=>\ndeptid=>\nappid=>\nappkey=>",
    values: "",
    group: "center",
    sort: NumberInt("980"),
    help: "一行一个,pull_url拉数据接口;push_url推送接口;merid商户CODE;deptid部门CODE;appid应用CODE",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1765293602"),
    type: NumberInt("2")
} ]);

// ----------------------------
// Collection structure for danmaku
// ----------------------------
db.getCollection("danmaku").drop();
db.createCollection("danmaku");
db.getCollection("danmaku").createIndex({
    "object_id": NumberInt("1")
}, {
    name: "index_object_id"
});
db.getCollection("danmaku").createIndex({
    "object_type": NumberInt("1")
}, {
    name: "index_object_type"
});
db.getCollection("danmaku").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("danmaku").createIndex({
    pos: NumberInt("1")
}, {
    name: "index_pos"
});
db.getCollection("danmaku").createIndex({
    "sub_id": NumberInt("1")
}, {
    name: "index_sub_id"
});
db.getCollection("danmaku").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});

// ----------------------------
// Documents of danmaku
// ----------------------------

// ----------------------------
// Collection structure for domain
// ----------------------------
db.getCollection("domain").drop();
db.createCollection("domain");
db.getCollection("domain").createIndex({
    domain: NumberInt("1")
}, {
    name: "index_domain",
    unique: true
});

// ----------------------------
// Documents of domain
// ----------------------------

// ----------------------------
// Collection structure for email_log
// ----------------------------
db.getCollection("email_log").drop();
db.createCollection("email_log");
db.getCollection("email_log").createIndex({
    email: NumberInt("1")
}, {
    name: "index_email"
});

// ----------------------------
// Documents of email_log
// ----------------------------

// ----------------------------
// Collection structure for job
// ----------------------------
db.getCollection("job").drop();
db.createCollection("job");
db.getCollection("job").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});
db.getCollection("job").createIndex({
    queue: NumberInt("1")
}, {
    name: "index_queue"
});
db.getCollection("job").createIndex({
    "server_name": NumberInt("1")
}, {
    name: "index_server_name"
});
db.getCollection("job").createIndex({
    "plan_at": NumberInt("1")
}, {
    name: "index_plan_at"
});

// ----------------------------
// Documents of job
// ----------------------------

// ----------------------------
// Collection structure for movie
// ----------------------------
db.getCollection("movie").drop();
db.createCollection("movie");
db.getCollection("movie").createIndex({
    mid: NumberInt("1")
}, {
    name: "index_mid",
    unique: true
});
db.getCollection("movie").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});

// ----------------------------
// Documents of movie
// ----------------------------

// ----------------------------
// Collection structure for movie_block
// ----------------------------
db.getCollection("movie_block").drop();
db.createCollection("movie_block");
db.getCollection("movie_block").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of movie_block
// ----------------------------

// ----------------------------
// Collection structure for movie_category
// ----------------------------
db.getCollection("movie_category").drop();
db.createCollection("movie_category");

// ----------------------------
// Documents of movie_category
// ----------------------------

// ----------------------------
// Collection structure for movie_dis_love
// ----------------------------
db.getCollection("movie_dis_love").drop();
db.createCollection("movie_dis_love");
db.getCollection("movie_dis_love").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("movie_dis_love").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of movie_dis_love
// ----------------------------

// ----------------------------
// Collection structure for movie_download
// ----------------------------
db.getCollection("movie_download").drop();
db.createCollection("movie_download");
db.getCollection("movie_download").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("movie_download").createIndex({
    "updated_at": NumberInt("1")
}, {
    name: "index_updated_at"
});

// ----------------------------
// Documents of movie_download
// ----------------------------

// ----------------------------
// Collection structure for movie_favorite
// ----------------------------
db.getCollection("movie_favorite").drop();
db.createCollection("movie_favorite");
db.getCollection("movie_favorite").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("movie_favorite").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of movie_favorite
// ----------------------------

// ----------------------------
// Collection structure for movie_keywords
// ----------------------------
db.getCollection("movie_keywords").drop();
db.createCollection("movie_keywords");
db.getCollection("movie_keywords").createIndex({
    "is_hot": NumberInt("1")
}, {
    name: "index_is_hot"
});

// ----------------------------
// Documents of movie_keywords
// ----------------------------

// ----------------------------
// Collection structure for movie_love
// ----------------------------
db.getCollection("movie_love").drop();
db.createCollection("movie_love");
db.getCollection("movie_love").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("movie_love").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of movie_love
// ----------------------------

// ----------------------------
// Collection structure for movie_nav
// ----------------------------
db.getCollection("movie_nav").drop();
db.createCollection("movie_nav");
db.getCollection("movie_nav").createIndex({
    position: NumberInt("1")
}, {
    name: "index_position"
});

// ----------------------------
// Documents of movie_nav
// ----------------------------

// ----------------------------
// Collection structure for movie_special
// ----------------------------
db.getCollection("movie_special").drop();
db.createCollection("movie_special");
db.getCollection("movie_special").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of movie_special
// ----------------------------

// ----------------------------
// Collection structure for movie_tag
// ----------------------------
db.getCollection("movie_tag").drop();
db.createCollection("movie_tag");

// ----------------------------
// Documents of movie_tag
// ----------------------------

// ----------------------------
// Collection structure for novel
// ----------------------------
db.getCollection("novel").drop();
db.createCollection("novel");
db.getCollection("novel").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});

// ----------------------------
// Documents of novel
// ----------------------------

// ----------------------------
// Collection structure for novel_block
// ----------------------------
db.getCollection("novel_block").drop();
db.createCollection("novel_block");
db.getCollection("novel_block").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of novel_block
// ----------------------------

// ----------------------------
// Collection structure for novel_chapter
// ----------------------------
db.getCollection("novel_chapter").drop();
db.createCollection("novel_chapter");
db.getCollection("novel_chapter").createIndex({
    "novel_id": NumberInt("1")
}, {
    name: "index_novel_id"
});

// ----------------------------
// Documents of novel_chapter
// ----------------------------

// ----------------------------
// Collection structure for novel_favorite
// ----------------------------
db.getCollection("novel_favorite").drop();
db.createCollection("novel_favorite");
db.getCollection("novel_favorite").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("novel_favorite").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of novel_favorite
// ----------------------------

// ----------------------------
// Collection structure for novel_keywords
// ----------------------------
db.getCollection("novel_keywords").drop();
db.createCollection("novel_keywords");
db.getCollection("novel_keywords").createIndex({
    "is_hot": NumberInt("1")
}, {
    name: "index_is_hot"
});

// ----------------------------
// Documents of novel_keywords
// ----------------------------

// ----------------------------
// Collection structure for novel_love
// ----------------------------
db.getCollection("novel_love").drop();
db.createCollection("novel_love");
db.getCollection("novel_love").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("novel_love").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of novel_love
// ----------------------------

// ----------------------------
// Collection structure for novel_nav
// ----------------------------
db.getCollection("novel_nav").drop();
db.createCollection("novel_nav");
db.getCollection("novel_nav").createIndex({
    position: NumberInt("1")
}, {
    name: "index_position"
});

// ----------------------------
// Documents of novel_nav
// ----------------------------

// ----------------------------
// Collection structure for novel_tag
// ----------------------------
db.getCollection("novel_tag").drop();
db.createCollection("novel_tag");

// ----------------------------
// Documents of novel_tag
// ----------------------------

// ----------------------------
// Collection structure for payment_log
// ----------------------------
db.getCollection("payment_log").drop();
db.createCollection("payment_log");
db.getCollection("payment_log").createIndex({
    "unique_id": NumberInt("1")
}, {
    name: "index_unique_id",
    unique: true
});
db.getCollection("payment_log").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});
db.getCollection("payment_log").createIndex({
    "trade_no": NumberInt("1")
}, {
    name: "index_trade_no"
});

// ----------------------------
// Documents of payment_log
// ----------------------------

// ----------------------------
// Collection structure for post
// ----------------------------
db.getCollection("post").drop();
db.createCollection("post");
db.getCollection("post").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("post").createIndex({
    source: NumberInt("1")
}, {
    name: "index_source"
});
db.getCollection("post").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});

// ----------------------------
// Documents of post
// ----------------------------

// ----------------------------
// Collection structure for post_block
// ----------------------------
db.getCollection("post_block").drop();
db.createCollection("post_block");
db.getCollection("post_block").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of post_block
// ----------------------------

// ----------------------------
// Collection structure for post_favorite
// ----------------------------
db.getCollection("post_favorite").drop();
db.createCollection("post_favorite");
db.getCollection("post_favorite").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("post_favorite").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of post_favorite
// ----------------------------

// ----------------------------
// Collection structure for post_keywords
// ----------------------------
db.getCollection("post_keywords").drop();
db.createCollection("post_keywords");
db.getCollection("post_keywords").createIndex({
    "is_hot": NumberInt("1")
}, {
    name: "index_is_hot"
});

// ----------------------------
// Documents of post_keywords
// ----------------------------

// ----------------------------
// Collection structure for post_love
// ----------------------------
db.getCollection("post_love").drop();
db.createCollection("post_love");
db.getCollection("post_love").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("post_love").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});

// ----------------------------
// Documents of post_love
// ----------------------------

// ----------------------------
// Collection structure for post_nav
// ----------------------------
db.getCollection("post_nav").drop();
db.createCollection("post_nav");
db.getCollection("post_nav").createIndex({
    position: NumberInt("1")
}, {
    name: "index_position"
});

// ----------------------------
// Documents of post_nav
// ----------------------------

// ----------------------------
// Collection structure for post_tag
// ----------------------------
db.getCollection("post_tag").drop();
db.createCollection("post_tag");

// ----------------------------
// Documents of post_tag
// ----------------------------

// ----------------------------
// Collection structure for quick_reply
// ----------------------------
db.getCollection("quick_reply").drop();
db.createCollection("quick_reply");

// ----------------------------
// Documents of quick_reply
// ----------------------------

// ----------------------------
// Collection structure for report_adv_app_log
// ----------------------------
db.getCollection("report_adv_app_log").drop();
db.createCollection("report_adv_app_log");
db.getCollection("report_adv_app_log").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("report_adv_app_log").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("report_adv_app_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_adv_app_log
// ----------------------------

// ----------------------------
// Collection structure for report_adv_log
// ----------------------------
db.getCollection("report_adv_log").drop();
db.createCollection("report_adv_log");
db.getCollection("report_adv_log").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("report_adv_log").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("report_adv_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_adv_log
// ----------------------------

// ----------------------------
// Collection structure for report_audio_log
// ----------------------------
db.getCollection("report_audio_log").drop();
db.createCollection("report_audio_log");
db.getCollection("report_audio_log").createIndex({
    "audio_id": NumberInt("1")
}, {
    name: "index_audio_id"
});
db.getCollection("report_audio_log").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("report_audio_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_audio_log
// ----------------------------

// ----------------------------
// Collection structure for report_channel_log
// ----------------------------
db.getCollection("report_channel_log").drop();
db.createCollection("report_channel_log");
db.getCollection("report_channel_log").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("report_channel_log").createIndex({
    date: NumberInt("1")
}, {
    name: "index_date"
});
db.getCollection("report_channel_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_channel_log
// ----------------------------

// ----------------------------
// Collection structure for report_comics_log
// ----------------------------
db.getCollection("report_comics_log").drop();
db.createCollection("report_comics_log");
db.getCollection("report_comics_log").createIndex({
    "comics_id": NumberInt("1")
}, {
    name: "index_comics_id"
});
db.getCollection("report_comics_log").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("report_comics_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_comics_log
// ----------------------------

// ----------------------------
// Collection structure for report_hour_log
// ----------------------------
db.getCollection("report_hour_log").drop();
db.createCollection("report_hour_log");
db.getCollection("report_hour_log").createIndex({
    month: NumberInt("1")
}, {
    name: "index_month"
});
db.getCollection("report_hour_log").createIndex({
    date: NumberInt("1")
}, {
    name: "index_date"
});
db.getCollection("report_hour_log").createIndex({
    pid: NumberInt("1")
}, {
    name: "index_pid"
});
db.getCollection("report_hour_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_hour_log
// ----------------------------

// ----------------------------
// Collection structure for report_movie_log
// ----------------------------
db.getCollection("report_movie_log").drop();
db.createCollection("report_movie_log");
db.getCollection("report_movie_log").createIndex({
    "movie_id": NumberInt("1")
}, {
    name: "index_movie_id"
});
db.getCollection("report_movie_log").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("report_movie_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_movie_log
// ----------------------------

// ----------------------------
// Collection structure for report_novel_log
// ----------------------------
db.getCollection("report_novel_log").drop();
db.createCollection("report_novel_log");
db.getCollection("report_novel_log").createIndex({
    "novel_id": NumberInt("1")
}, {
    name: "index_novel_id"
});
db.getCollection("report_novel_log").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("report_novel_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_novel_log
// ----------------------------

// ----------------------------
// Collection structure for report_post_log
// ----------------------------
db.getCollection("report_post_log").drop();
db.createCollection("report_post_log");
db.getCollection("report_post_log").createIndex({
    "post_id": NumberInt("1")
}, {
    name: "index_post_id"
});
db.getCollection("report_post_log").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("report_post_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_post_log
// ----------------------------

// ----------------------------
// Collection structure for report_server_log
// ----------------------------
db.getCollection("report_server_log").drop();
db.createCollection("report_server_log");
db.getCollection("report_server_log").createIndex({
    type: NumberInt("1")
}, {
    name: "index_type"
});
db.getCollection("report_server_log").createIndex({
    date: NumberInt("1")
}, {
    name: "index_date"
});
db.getCollection("report_server_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_server_log
// ----------------------------

// ----------------------------
// Collection structure for report_user_channel_log
// ----------------------------
db.getCollection("report_user_channel_log").drop();
db.createCollection("report_user_channel_log");
db.getCollection("report_user_channel_log").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("report_user_channel_log").createIndex({
    date: NumberInt("1")
}, {
    name: "index_date"
});
db.getCollection("report_user_channel_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of report_user_channel_log
// ----------------------------

// ----------------------------
// Collection structure for sms_log
// ----------------------------
db.getCollection("sms_log").drop();
db.createCollection("sms_log");
db.getCollection("sms_log").createIndex({
    phone: NumberInt("1")
}, {
    name: "index_phone"
});

// ----------------------------
// Documents of sms_log
// ----------------------------

// ----------------------------
// Collection structure for user
// ----------------------------
db.getCollection("user").drop();
db.createCollection("user");
db.getCollection("user").createIndex({
    username: NumberInt("1")
}, {
    name: "index_username",
    unique: true
});
db.getCollection("user").createIndex({
    phone: NumberInt("1")
}, {
    name: "index_phone",
    unique: true
});
db.getCollection("user").createIndex({
    account: NumberInt("1")
}, {
    name: "index_account",
    unique: true
});
db.getCollection("user").createIndex({
    "device_type": NumberInt("1")
}, {
    name: "index_device_type"
});
db.getCollection("user").createIndex({
    "group_id": NumberInt("1")
}, {
    name: "index_group_id"
});
db.getCollection("user").createIndex({
    "group_dark_id": NumberInt("1")
}, {
    name: "index_group_dark_id"
});
db.getCollection("user").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("user").createIndex({
    "parent_id": NumberInt("1")
}, {
    name: "index_parent_id"
});
db.getCollection("user").createIndex({
    "register_at": NumberInt("1")
}, {
    name: "index_register_at"
});
db.getCollection("user").createIndex({
    "register_date": NumberInt("1")
}, {
    name: "index_register_date"
});
db.getCollection("user").createIndex({
    "login_at": NumberInt("1")
}, {
    name: "index_login_at"
});
db.getCollection("user").createIndex({
    "login_date": NumberInt("1")
}, {
    name: "index_login_date"
});
db.getCollection("user").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});
db.getCollection("user").createIndex({
    "updated_at": NumberInt("1")
}, {
    name: "index_updated_at"
});

// ----------------------------
// Documents of user
// ----------------------------

// ----------------------------
// Collection structure for user_buy_log
// ----------------------------
db.getCollection("user_buy_log").drop();
db.createCollection("user_buy_log");
db.getCollection("user_buy_log").createIndex({
    "order_sn": NumberInt("1")
}, {
    name: "index_order_sn",
    unique: true
});
db.getCollection("user_buy_log").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("user_buy_log").createIndex({
    username: NumberInt("1")
}, {
    name: "index_username"
});
db.getCollection("user_buy_log").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("user_buy_log").createIndex({
    "object_id": NumberInt("1")
}, {
    name: "index_object_id"
});
db.getCollection("user_buy_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});
db.getCollection("user_buy_log").createIndex({
    "user_id": NumberInt("1"),
    "object_type": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_object_type_updated_at"
});

// ----------------------------
// Documents of user_buy_log
// ----------------------------

// ----------------------------
// Collection structure for user_code
// ----------------------------
db.getCollection("user_code").drop();
db.createCollection("user_code");
db.getCollection("user_code").createIndex({
    "code_key": NumberInt("1")
}, {
    name: "index_code_key"
});
db.getCollection("user_code").createIndex({
    code: NumberInt("1")
}, {
    name: "index_code",
    unique: true
});

// ----------------------------
// Documents of user_code
// ----------------------------

// ----------------------------
// Collection structure for user_code_log
// ----------------------------
db.getCollection("user_code_log").drop();
db.createCollection("user_code_log");
db.getCollection("user_code_log").createIndex({
    "code_key": NumberInt("1")
}, {
    name: "index_code_key"
});
db.getCollection("user_code_log").createIndex({
    code: NumberInt("1")
}, {
    name: "index_code"
});
db.getCollection("user_code_log").createIndex({
    "code_id": NumberInt("1")
}, {
    name: "index_code_id"
});
db.getCollection("user_code_log").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("user_code_log").createIndex({
    username: NumberInt("1")
}, {
    name: "index_username"
});

// ----------------------------
// Documents of user_code_log
// ----------------------------

// ----------------------------
// Collection structure for user_fans
// ----------------------------
db.getCollection("user_fans").drop();
db.createCollection("user_fans");
db.getCollection("user_fans").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("user_fans").createIndex({
    "home_id": NumberInt("1")
}, {
    name: "index_home_id"
});
db.getCollection("user_fans").createIndex({
    "user_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_user_id_updated_at"
});
db.getCollection("user_fans").createIndex({
    "home_id": NumberInt("1"),
    "updated_at": NumberInt("-1")
}, {
    name: "index_home_id_updated_at"
});

// ----------------------------
// Documents of user_fans
// ----------------------------

// ----------------------------
// Collection structure for user_favorite
// ----------------------------
db.getCollection("user_favorite").drop();
db.createCollection("user_favorite");
db.getCollection("user_favorite").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("user_favorite").createIndex({
    "object_type": NumberInt("1")
}, {
    name: "index_object_type"
});

// ----------------------------
// Documents of user_favorite
// ----------------------------

// ----------------------------
// Collection structure for user_group
// ----------------------------
db.getCollection("user_group").drop();
db.createCollection("user_group");
db.getCollection("user_group").createIndex({
    "is_disabled": NumberInt("1")
}, {
    name: "index_is_disabled"
});

// ----------------------------
// Documents of user_group
// ----------------------------
db.getCollection("user_group").insert([ {
    _id: NumberInt("1"),
    name: "体验卡",
    description: "体验卡,不对外启用,仅作为邀请赠送会员",
    "is_disabled": NumberInt("1"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    rate: NumberInt("100"),
    "coupon_num": NumberInt("0"),
    price: 1000,
    "old_price": 1000,
    "day_num": NumberInt("1"),
    "gift_num": NumberInt("0"),
    "download_num": NumberInt("0"),
    "day_tips": "",
    "price_tips": "",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1767927284"),
    icon: "",
    right: {
        show: {
            4: "ziyuan",
            5: "rigeng"
        },
        logic: [
            "movie",
            "comics",
            "post",
            "novel",
            "audio"
        ]
    }
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("2"),
    name: "新人卡",
    description: null,
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    rate: NumberInt("100"),
    "coupon_num": NumberInt("0"),
    price: 100,
    "old_price": 100,
    "day_num": NumberInt("1"),
    "gift_num": NumberInt("0"),
    "download_num": NumberInt("0"),
    "day_tips": "",
    "price_tips": "",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1767927261"),
    icon: "",
    right: {
        show: {
            4: "ziyuan",
            5: "rigeng",
            7: "kefu"
        },
        logic: {
            0: "movie",
            1: "comics",
            2: "post",
            3: "novel",
            4: "audio",
            9: "do_comment",
            10: "do_danmaku",
            13: "do_download"
        }
    }
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("3"),
    name: "月卡会员",
    description: "免费看VIP视频\n赠送300钻石\n购片8折",
    "is_disabled": NumberInt("0"),
    sort: NumberInt("100"),
    img: "",
    group: "normal",
    rate: NumberInt("80"),
    "coupon_num": NumberInt("0"),
    price: 100,
    "old_price": 200,
    "day_num": NumberInt("30"),
    "gift_num": NumberInt("0"),
    "download_num": NumberInt("0"),
    "day_tips": "30天",
    "price_tips": "100",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1767927228"),
    icon: "",
    right: {
        show: {
            3: "zhekou",
            4: "ziyuan",
            5: "rigeng",
            6: "sixin",
            7: "kefu"
        },
        logic: {
            0: "movie",
            1: "comics",
            2: "post",
            3: "novel",
            4: "audio",
            9: "do_comment",
            10: "do_danmaku",
            14: "do_chat"
        }
    }
} ]);
db.getCollection("user_group").insert([ {
    _id: 4,
    name: "季卡",
    description: null,
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    icon: "",
    rate: NumberInt("100"),
    "coupon_num": NumberInt("0"),
    price: 100,
    "old_price": 100,
    "day_num": NumberInt("1"),
    "gift_num": NumberInt("0"),
    "download_num": NumberInt("0"),
    "day_tips": "",
    "price_tips": "",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1767927178"),
    right: {
        show: {
            3: "zhekou",
            4: "ziyuan",
            5: "rigeng",
            6: "sixin",
            7: "kefu"
        },
        logic: {
            0: "movie",
            1: "comics",
            2: "post",
            3: "novel",
            4: "audio",
            9: "do_comment",
            10: "do_danmaku",
            11: "do_post",
            12: "do_movie",
            13: "do_download",
            14: "do_chat",
            15: "do_chat_call",
            16: "do_live"
        }
    }
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("5"),
    name: "年卡会员",
    description: "免费看VIP视频\n赠送500钻石\n体验AI女友",
    "is_disabled": NumberInt("0"),
    sort: NumberInt("90"),
    img: "",
    group: "normal",
    rate: NumberInt("80"),
    "coupon_num": NumberInt("0"),
    price: 200,
    "old_price": 200,
    "day_num": NumberInt("365"),
    "gift_num": NumberInt("500"),
    "download_num": NumberInt("0"),
    "day_tips": "365天",
    "price_tips": "",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1767927141"),
    icon: "",
    right: {
        show: {
            2: "tequan",
            3: "zhekou",
            4: "ziyuan",
            5: "rigeng",
            6: "sixin",
            7: "kefu"
        },
        logic: [
            "movie",
            "comics",
            "post",
            "novel",
            "audio",
            "do_nickname",
            "do_headico",
            "do_headbg",
            "do_sign",
            "do_comment",
            "do_danmaku",
            "do_post",
            "do_movie",
            "do_download",
            "do_chat",
            "do_chat_call",
            "do_live"
        ]
    }
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("20"),
    name: "终身",
    description: null,
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    rate: NumberInt("100"),
    "coupon_num": NumberInt("0"),
    price: 300,
    "old_price": 300,
    "day_num": NumberInt("1"),
    "gift_num": NumberInt("0"),
    "download_num": NumberInt("0"),
    "day_tips": "",
    "price_tips": "",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1767929762"),
    icon: "",
    right: {
        show: {
            1: "yuanwei",
            2: "tequan",
            3: "zhekou",
            4: "ziyuan",
            5: "rigeng",
            6: "sixin",
            7: "kefu"
        },
        logic: [
            "movie",
            "comics",
            "post",
            "novel",
            "audio",
            "do_nickname",
            "do_headico",
            "do_headbg",
            "do_sign",
            "do_danmaku",
            "do_comment",
            "do_post",
            "do_movie",
            "do_download",
            "do_chat",
            "do_chat_call",
            "do_live"
        ]
    },
    "activity_id": "6951986350454127640d7442"
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("30"),
    name: "至尊",
    description: null,
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "/hc237/uploads/default/other/2025-10-24/fb872594fb8b6b7a2a783dde3a7bfbbd.jpg",
    group: "normal",
    rate: NumberInt("100"),
    "coupon_num": NumberInt("0"),
    price: 500,
    "old_price": 500,
    "day_num": NumberInt("1"),
    "gift_num": NumberInt("0"),
    "download_num": NumberInt("10000"),
    "day_tips": "",
    "price_tips": "",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1767929597"),
    right: {
        show: [
            "yuepao",
            "yuanwei",
            "tequan",
            "zhekou",
            "ziyuan",
            "rigeng",
            "sixin",
            "kefu"
        ],
        logic: [
            "movie",
            "comics",
            "post",
            "novel",
            "audio",
            "do_nickname",
            "do_headico",
            "do_headbg",
            "do_sign",
            "do_danmaku",
            "do_comment",
            "do_post",
            "do_movie",
            "do_download",
            "do_chat",
            "do_chat_call",
            "do_live"
        ]
    },
    icon: "",
    "activity_id": "6960022f50454127640d7444"
} ]);

// ----------------------------
// Collection structure for user_order
// ----------------------------
db.getCollection("user_order").drop();
db.createCollection("user_order");
db.getCollection("user_order").createIndex({
    "order_sn": NumberInt("1")
}, {
    name: "index_order_sn",
    unique: true
});
db.getCollection("user_order").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("user_order").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("user_order").createIndex({
    "register_at": NumberInt("1")
}, {
    name: "index_register_at"
});
db.getCollection("user_order").createIndex({
    status: NumberInt("1")
}, {
    name: "index_status"
});
db.getCollection("user_order").createIndex({
    "trade_sn": NumberInt("1")
}, {
    name: "index_trade_sn"
});
db.getCollection("user_order").createIndex({
    "jet_lag": NumberInt("1")
}, {
    name: "index_jet_lag"
});
db.getCollection("user_order").createIndex({
    "pay_date": NumberInt("1")
}, {
    name: "index_pay_date"
});
db.getCollection("user_order").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of user_order
// ----------------------------

// ----------------------------
// Collection structure for user_product
// ----------------------------
db.getCollection("user_product").drop();
db.createCollection("user_product");

// ----------------------------
// Documents of user_product
// ----------------------------
db.getCollection("user_product").insert([ {
    _id: NumberInt("1"),
    name: "30金币",
    type: "point",
    num: NumberInt("30"),
    "gift_num": NumberInt("0"),
    "vip_num": NumberInt("0"),
    price: 30,
    sort: NumberInt("0"),
    "price_tips": "",
    description: "",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1754527653"),
    "updated_at": NumberInt("1754527653")
} ]);
db.getCollection("user_product").insert([ {
    _id: NumberInt("2"),
    name: "50金币",
    type: "point",
    num: NumberInt("30"),
    "gift_num": NumberInt("0"),
    "vip_num": NumberInt("0"),
    price: 50,
    sort: NumberInt("0"),
    "price_tips": "",
    description: "",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1754527653"),
    "updated_at": NumberInt("1754527653")
} ]);
db.getCollection("user_product").insert([ {
    _id: NumberInt("3"),
    name: "100金币",
    type: "point",
    num: NumberInt("30"),
    "gift_num": NumberInt("0"),
    "vip_num": NumberInt("0"),
    price: 100,
    sort: NumberInt("0"),
    "price_tips": "",
    description: "",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1754527653"),
    "updated_at": NumberInt("1754527653")
} ]);
db.getCollection("user_product").insert([ {
    _id: NumberInt("4"),
    name: "200金币",
    type: "point",
    num: NumberInt("30"),
    "gift_num": NumberInt("0"),
    "vip_num": NumberInt("0"),
    price: 200,
    sort: NumberInt("0"),
    "price_tips": "",
    description: "",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1754527653"),
    "updated_at": NumberInt("1754527653")
} ]);
db.getCollection("user_product").insert([ {
    _id: NumberInt("5"),
    name: "300金币",
    type: "point",
    num: NumberInt("30"),
    "gift_num": NumberInt("0"),
    "vip_num": NumberInt("0"),
    price: 300,
    sort: NumberInt("0"),
    "price_tips": "",
    description: "",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1754527653"),
    "updated_at": NumberInt("1754527653")
} ]);
db.getCollection("user_product").insert([ {
    _id: NumberInt("6"),
    name: "500金币",
    type: "point",
    num: NumberInt("30"),
    "gift_num": NumberInt("0"),
    "vip_num": NumberInt("0"),
    price: 500,
    sort: NumberInt("0"),
    "price_tips": "",
    description: "",
    "is_disabled": NumberInt("0"),
    "created_at": NumberInt("1754527653"),
    "updated_at": NumberInt("1754527653")
} ]);

// ----------------------------
// Collection structure for user_recharge
// ----------------------------
db.getCollection("user_recharge").drop();
db.createCollection("user_recharge");
db.getCollection("user_recharge").createIndex({
    "order_sn": NumberInt("1")
}, {
    name: "index_order_sn",
    unique: true
});
db.getCollection("user_recharge").createIndex({
    "trade_sn": NumberInt("1")
}, {
    name: "index_trade_sn"
});
db.getCollection("user_recharge").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("user_recharge").createIndex({
    username: NumberInt("1")
}, {
    name: "index_username"
});
db.getCollection("user_recharge").createIndex({
    "record_type": NumberInt("1")
}, {
    name: "index_record_type"
});
db.getCollection("user_recharge").createIndex({
    "channel_name": NumberInt("1")
}, {
    name: "index_channel_name"
});
db.getCollection("user_recharge").createIndex({
    "register_at": NumberInt("1")
}, {
    name: "index_register_at"
});
db.getCollection("user_recharge").createIndex({
    "jet_lag": NumberInt("1")
}, {
    name: "index_jet_lag"
});
db.getCollection("user_recharge").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of user_recharge
// ----------------------------

// ----------------------------
// Collection structure for user_share_log
// ----------------------------
db.getCollection("user_share_log").drop();
db.createCollection("user_share_log");
db.getCollection("user_share_log").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("user_share_log").createIndex({
    label: NumberInt("1")
}, {
    name: "index_label"
});
db.getCollection("user_share_log").createIndex({
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of user_share_log
// ----------------------------

// ----------------------------
// Collection structure for user_up
// ----------------------------
db.getCollection("user_up").drop();
db.createCollection("user_up");
db.getCollection("user_up").createIndex({
    nickname: NumberInt("1")
}, {
    name: "index_nickname"
});
db.getCollection("user_up").createIndex({
    categories: NumberInt("1")
}, {
    name: "index_categories"
});

// ----------------------------
// Documents of user_up
// ----------------------------

// ----------------------------
// Collection structure for user_withdraw
// ----------------------------
db.getCollection("user_withdraw").drop();
db.createCollection("user_withdraw");
db.getCollection("user_withdraw").createIndex({
    "user_id": NumberInt("1")
}, {
    name: "index_user_id"
});
db.getCollection("user_withdraw").createIndex({
    "order_sn": NumberInt("1")
}, {
    name: "index_order_sn"
});
db.getCollection("user_withdraw").createIndex({
    "record_type": NumberInt("1")
}, {
    name: "index_record_type"
});

// ----------------------------
// Documents of user_withdraw
// ----------------------------
