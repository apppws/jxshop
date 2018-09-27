<?php
    namespace models;
    use PDO;
    // 在这个父模型中 写 增 删 改 查
    class Model{
        
        // db属性
        protected $_db; 
        // 表名
        protected $table;
        // 表单中数据 由子类来设置
        protected $data;
        // 调用DB类
        public function __construct(){
            $this->_db = \libs\DB::make();
        }
        // 增
        public function insert(){
            // echo '0';
            // 先定义 键、值、？ 
            $keys = []; 
            $values = [];
            $to =[]; //用来保存 ？
            // 循环 
            foreach($this->data as $k=>$v){
                $keys[] = $k;
                $values[] = $v;
                $to[] = '?';
            }
            // 把键和值 数组转成字符串
            $keys = implode(',',$keys);
            // var_dump($keys);
            // var_dump($values);
            // var_dump($this->data);
            $to = implode(',',$to);
            // 执行sql语句
            $sql = "INSERT INTO {$this->table}($keys) VALUES ($to)";
            // var_dump($sql);
            // 预处理
            $stmt = $this->_db->prepare($sql);
            // 执行
             $stmt->execute($values);
            // $this->data['id'] = $this->_db->lastInsertId();
        }
        // 接收表单中的数据
        public function fill($data){
            
            // 判断是否在这个白名单中
            foreach($data as $k=>$v){
                // 判断如果没有就删除
                if(!in_array($k,$this->fillable)){
                    // 删除这个值的下标
                    unset($data[$k]);
                }
            }
            // var_dump($data);
            $this->data = $data;
            // var_dump($this->data);
            
        }
        // 删
        public function delete($id){
            // 预处理
            $stmt = $this->_db->prepare("DELETE FROM {$this->table} WHERE id =?");
            // 执行
            $stmt->execute([$id]);
        }
        // 更新
        public function update($id){
            
            $set = [];
            $to = [];
            foreach($this->data as $k=>$v){
                $set[] = "$k=?";
                $values[] = $v;
                $to[]="?";
            }
            $set = implode(',',$set);
            $values[] = $id;
            // 执行sql语句
            $sql = "UPDATE {$this->table} SET $set WHERE id=$to";
            // 预处理一下
            $stmt = $this->_db->prepare($sql);
            // 执行
            $stmt->execute($values);
        }
        // 查(所有)翻页
        public function findAll($option=[]){
            // 设定一些值
            $_option = [
                'fields'=> "*",   // 所有数据
                'where'=>1,   // 添加默认为 1
                'order_by'=>'id',  // 根据id查询
                'order_way'=>'desc',  //排序方式倒序
                'per_page'=>20,  // 每页 20 条
            ];
            // 合并用户的配置
            if($option){
                $_option = array_merge($_option,$option);  //如果有传数组 就把和$_option 合并为一个数组
            }
            /****翻页****/ 
            // 1 获取当前页
            $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
            // 2计算初始值  当前页-1 * 每页条数
            $offset = ($page-1)*$_option['per_page'];
            // 3 sql 语句 
            $sql = "SELECT {$_option['fields']}
                    FROM {$this->table}
                    WHERE {$_option['where']} 
                    ORDER BY {$_option['order_by']} {$_option['order_way']} 
                    LIMIT $offset,{$_option['per_page']}";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            /****获取总的记录数****/ 
            // 1 获取总的记录数
             $stmt = $this->_db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$_option['where']} ");
             $stmt->execute();
             $count = $stmt->fetch(PDO::FETCH_COLUMN);
            //2 总的页数  取整数并向上取整
             $pageCount = ceil($count/$_option['per_page']);
            // 3 翻页按钮
            $page_str="";
            // 4 判断总的页数是否>1
            if($pageCount>1){
                // 循环添加标签
                for($i=0;$i<=$pageCount;$i++){
                    $page_str .= '<a href="?page='.$i.'">'.$i.'</a> ';
                }
            }

            // 5 返回
            return [
                'data'=>$data, //数据
                'page'=>$page_str //按钮
            ];

        }
        // 查(一个)
        public function findOne($id){
            $stmt = $this->_db->prepare("SELECT * FROM {$this->table} WHERE id=?");
            $stmt->execute([
                $id
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        

    }
?>