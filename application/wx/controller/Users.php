<?php

namespace app\wx\controller;

use EasyWeChat\Foundation\Application;
use think\Db;
use think\Request;

class Users extends Base
{
    //热卖商品5条
   public function hots(){
       $user = session('wechat_user');
       var_dump($user['id']);
   }
   public function bd(){
       $re=request();
       $type=$re->post('type');
       $users = session('wechat_user');
       $openid=$users['id'];
       $open=Db::name('users')->where(['openid'=>$openid])->find();
       if($re->isPost()){
            $user=Db::name('users')->where(['username'=>$re->post('username')])->find();
            if($user){
                //判断是绑定账号
                if ($type==='binding') {
                    if (password_verify($re->post('password'),$user['password_hash'])) {
                       $result=Db::name('users')->where(['id'=>$user['id']])->update([
                           'openid'=>$openid,
                       ]);
                        if ($result) {
                            $this->success('绑定成功', 'Users/succ');
                        }
                    }else{
                        echo '密码错误';
                    }
                }
                //判断是解绑账号
                if($type==='jiebang'){
                    $result=Db::name('users')->where(['id'=>$user['id']])->update([
                        'openid'=>'',
                    ]);
                    if ($result) {
                        $this->success('解绑成功', 'Users/succ');
                    }
                }

            }else{
                echo '账号不存在';
            }
       }
       //判断显示视图
       if($open){
           return view('jiebang');
       }else{
           return view('binding');
       }

   }

   public function succ(){
       return view('success');
   }
}
