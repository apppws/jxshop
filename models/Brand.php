<?php
namespace models;

class Brand extends Model
{
    // 设置这个模型对应的表
    protected $table = 'brand';
    // 设置允许接收的字段
    protected $fillable = ['brand_name','logo'];

    // 执行钩子函数
    public function _before_write()
    {
        $this->_delete_img();
        // 实现上传图片的代码
        $uploadfile = new \libs\Uploadfile;
        $logo = '/uploads/' .$uploadfile->upload('logo','brand');
        $this->data['logo'] = $logo;
    }

    // 钩子函数在删除之前被调用
    public function _before_delete()
    {
        $this->_delete_img();
    }

    // 删除原图片
    public function _delete_img(){
        // 判断如果是修改就删除
        if(isset($_GET['id'])){
            // 先从数据库中取出原logo
            $oldlogo = $this->findOne($_GET['id']);
            // 删除
            @unlink(ROOT.'public'.$oldlogo['logo']);
        }
    }
}