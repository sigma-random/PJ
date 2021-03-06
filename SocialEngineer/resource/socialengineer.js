
//var ProxyUrl = http://secure.sinaapp.com/SocialEngineer/proxy.php?callback=";
var ProxyUrl = "http://127.0.0.1/socialengineer/proxy.php?callback=";
//var ProxyUrl = "search.php";
var cfgTrigger = 0;
var SelectedDB;        //保存选中的要查询的数据库
var AvalidDB ;          //保存可用数据库
var SelectedCols ;        //保存选中的要查询的信息字段的值
var AvalidCols ;          //保存可用信息字段
var AllDBDataCount = 0;     //当前数据库中的数据量
var preSearchWord;
var curSearchWord;
var NewSearchSession;
var pagenum;                //每页显示的记录数
var isFirstPage;
var isLastPage;
var hasNextPage;
var hasPrePage;
var curPage;
var searchLogs;
var isFailed;

var PrePanel,CurPanel;
var PreMenuBlock,CurMenuBlock;


var objDrag; 
var drag=false; 
var dragX=0; 
var dragY=0; 

$(document).ready(function(){
    
     
    initData();
    
    //获取配置数据 
    InitSearchConfig();
    
    InitSearchCfgMenu();
    
    InitSearchCfgPanel();
    
    initUI();
    
    SwitchSearchButton('off');
    
    $("#cfgtrriger").click(DoDBConfig);
    $("#dosearch").click(NewSearching); 
    $("#prepage").click(PrePage);
    $("#nextpage").click(NextPage);
    
    $('input[name="showresultcount"]').click(
        function(){alert("显示结果数将会导致搜索时间变慢!");
    });

	objDrag=document.getElementById("title-login"); 
	drag=false; 
	dragX=0; 
	dragY=0; 
	objDrag.attachEvent("onmousedown",startDrag); 
	objDrag.attachEvent("onmousemove",Drag); 
	objDrag.attachEvent("onmouseup",stopDrag); 
     
});



function initData(){
    
    cfgTrigger = 0;
    preSearchWord = curSearchWord = $("#keyword").val();
    curPage = 1;
    searchLogs = [];
    isFirstPage = isLastPage = false;
    hasNextPage = hasPrePage =false;
    NewSearchSession = false;
    isFailed = true;

    CurPanel = 'dbcfgpanel';

}


function initUI(){
    
    $.AutomLeafStart({
        leafsfolder:"resource/leaf/",
        howmanyimgsare:3,
        initialleafs:20,
        maxYposition:-20,
        multiplyclick:true,
        multiplynumber:1,
        infinite:true,
        fallingsequence:5000
    });
}



function InitSearchCfgMenu(){
    
    CurMenuBlock="dbcfgtrigger"; 
    
   	$("#dbcfgtrigger").attr("class","dbcfgtrigger");
	$("#colcfgtrigger").attr("class","colcfgtrigger");
	$("#smtrigger").attr("class","smtrigger");
    
    $("#dbcfgtrigger").click(OnChangeCfgMenu);
    $("#dbcfgtrigger").toggleClass("changecolor");
        
	$("#colcfgtrigger").click(OnChangeCfgMenu);
	$("#smtrigger").click(OnChangeCfgMenu);  
}

function InitSearchCfgPanel(){

    CurPanel = 'dbcfgpanel';
    
}

function OnChangeCfgMenu(){

	PrePanel=CurPanel;
	PreMenuBlock=CurMenuBlock;
	CurMenuBlock=this.id;

	switch(CurMenuBlock){
	   
		case 'dbcfgtrigger':
			CurPanel='dbcfgpanel';
			break;
		case 'colcfgtrigger':
			CurPanel='colcfgpanel';
			break;
		case 'smtrigger':
			CurPanel='smcfgpanel';			
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
	$(panelselector).fadeToggle(50,function(){});
	$(panelselector).fadeOut(100,function(){
    	eval('var panelselector = "#"+CurPanel');
    	$(panelselector).fadeIn(50,function(){});
	});

}


function DoDBConfig(){
    
    cfgTrigger^=1;
    
    if(cfgTrigger){

        if(SelectedDB.length<=0){
            InitSearchConfig();
        }    
        $("#nextpage").fadeOut(500);
        $("#prepage").fadeOut(500);
        $("#searchresultpanel").fadeOut(500,function(){
            $("#cfgtrriger").attr("disabled",true);
            $("#cfgtrriger").css("color","gray");    
            $("#cfgvalues").show(1000 , function(){
                $("#cfgtrriger").text("保存配置");
                $("#cfgtrriger").css("color","red");
                $("#cfgtrriger").attr("disabled",false);            
            });                     
        });
  
    }else{
        
        $("#cfgtrriger").attr("disabled",true); 
        $("#cfgtrriger").css("color","gray"); 
        $("#cfgtrriger").text("保存中...");
        $("#cfgvalues").hide(1000 , function(){
            //获取所有选中的数据库
            GetSelectedDB();  
            //获取所有选中的字段
            GetSelectedCols();
            $("#cfgtrriger").css("color","black");
            $("#cfgtrriger").text("参数配置"); 
            $("#cfgtrriger").attr("disabled",false);
            if( !isFailed ){
                $("#searchresultpanel").fadeIn(1000,function(){
                }); 
                if(hasNextPage && hasPrePage){
                    $("#prepage").fadeIn(1000);
                    $("#nextpage").fadeIn(1000);
                }
                if(hasNextPage && !hasPrePage){
                    $("#nextpage").fadeIn(1000);
                }
                if(hasPrePage && !hasNextPage){
                    $("#prepage").fadeIn(1000);
                }               
            }
              
        });

    }
}

//获取配置数据
function InitSearchConfig(){
    
    $.ajax({
    	type: "POST",
    	url:ProxyUrl,
        dataType:"jsonp",	  //使用json跨域传输
    	data:{'m':'1'},
    	async:true,
    	success: function(data){
            //服务端脚本返回的是json数据格式
            //var json_data = eval('('+data+')');
            var json_data = data;
            if(json_data['result']=='error'){
                alert(decodeURIComponent(json_data['data']));
                SwitchSearchButton('off');
                return false;
            }
            //可以的数据库
            AvalidDB = json_data['data']['dbs']; 
            //可用的信息列名
            AvalidCols = json_data['data']['cols'];
            //每一页输出的记录数
            pagenum = json_data['data']['pageNum'];
            
            AllDBDataCount = json_data['data']['allcount'];
            var msg = "当前数据库共有: "+AllDBDataCount+" 条数据。";
            //alert(msg);
            $("#searchinfo").html(msg);
            $("#searchinfo").css("display","block"); 
            $("#dbcfgpanel").html(AvalidDB);

            $("#colcfgpanel").html(AvalidCols);
            
           
            
            SwitchSearchButton('on');
            
        }
    });    
    
}


function SwitchSearchButton(trigger){
    
    if(trigger=='on'){
        $("#cfgtrriger").attr("disabled",false);
        $("#cfgtrriger").css("color","black");
        $("#cfgtrriger").css("cursor","pointer");
        $("#dosearch").attr("disabled",false);
        $("#dosearch").css("color","black");
        $("#dosearch").css("cursor","pointer");            
    }else{
        $("#cfgtrriger").attr("disabled",true);
        $("#cfgtrriger").css("color","gray");
        $("#cfgtrriger").css("cursor","not-allowed");
        $("#dosearch").attr("disabled",true);
        $("#dosearch").css("color","gray"); 
        $("#dosearch").css("cursor","not-allowed");          
    }

    
}


//获取name属性为dbname值的复选框值 
function GetSelectedDB(){  

    tmp ="";
    $('input[name="dbname"]:checked').each(function(){   
        tmp = tmp + $(this).val() + '-' ;
    });  

    SelectedDB = tmp;
    //alert("SelectedDB = "+SelectedDB);
  
} 


//获取name属性为变量colname的复选框值 
function GetSelectedCols(){
        
    tmp = "";
    $('input[name="colsname"]:checked').each(function(){
        tmp = tmp + $(this).val() + '-' ;
    });  

    SelectedCols = tmp;
    //alert("SelectedCols = "+SelectedCols);
}



function NewSearching(){
    
    $.AutomLeafAdd({leafsfolder:"resource/leaf/",add:5});
    
    if(!DoChecking()){
        return false;
    }
    
    curPage = 1;
    NewSearchSession = true;
    curSearchWord = $("#keyword").val();
    if(preSearchWord != curSearchWord ){

        $("#prepage").css("cursor","not-allowed");
        $("#prepage").attr("disabled",true);
        $("#nextpage").css("cursor","not-allowed");
        $("#nextpage").attr("disabled",true); 
    }
    Searching();

}


function Searching(){
    

    $("#dosearch").attr("disabled","disabled"); 
    $("#dosearch").css("color","gray");  
    $("#dosearch").css("cursor","not-allowed");          

    if($("#cfgtrriger").text()=='保存配置'){
        $("#cfgtrriger").text('保存中...');
        $("#cfgtrriger").css("color","gray");
    }
    $("#cfgvalues").hide(1000,function(){

        $("#cfgtrriger").css("color","black");
        $("#cfgtrriger").text("参数配置");        
        $("#dosearch").text("查询中...");  
        $("#searching_pic").css("display","block");
        $("#searching_pic").css("cursor","progress"); 

        keyword = curSearchWord;
        searchtype = $("#searchtype").val();
        searchmethod = $('input[name="searchmethod"]:checked').val();
     
        $.ajax({
            	type: "POST",
            	url:ProxyUrl,
                dataType:"jsonp",//使用json跨域传输	
            	data:{
            	   'm':'2',
                   'dbs':SelectedDB,
                   'cols':SelectedCols,
                   'st':searchtype,
                   'sm':searchmethod,
                   'query':keyword,
                   'page':curPage
                   },
            	async:true,
            	success: function(data){
        
                    $("#dosearch").text("查  询");  
                    $("#dosearch").css("color","black");
                    $("#dosearch").css("cursor","pointer");        
                    $("#searching_pic").css("display","none");
                    $("#searching_pic").css("cursor","allow"); 
                    $("#cfgtrriger").css("cursor","pointer");
                    $("#dosearch").attr("disabled",false); 
                    $("#cfgtrriger").attr("disabled",false);  
                    //服务端脚本返回的是json数据格式，并使用url编码
                    //var json_data = eval('('+data+')');
                    var json_data = data;
                    if(json_data['result'] == 'error'){
                        alert(decodeURIComponent(json_data['data']));
                        isFailed = true;
                        return false;
                    }
					var totalCount = json_data['data']['totalCount'];
                    var count = json_data['data']['count'];
                    var time = json_data['data']['time'];
                    var html = json_data['data']['html'];
                    var msg = "本次搜索用时: "+time+"秒.";
                    if(count>0){
                        
                        isFailed = false;
                        
                        $("#prepage").css("cursor","pointer");
                        $("#prepage").attr("disabled",false);
                        $("#nextpage").css("cursor","pointer");
                        $("#nextpage").attr("disabled",false);
                        
                        searchLogs[curPage] = html;
                        
                        if(NewSearchSession && curPage ==1){        
                            if( count == pagenum){
                                
                                isFirstPage = true;
                                hasNextPage = true;
                                hasPrePage = false;
                                isLastPage = false;
                                $("#prepage").css("display","none");
                                $("#nextpage").css("display","inline-block");
                            }
                            if(count < pagenum){
                                isLastPage = isFirstPage = true;
                                hasNextPage = hasPrePage = false;
                                $("#prepage").css("display","none");
                                $("#nextpage").css("display","none");  
                            }
                            
                        }else if(curPage>1){
                            isFirstPage = isLastPage = false;
                            hasNextPage = hasPrePage = true;                        
                            $("#prepage").css("display","inline-block");
                            $("#nextpage").css("display","inline-block");        
                            if(count < pagenum){
                                isFirstPage = false;
                                isLastPage = true;  
                                hasNextPage = false;
                                hasPrePage = true;                         
                                $("#prepage").css("display","inline-block");
                                $("#nextpage").css("display","none"); 
                            }
                        }
                        $("#searchresultpanel").html(html);
                        $("#searchresultpanel").css("display","block"); 
                        msg += "&nbsp;&nbsp;&nbsp;&nbsp;共"+totalCount+"条记录";
                        msg += "&nbsp;&nbsp;&nbsp;&nbsp;(第"+curPage+"页)";
                        $("#searchinfo").css("color","white");  
                        
                    } else{
                        isLastPage = isFirstPage = false;
                        hasNextPage = hasPrePage = false; 
                        isFailed = true;
                        $("#searchresultpanel").html("");
                        $("#searchresultpanel").css("display","none"); 
                        $("#prepage").css("display","none");
                        $("#nextpage").css("display","none");
                        msg += "&nbsp;&nbsp;&nbsp;&nbsp;(未查到任何结果)"; 
                        $("#searchinfo").css("color","red");                        
                    } 
                    
                    $("#searchinfo").html(msg);
                    $("#searchinfo").css("display","block"); 
            	}
        });         
        
    });

}

function DoChecking(){
    
    //获取所有选中的数据库
    GetSelectedDB();
    //获取所有选中的字段
    GetSelectedCols();
    
    if($("#keyword").val().length<=0){
        alert("请输入要查询的数据!");
        return false;
    }
    if(SelectedDB.length<=0){
        alert("请选择要查询的数据库!");
        return false;
    } 
    if(SelectedCols.length<=0){
        alert("请选择要查询的信息字段!");
        return false;
    } 
    return true;
}


function PrePage(){
    curPage --;
    Searching();
}

function NextPage(){
    curPage ++;
    Searching();
}

function isNewSearchSession(){
    
    curSearchWord = $("#keyword").val();
    if(preSearchWord != curSearchWord && curPage == 1){
        NewSearchSession = true;
        searchLogs = [];
        
    }else{
        NewSearchSession = false;
    }
    return NewSearchSession;
    
}



function open_login_panel()
{
	document.getElementById("layer").style.display = "block";
	//显示登陆框
	document.getElementById("mylogin").style.display = "block";
	setTimeout('document.getElementById("mylogin").className = "login_show"',1);

	setTimeout(
	'document.getElementById("login-content").style.display = "block";'
	,300);


}

function close_login_panel()
{

	
	//关闭登陆框
	document.getElementById("mylogin").className = "login_hide";
	setTimeout('document.getElementById("login-content").style.display = "none";',200);
	setTimeout('document.getElementById("mylogin").style.display = "none";',900);
	setTimeout('document.getElementById("layer").style.display = "none";',900);
	
}

function open_S2_16()
{
	window.open("/s2-16/") ;
}





function startDrag(){ 
	if(event.button==1&&event.srcElement.tagName.toUpperCase()=="DIV"){ 
	objDrag.setCapture(); 
	objDrag.style.background="#77d42a"; 
	drag=true; 
	dragX=event.clientX; 
	dragY=event.clientY; 
	document.getElementById("closelogin").className = "close_spin";

	} 
}

function Drag(){ 
	if(drag){ 
	var oldwin=objDrag.parentNode.parentNode; 
	oldwin.style.left=oldwin.offsetLeft+event.clientX-dragX; 
	oldwin.style.top=oldwin.offsetTop+event.clientY-dragY; 
	oldwin.style.left=event.clientX-100; 
	oldwin.style.top=event.clientY-10; 
	oldwin.style.left = 100;
	//alert(oldwin.style.left);
	dragX=event.clientX; 
	dragY=event.clientY; 
	} 
}

function stopDrag(){ 
	objDrag.style.background="#888888"; 
	objDrag.releaseCapture(); 
	drag=false; 
	document.getElementById("closelogin").className = "close";
}
