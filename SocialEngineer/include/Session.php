<?php

/**
 * @author random
 * @copyright 2013-08-07
 */

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
        
        
        
    function killSession(){
        
        session_unset();
        session_destroy();
    
    }
    


?>