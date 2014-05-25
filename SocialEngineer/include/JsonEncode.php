<?php

    /**
     * @author random
     * @copyright 2013-08-06
     */
     
     
   
   
   
   
	//生成json数据格式,如果是中文将返回中文的url编码 特别要小心空格会被编码为%2b,即+号
	function GenJsonData( $type ='success', $data='ok',$isUrlEncode=0) {
		
        if($isUrlEncode){
    		//判断$data是否为数组
            if(is_array($data)){
                foreach($data as &$value){
                    $value = urlEncode(iconv('gb2312','utf-8',$value ));
                     //$value = urlEncode($value);
                }
            }else{
                $data = urlEncode(iconv('gb2312','utf-8',$data));
                 //$data = urlEncode($data);
            }
        }
		$data = json_encode(array("result"=>$type,"data"=>$data));
        
		return jsonpWrapper($data);
	
    }


    
    
        //如果$jsonp_callback的值不为空的话，那么使用jsonp格式传输，否则只适应json格式
    function jsonpWrapper($jsondata){
        
        if(!defined("JSONP")){
            
            die("JSONP宏定义未设置!!");
        }
        $jsonp_callback = constant("JSONP");
        
        if(isset($jsonp_callback)&&strlen($jsonp_callback)>0){
            
            $jsondata = $jsonp_callback.'('.$jsondata.')';
        }
        
        return $jsondata;
        
    }   
    
    

?>