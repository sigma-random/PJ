<?php

/**
 * @author random
 * @copyright 2013-08-08
 */

    /*
    ����html��ʽ��tableԪ�أ�
    1��һά���飬û�б���
    2����ά���飬���б���
    
        $col_num�����Ƕ�һά���鹹�����Ƕ����������
    */
    function gen_html_table($header , $data , $col_num=3){
        
        $html_contents = "<table>";
        //����б��������ɱ���
        if(isset($header) && is_array($data) &&count($data) > 0){
            $html_contents.="<tr>";
            foreach($header as $value){
                $html_contents.="<th>".$value."</th>";
            }
            $html_contents.="</tr>";
        }        
        if( isset($data) && is_array($data) && count($data) > 0){
            //��ά����
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
            //һά����
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