<?php
namespace models;

class Goods extends Model
{
    // 设置这个模型对应的表
    protected $table = 'goods';
    // 设置允许接收的字段
    protected $fillable = ['goods_name', 'logo', 'is_on_sale', 'description', 'cat1_id', 'cat2_id', 'cat3_id', 'brand_id'];
   
    
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
    // 执行钩子函数
    public function _before_write()
    {
        // 判断如果上传了logo  就删除原logo上传新的logo  
        // error 0 代表上传了  4代表没有上传
        if($_FILES['logo']['error']==0){
            $this->_delete_img();
            // 实现上传图片的代码
            $uploadfile = new \libs\Uploadfile;
            $logo = '/uploads/' . $uploadfile->upload('logo', 'goods');
            $this->data['logo'] = $logo;
        }
       
    }



    // 添加 修改之后执行
    public function _after_write()
    {
        $goodId = isset($_GET['id'])?$_GET['id']:$this->data['id'];
        // var_dump($_FILES);
        /**
         * 一、处理商品属性
         */
        // 0 先删除原来的数据
        $stmt = $this->_db->prepare("DELETE FROM goods_attribute WHERE goods_id=?");
        $stmt->execute([$goodId]);

        // 1 先预处理数据
        $stmt = $this->_db->prepare("INSERT INTO goods_attribute(attr_name,attr_value,goods_id) VALUES (?,?,?)"); 
        // 2 循环每个属性 插入到属性表中
        foreach($_POST['attr_name'] as $k=>$v){
            // 执行数据 
            $stmt->execute([
                $v,
                $_POST['attr_value'][$k],
                $goodId
            ]);
        }

        /**
         * 二、商品图片
         */
        // 0 如果有要删除图片的id  那就把图片给删除
        if(isset($_POST['del_image']) && $_POST['del_image']!=''){
            // 0.1 先根据id把图片给取出来
            $stmt = $this->_db->prepare("SELECT path FROM goods_image WHERE id IN ({$_POST['del_image']})");
            $stmt->execute();
            $path = $stmt->fetch(\PDO::FETCH_ASSOC);
            // 0.2 循环得到这个path 并删除
            foreach($path as $v){
                @unlink(ROOT."public/".$v['path']);
            }
            // 0.3 从数据库中的图片记录删除
            $stmt = $this->_db->prepare("DELETE FROM goods_image WHERE id IN ({$_POST['del_image']})");
            $stmt->execute();
        }
        // 1 调用上传图片libs  
        $uploader = \libs\Uploadfile::file();
        // 2 预处理
        $stmt = $this->_db->prepare("INSERT INTO goods_image(goods_id,path) VALUES (?,?)");
        // var_dump($stmt);
        // die;
        // 3 循环
        $_tmp = []; 
        // var_dump($_FILES);
        // die;
        foreach($_FILES['image']['name'] as $k=>$v){
            // 如果有图片并且上传成功时才处理图片
            if($_FILES['image']['error'][$k] == 0)
            {
                // 拼出每张图片需要的数组
                $_tmp['name'] = $v;
                $_tmp['type'] = $_FILES['image']['type'][$k];
                $_tmp['tmp_name'] = $_FILES['image']['tmp_name'][$k];
                $_tmp['error'] = $_FILES['image']['error'][$k];
                $_tmp['size'] = $_FILES['image']['size'][$k];

                // 把files 文件放到这个变量中
                $_FILES['tmp']=$_tmp;
                // 保存
                $path = '/uploads/' . $uploader->upload('tmp','goods');
                // var_dump($path);
                // die;
                // 执行sql 语句
                $stmt->execute([
                    $goodId,
                    $path
                ]);
            }
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
                $goodId,
                $v,
                $_POST['stock'][$k],
                $_POST['price'][$k]
            ]);
        }

        
    }

    // 取出所有数据
    public function getFullInfo($id){
        //基本信息
        $stmt = $this->_db->prepare('SELECT * FROM goods WHERE id=?');
        $stmt->execute([$id]);
        $info = $stmt->fetch(\PDO::FETCH_ASSOC);
        // 商品属性
        $stmt = $this->_db->prepare('SELECT * FROM goods_attribute WHERE goods_id=?');
        $stmt->execute([$id]);
        $arrts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 商品图片
        $stmt = $this->_db->prepare('SELECT * FROM goods_image WHERE goods_id=?');
        $stmt->execute([$id]);
        $images = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // var_dump($images);
        // 商品图片
        $stmt = $this->_db->prepare('SELECT * FROM goods_sku WHERE goods_id=?');
        $stmt->execute([$id]);
        $skus = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 返回
        return [
            'info'=>$info,
            'arrts'=>$arrts,
            'images'=>$images,
            'skus'=>$skus
        ];
    }

    
    // 钩子函数在删除之前被调用
    public function _before_delete()
    {
        $this->_delete_img();
    }

}