//发送邮箱验证码
 function sendemail($toemail, $title, $message, $attachment = null)
{
    Vendor('PHPMailer.class#phpmailer');
    $mail = new PHPMailer();

    $mail->isSMTP();// 使用SMTP服务
    $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码

    $mail->SMTPDebug = 0; // 关闭SMTP调试功能
    $mail->SMTPAuth  = true; // 启用 SMTP 验证功能

    $mail->Host = config('CFG_EMAIL_HOST');// 发送方的SMTP服务器地址
    $mail->SMTPAuth = true;// 是否使用身份验证
    $mail->Username = config('CFG_EMAIL_LOGINNAME');// 发送方的163邮箱用户名，就是你申请163的SMTP服务使用的163邮箱
    $mail->Password = config('CFG_EMAIL_PASSWORD');// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！
   // $mail->SMTPSecure = "ssl";// 使用ssl协议方式
    $mail->Port = config('CFG_EMAIL_PORT') ? config('CFG_EMAIL_PORT') : 465;// 163邮箱的ssl协议方式端口号是465/994

    $mail->setFrom(config('CFG_EMAIL_FROM'),config('CFG_EMAIL_FROM_NAME'));// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
    $mail->addAddress($toemail);
    // 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
    // $mail->addReplyTo(config('CFG_EMAIL_LOGINNAME'),"Reply");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址"aqchudao@126.com"
    //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
    //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
    //$mail->addAttachment("bug0.jpg");// 添加附件
    $mail->Subject = $title;// 邮件标题"这是一个测试邮件"
    $mail->Body = $message; // 邮件正文"邮件内容是 <b>您的验证码是：123456</b>，哈哈哈！";
    $mail->IsHTML(true); //body is html
    //$mail->AltBody = "This is the plain text纯文本";//这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用

    // 添加附件
    if (is_array($attachment)) {
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }

    if(!$mail->send()){// 发送邮件
       return ['code'=>0,'msg'=>'发送失败，'.$mail->ErrorInfo];
        // echo "Message could not be sent.";
        // echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息
    }else{
    	return ['code'=>1,'msg'=>'发送成功'];
        // echo '发送成功';
    }
}

//获取验证码 判断这个手机号码是否被注册
function getphcode($tel,$content){
    $rearr = array('code'=>1,'msg'=>'','data'=>array());
    if(!$tel){
        $rearr['code']=0;
        $rearr['msg']='号码为空，发送失败';
    }
    if($content=='verificate'){
        $sendcode=cutcode(time(),4);
        cache('phcode',''.$tel.'+'.$sendcode.'',18000);
        $content = '尊敬的用户，您的验证码是：'.$sendcode.'。在30分钟内有效，红纽扣工作人员不会向您索取，请勿泄露。';
    }
    $message = 'http://www.smswst.com/api/httpapi.aspx?action=send&account=*******&password=*****&mobile='.$tel.'&content='.$content.'&sendTime=&AddSign=Y';
    $re = file_get_contents($message);
    $obj = simplexml_load_string($re);
    $rearr['code']='1';
    $rearr['msg']='发送短信成功！';
    return $rearr;
}