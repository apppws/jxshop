<?php
    // 1 常量
    define("ROOT",__DIR__."/../");
    // 2 自动加载
    function autoload($class){
        $path = str_replace('\\','/',$class);
        require_once ROOT.$path.'.php';
    }
    spl_autoload_register('autoload');
    // 3 路由
    $controller = "IndexController";
    $action = "index";
    if(isset($_SERVER['PATH_INFO'])){
        $route = explode('/',$_SERVER['PATH_INFO']);
        $controller = '\controllers\\'.ucfirst($route[1]).'Controller';
        $action = $route[2];
    }
    $c = new $controller;
    $c->$action();
    // 4.视图
    function view($file,$data=[]){
        // 压缩
        if(isset($data))
        extract($data);
        include(ROOT.'views/'.$file.'.html');
    }
?>