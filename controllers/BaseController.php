<?php 
namespace controllers;
class BaseController{
    public function __construct()
    {
        // 判断是否登陆
        if(!isset($_SESSION['id'])){
            redirect('/login/login');
        }
        // 获取要访问的路径
        $path = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'],'/') : 'index/index';
        //设置白名单 
        $wpath = ['index/index','index/top','index/menu','index/main'];
        // 判断是否有权访问 
        if(!in_array($path,array_merge($wpath,$_SESSION['url_path']))){
            die('无权访问');
        }
    }
}
?>