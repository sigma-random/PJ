<?php
/**
 * @author random
 * @copyright 2013-08-06
 */

error_reporting(false);

define("DB","socialengineer");

require_once("JsonEncode.php");



class MysqlOperater{

    var $db_host;
    var $db_port;
    var $db_name;
    var $db_user;
    var $db_pass;
    var $database;
    var $conn;
    var $sql;
    var $result;

/*
    function __construct($host ,$port ,$user ,$pass ){
        
        $this->db_host = $host;
        $this->db_port = $port;
        $this->db_user = $user;
        $this->db_pass = $pass;
        $this->conn = 0;
        $this->sql = "";
        
        
    }
*/    
    function __construct( ){

        $this->db_host = "127.0.0.1";//127.0.0.1";
        $this->db_port = "3306";
        $this->db_user = "root";
        $this->db_pass = "root";
        $this->conn = 0;
        $this->sql = "";
        
    }
    
    function db_conn($database){
        
        $this->database = $database;
        $this->conn = mysql_connect(
            $this->db_host.":".$this->db_port,
            $this->db_user,
            $this->db_pass);
            
        if(!$this->conn){    
            
            print GenJsonData("error","数据库连接失败:".mysql_error(),1);
            exit;
            die("<p>数据库连接失败:".mysql_error()."</p>");
            
        }
        
        $select=mysql_select_db( $this->database , $this->conn);
        if(!$select){
            print GenJsonData("error","选择数据库${database}失败:".mysql_error(),1);
            exit;
            die("<p>选择数据库${database}失败:".mysql_error()."</p>");
        }
        //print "<p>成功连接数据库${$this->database}</p>";
        
    }

    function db_query($sql){
     
      if($sql){
            $this->sql=$sql;
      }
      if(!($this->result=mysql_query($this->sql,$this->conn))){ 
            print GenJsonData("error","执行sql语句失败:".mysql_error(),1);
            exit;
            die("执行sql语句失败:".mysql_error());
      }else{
         return $this->result;   
      } 
    }

    function db_close(){
        
        if($this->conn){
            mysql_close($this->conn);
            unset($thi->conn);
        }
    }

}


?>