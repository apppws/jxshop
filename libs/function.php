<?php

// 视图
function view($file, $data=[])
{
    // 压缩数组（为了页面中可以直接使用变量）
    extract($data);
    // var_dump($file);
    include(ROOT . '/views/'.$file.'.html');
}
