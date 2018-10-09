<?php 
namespace controllers;
use models\Admin;
class LoginController{

    // 显示登录页面
    public function login(){
        view('login/login');
    }
    // 处理登录的页面
    public function dologin(){
        // 1 接收表单
        $username = $_POST['username'];
        $password = $_POST['password'];
        // 2 调用模型
        $model = new Admin;
        try{
            $model->login($username,$password);
            redirect('/');
        }catch(\Exception $e){
            // 抛出错误异常
            redirect('/login/login');
        }
    }

    public function logout(){
        $model = new Admin;
        $model->logout();
        redirect('/login/login');
    }
}

?>