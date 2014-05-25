<?php

/**
 * @author random
 * @copyright 2013-08-08
 */

    /*
    生成html格式的table元素：
    1、一维数组，没有标题
    2、二维数组，含有标题
    
        $col_num参数是对一维数组构造表格是定义表格的列数
    */
    function gen_html_table($header , $data , $col_num=3){
        
        $html_contents = "<table>";
        //如果有标题则生成标题
        if(isset($header) && is_array($data) &&count($data) > 0){
            $html_contents.="<tr>";
            foreach($header as $value){
                $html_contents.="<th>".$value."</th>";
            }
            $html_contents.="</tr>";
        }        
        if( isset($data) && is_array($data) && count($data) > 0){
            //二维数组
            if(is_array($data[0])){
                $rownum = count($data);
                for($i=0 ;$i<$rownum;$i++){
                    $values = $data[$i];
                    $html_contents.="<tr>";
                    foreach($values as $value){
                        $html_contents.="<td>".$value."</td>";
                    }
                    $html_contents.="</tr>";
                }
            }
            //一维数组
            else{
                $count = count($data);
                $rownum = ceil($count / $col_num);  
                for($i=0;$i<$rownum;$i++){
                    $html_contents.="<tr>";
                    for($j=0;$j<$col_num;$j++){
                        $html_contents.="<td>".$data[$i*$col_num+$j]."</td>";
                    }
                    $html_contents.="</tr>";
                } 
            }
        }
        
        $html_contents .="</table>"; 
        
        //print $html_contents;
        
        return $html_contents;
    }

?>