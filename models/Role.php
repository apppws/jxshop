<?php
namespace models;

class Role extends Model
{
    // 设置这个模型对应的表
    protected $table = 'role';
    // 设置允许接收的字段
    protected $fillable = ['role_name'];

    // 添加修改完之后 
    protected function _after_write(){

        $priId = isset($_GET['id'])?$_GET['id']:$this->data['id'];
        // var_dump($priId);
        // exit;
        $stmt = $this->_db->prepare("DELETE FROM role_privlege WHERE role_id=?");
        $stmt->execute([
            $priId
        ]);
        // exit;
        // 预处理 
        $stmt = $this->_db->prepare("INSERT INTO role_privlege(pri_id,role_id) VALUES(?,?)");
        // var_dump($stmt);
        // die;
        // 循环所有勾选的权限
        foreach($_POST['pri_id'] as $v){
            // 执行 
            $stmt->execute([
                $v,
                $priId
            ]);
        }
    }
    // 在删除之前
    protected function _before_delete(){
        $stmt = $this->_db->prepare("DELETE FROM role_privilege WHERE role_id=?");
        $stmt->execute([
            $_GET['id']
        ]);
    }
}