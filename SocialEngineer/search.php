<?php
    
    /**
     * @author random
     * @copyright 2013-08-06
     */
     
    //session_start(); 
    
    error_reporting(true);
    set_time_limit(999999);
    
    //header("Content-type: text/html; charset=gb2312");//charset=utf-8
    
    
    //����jsonp������include/JsonEncode.php�е�JsonpWrapper����Ҫ�õ�
    define("JSONP",@$_REQUEST['callback']);
    
    
    require_once("include/StartPHPIDS.php");
    require_once("include/MysqlOperater.php");
    require_once("include/JsonEncode.php");
    require_once("include/timer.php"); 
    require_once("include/GenHtml.php");    
    
      
    //session_start();
    

    $debug = false;
    
    $pageNum = 8;           //ÿҳ��ʾ8����¼
    
    $configData = array();  //�������3��������Ϣ������
    
    $method = @$_REQUEST['m'];
    
    
    

    if(!isset($method)){
        print GenJsonData("error","�����������!",1);
        exit;
    }
    
    switch($method){
        
        //��ȡ���ݿ��д�ŵ����ݿ��Ӧ����վ����
        case 1:{
            
            print getConfigData();
            break;
            
        }
        
        //��ѯ���ݿ�
        case 2:{

            $db_id = @$_REQUEST['dbs'];
            
            $col_id = @$_REQUEST['cols'];
            
            $keyword = @$_REQUEST['query'];

            $page = @$_REQUEST['page'];

            $searchtype = @$_REQUEST['st'];         //��������:�û��������䡢�绰��

            $searchmethod = @$_REQUEST['sm'];       //������ʽ: 1����ȷ������2��ģ������
            
           
            $keyword = rtrim($keyword);
            
            if( $searchmethod == '2' && strlen($keyword) < 5 ){
                
                print GenJsonData("error","�ݲ��ṩ����С��5��ģ������!",1);
                break;
                
            }
     
            if(!isset($db_id)){
                print GenJsonData("error","��ѡ��Ҫ��ѯ�����ݿ�!",1);    //��ѡ��Ҫ��ѯ�����ݿ�!
                break;
            }
            
   
            
            if(!isset($col_id)){
                print GenJsonData("error","��ѡ��Ҫ��ѯ���ֶ�!",1);    //��ѡ��Ҫ��ѯ���ֶ�!
                break;
            }
                       
            if(!isset($keyword)){
                print GenJsonData("error","������Ҫ��ѯ������!",1);    //������Ҫ��ѯ������!
                break;
            }
            
            if(strlen($page)<=0){
                $page = 1;
            }
            
            
            /*����û�����Ҫ��ѯ�����ݿ���ֶ��Ƿ�Ϸ����������ò�ѯ*/
                   
            //$db_id������1-2-3-4���ַ�����ʽ��$db_id_array�����ݿ��ڱ��е�����ID����           
            $db_id = rtrim($db_id,'-');
            $db_id_array = explode('-',$db_id);
            $db_id  = $db_id_array[0];  

            $col_id = rtrim($col_id,'-');
            $col_id_array = explode('-',$col_id);
                                   
            print keyword_search($db_id_array,$searchmethod,$searchtype,$keyword,$page,$col_id_array);
            
            break;
        
        }
        
        case 3:{
            $domain = 'csdn';
            print getTotalDataCount($domain);
            break;
        
        }
    
    }


?>


<?php

        /**
         *  �������ݿ�������ֿ�������session��¼���ж��û��Ƿ�ı����������ԣ����������ʡ��������ݿ⡢������Ϣ�ֶΣ�
         *  �����������û�з����仯�������ǻ�ҳ��ѯ��������ô���µ�һЩ�������Ա����ظ�ִ�У���߲�ѯЧ��
         * 
         */

    
    function getConfigData(){

        global $configData;
        
        $DBInstance =  new MysqlOperater( );
        
        $DBInstance->db_conn(constant("DB"));
        
        $DBInstance->db_query("set names utf8");         
          
        getPageNum();
        list_columns($DBInstance);
        list_domain($DBInstance);
        getTotalDataCount($DBInstance);  

        $DBInstance->db_close();
        
        return GenJsonData( 'success', $configData,0);

    }
    
    function getPageNum(){
        
        global $pageNum , $configData;
        
        $configData['pageNum'] = $pageNum;
        
    }
    
    //�г�Ҫ��ʾ����Ϣ����
    function list_columns($DBInstance){
        
        global $configData;
      
        $result = $DBInstance->db_query("select cid,cols_name,description,selected  from se_columns_info where isShow = 1 ");  //se_database_info

        $cols = array();
                
        while($data = mysql_fetch_row($result)){
        
            if($data[3] == 1 ){
                $data = '<input type="checkbox"  name="colsname" checked="checked"  value="'.$data[0].'"/>'.$data[2];
            }else{
                $data = '<input type="checkbox"  name="colsname"  value="'.$data[0].'"/>'.$data[2];
            }
        
            array_push($cols,$data);
        }
        
        $content = gen_html_table(null,$cols,3);
        
        $configData['cols'] = $content;
        
    }
        
    //�г����п����õ�����
    function list_domain($DBInstance){
        
        global  $configData;
           
        $result = $DBInstance->db_query("select id,domain,description,selected from se_tables_info where isShow = 1 ");  //se_database_info
        
        $desc=array();
        
        while($data = mysql_fetch_row($result)){
            
            if($data[3] == 1 ){
                $data = '<input type="checkbox"  name="dbname" checked="checked" value="'.$data[0].'"/>'.$data[2];
            }else{
                $data = '<input type="checkbox"  name="dbname" value="'.$data[0].'"/>'.$data[2];
            }
            array_push($desc,$data);
            
        }
        
        $content = gen_html_table(null,$desc,3);
        
        $configData['dbs'] = $content;
        
    }
    
    //��ȡ��Ӧ$domain�����ݿ���������
    function getTotalDataCount($DBInstance,$domains=""){
        
        global  $configData;
       
        $AllDBDataCount = 0;     
       
        /*
        foreach($domains as $domain){
                       
            //��ȡ����domain�����ݱ�����
            $sql = "select table_name  from `se_tables_info` where domain ='" . $domain ."'";
            $result = $DBInstance->db_query($sql);  //se_database_info
            $data = mysql_fetch_array($result);
            $table_name = $data['table_name'];
            
            //$sql = "select count(username)  from `". $table_name."`";
            $sql = "show table status from ".constant("DB")." where Name='". $table_name."'";
            $result = $DBInstance->db_query($sql);  
            
         
            $data = mysql_fetch_array($result);
            AllDBDataCount += $data['Rows'];
        }
        */
        //��ȡ���б�������userinfo���������� 
        
        $sql = "show table status from socialengineer where name like '%userinfo'";
        
        $result = $DBInstance->db_query($sql); 
        
        while($data = mysql_fetch_array($result,MYSQL_ASSOC)){
            
            $AllDBDataCount += $data['Rows'];
        }
        
        $configData['allcount'] = $AllDBDataCount;
        
    }
        
    function keyword_search($db_id_array , $searchmethod , $searchtype ,$keyword , $page ,$col_id_array){
        
        global $pageNum,$debug;
        $colsdesc= array();
        $colsname= array();
        $col_id_str = "";
        $col_name_str = "";

        
        $col_id_str = implode(",",$col_id_array);
        $col_id_str = rtrim($col_id_str,",");
                
        //��ʱ��
    	$timer = new Timer(); 
    	$timer->start();  

        $start = ($page - 1)*$pageNum;
              
        $DBInstance =  new MysqlOperater( );
        $DBInstance->db_conn(constant("DB"));
        $DBInstance->db_query("set names utf8"); 
        
        //����ת��
        foreach($db_id_array as &$db_id){
            $db_id = mysql_real_escape_string($db_id , $DBInstance->conn);
        }
        $col_id_str= mysql_real_escape_string($col_id_str , $DBInstance->conn);
        $keyword =  mysql_real_escape_string($keyword , $DBInstance->conn);
        $cols =  mysql_real_escape_string($cols , $DBInstance->conn);
        $searchtype = mysql_real_escape_string($searchtype , $DBInstance->conn);

        //��ȡ��Ϣ�ֶ�ID��Ӧ���ֶ�����
        //exp:  select description from se_columns_info where cid in(2,3);
        $sql = "select cols_name,description from se_columns_info where cid in(" . $col_id_str.")";
        //print $sql."<br>";
        $result = $DBInstance->db_query($sql);
        while($data = mysql_fetch_row($result)){
            array_push($colsname,$data[0]);
            array_push($colsdesc,$data[1]);            
        }

        $col_name_str = implode(",",$colsname);
        $col_name_str = rtrim($col_name_str,",");
                  
        //��ȡ��Ӧ$db_id_array���������ݱ�����
        $table_name = array();
        $count = count($db_id_array);
        for($i=0;$i<$count;$i++){        
            $sql = "select table_name  from se_tables_info where id =" . $db_id_array[$i];
            $result = $DBInstance->db_query($sql);
            $data = mysql_fetch_row($result); 
            $table_name[$i] = $data[0];
        }
        
        //�����ѯ�����
        //EXP:  select sum(a)  from (select  count(1) as a from se_csdn_userinfo where username like 'wyl%' union select  count(1)  as a  from se_iscc_userinfo where username like 'wyl%') as t;
        $sql = "select count(1) from `".$table_name."` where ".$searchtype;
        $sql = "";
        for($i=0;$i<$count;$i++){
            $sql .= "select count(1)  as a from `".$table_name[$i]."` where ".$searchtype;
            //��ȷ����
            if($searchmethod == '1'){
                $sql .= " = '".$keyword."'";
            }else{
                $sql .= " like '".$keyword."%' ";
            }
            if($count>1 && $i< $count-1){ 
               $sql .= " union ";
            }
        }
        $sql = "select sum(a)  from (" . $sql . ") as t";        
        $result = $DBInstance->db_query($sql); 
        $data = mysql_fetch_row($result);
        $totalCount = $data[0];
        //print "<p>��".$totalCount."����¼!</p>";
                     
        //��ѯ���
        //exp:select * from (select username,password from `se_csdn_userinfo` where username like 'admin%' union select username,password  from `se_iscc_userinfo` where username like 'admin%' ) as t;
        $sql = "select ".$col_name_str." from `".$table_name."` where ".$searchtype;
        $sql = "";
        for($i=0;$i<$count;$i++){
            $sql .= "select ".$col_name_str." from `".$table_name[$i]."` where ".$searchtype;
            //��ȷ����
            if($searchmethod == '1'){
                $sql .= " = '".$keyword."'";
            }else{
                $sql .= " like '".$keyword."%' ";
            }
            if($count>1 && $i< $count-1){ 
               $sql .= " union ";
            }
        }
        $sql = "select * from (" . $sql .") as t ";
 
        //��ҳ��ѯ
        $sql .= "limit $start,$pageNum";
        
        $result = $DBInstance->db_query($sql); 
        $Count = mysql_num_rows($result);
        
        $DBInstance->db_close();
         
        $timer->stop();
        
        if($debug)
            print "������ʱ: ".$timer->spent()."<br/>";
 
        $datas =  array();
        while($data = mysql_fetch_array($result,MYSQL_ASSOC)){//�˴�����MYSQL_ASSOC��ʾ��ֻ�������ֶ�����ʾ�����飬�����������ֱ�ʾ��
            array_push($datas,$data);
        }

        $header = $colsdesc;
        
        $content = gen_html_table($header,$datas,0);
        
        $result = array("totalCount"=>$totalCount,"count"=>$Count,"time"=>$timer->spent(),"html"=>$content);
        
        //����json���ݸ�ʽ
        return GenJsonData( 'success', $result,0);   
 
    }
    
 

?>


<?php

    function initSession(){
            
            
        $_SESSION['isNewSearchSession'] = false;
        $_SESSION['keyword'] = '';
        
        $_SESSION['dbs'] = '';
        $_SESSION['cur_db_index'] = '';
        $_SESSION['curDB'] = '';
        
        $_SESSION['Pages'] = 0;
        $_SESSION['curPage'] = 0;
        $_SESSION['start_index'] = 0;
        
        $_SESSION['cols'] = '';
        
        $_SESSION['totalCount'] = 0;
            
                 
    }
    
    function checkNewSearchSession($dbs , $keyword , $cols){
        
        
        
        
    } 
            
    function killSession(){
        
        session_unset();
        session_destroy();
    
    }
    



?>









