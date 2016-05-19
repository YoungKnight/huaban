<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
    

    	//解析模板
    	$this->display();
    }
}