<?php

namespace App\Tasks;

use App\Models\Movie\MovieModel;
use App\Models\User\UserModel;
use App\Repositories\Api\ChatRepository;
use App\Repositories\Api\ComicsRepository;
use App\Repositories\Api\MovieRepository;
use App\Repositories\Api\UserRepository;
use App\Services\Admin\AdminUserService;
use App\Services\Common\Chat\ChatService;
use App\Services\Common\IpService;
use App\Services\Movie\MovieDisLoveService;
use App\Services\Movie\MovieHistoryService;
use App\Services\Movie\MovieService;
use App\Services\User\UserActiveService;

class TestTask
{
    public function decodeAction()
    {
        $password = '123456';
        $slat     = rand(10000, 99999);
        dd(AdminUserService::makePassword($password, $slat), $slat);
        //        $str=file_get_contents();
        //        $decrypted = openssl_decrypt($str, 'AES-128-ECB','525202f9149e061d', OPENSSL_RAW_DATA);
    }

    public function testAction()
    {
        $rows = MovieModel::find([], [], [], 0, 10000);
        foreach ($rows as $row) {
            MovieModel::updateRaw(['$set' => ['real_dislove' => 0, 'dislove' => 0]], ['_id' => $row['_id']]);
        }

        dd(MovieService::doSearch());

        dd(UserModel::count(['channel_name' => ['$in' => ['zjdy5686']], 'register_date' => '2025-12-07'], 'register_date'));
        dd(UserModel::find(['channel_name' => 'zjdy5686', 'register_date' => '2025-12-07'], [], [], 0, 10, 'register_date'));
        $monthTimestamp = time();
        dd(date('Y-m-t', $monthTimestamp));
        dd(UserRepository::getFollowList(500305, 500305, 'follow'));
        dd(UserRepository::doFollow(500396, 500305, 'follow'));
        dd(MovieDisLoveService::do(1, '119463ea4f443c8d'));
        dd(UserActiveService::do(500005, '/'));
        dd(ChatRepository::chat(500005, 'all', 1));
        dd(MovieHistoryService::getIds(500037));
        //        dd(UserFansService::do(500001,500006));
        //        dd(ChatService::sendSystemMessage('500006','funds.vip',[
        //            'content'=>'充值 VIP 已到账',
        //            'order_sn'=>CommonUtil::createOrderNo('TEST'),
        //            'link'=>'/vip'
        //        ]));

        //        dd(M3u8Service::encode('/4d113cd509016e9366a939df8206bac164569b5fdbbe501c59486bc7af38c7de21f30aa1516c34c3fcbaa5a8d4f76896cd40de9c94c65d9bb2b4ce9ef2d073fc1dbaacf5260d210b6012e6afc544619b.m3u8','aws'));
        //        dd(M3u8Service::decode('634d2c45cfffbefbefad198489dab1d7'));

        //        dd(ChatService::doRecallMessage(500001,70));
        //        dd(ChatService::doDelMessage(500001,33));
        //        dd(DanmakuService::do(500005,'c23fa6ee6dcbca5b','movie',1,'14','16777215','1','子弹','c23fa6ee6dcbca5b'));

        //        dd(ComicsRepository::getDetail(500005,'fd641047419d9518'));

        //        dd(ChatRepository::message(500001,'service','13',));
        //        dd(ChatRepository::doDelMessage(500001,'32,33,'));
        //        dd(ChatService::getUnreadCount(500001));

        //        dd(ChatService::sendSystemMessage(500001,'text',['text'=>'支付成功通知:时间'.date('Y-m-d H:i:s')]));
        dd(ChatService::sendSingleMessage(500296, 500276, uniqid(), 'text', ['text' => '你好,请问有什么可以帮到您:' . date('Y-m-d H:i:s')]));
        //        dd(ChatService::sendSingleMessage('service',500227,uniqid(),'text',['text'=>'你好,请问有什么可以帮到您:'.date('Y-m-d H:i:s')]));
        //        dd(ChatService::sendSingleMessage(500001,'service',uniqid(),'text',['text'=>'退钱:'.date('Y-m-d H:i:s')]));

        dd(ChatRepository::doDelChat(500001, '500001_service,'));
        //        dd(ChatRepository::chat(500001,'all'));
        //        dd(ChatService::doDelMessage(500001,'7'));
        //        dd(ChatService::doReadMessage(500001,'500001_500005','9'));
        dd(ChatService::sendSingleMessage(500005, 500001, uniqid(), 'text', ['text' => '时间:' . date('Y-m-d H:i:s')]));

        dd(ComicsRepository::getChapterDetail(500005, 'fa738c983135bbea'));
        //        dd(ComicsRepository::doHistory(500005,'b618c21d88514c09','350f21d2be0b5806',2));
        $linksIds = ['60f69e53070386cdcde52a9760ff8d97', '2715b47ee08a6fb882171a038ad67693', '8f045c503f1ad2c689cb35809041749a', '91cff85b39840ef52ce3db285124ca1f', '63fe155f12a51bc3e489c3605a434508', '61fce7cd0666a08cf7e820ed5367bc72', '8a631718789a2d9c306b982f3784e740', 'd7d617272be79ba1baea55918366c859', '06426faf49b1a4441c837d213e292a96', '752f2b25a143cf8bb84e5d2ee5aa1d41', '0d5324036ab6e0723c524f3479ee24fa', 'e4fbf3fb9aeebe2bdf99dd59ee1ba962', 'd80173e540f8c778d7089ef3d65e273f', '5b44d568c7803b5aea37da5919016723', '14920b9e5ff24498d6f8ca1714ca8ecc', '65ed94c06822e1ef53b714d3649eb103', '8fe913295f599f45005bf15f307bc08c', '10b5704b03b0c2d2e5e358da6dce4e49', 'f84dc62374bb594c78692d10cd3ad502', '5df06b5de83d49750735f95bc6adb263', 'c5aa4be7429e69d15b6720b45b5bd745', '2861121ea6e83987a05d5f8d43345adf', '432d9cff00c0ee6a02f25d4cb4540f5f', '52085e08693b57e048e595fdde3c9207', '72aaea3656420da65dc1f48258131b64', '575757528b25e8de9b60715cc53e4c37', '112645df041f489f00303b7968edef85', '70a00c9b7be4382a44db6bb17ca25300', 'e8fe3520549528924edf746c73a5b76d', '804992ef79798ad88db03fd8c3444341', '481af7ee276c44a65ba2575de80bf723', '704842b827cdc6a5878a8e3716fb0a17', 'd769065dcedb098a2f2c8c6f54abc8ea', 'f12f9e870e89d6899133a1d2c1d417b4', '7d2d512cb107968c36497d676c8a22f7', '56da6300101f698ba5627d326e4f74a1', '629143f19233f310f319ac0f738e4c71', 'a505c508505b6142abbe591bfe6efd5c', 'e66d55f647967ac1379e9f3eaa4ff401', 'd026204514220c81d2b4a3c7f56a0aa9', 'c181057c576ec7567e511c284eb30680', 'e8a15505b8cbcfcfd8eaadec757ae61b', '7c36522e7ca073c89eac9333bed920af', 'fc68c6a393aaa409dfc559c78d4ba11a', '5d0313caae57c2625d60cd7bf54a65ec', '2fdb0515911c13192a96c0242b5a2bd2', 'd7d79bb2af6a1137a614b03f834cfe0e', 'b1fba38c51900616a5c26be46bbd9146', '7906a4921bdfd8503d6ff3a07818564a', '2ea52105a112778ca598cbd1fc04cdb2', 'e4cc2a66f3884d5b586f926f83b066a1', '698ab532d02cd581a872e1ba7c62c22a', 'c4cf0e58a82d13032eb9d285357c6c28', 'b1e2f2556840ed67800b50a772dd51fb', '610ef22ef35e5cdf171245cf72d7b114', 'f6ddb4293176847d38eadb2e378d4037', '89bf58a2c2c95fb5592b2378b2396859', 'e9097d7b26756380c5073fd4daaa04ac', '2cd4ddca6565722d8383f0cc6067d491', '2624043439bf85c378b6dd360f74e0e4', '648ea207951ddbe41caf9c8c45726fe5', '904c735c577c64e954111a1461191226', 'eaa1ee02a2eff3bca3c73555277ddebb', '281fb57e55649f03438cdd57e4daf74e', '81fd55a5515795710d5cdc0644a2bd5c', '7890b2e9efd0c26ec9b5cc48f1e25b2b', 'b6229732aff1601a3daf75e58b1d8929', '1ba12d44508273189e063cd87fb039df', '972882d9eb7e5905ae1c420dc3ae46f4', '8bc6de658770013f5ae24dd8327a8128', 'fe83c64d3fd6f926ceee66bf118218ca', '9bc6a6984e3d886adcc7300e90b9336b', 'fc08294a5b5840cd6eeb89bc9b1725d7', 'd81e5bae2635a034eadbd8d03882d638', 'a3818f08886ab6cae4fb9002b60d6d46', '64739ee3d57c5d3b0939904cba2dc8ba', '12ff689cf59131f5f32fb46abe1b038c', '813f4ce818d3fee1cbcfc86e5a7feb47', 'ce2ea38aeb1d36e536394947b08a2310', '0df4e760ec64c4273b36d4e987772b13', '6a56ee3c163c7a12906251b9e50daf00', 'bdb93c32b3d94f79f916a1cefc32f144', 'f0a83992b5af59401b696737735a5361', 'bdfa664de03f64269e08bd8c89dca564', 'c633a2448fdef44119d3c9640532589e', 'a72d2eaeb705d8f81cb3c660bf1bf744'];
        dd(MovieHistoryService::delete(500005, 'a9966b8e10f646c8'));

        foreach ($linksIds as $linksId) {
            dump(MovieRepository::doHistory(500005, 'a9966b8e10f646c8', $linksId, 2));
        }

        $ip = '240e:87c:71a:639a:3dff:ffff:ffff:ffff';

        dd(IpService::parse($ip));
        UserActiveService::do(2, 'system-info', '1');
        dd(UserActiveService::getTotalCount(), UserActiveService::getRouteCount('user-info', '1'));
    }
}
