<?
//error_reporting(0);
if(empty($query))$query="inurl:index.action"; 
if(empty($num))$num="1"; 
?> 
<html> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=gb2312"> 
<title>Google Hacking</title> 
<style type="text/css"> 
body,td { 
font-family: "Tahoma"; 
font-size: "12px"; 
line-height: "150%"; 
} 
.smlfont { 
font-family: "Tahoma"; 
font-size: "11px"; 
} 
.INPUT { 
FONT-SIZE: "12px"; 
COLOR: "#000000"; 
BACKGROUND-COLOR: "#FFFFFF"; 
height: "18px"; 
border: "1px solid #666666"; 
padding-left: "2px"; 
} 
.redfont { 
COLOR: "#A60000"; 
} 
a:link,a:visited,a:active { 
color: "#000000"; 
text-decoration: underline; 
} 
a:hover { 
color: "#465584"; 
text-decoration: none; 
} 
.top {BACKGROUND-COLOR: "#CCCCCC"} 
.firstalt {BACKGROUND-COLOR: "#EFEFEF"} 
.secondalt {BACKGROUND-COLOR: "#F5F5F5"} 
</style> 
Google Hacking--- by superhei@ph4nt0m.org<br> 
<form method="POST"> 
Query: <input name="query" value="<?=$query?>" type="text" size="50"> 
Num: <input name="num" value="<?=$num?>" type="text" size="5"> 
<input value="go" type="submit"><input name="do" value="connect" type="hidden"> 
</form> 
<HR WIDTH="550" ALIGN="LEFT"> 
<? 
ini_set("max_execution_time",0); 
error_reporting(7); 

$query=$_POST[query]; 
$num=$_POST[num]; 

if(!isset($query))
	exit;

/*
$url = "http://www.google.com.hk/search?q=".$query."&num=".$num."&lr=&start=0"; 
echo "search url :".$url."</br>";
$fp = fopen($url, "r" ); 

if($fp==NULL){
	echo "fail search..."."</br>";
	exit;
}



while(!feof($fp))
	$contents.=fread($fp,1024); 
echo "search result :".$contents."</br>";
*/

/*
<h3 class="r"><a href="http://123.232.123.23/index.action" onmousedown="return rwt(
<cite>123.232.123.23/<b>index.action</b>

*/

//$pattern="|<a class=l href=['\"]?([^ '\"]+)['\" ]|U"; 
$contents="<cite>123.232.123.23/<b>index.action</b>";
$pattern="|<a class=l href=['\"]?([^ '\"]+)['\" ]|U"; 
//$pattern="#<cite>(*)<b>(*)\.action</b></cite>#Ui";
$pattern="#<cite>(.*)<b>(.*)</b>#Ui";

preg_match_all($pattern,$contents, $regArr, PREG_SET_ORDER); 
$count = count($regArr);
$result = "http://";
echo "count = ".$count."</br>";
$result .= $regArr[0][1].$regArr[0][2];
echo "<a href=".$result.">".$result."</a></br>";
//for($i=0;$i<count($regArr);$i++){ 
//	echo "<a href=".$regArr[$i][1]." target=\"_blank\">".$regArr[$i][1]."</a></td><br>"; 
//}	 
fclose($fp); 

?>


<?php

// This example request includes an optional API key which you will need to 
// remove or replace with your own key. 
// Read more about why it's useful to have an API key. 
// The request also includes the userip parameter which provides the end 
// user's IP address. Doing so will help distinguish this legitimate 
// server-side traffic from traffic which doesn't come from an end-user. 
$url ="http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=site:baidu.com&rsz=8&start=0&key=INSERT-YOUR-KEY&userip=USERS-IP-ADDRESS"; 

// sendRequest 
// note how referer is set manually 
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_REFERER,"http://baidu.com" /* Enter the URL of your site here */); 
$body = curl_exec($ch); 
curl_close($ch); 

// now, process the JSON string 
$json = json_decode($body); 
echo "data=".$json;
// now have some fun with the results... 

?>