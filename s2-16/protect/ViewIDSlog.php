<?php

$PHPIDS_LIB=dirname(__FILE__)."/../../phpids/0.6/lib/";
//$PHPIDS_LIB="Z:\WebSpace\php/phpids/0.6/lib/";
define("PHPIDS_LIB",$PHPIDS_LIB);
$ids_lib=PHPIDS_LIB;
require_once $ids_lib.'/IDS/log/ShowIDSlog.inc.php';
 
$page = NewPage();
$page[ 'title' ] .= $page[ 'title_separator' ].'PHPIDS Log';
$page[ 'page_id' ] = 'log';
//$page[ 'clear_log' ]; <- Was showing error.

$page[ 'body' ] .= "
<div class=\"body_padded\" style=\"	padding-left: 20px; padding-right: 20px;\">
	<h1> WAF Log</h1>
	
	<p>". ReadIDSLog() ."</p>
	
	<br />
	<br />
	
	<form action=\"#\" method=\"POST\">
    <input type=\"submit\" value=\"ClearLog\" name=\"clear_log\">
    </form>
	
	".ClearIDSLog()."
	
</div>
";

HtmlEcho( $page );

?>
