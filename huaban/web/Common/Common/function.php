<?php 
	
		function sendMail($to, $title ,$content){
		//步骤  
		//1复制文件到当前项目下的Thinkphp/libary/Org/Util
		 // (class.pop3.php class.smtp.php class.phpmailer.php)
		//2.修改类文件的名称
		//3.修改命名空间
	  // 4.注意在PHPMailer中最后一个继承
		   $mail = new \Org\Util\PHPMailer();
	       $mail->CharSet = "utf-8";  //设置采用utf8中文编码
	       $mail->IsSMTP(); //设置采用SMTP方式发送邮件
	       $mail->Host = "smtp.163.com"; //设置邮件服务器的地址
	       $mail->Port = 25; //设置邮件服务器的端口，默认为25
	       $mail->From = C('EmailUsername');  //设置发件人的邮箱地址
	       $mail->FromName = "我的小站"; //设置发件人的姓名
	       $mail->SMTPAuth = true;//设置SMTP是否需要密码验证，true表示需要
	       $mail->Username = C('EmailUsername');
	       $mail->Password = C('EmailPassword');
	       $mail->Subject = $title;  //设置邮件的标题

	       $mail->AltBody = "text/html"; // optional, comment out and test

	       $mail->Body = $content;

	       $mail->IsHTML(true);//设置内容是否为html类型

	       $mail->AddAddress(trim($to), $name);  //设置收件的地址
	       if (!$mail->Send()) {            //发送邮件
	           echo '发送失败:'.$mail->ErrorInfo;
	       } else {
	           echo "发送成功";
	       }
		}
	

	function Uploads($filename){
		//处理图片
		if($_FILES[$filename]['error'] == 0){
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
		        $str =  ltrim($upload->rootPath,'.').$info[$filename]['savepath'].$info[$filename]['savename'];
		        $_POST[$filename] = $str;
		    }
		}
	}

	// 检测输入的验证码是否正确，$code为用户输入的验证码字符串
	function check_verify($code, $id = ''){    
		$verify = new \Think\Verify();    
		return $verify->check($code, $id);
	}







 ?>