<?php
namespace controllers;

use models\Role;

class RoleController extends BaseController{
    // 列表页
    public function index()
    {
        $model = new Role;
        $data = $model->findAll([
            'fields'=>'a.id,a.role_name,GROUP_CONCAT(c.pri_name) pri_list',
            'join'=>'a left join role_privlege b on a.id=b.role_id  left join privilege c on b.pri_id=c.id ',
            'groupby'=>'group by a.id'
        ]);
        // var_dump($data);
        view('role/index', $data);
    }

    // 显示添加的表单
    public function create()
    {
        // 取出所有权限 
        $primodel = new \models\Privilege;
        $data = $primodel->tree();
        // var_dump($data);
        view('role/create',[
            'data'=>$data
        ]);
    }

    // 处理添加表单
    public function insert()
    {
        $model = new Role;
        $model->fill($_POST);
        $model->insert();
        redirect('/role/index');
    }

    // 显示修改的表单
    public function edit()
    {
        $model = new Role;
        $data=$model->findOne($_GET['id']);
        
        $primodel = new \models\Privilege;
        $priData = $primodel->tree();

        view('role/edit', [
            'data' => $data,   
            'priData'=>$priData 
        ]);
    }

    // 修改表单的方法
    public function update()
    {
        $model = new Role;
        $model->fill($_POST);
        $model->update($_GET['id']);
        redirect('/role/index');
    }

    // 删除
    public function delete()
    {
        $model = new Role;
        $model->delete($_GET['id']);
        redirect('/role/index');
    }
}