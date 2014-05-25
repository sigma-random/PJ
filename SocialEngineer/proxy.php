<?php

/**
 * @author random
 * @copyright 2013
 */

    $url = "http://random90s.xicp.net/SocialEngineer/search.php";
	$url = "http://127.0.0.1/socialengineer/search.php";
    /*
    $data = "";
    foreach($_REQUEST as $key => $value){
        
        $data .= $key."=".$value."&";
    }
    $target_url = $url.$data;
    //print $target_url;
    */
  
    print getHtmlContent($url,1,'POST',$_REQUEST);
    


?>


<?php

 	//获取网页内容
	function getHtmlContent($url,$type=1,$method='GET',$data=null,$cookie=null){
		
        $contents = null;
        switch($type){
                        
            case 1:{//支持GET、POST
            
                $contents = curl_content($url,$method,$data,$cookie);
                break;
            }            
            
            case 2:{//支持GET、POST
                $contents = fget_content($url,$method,$data,$cookie);
                break;
            }            
             
            case 3:{//只支持GET
                
                $contents = fopen_content($url,$method,$data,$cookie);
                break;
            }
        }
        
        /*
		$len = strlen($content);
		echo "len = ".$len."<br/>";

		//去除响应数据中的除了\r \n \t 以外的不可显示字符
		for($i=0;$i<$len;$i++){
			if($content[$i]<"\1F" && 
				//$content[$i] != "\r" &&
				$content[$i] != "\t"&&
				$content[$i] != "\n"){
				break;
			}
		}
		$content = substr($content,0,$i);
        */
		return $contents;  
	}   
   

    function curl_content($url,$method,$data,$cookie=null){
        //支持GET、POST
		$contents = null;
        if(0 == strcasecmp($method,'POST')){
            $data = http_build_query($data);
        }else{
            $method = 'GET';      
        }       
        //print "curl ".$method."......";
	    $ch = curl_init();
        if(!isset($ch)|| $ch == null){
            die("Don't support php_curl !");
        }
        curl_setopt($ch,CURLOPT_URL,$url);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if(0 == strcasecmp($method,'POST')){
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }
        $contents = curl_exec($ch);
        curl_close($ch);  
        return $contents; 

    }
    
    function fget_content($url,$method,$data,$cookie=null){
        //支持GET、POST
		$contents = null;
		$post_data = null;
        if(0 == strcasecmp($method,'POST')){
            $post_data = http_build_query($data); 
        }else{
            $method = 'GET';
        }    
        //print "file_get_contents ".$method."......";
		$opts = array (  
		'http' => array (  
		'method' => $method,  
		'header'=> "Cookie: ".$cookie."\r\n",
        'content'=> $post_data));  
		//生成请求的句柄文件  
		$context = stream_context_create($opts);  
		$contents  = @file_get_contents($url, false, $context);    
        return $contents;
    }
       
    function fopen_content($url,$method,$data,$cookie=null){
    
        //只支持GET
        $contents=null;            
        //print "fopen GET......";
        $fp = fopen($url, "r" ); 
        if($fp == NULL){
        	return $method;
        }
        //一次读1024字节
        while(!feof($fp))
        	$contents .= fread($fp,1024); 
        fclose($fp);
        return $contents;
    }

	function Logs($info="random test"){
		$data = NULL;
		$tabs = "\t";
		$newline = "\n\n";
		$markline = "";//"-----------------------------------------------------------------------------------------------------------------------------------------------------------";

		$file = "./protect/#servers#.txt";	
		$useragent = @$_SERVER['HTTP_USER_AGENT'];
		$server_ip = GetClientIp().'['.@$_SERVER['REMOTE_HOST'].']';
		$uri = $_SERVER['REQUEST_URI'] ;//."?file=".$_SERVER['QUERY_STRING']; 
		$reference = @$_SERVER['HTTP_REFERER'];
		$date = date("Y-m-d h:i:s");
		$data .= "Time".$tabs."==>".$tabs.$date.$newline;
		$data .= "Agent".$tabs."==>".$tabs.$useragent.$newline;
		$data .= "IP".$tabs."==>".$tabs.$server_ip.$newline;
		$data .= "URI".$tabs."==>".$tabs.$uri.$newline;
		$data .= "Refer".$tabs."==>".$tabs.$reference.$newline;
		if(isset($info))
		$data .= $markline.$newline;
		$f = fopen($file, 'a+');  
		fputs($f, $data);  
		fclose($f);	

	}

	//获取客户端IP
	function GetClientIp(){
		global $ip;
		if (getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
		else if(getenv("HTTP_X_FORWARDED_FOR"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if(getenv("REMOTE_ADDR"))
		$ip = getenv("REMOTE_ADDR");
		else $ip = "Unknow";
		return $ip;
	}


?>