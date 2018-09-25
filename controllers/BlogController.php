<?php
namespace controllers;
class BlogController{
    // 列表页
    public function index(){
        view('blog/index');
    }
    // 增加
    public function create(){
        view('blog/create');
    }
    // 处理添加的页面
    public function insert(){
        
    }
    // 删除
    public function delete(){
        
    }
    // 修改
    public function edit(){
        view('blog/edit');
    }
    // 处理修改页面
    public function upload(){

    }
}
