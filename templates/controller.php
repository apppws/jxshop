namespace controllers;
class <?=$cname?>{
    // 列表页
    public function index(){
        view('<?=$tableName?>/index');
    }
    // 增加
    public function create(){
        view('<?=$tableName?>/create');
    }
    // 处理添加的页面
    public function insert(){
        
    }
    // 删除
    public function delete(){
        
    }
    // 修改
    public function edit(){
        view('<?=$tableName?>/edit');
    }
    // 处理修改页面
    public function upload(){

    }
}
