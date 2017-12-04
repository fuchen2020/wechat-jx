<?php

namespace app\wx\controller;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\News;
use think\Controller;
use think\Db;
use think\Request;

class Index extends Controller
{
    public function index()
    {

        $app = new Application(config('options'));
        // 从项目实例中得到服务端应用实例。
        $server = $app->server;
        $server->setMessageHandler(function ($message) {
            // $message->FromUserName // 用户的 openid
            // $message->MsgType // 消息类型：event, text....

            //判断热卖商品
            if($message->EventKey === 'V10001_SHOP_SERVER'){
                $goods=Db::name('goods')->order('id desc')->limit(5)->select();
                $goodss = [];
                foreach ($goods as $k => $v) {
                    $goodss[] = new News([
                        'title' => $v['name'],
                        'description' => $v['sn'],
                        'url' => $v['logo'],
                        'image' => $v['logo'],
                    ]);
                }
                return $goodss;
            }
            //判断生活服务
            if ($message->EventKey === "V1001_GOOD_SEVER") {
                return "您好！我是生活服务大全!\n1.获取当前天气状况：城市名+天气\n2.获取当前美女排行榜：美女+\n['欧美','韩版','日系','英伦','OL风',\n'学院','淑女','性感','复古','街头','休闲',\n'民族','甜美','运动','可爱','其他']";
            }
            //判断商务合作
            if ($message->EventKey === "V1002_GOOD") {
                return "您好！我是商务合作!";
            }
            //天气预报接口
            if (strpos($message->Content, "天气")) {
                $result = file_get_contents("http://route.showapi.com/9-2?showapi_appid=51019&showapi_timestamp=" . getMillisecond() . "&showapi_sign=e172be1524634e869f5b4da54073d997&areaid=&area=" . substr($message->Content, 0, strrpos($message->Content, '+')) . "&needMoreDay=0&needIndex=0&needHourData=0&need3HourForcast=0&needAlarm=0&");
                $arr = \GuzzleHttp\json_decode($result, true);
                return $arr['showapi_res_body']['now']['weather'];
            }
            //今日笑话
            if ($message->EventKey === "V1001_GOOD_XH") {
                $result = file_get_contents("http://route.showapi.com/107-32?showapi_appid=51019&showapi_sign=e172be1524634e869f5b4da54073d997&");
                $arr = \GuzzleHttp\json_decode($result, true);
                $strs = $arr['showapi_res_body']['list'];
                $re = '';
                $num = 1;
                foreach ($strs as $k => $v) {
                    if ($k < 6) {
                        $num = $k + 1;
                        $re = $re . '第' . $num . '条：' . str_replace('<br/>', '', $v['content']) . "\n\n";
                    }
                }
                return $re;
            }
            //判断美女排行
            if (strpos($message->Content, "美女")) {
                $result = file_get_contents("http://route.showapi.com/126-2?showapi_appid=51019&showapi_sign=e172be1524634e869f5b4da54073d997&type=".substr($message->Content, 0, strrpos($message->Content, '+'))."&order=1&page=1&");
                echo "<pre>";
                $arr = json_decode($result, true);
                $strs=$arr['showapi_res_body']['pagebean']['contentlist'];
                $img=[];
                foreach ($strs as $k=>$v){
                    if($k<8){
                        $img[$k]['title']=$v['realName'];
                        $img[$k]['pic']=$v['avatarUrl'];
                        $img[$k]['url']=$v['link'];
                        $img[$k]['desc']=$v['city'];
                    }
                }
                $mn = [];
                foreach ($img as $k => $v) {
                    $mn[] = new News([
                        'title' => $v['title'],
                        'description' => $v['desc'],
                        'url' => $v['url'],
                        'image' => $v['pic'],
                    ]);
                }
                return $mn;
            }

        });
        $response = $app->server->serve();
        // 将响应输出
        $response->send(); // Laravel 里请使用：return $response;
    }
    //获取菜单实例
    public function getMemu()
    {
        $app = new Application(config('options'));
        $menu = $app->menu;
        $menus = $menu->all();
        var_dump($menus);
    }

    //设置菜单
    public function setMemu()
    {
        $buttons = [
            [
                "type" => "click",
                "name" => "热卖商品",
                "key" => "V10001_SHOP_SERVER"
            ],
            [
                "name" => "娱乐在线",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "今日笑话",
                        "key" => "V1001_GOOD_XH"
                    ],
                    [
                        "type" => "click",
                        "name" => "生活服务",
                        "key" => "V1001_GOOD_SEVER"
                    ],
                ],
            ],
            [
                "name" => "个人中心",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "我的订单",
                        "key" => "V1002_GOOD_DD"
                    ],
                    [
                        "type" => "click",
                        "name" => "我的信息",
                        "key" => "V1002_GOOD_INFO"
                    ],
                    [
                        "type" => "view",
                        "name" => "绑定账号",
                        "url" => "http://wx.chenziyong.vip/wx/users/bd"
                    ],
                ],
            ],
        ];
        $app = new Application(config('options'));
        $menu = $app->menu;
        $menu->add($buttons);
    }

    public function getTQ()
    {
        $result = file_get_contents("http://route.showapi.com/126-2?showapi_appid=51019&showapi_sign=e172be1524634e869f5b4da54073d997&type=".'韩版'."&order=1&page=1&");
        echo "<pre>";
        $arr = json_decode($result, true);
//        var_dump($arr);
        $strs=$arr['showapi_res_body']['pagebean']['contentlist'];
        $img=[];
        foreach ($strs as $k=>$v){
            if($k<8){
                $img[$k]['title']=$v['realName'];
                $img[$k]['img']=$v['avatarUrl'];
                $img[$k]['url']=$v['link'];
                $img[$k]['desc']=$v['city'];
            }
        }
        var_dump($img);
    }

    public function callback(){
        $app = new Application(config('options'));
        $oauth = $app->oauth;
      // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        session('wechat_user',$user->toArray());
        $targetUrl = empty(session('target_url')) ? '/' : session('target_url');
//        header('location:'. $targetUrl); // 跳转到 user/profile
        $this->redirect($targetUrl);
    }

    public function auth(){
        $app = new Application(config('options'));
        $oauth = $app->oauth;
       // 未登录
        if (empty(session('wechat_user'))) {
            $url=request()->baseUrl();
            session('target_url',$url);
//            return $oauth->redirect();
            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
             $oauth->redirect()->send();
        }
// 已经登录过
        $user = session('wechat_user');
        var_dump($user);
    }
}