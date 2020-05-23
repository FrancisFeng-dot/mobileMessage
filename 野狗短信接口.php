function send_message($phone,$message){
    $url = 'http://www.smswst.com/api/httpapi.aspx?action=send&account=11111111111&password=55555&mobile='.$phone.'&content='.$message.'&sendTime=&AddSign=Y';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回    
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回    
    $r = curl_exec($ch);
    curl_close($ch); 
   //$a = getphcode($url,$_POST['username'],$sendcode);
   if($r){
    return'1';
   }else{
    return '0';
   }
}

function cutcode($str,$num){
    $ltrl = strlen($str);
     $start = $ltrl - $num; 
     $encoding = 'utf-8'; 
    $lstr = mb_substr($str,$start,$num,$encoding);
    return $lstr;
}

//野狗
function getcode($tel,$content)
{
    $rearr = array('code'=>1,'msg'=>'','data'=>array());
    if(!$tel){
        $rearr['code']=0;
        $rearr['msg']='号码为空，发送失败';
    }
    if($content=='verificate'){
        $sendcode=cutcode(time(),4);
        cache('phcode',''.$tel.'+'.$sendcode.'',18000);
        $content = '尊敬的用户，您的验证码是：'.$sendcode.'。在30分钟内有效，工作人员不会向您索取，请勿泄露。';
    }
    $message = 'http://www.smswst.com/api/httpapi.aspx?action=send&account=11111111111&password=@x123456&mobile='.$tel.'&content='.$content.'&sendTime=&AddSign=Y';
    $time=get_total_millisecond();
    $appId='wx7f78ed44b12f996f';
    $templateId=100343;
    $sign_key = 'yWBX5VvqAgw2NisCMIttnyMG92TTzTSE4eWHmIIT';
    $sign_data = array('mobile' => $tel, 'templateId' =>$templateId, 'timestamp' => $time,
        "params"=>'["'.$sendcode.'"]');
    // 以字母升序(A-Z)排列
    ksort($sign_data);
    // var_dump($sign_data);
    $sign_str = http_build_query($sign_data) . '&'. $sign_key;
    //DEBUG
    //生成数字签名的方法 https://docs.wilddog.com/guide/sms/signature.html#生成数字签名的方法
    $signature= hash("sha256", urldecode($sign_str));
    $url = "https://api.wilddog.com/sms/v1/${appId}/code/send";
    // 不同接口参数不同， 详细参数请参见 https://docs.wilddog.com
    $post_data = array ('signature' => $signature,"mobile" => $tel,"timestamp" => $time,"templateId" => $templateId,
        "params"=>'["'.$sendcode.'"]');
    $form_string= http_build_query($post_data);
    // // DEBUG
    // echo "打印sign_str\n";
    // var_dump($sign_str);
    // echo "打印signature\n";
    // var_dump($signature);
    // echo "打印发送的数据\n";
    // var_dump($form_string);
    $header = array(
        'Content-Type: application/x-www-form-urlencoded',
    );
    $ch = curl_init();
    // DEBUG 打印curl请求和响应调试日志
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, buildQuery($post_data));
    $output = curl_exec($ch);
    curl_close($ch);
    // DEBUG
    // echo "打印获得的数据\n";
    $re = json_decode($output, true);
    p($re);
    $rearr = [];
     $rearr['code']='0';
     $rearr['msg']='获取验证码失败！';
    if(isset($re['status'])&&$re['status']=='ok'){
        $rearr['code']='1';
     $rearr['msg']='获取验证码成功！';
    } 
    return $rearr;
}