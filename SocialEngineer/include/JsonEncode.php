<?php

    /**
     * @author random
     * @copyright 2013-08-06
     */
     
     
   
   
   
   
	//����json���ݸ�ʽ,��������Ľ��������ĵ�url���� �ر�ҪС�Ŀո�ᱻ����Ϊ%2b,��+��
	function GenJsonData( $type ='success', $data='ok',$isUrlEncode=0) {
		
        if($isUrlEncode){
    		//�ж�$data�Ƿ�Ϊ����
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


    
    
        //���$jsonp_callback��ֵ��Ϊ�յĻ�����ôʹ��jsonp��ʽ���䣬����ֻ��Ӧjson��ʽ
    function jsonpWrapper($jsondata){
        
        if(!defined("JSONP")){
            
            die("JSONP�궨��δ����!!");
        }
        $jsonp_callback = constant("JSONP");
        
        if(isset($jsonp_callback)&&strlen($jsonp_callback)>0){
            
            $jsondata = $jsonp_callback.'('.$jsondata.')';
        }
        
        return $jsondata;
        
    }   
    
    

?>