<?php
	/**
	 * @author random
	 * @copyright 2013
	 *
	 *	使用google web api搜索后缀为action和do的url
	 */
	error_reporting(1);
	set_time_limit(0);
	define("API_KEY","AIzaSyDZaIz2AJ0L-xt2G_BJdWo01nKeJlUZ95I");
	define("WAIT_TIME",120);
	define("URL_FILE","url.dat");


	google_new_search();
	
	
	//google_old_search();

	//使用旧的google web search api
	function google_old_search(){
		/*
			http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=inurl:home.action&start=63&rsz=1
		*/
		$q = @$_REQUEST['q'] != null ?@$_REQUEST['q']:'filetype:action';
		$rsz =  @$_REQUEST['size'] != null ?@$_REQUEST['size']:8 ;
		$start =  @$_REQUEST['index'] != null ?@$_REQUEST['index']:0 ;
		$url = "http://ajax.googleapis.com/ajax/services/search/web";
		$responseStatus = null;
		$resultTotalCount = 0;
		$count = 0;	
		$content = null;
		$json = null;

		saveDataToFile(date('y-m-d h:i:s'));
		do {
			print "\n===============================================================\n";
			$get_data = "v=1.0&q=".$q."&start=".$start."&rsz=".$rsz;
			$content = getHttpsContent($url,'GET',$get_data);
			//print $content."\n";
			$json = json_decode($content );	
			//var_dump( $json );
			$responseStatus = $json->{'responseStatus'};
			print "responseStatus: ".$responseStatus."\n";
			if($responseStatus == '200' ){
				$resultTotalCount = $json->{'responseData'}->{'cursor'}->{'estimatedResultCount'};	 	//$json->{'responseData'}->{'cursor'}->{'resultCount'};	 
				print "resultTotalCount: ".$resultTotalCount."条\n";
				$count  = count($json->{'responseData'}->{'results'});
				print "count: " . $count."\n";
				foreach( $json->{'responseData'}->{'results'} as $result){
					$action_url = $result->{'unescapedUrl'};
					$action_title = $result->{'title'};
					print "action_url: ".$action_url."\n";
					saveDataToFile(extractUrl($action_url));
				}
			}else if($responseStatus == '403' ){		//被google限制搜索则停止60秒
				print "Restricted by google !!!! Waitting for ".constant("WAIT_TIME")." seconds...\n";
				sleep(constant("WAIT_TIME"));
				continue;
			}else if($responseStatus == '400' ){		//搜索完毕
				print "Search over...\n";
				break;
			}
			$start += $count;
			sleep(5);
		}while( $responseStatus == '200' ||  $responseStatus == '403');

	}

	//使用最新的google web search api，需要提供正确有效的Google API KEY
	function google_new_search(){
		/*
			Google API KEY  ： AIzaSyDZaIz2AJ0L-xt2G_BJdWo01nKeJlUZ95I

			https://www.googleapis.com/customsearch/v1?key=AIzaSyDZaIz2AJ0L-xt2G_BJdWo01nKeJlUZ95I&cx=013036536707430787589:_pqjad5hr1a&q=inurl:home.action&alt=json&num=1&start=0
		*/
		$API_KEY = "AIzaSyDZaIz2AJ0L-xt2G_BJdWo01nKeJlUZ95I";
		$q = @$_REQUEST['q'] != null ?@$_REQUEST['q']:'filetype:action';
		$num =  @$_REQUEST['size'] != null ?@$_REQUEST['size']:8 ;
		$start =  @$_REQUEST['index'] != null ?@$_REQUEST['index']:0 ;
		$url = "https://www.googleapis.com/customsearch/v1";
		$searchTime = 0;
		$resultTotalCount = 0;
		$count = 0;	
		$content = null;
		$json = null;

		saveDataToFile(date('y-m-d h:i:s'));
	 
			print "\n===============================================================\n";
			$get_data = "key=".$API_KEY."&cx=013036536707430787589:_pqjad5hr1a"."&q=".$q."&start=".$start."&num=".$num;
			$content = getHttpsContent($url,'GET',$get_data);
			print $content."\n";
			$json = json_decode($content );	
			//var_dump( $json );
			$resultTotalCount = $json->{'searchInformation'}->{'totalResults'};	 	
			print "resultTotalCount: ".$resultTotalCount."条\n";
			$searchTime = $json->{'searchInformation'}->{'searchTime'};
			print "searchTime: ".$searchTime."秒\n";
			$count  = count($json->{'items'});
			print "count: " . $count."\n";
			foreach( $json->{'items'} as $result){
				$action_url = $result->{'link'};
				$action_title = $result->{'title'};
				print "action_url: ".$action_url."\n";
				saveDataToFile(extractUrl($action_url));
			}
		 
			$start += $count;
			sleep(5);
	 
	}
	

	//匹配以do和action作为后缀的url
	function extractUrl($url){
		//$url = "http://www.umu.se/ViewPage.do?siteNodeId=4510&languageId=1&contentId=216307";
		preg_match("/^(http:\/\/)*([^\?]*\.(action|do))(\?\S*)?/is",$url,$matchs);
		return $matchs[1].$matchs[2];
	}

	function saveDataToFile($data){
		$fp = fopen(constant("URL_FILE"),"a+");
		if($fp!=null && strlen($data)>0 ){
			fputs($fp,$data);
			fputs($fp,"\n");
			fclose($fp);
		}
	}

	function getHttpsContent($url,$method,$data,$cookie=null){
        //支持GET、POST
		$contents = null;
        if(0==strcasecmp($method,'POST')){
            $data = http_build_query($data);
        }else{
            $method='GET';
            $url=$url.'?'.$data;
        }
		print "url=".$url."\n";
	    $ch = curl_init();
        if(!isset($ch)|| $ch==null){
            die("Don't support php_curl !");
        }
        curl_setopt($ch,CURLOPT_URL,$url);
        //curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        if(0==strcasecmp($method,'POST')){
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }
        $contents = curl_exec($ch);
        curl_close($ch);  
        return $contents; 
 
	}
	
?>

 