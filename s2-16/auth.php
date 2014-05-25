<?php

/* 
 * To change this template, please replace echo using your implement
 */
	define("AUTH_USER","xiaoqiang");
	define("AUTH_PASS","xiaoqiang");
	session_start();
	if (!isset($_SERVER['PHP_AUTH_USER']) ) {
		session_destroy();
		header('WWW-Authenticate: Basic realm="sae auth"');
		//echo "username  is empty !";
		header('HTTP/1.1 401 Unauthorized');

	} else {

		//echo "username: ".$_SERVER['PHP_AUTH_USER'];
		//echo "<br/>";
		//echo "pass: ".$_SERVER['PHP_AUTH_PW'];
		//echo "<br/>";

		if($_SERVER['PHP_AUTH_USER'] ==  constant('AUTH_USER') &&
			$_SERVER['PHP_AUTH_PW'] == constant('AUTH_PASS')){

				$_SESSION['auth_user'] = $_SERVER['PHP_AUTH_USER'];
				header('location: ./index.php');
				//echo "welcome {$_SERVER['PHP_AUTH_USER']} !";

		}else{
			session_destroy();
			echo "username or password is wrong !";
			header('WWW-Authenticate: Basic realm="sae auth"');
		}
	}
?>