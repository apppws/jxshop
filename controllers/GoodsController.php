<?php
namespace controllers;

use models\Goods;

class GoodsController extends BaseController{
    // 列表页
    public function index()
    {
        $model = new Goods;
        $data = $model->findAll();
        view('goods/index', $data);
    }
    // 获取子分类
    public function ajax_get_cat()
    {
        $id = (int)$_GET['id'];
        // 根据这个ID查询子分类
        $model = new \models\Category;
        $data = $model->getCat($id);
        // 转成 JSON
        echo json_encode($data);
    }

    // 显示添加的表单
        public function create()
        {
            // 取出一级分类
            $model = new \models\Category;
            $topCat = $model->getCat();

            view('goods/create', [
                'topCat' => $topCat['data'],
            ]);
        }

        

    // 处理添加表单
    public function insert()
    {
        $model = new Goods;
        $model->fill($_POST);
        $model->insert();
        redirect('/goods/index');
    }

    // 显示修改的表单
    public function edit()
    {
        // var_dump($_GET['id']);
        $model = new Goods;
        $data=$model->getFullInfo($_GET['id']);
        // echo "<pre>";
        // var_dump($data);
        // 取出一级分类
        $model = new \models\Category;
        $topCat = $model->getCat();
        // echo "<pre>";
        // var_dump($topCat);
        view('goods/edit', [
            'data' => $data,    
            'topCat' => $topCat['data']
        ]);
    }

    // 修改表单的方法
    public function update()
    {
        $model = new Goods;
        $model->fill($_POST);
        $model->update($_GET['id']);
        redirect('/goods/index');
    }

    // 删除
    public function delete()
    {
        $model = new Goods;
        $model->delete($_GET['id']);
        redirect('/goods/index');
    }
  
}