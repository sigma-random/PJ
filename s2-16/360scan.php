<?php
define("WEBSCAN_KEY", "e683ff51b2c62011030eb54417a18676");
class scan{
	private $directory = '.';
	private $extension = array('php');
	private $_files = array();
	private $filelimit = 5000;
	private $scan_hidden = true;
	private $_self = '';
	private $_regex ='(preg_replace.*\/e|`.*?\$.*?`|\bcreate_function\b|\bpassthru\b|\bshell_exec\b|\bexec\b|\bbase64_decode\b|\bedoced_46esab\b|\beval\b|\bsystem\b|\bproc_open\b|\bpopen\b|\bcurl_exec\b|\bcurl_multi_exec\b|\bparse_ini_file\b|\bshow_source\b|cmd\.exe|KAdot@ngs\.ru|小组专用大马|提权|PHP\s?反弹|shell\s?加强版|WScript\.shell|PHP\s?Shell|Eval\sPHP\sCode|Udp1-fsockopen|xxddos|Send\sFlow|fsockopen\("(udp|tcp)|SYN\sFlood)';
	private $_shellcode='';
	private $_shellcode_line=array();
	private $_log_array= array();
	private $_log_count=0;
	private $webscan_url="http://safe.webscan.360.cn/webshell/upload";
	private $action='';
	private $taskid=0;


	function __construct(){


		if (isset($_POST['action'])&&isset($_POST['key'])&&$_POST['key']==WEBSCAN_KEY&&isset($_POST['task'])) {
			$this->action = $_POST['action'];
			$this->taskid = $_POST['task'];
		}
	}




	private function is__writable($path) {

		if ($path{strlen($path)-1}=='/')
		return is__writable($path.uniqid(mt_rand()).'.tmp');

		if (file_exists($path)) {
			if (!($f = @fopen($path, 'r+')))
			return false;
			fclose($f);
			return true;
		}


		if (!($f = @fopen($path, 'w')))
		return false;
		fclose($f);
		@unlink($path);
		return true;
	}

	private function ck_state(){

		$a=@fopen("scan_lock.tmp", "a");
		fclose($a);

	}

	private function is_utf8($word)
	{
		if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word) == true)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private	function check_environment()
	{

		$r = array("status"=>1,"allow_url_fopen"=>0,"writeable"=>0);

		if (ini_get('allow_url_fopen')) {
			$r["allow_url_fopen"] = 1;
		}

		if ($this->is__writable('test.tmp'))
		{
			$r["writeable"] = 1;
		}

		if($r["allow_url_fopen"] && $r["writeable"])
		{
			$r["status"] = 1;
		}

		echo json_encode($r);
		exit;
	}


	private function webscan_curl($url , $postdata = array()){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close($ch);
		return array('httpcode'=>$httpcode,'response'=>$response);
	}

	private function post($url,$log=array()){
		if(! function_exists('curl_init')) {
	 	$postdata = http_build_query($log);
	 	$context = stream_context_create(array('http' => array('method' => 'POST', 'header' => "Content-type: application/x-www-form-urlencoded\r\n",'content' => $postdata)));
	 	$server_version = @file_get_contents($url, 0, $context);
		}
		else{
			$this->webscan_curl($url,$log);
		}

	}

	private function findstr($filepath,$shellstr){
		$a=false;
		$text=@file_get_contents($filepath);
		if(!$this->is_utf8($text)){
			$text=iconv("GBK","UTF-8",$text);
		}
		$_content = explode("\n", $text);
		for ($line = 0; $line < count($_content); $line++)
		{
			$date = preg_match_all("/".$shellstr."/i", $_content[$line],$matches);
			if($date){
				$this->_shellcode[$line+1]=$_content[$line];
				$a=true;
			}
		}
		return $a;
	}
	private function upload_log($a = array()) {
		if($this->_log_count==50){
			$this->post($this->webscan_url,array('log' => json_encode($this->_log_array),'key'=>WEBSCAN_KEY,'task'=>$this->taskid));
			$this->_log_count=0;
			$this->_log_array=array();
		}
		else{
			$this->_log_array[]=$a;
			$this->_log_count++;
		}

	}
	private function listdir($dir) {
		$handle = opendir($dir);
		if ($this->filelimit > 0) {
			if (count($this->_files) > $this->filelimit) {
				return true;
			}
		}
		while (($file = readdir($handle)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			$filepath = $dir == '.' ? $file : $dir . '/' . $file;

			if (is_link($filepath)) {
				continue;
			}
			if (is_file($filepath)) {
				if (substr(basename($filepath), 0, 1) != "." || $this->scan_hidden) {
					$extension = pathinfo($filepath);
					if (is_string($this->extension) && $this->extension == '*') {
						if ($this->filelimit > 0) {
							$this->_files[] = $filepath;
						}
					} else {
						if (isset($extension['extension']) && in_array($extension['extension'], $this->extension)) {
							if ($this->_self != basename($filepath)) {
								if ($this->filelimit > 0) {
									$this->_files[] = $filepath;
								}
							}
						}
					}
				}
			} else if (is_dir($filepath)) {
				if (substr(basename($filepath), 0, 1) != "." || $this->scan_hidden) {
					$this->listdir($filepath);
				}
			}
		}
		closedir($handle);
	}

	private function anaylize() {
		foreach ($this->_files as $file) {
			if(!$this->is_utf8($file)){
			$filename=iconv("GBK","UTF-8",$file);
			}
			if($this->findstr($file,$this->_regex))
			{
					
				self::upload_log(array($filename => array('Trojan' => 1,'time' => date("Y-m-d H:i:s",filemtime($file)),'md5'=>md5(file_get_contents($file)),'size'=>filesize($file),'shellcode'=>$this->_shellcode) ));
				$this->_shellcode=array();
			}
			/*	else{
				self::upload_log(array($filename => array('Trojan' => 0)));
				}
				*/
		}
		if ($this->_log_count>0) {
			$this->post($this->webscan_url,array('log' => json_encode($this->_log_array),'key'=>WEBSCAN_KEY,'task'=>$this->taskid));
		}
		sleep(5);
		@unlink('scan_lock.tmp');
		//@unlink(__FILE__);
		$this->post($this->webscan_url,array('state'=>'1','key'=>WEBSCAN_KEY,'task'=>$this->taskid));
	}

	public function start() {
		switch ($this->action) {
			case 'check_environment':
				$this->check_environment();
				break;
			case 'shell_scan':
				set_time_limit(0);
				ignore_user_abort();
				$this->ck_state();
				$this->listdir($this->directory);
				$this->anaylize();
				break;
			default:
				echo "360webscan";
				break;
		}
	}

}

if (file_exists('scan_lock.tmp'))  {
	exit("scannig");
}
$a=new scan();

$a->start();
?>
