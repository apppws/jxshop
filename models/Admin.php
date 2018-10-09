<?php
namespace models;

class Admin extends Model
{
    // 设置这个模型对应的表
    protected $table = 'admin';
    // 设置允许接收的字段
    protected $fillable = ['username', 'password'];

    public function _before_write()
    {
        $this->data['password'] = md5($this->data['password']);
    }

   // 添加修改完之后 
    protected function _after_write()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : $this->data['id'];
        // var_dump($priId);
        // exit;
        $stmt = $this->_db->prepare("DELETE FROM admin_role WHERE admin_id=?");
        $stmt->execute([
            $id
        ]);
        // exit;
        // 预处理 
        $stmt = $this->_db->prepare("INSERT INTO admin_role(admin_id,role_id) VALUES(?,?)");
        // var_dump($stmt);
        // die;
        // 循环所有勾选的权限
        foreach ($_POST['role_id'] as $v) {
        // 执行 
            $stmt->execute([
                $id,
                $v
            ]);
        }
    }

    // 删除
    protected function _before_delete()
    {
        $stmt = $this->_db->prepare("DELETE FROM admin_role WHERE admin_id = ?");
        $stmt->execute([
            $_GET['id']
        ]);
    }

    // 登录注册
    public function login($username, $password)
    {
        // 预处理
        $stmt = $this->_db->prepare("SELECT * FROM admin WHERE username=? AND password=?");
        // 执行
        $stmt->execute([
            $username,
            md5($password)
        ]);
        // 取数据
        $info = $stmt->fetch(\PDO::FETCH_ASSOC);
        // 判断是否存在 并保存到sessin 中
        if ($info) {
            $_SESSION['id'] = $info['id'];
            $_SESSION['username'] = $info['username'];
            $_SESSION['url_path'] = $this->getUalPath($_SESSION['id']);
            // var_dump($_SESSION['url_path']);
            // exit;
        } else {
            throw new \Exception('用户名或者密码错误');
        }
    }
    // 退出登录
    public function logout()
    {
        $_SESSION = [];
    }

    // 获取一个管理员有权访问的路径  
    // 参数 用户id
    public function getUalPath($adminId)
    {
        $sql = "SELECT c.url_path 
                FROM admin_role a 
                LEFT JOIN role_privlege b ON a.role_id  = b.role_id
                LEFT JOIN privilege c ON b.pri_id=c.id
                WHERE a.admin_id=? AND c.url_path!=''";
        // 预处理
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([$adminId]);
        // 取数据
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // var_dump($data);
        // 把二维数组 转成一维数组
        $_ret = [];
        foreach ($data as $v) {
            // 判断是否有多个url_path
            if (FALSE === strpos($v['url_path'], ',')) {
                // 如果没有直接把那个地址拿来用
                $_ret[] = $v['url_path'];
            } else {
                // 如果有逗号合并
                $_t = explode(',', $v['url_path']);
                $_ret = array_merge($_ret, $_t);
            }
        }

        return $_ret;
    }
}