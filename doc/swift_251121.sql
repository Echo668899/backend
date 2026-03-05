/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1_27017
 Source Server Type    : MongoDB
 Source Server Version : 60016
 Source Host           : 127.0.0.1:27017
 Source Schema         : swift_app

 Target Server Type    : MongoDB
 Target Server Version : 60016
 File Encoding         : 65001

 Date: 21/11/2025 04:26:13
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
    "login_at": NumberInt("1735664400"),
    "login_ip": "127.0.0.1",
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
    id: NumberInt("3000")
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
    id: NumberInt("200")
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
    id: NumberInt("336")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("68f29eb748cb30a73171997b"),
    name: "adv",
    id: NumberInt("0")
} ]);
db.getCollection("collection_ids").insert([ {
    _id: ObjectId("691173dadb462a2c4b404da5"),
    name: "account_log",
    id: NumberInt("68")
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
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("900"),
    help: "一行一个,格式: {drive}=>{url}",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763513751")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("203"),
    code: "cdn_video",
    name: "CDN-视频域名",
    type: NumberInt("2"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("800"),
    help: "一行一个,格式: {drive}=>{url}",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763513751")
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
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("250"),
    help: "媒资库链接(回显)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763513751")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("206"),
    code: "media_url_video",
    name: "媒资链接-视频",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("246"),
    help: "媒资库链接(回显)",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763513751")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("207"),
    code: "media_api",
    name: "媒资链接-接口地址",
    type: NumberInt("1"),
    value: "",
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
    value: "",
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
    value: "",
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
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("200"),
    help: "媒资库上传地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763513751")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("211"),
    code: "upload_key",
    name: "媒资链接-上传Key",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("190"),
    help: "媒资库上传地址",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763513751")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("213"),
    code: "media_url_m3u8",
    name: "M3U8回源域名",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("0"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763513751")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("214"),
    code: "media_dir",
    name: "媒资文件夹",
    type: NumberInt("1"),
    value: "",
    values: "",
    group: "cdn",
    sort: NumberInt("0"),
    help: "",
    "created_at": NumberInt("1735664400"),
    "updated_at": NumberInt("1763513751")
} ]);
db.getCollection("config").insert([ {
    _id: NumberInt("215"),
    code: "media_appid",
    name: "媒资链接-接口AppId",
    type: NumberInt("1"),
    value: "",
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
    value: "",
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
    "created_at": NumberInt("1")
}, {
    name: "index_created_at"
});

// ----------------------------
// Documents of user_fans
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
    level: NumberInt("1"),
    "promotion_type": NumberInt("0"),
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
    "updated_at": NumberInt("1754527585")
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("2"),
    name: "新人卡",
    description: "",
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    level: NumberInt("2"),
    "promotion_type": NumberInt("0"),
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
    "updated_at": NumberInt("1754527585")
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("3"),
    name: "月卡",
    description: "",
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    level: NumberInt("3"),
    "promotion_type": NumberInt("0"),
    rate: NumberInt("100"),
    "coupon_num": NumberInt("0"),
    price: 50,
    "old_price": 50,
    "day_num": NumberInt("1"),
    "gift_num": NumberInt("0"),
    "download_num": NumberInt("0"),
    "day_tips": "",
    "price_tips": "",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1754527585")
} ]);
db.getCollection("user_group").insert([ {
    _id: 4,
    name: "季卡",
    description: "",
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    level: NumberInt("4"),
    "promotion_type": NumberInt("0"),
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
    "updated_at": NumberInt("1754527585")
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("5"),
    name: "年卡",
    description: "",
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    level: NumberInt("5"),
    "promotion_type": NumberInt("0"),
    rate: NumberInt("100"),
    "coupon_num": NumberInt("0"),
    price: 200,
    "old_price": 200,
    "day_num": NumberInt("1"),
    "gift_num": NumberInt("0"),
    "download_num": NumberInt("0"),
    "day_tips": "",
    "price_tips": "",
    "created_at": NumberInt("1754527585"),
    "updated_at": NumberInt("1754527585")
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("20"),
    name: "终身",
    description: "",
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "",
    group: "normal",
    level: NumberInt("20"),
    "promotion_type": NumberInt("0"),
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
    "updated_at": NumberInt("1754527585")
} ]);
db.getCollection("user_group").insert([ {
    _id: NumberInt("30"),
    name: "至尊",
    description: "",
    "is_disabled": NumberInt("0"),
    sort: NumberInt("0"),
    img: "/hc237/uploads/default/other/2025-10-24/fb872594fb8b6b7a2a783dde3a7bfbbd.jpg",
    group: "normal",
    level: NumberInt("30"),
    "promotion_type": NumberInt("0"),
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
    "updated_at": NumberInt("1761932518")
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
    "record_type": NumberInt("1")
}, {
    name: "index_record_type"
});

// ----------------------------
// Documents of user_withdraw
// ----------------------------
