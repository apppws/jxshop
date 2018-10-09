<?php 
namespace controllers;
class BaseController{
    public function __construct()
    {
        // 判断是否登陆
        if(!isset($_SESSION['id'])){
            redirect('/login/login');
        }
    }
}
?>