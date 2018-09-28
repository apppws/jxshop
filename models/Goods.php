<?php
namespace models;

class Goods extends Model
{
    // 设置这个模型对应的表
    protected $table = 'goods';
    // 设置允许接收的字段
    protected $fillable = ['goods_name', 'logo', 'is_on_sale', 'description', 'cat1_id', 'cat2_id', 'cat3_id', 'brand_id'];
   
    // 执行钩子函数
    public function _before_write()
    {
        $this->_delete_img();
        // 实现上传图片的代码
        $uploadfile = new \libs\Uploadfile;
        $logo = '/uploads/' . $uploadfile->upload('logo', 'brand');
        $this->data['logo'] = $logo;
    }

    // 钩子函数在删除之前被调用
    public function _before_delete()
    {
        $this->_delete_img();
    }

    // 删除原图片
    public function _delete_img()
    {
        // 判断如果是修改就删除
        if (isset($_GET['id'])) {
            // 先从数据库中取出原logo
            $oldlogo = $this->findOne($_GET['id']);
            // 删除
            @unlink(ROOT . 'public' . $oldlogo['logo']);
        }
    }

    // 添加 修改之后执行
    public function _after_write()
    {
        // var_dump($_FILES);
        /**
         * 一、处理商品属性
         */
        // 1 先预处理数据
        $stmt = $this->_db->prepare("INSERT INTO goods_attribute(attr_name,attr_value,goods_id) VALUES (?,?,?)"); 
        // 2 循环每个属性 插入到属性表中
        foreach($_POST['attr_name'] as $k=>$v){
            // 执行数据 
            $stmt->execute([
                $v,
                $_POST['attr_value'][$k],
                $this->data['id']
            ]);
        }

        /**
         * 二、商品图片
         */
        // 1 调用上传图片libs  
        $uploadfile  = new \libs\Uploadfile;
        // 2 预处理
        $stmt = $this->_db->prepare("INSERT INTO goods_image(goods_id,path) VALUES (?,?)");
        // var_dump($stmt);
        // die;
        // 3 循环
        $_tmp = []; 
        // var_dump($_FILES);
        // die;
        foreach($_FILES['image']['name'] as $k=>$v){
            // 拼出每张图片需要的数组
            $_tmp['name'] = $v;
            $_tmp['type'] = $_FILES['image']['type'][$k];
            $_tmp['tmp_name'] = $_FILES['image']['tmp_name'][$k];
            $_tmp['error'] = $_FILES['image']['error'][$k];
            $_tmp['size'] = $_FILES['image']['size'][$k];

            // 把files 文件放到这个变量中
            $_FILES['tmp']=$_tmp;
            // 保存
            $path = '/uploads/' . $uploadfile->upload('tmp','goods');
            // var_dump($path);
            // die;
            // 执行sql 语句
            $stmt->execute([
                $this->data['id'],
                $path
            ]);
        }

        /**
         * SKU 
         */
        // 1 预处理
        $stmt = $this->_db->prepare("INSERT INTO goods_sku(goods_id,sku_name,stock,price) VALUES(?,?,?,?)");
        // 2 循环
        foreach($_POST['sku_name'] as $k=>$v){
            // 3 执行
            $stmt->execute([
                $this->data['id'],
                $v,
                $_POST['stock'][$k],
                $_POST['price'][$k]
            ]);
        }

        
    }

}