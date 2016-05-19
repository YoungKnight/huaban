<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends CommonController {
	//显示用户列表
    public function index(){
        // echo '后台用户列表';
        //创建对象
        $user = M('user');
   
        //查看sql语句
        // echo $user->_sql();
        // echo $user->getLastSql();

       //获取每页显示的数量
       $num = !empty($_GET['num']) ? $_GET['num'] : 2;

        //获取关键字
        if(!empty($_GET['keyword'])){
            //有关键字
            $where = "username like '%".$_GET['keyword']."%'";
        }else{
            $where = '';
        }


        // 查询满足要求的总记录数
        $count = $user->where($where)->count();
        // 实例化分页类 传入总记录数和每页显示的记录数
        $Page = new \Think\Page($count,$num);
        //获取limit参数
        $limit = $Page->firstRow.','.$Page->listRows;

         //执行查询
        // $users = $user->join('userinfo on user.id = userinfo.uid')->where($where)->limit($limit)->select();
        $users = $user->join('left join userinfo on user.id = userinfo.uid')->where($where)->select();

        var_dump($users);
        // 分页显示输出
        $pages = $Page->show();
      
        //处理hobby字段
        foreach ($users as $k => $v) {
           $users[$k]['hobby'] = str_replace(array('0','1','2'), array('篮球','足球','球球'),$v['hobby']);
        }
        
        //分配变量
        $this->assign('users',$users);
        $this->assign('pages',$pages);
    	//解析模板
    	$this->display();
    }

    //显示用户的添加页面
    public function add(){
    	// echo '用户添加';
    	//解析模板
    	$this->display();
    }

    //处理用户的数据添加
    public function insert(){

        //创建表对象
        $user = M('user');
        // var_dump($user);
      
        //处理图片
        if($_FILES['pic']['error'] == 0){
            $upload = new \Think\Upload();// 实例化上传类    
            $upload->maxSize   =     3145728 ;// 设置附件上传大小    
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
            $upload->rootPath  =       './Public';
            $upload->savePath  =      '/Uploads/'; // 设置附件上传目录   
            // 上传文件     
            $info   =   $upload->upload();    
            if(!$info) {// 上传错误提示错误信息       
                $this->error($upload->getError());    
            }else{// 上传成功        
                // $this->success('上传成功！'); 
                $str =  ltrim($upload->rootPath,'.').$info['pic']['savepath'].$info['pic']['savename'];
                $_POST['pic'] = $str;
            }
        }

        //创建模型
        // $m = new \Think\Model();
        $m = M();
        //开启事物
        $m->startTrans();

        //创建数据
        $user->create();
        //添加数据
        $id = $user->add();
        // var_dump($id);die;
        if(!$id){
        	//事物回滚
       		$m->rollback();
        }

        //创建附表
        $userinfo = M('userinfo');

        //处理uid
        $_POST['uid'] = $id;
    

          //处理hobby字段
        if(!empty($_POST['hobby'])){
            $_POST['hobby'] = implode(',', $_POST['hobby']);
        }
        //创建附表数据
        $userinfo->create();
        //执行添加
        $res = $userinfo->add();
        // var_dump($res);
       if($res){
            //事物的提交
       		$m->commit();
             //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('新增成功', U('Admin/User/index'),3);
       }else{
           	//事物回滚
       		$m->rollback();
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败');
       }

    }

    //用户的修改
    public function save(){
        // var_dump($_GET);
        $id = I('get.id');
        //创建表对象
        $user = M('user');
        //查询当前用户的数据
        $info = $user->find($id);
        // var_dump($info);die;
        //处理爱好字段
        $hobby = explode(',',$info['hobby']);

        //分配变量
        $this->assign('info',$info);
        $this->assign('hobby',$hobby);

        //解析模板
        $this->display();
    }

    //执行修改
    public function update(){
        // var_dump($_POST);
         //创建数据表对象
        $user = M('user');

        //处理hobby字段
        if(!empty($_POST['hobby'])){
            $_POST['hobby'] = implode(',', $_POST['hobby']);
        }

        //处理图片
        if($_FILES['pic']['error'] == 0){
            $upload = new \Think\Upload();// 实例化上传类    
            $upload->maxSize   =     3145728 ;// 设置附件上传大小    
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
            $upload->rootPath  =       './Public';
            $upload->savePath  =      '/Uploads/'; // 设置附件上传目录   
            // 上传文件     
            $info   =   $upload->upload();    
            if(!$info) {// 上传错误提示错误信息       
                $this->error($upload->getError());    
            }else{// 上传成功        
                // $this->success('上传成功！'); 
                $str =  ltrim($upload->rootPath,'.').$info['pic']['savepath'].$info['pic']['savename'];
                $_POST['pic'] = $str;
            }

            //获取原来图片的;路径
            $res = $user->find($_POST['id']);
            $pic = $res['pic'];
            // var_dump($pic);
            //删除图片
            unlink('.'.$pic);
        }


       
        //创建数据
        $res = $user->create();
        //执行修改
       $res =  $user->save();
       // var_dump($res);
        //执行添加
       if($res){
             //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('更新成功', U('Admin/User/index'),3);
       }else{
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('更新失败');
       }
    }


    public function ajaxdel(){
        // var_dump($_GET);
        //创建表对象
        $user = M('user');
        //获取id
        $id = $_GET['id'];
        //执行删除
        $res = $user->delete($id);

        // 向ajax返回数据
        if($res){
            echo 1;
        }else{
            echo 0;
        }
    }
}