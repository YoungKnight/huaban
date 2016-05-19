<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends CommonController {
	//显示商品列表
    public function index(){
        //创建对象
        $goods = M('goods');

        $res = $goods->find(3);
        // var_dump($res);
        // echo $res['content'];


        $res = htmlspecialchars_decode($res['content']);
        echo $res;
        $res = $goods->find(4);
        // var_dump($res);
        echo '<hr>';
        echo $res['content'];
       //获取每页显示的数量
       $num = !empty($_GET['num']) ? $_GET['num'] : 2;

        //获取关键字
        if(!empty($_GET['keyword'])){
            //有关键字
            $where = "goodsname like '%".$_GET['keyword']."%'";
        }else{
            $where = '';
        }


        // 查询满足要求的总记录数
        $count = $goods->where($where)->count();
        // 实例化分页类 传入总记录数和每页显示的记录数
        $Page = new \Think\Page($count,$num);
        //获取limit参数
        $limit = $Page->firstRow.','.$Page->listRows;

         //执行查询
        $goodss = $goods->where($where)->limit($limit)->select();

        // 分页显示输出
        $pages = $Page->show();
      
        //处理hobby字段
        foreach ($goodss as $k => $v) {
           $goodss[$k]['hobby'] = str_replace(array('0','1','2'), array('篮球','足球','球球'),$v['hobby']);
        }
        
        //分配变量
        $this->assign('goodss',$goodss);
        $this->assign('pages',$pages);
    	//解析模板
    	$this->display();
    }

    //显示商品的添加页面
    public function add(){
        
        //创建对象
        $cate = M('cate');
        $goods = M('goods');
        
        //查询所有分类

        $res = $goods->find(4);
        // var_dump($res);
        // echo '<hr>';
        // echo $res['content'];

         //执行查询
        $cates = $cate->order('concat(path,id) asc')->select();

        //遍历
        foreach ($cates as $k => $v) {
            //获取要添加|---个数
            $c = count(explode(',',$v['path']))-2;
            $cates[$k]['name'] = str_repeat('|----',$c).$v['name'];

        }
        //分配变量
        $this->assign('cates',$cates);
        $this->assign('res',$res);


    	//解析模板
    	$this->display();
    }

    //处理商品的数据添加
    public function insert(){


        // var_dump($_POST);die;
        //调用函数处理图片上传
        Uploads('pic');
        // var_dump($_POST);die;
        $_POST['create_time'] = time();

        //创建表对象
        // $goods = M('goods');
        $goods = D('goods');
        //创建数据
        if(!$goods->create()){
            //获取错误信息
            $info = $goods->getError();
            $this->error($info);
        }
        // var_dump($_POST);die;
        //执行添加
       if($goods->add()){
             //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('新增成功', U('Admin/goods/index'),3);
       }else{
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败');
       }

    }

    //商品的修改
    public function save(){
        // var_dump($_GET);
        $id = I('get.id');
        //创建表对象
        $goods = M('goods');
        //查询当前商品的数据
        $info = $goods->find($id);
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
        $goods = M('goods');

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
            $res = $goods->find($_POST['id']);
            $pic = $res['pic'];
            // var_dump($pic);
            //删除图片
            unlink('.'.$pic);
        }


       
        //创建数据
        $res = $goods->create();
        //执行修改
       $res =  $goods->save();
       // var_dump($res);
        //执行添加
       if($res){
             //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('更新成功', U('Admin/goods/index'),3);
       }else{
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('更新失败');
       }
    }


    public function ajaxdel(){
        // var_dump($_GET);
        //创建表对象
        $goods = M('goods');
        //获取id
        $id = $_GET['id'];
        //执行删除
        $res = $goods->delete($id);

        // 向ajax返回数据
        if($res){
            echo 1;
        }else{
            echo 0;
        }
    }
}