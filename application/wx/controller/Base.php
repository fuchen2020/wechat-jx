<?php

namespace app\wx\controller;

use think\Controller;
use think\Request;
use EasyWeChat\Foundation\Application;

class Base extends Controller
{
    //微信授权
    protected function _initialize()
    {
        parent::_initialize();
        $app = new Application(config('options'));
        $oauth = $app->oauth;
        // 未登录
        if (empty(session('wechat_user'))) {
            $url=request()->baseUrl();
            session('target_url',$url);
            $oauth->redirect()->send();
        }

    }
}
