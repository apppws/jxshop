<?php
namespace controllers;
use PDO;
class CodeController{

    // 生成代码
    public function make(){

        // 1 生成代码的表名 先接收
        $tableName = $_GET['name'];
        // 取出这个表中所有的字段信息
        $sql = "SHOW FULL FIELDS FROM $tableName";
        $db = \libs\Db::make();
        // 预处理
        $stmt = $db->prepare($sql);
        // 执行 SQL
        $stmt->execute();
        // 取出数据
        $fields = $stmt->fetchAll( \PDO::FETCH_ASSOC );

        // 收集所有字段的白名单
        $fillable = [];
        foreach($fields as $v)
        {
            if($v['Field'] == 'id' || $v['Field'] == 'created_at')
                continue ;
            $fillable[] = $v['Field'];
        }
        $fillable = implode("','", $fillable);
        $mname = ucfirst($tableName);
        // 2 生成控制器
        $cname = ucfirst($tableName).'Controller';
        /**
         * 加载模板(控制器)
         * */
         //1.开启缓存区
        ob_start(); 
        // 2.引入这个文件加载到缓存区
        include(ROOT.'templates/controller.php');
        // 3 清除缓存
        $str = ob_get_clean();
        // 4 取数据 并放到哪个地址
        file_put_contents(ROOT.'controllers/'.$cname.'.php',"<?php\r\n".$str);
        /**
         * 加载模板(模型)
         * */
        
        // var_dump($mname);
         //1.开启缓存区
         ob_start(); 
         // 2.引入这个文件加载到缓存区
         include(ROOT.'templates/model.php');
         // 3 清除缓存
         $str = ob_get_clean();
         // 4 取数据 并放到哪个地址
         file_put_contents(ROOT.'models/'.$mname.'.php',"<?php\r\n".$str);
         /**
         * 加载模板(视图)
         * */
        // 生成视图目录 如果有这个目录就不用报错
        @mkdir(ROOT.'views/'.$tableName,0777);

        // 取出表中的字段 
        $sql = "SHOW FULL FIELDS FROM $tableName";
        $db = \libs\DB::make();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        // 取数据
        $filed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($filed);
        // exit;
        //1.开启缓存区
        ob_start(); 
        // 2.引入这个文件加载到缓存区
        include(ROOT.'templates/create.html');
        // 3 清除缓存
        $str = ob_get_clean();
        // 4 取数据 并放到哪个地址
        file_put_contents(ROOT.'views/'.$tableName.'/create.html', $str);

        //1.开启缓存区
        ob_start(); 
        // 2.引入这个文件加载到缓存区
        include(ROOT.'templates/edit.html');
        // 3 清除缓存
        $str = ob_get_clean();
        // 4 取数据 并放到哪个地址
        file_put_contents(ROOT.'views/'.$tableName.'/edit.html', $str);

        //1.开启缓存区
        ob_start(); 
        // 2.引入这个文件加载到缓存区
        include(ROOT.'templates/index.html');
        // 3 清除缓存
        $str = ob_get_clean();
        // 4 取数据 并放到哪个地址
        file_put_contents(ROOT.'views/'.$tableName.'/index.html', $str);
    }

}

?>