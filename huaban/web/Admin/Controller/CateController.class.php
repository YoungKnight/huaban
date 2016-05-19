<?php
namespace Admin\Controller;
use Think\Controller;
class CateController extends CommonController {
    // 显示分类列表
    public function index(){
        // echo '后台分类列表';
        // 创建对象
        $cate=M('category');
        // var_dump($cates);
        // 获取每页显示的数量
        $num=!empty($_GET['num'])?$_GET['num']:5;
        // 获取关键字
        if(!empty($_GET['keyword'])){
            // 有关键字
            $where="name like '%".$_GET['keyword']."%'";
        }else{
            $where='';
        }
        // 查询满足要求的总记录数
        $count=$cate->where($where)->count();
        // 实例化分页类 传入总记录数和每页显示的记录数
        $Page=new \Think\Page($count,$num  );
        // 获取limit参数
        $limit=$Page->firstRow.','.$Page->listRows;
        // 执行查询
        $cates=$cate->where($where)->limit($limit)->select();
        $pages=$Page->show();//分页输出
        // 分配变量 
        $this->assign('cates',$cates);
        $this->assign('pages',$pages);
        // 解析模板
        $this->display();
    }

    // 显示分类的添加页面
    public function add(){
        // echo '分类的添加';
        //解析模板
        $this->display();
    }


    // 处理分类的数据添加
    public function insert(){
        // var_dump($_POST);die;
        $rules = array(
            array('name','','分类名已存在',0,'unique',1),
            array('name','require','分类名不能为空')
            );
        // var_dump($rules);die;
        // 
      
        $cate=M('category'); // 实例化User对象
        if (!$cate->validate($rules)->create()){     // 如果创建失败 表示验证没有通过 输出错误提示信息 
             $info= $cate->getError();
             $this->error($info, U('Admin/Cate/add'),3);die;
            // exit();
        }else{   
            // 验证通过 可以进行其他数据操作
            // 执行添加
            $res=$cate->add();
            // var_dump($res);die;
            $this->success('新增成功', U('Admin/Cate/index'),3);
        }
        // 创建表对象
        // $cate=M('category');
        // var_dump($cate);
        // var_dump($_POST);die;
        // 创建数据
        // $cate->create();
        // var_dump($res);
        // 处理cate字段
        // if($_POST['name']==''){
        //     $this->error('新增失败', U('Admin/Cate/add'),3);die;
        //    // redirect('/Admin/Cate/add/', 2, '添加失败,页面跳转中...');die;
        // }else{
        //     // 执行添加
        //     $res=$cate->add();
        //     // var_dump($res);die;
        //     $this->success('新增成功', U('Admin/Cate/index'),3);
        // }
        // var_dump($_POST['category']);die;
    }
    //修改状态
    public function change(){
        //var_dump($_GET);die;
        $id = $_GET['id'];
        $data['display'] = $_GET['display'];
        $cate = M('Category');
        $cate->where("id={$id}")->save($data);
        $this->redirect('index');
    }

    //分类的修改
    public function save(){
        // var_dump($_GET);
        $id=I('get.id');
        // 创建表对象
        $cate=M('category');
        // 查询当前用户的数据
        $info=$cate->find($id);
        // var_dump($info);die;
        $this->assign('info',$info);
        // 解析模板
        $this->display();
    }

    //执行修改
    public function update(){
        $rules = array(
            array('name','','分类名已存在',0,'unique',3),
            array('name','require','分类名不能为空'),
            );
        // var_dump($rules);die;
        
        // dump($_POST);
        // die();
        $cate = M("category"); // 实例化User对象
        // var_dump($cate->validate($rules)->create());die;
        if (!$cate->validate($rules)->create()){     // 如果创建失败 表示验证没有通过 输出错误提示信息 
             $massge= $cate->getError();
             $this->error($massge, U('Admin/Cate/save'),3);die;
            // exit();
        }else{   
            //验证通过 可以进行其他数据操作
            //执行添加
            $res=$cate->save();
            //var_dump($res);die;
            $this->success('修改成功', U('Admin/Cate/index'),3);
        }    
    }

    public function ajaxdel(){
        // var_dump($_GET);
        // 创建表对象
        $cate=M('category');
        // 获取id
        $id=$_GET['id'];
        // 执行删除
        $res=$cate->delete($id);
        // 向ajax返回数据
        if($res){
            echo 1;
        }else{
            echo 0;
        }
    }
    
}
    
    