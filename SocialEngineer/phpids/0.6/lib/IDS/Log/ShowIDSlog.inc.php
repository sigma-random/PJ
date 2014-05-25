<?php
define("PHPIDS_LOG",dirname(__FILE__)."/../tmp/phpids_log.txt");
function &NewPage() {
	$returnArray = array(
		'title' => 'WAF Log View',
		'title_separator' => ' :: ',
		'body' => '',
		'page_id' => '',
		'help_button' => '',
		'source_button' => '',
	);
	return $returnArray;
}
//---END

function HtmlEcho( $pPage ) {
	echo "
			<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
			<html xmlns=\"http://www.w3.org/1999/xhtml\">
				<head style=\"\">
					<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
					<title>{$pPage['title']}</title>
				</head>
				<body class=\"home\" 
							style=\"background: #e7e7e7;\"
				>
					<div 
							id=\"container\" 
							style=\"width: 900px;
										height: 100%;
										margin-left: auto;
										margin-right: auto;
										background:#f4f4f4;
										font-size: 13px;\"
					>
						<div 
								id=\"header\" 
								style=\"padding: 10px;
											overflow:hidden;
											background: #2f2f2f;
											border-bottom: 5px solid #A1CC33;
											text-align: center;\"
						>
							<img src='img/waf.jpg'  alt=\"WAF Log\" />
						</div>
						<div 
								id=\"main_body\" 
								style=\"float:right;
											width: 693px;
											background: #f4f4f4;
											padding-top: 20px;
											padding-bottom: 10px;
											font-size: 13px;\"
						>
							{$pPage['body']}
							<br />
							<br />
							<br />
						</div>
						<div class=\"clear\" style=\"clear: both;\">
						</div>
						<div id=\"system_info\" style=\"\">
						</div>
					</div>
				</body>
			</html>";
}
//---END

function GetIDSVersion() {
	return '0.6';
}

// PHPIDS Log parsing function 
function ReadIDSLog() {

	$file_array = file(PHPIDS_LOG);
	
	$data = '';

	foreach ($file_array as $line_number => $line){
		$line = explode(",", $line);
		$line = str_replace("\""," ",$line);
		
		$datetime = $line[1];
		$vulnerability = $line[3];
		$variable = urldecode($line[4]);
		$request = urldecode($line[5]);
		$ip = $line[6];	
		$data .= "<div 
								id=\"waflog\" 
								style=\"border: 1px solid #C0C0C0;
											padding: 5px;
											margin: 10px 0px 10px 0px;
											background-color: #f8fafa;\"
						>
							<table>
								<tr>
									<td><b>Date/Time:</b></td>     
									<td> " . $datetime . "</td>
								</tr>
								<tr>
									<td><b >Vulnerability:</b></td>
									<td> " . $vulnerability . "</td>
								</tr>
								<tr>
									<td><b>Request:</b></td>
									<td> " .  htmlspecialchars($request) . "</td>
								</tr>
								<tr>
									<td><b>Variable:</b></td>
									<td> " . htmlspecialchars($variable) . "</td>
								</tr>
								<tr>
									<td><b>IP:</b></td>
									<td> " . $ip . "</td>
								</tr>
							</table>".
					"</div>";
	}

return $data;
}

// Clear PHPIDS log
function ClearIDSLog()	{
	if (isset($_POST['clear_log'])&&'ClearLog'==$_POST['clear_log']) { 
		$fp = fopen(PHPIDS_LOG, 'w');
		fclose($fp);
		echo( "<script>alert('WAF log cleared');</script><meta http-equiv=\"refresh\" content=\"0\">" );
	}
}

?>