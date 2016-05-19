<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {

	//功能类似构造方法,率先执行的方法
	public function _initialize(){
		// echo '你调用了我';
		//用户的登录检测
		// $id = session('uid');
		// //检测
		// if(empty($id)){
		// 	//没有登录
		// 	$this->error('您还没有登录',U('Admin/Login/index'),3);
		// }
	}
}