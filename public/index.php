<?php
    // 1 常量
    define('ROOT', __DIR__."/../");
    // var_dump(__DIR__);
    // 2 自动加载
    function autoload($class){
        $path = str_replace('\\', '/', $class);
        // var_dump($path);
        require(ROOT . $path . '.php');
    }
    spl_autoload_register('autoload');
    // 4引入函数文件
    require(ROOT.'libs/function.php');
    // 3 路由
    $controller = "\controllers\IndexController";
    $action = "index";
    if(isset($_SERVER['PATH_INFO'])){
        $route = explode('/',$_SERVER['PATH_INFO']);
        $controller = '\controllers\\'.ucfirst($route[1]).'Controller';
        $action = $route[2];
    }
    $c = new $controller;
    $c->$action();
   
?>