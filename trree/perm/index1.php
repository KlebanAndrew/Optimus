<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en"><head><title>Винил текс. Все для обивки :: Панель управления</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<style type="text/css" media="screen">@import "/templates/backend/css/undohtml.css";</style>
<style type="text/css" media="screen">@import "files/style.css";</style>
<style type="text/css" media="screen">@import "/templates/backend/js/pro_dropdown/pro_dropdown.css";</style>
<style type="text/css" media="screen">@import "/templates/backend/css/tabs.css";</style>
<script type="text/javascript" src="files/jquery-1.js"></script><script type="text/javascript" src="files/jquery_003.js"></script>
<script type="text/javascript" src="files/jquery.js"></script><script type="text/javascript" src="files/stuHover.js"></script>
<script type="text/javascript" src="files/main.js"></script>
<!-- base href="http://www.oblavka.ru/" --></head>
<body>
<script type="text/javascript" src="files/jquery-ui-1.js"></script>

<script type="text/javascript" src="files/jquery_004.js"></script>
<script type="text/javascript" src="files/jquery_002.js"></script>
<link rel="stylesheet" type="text/css" href="files/jquery.css">
<div class="browse">

<table class="widjets" width="100%">
    <tbody><tr>
        <td valign="top" width="190">
           
<h2>Неиспользуемые</h2>            
<div class="position ui-droppable ui-sortable">



<?php
define ('DB_HOST', 'localhost');
define ('DB_LOGIN', 'root');
define ('DB_PASSWORD', '');
define ('DB_NAME', 'cod_zvity');
$mysql_connect = mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD) or die("MySQL Error: " . mysql_error());
mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD) or die ("MySQL Error: " . mysql_error());
//mysql_query("set names utf8") or die ("<br>Invalid query: " . mysql_error());
mysql_select_db(DB_NAME) or die ("<br>Invalid query: " . mysql_error());


  
    $query = mysql_query("SELECT * FROM users WHERE perm <> 1") or die("Ecaeieoa, i?iecioea ioeaea");
    while ($row = mysql_fetch_array($query)) {
		echo '

           <div id="widjet-'.$row['id'].'" widjet="html" class="unused widjet">
                <div class="name">'.$row['name'].'</div> 
                <div class="title">Працівник</div>
                <div class="sub-menu">
                    <span class="edit" onclick="editWidjet('.$row['id'].')">ред</span>
                    <span class="remove" onclick="removeWidjet('.$row['id'].')">X</span>
                </div>
            </div>
		
		';
		
		}
	

  
  
  
  
  
  
?>


                        </div>
            
        </td>
        <td valign="top">
            <h2>Доступные позиции</h2>
            
            <table class="positions" border="1" width="100%">
                <tbody><tr>                    
                    <td width="25%">
                             <div class="label">(41)Семенчук О.В.</div>
                                <div class="position ui-droppable ui-sortable" id="scroller">
                                     <div id="widjet-29" widjet="0" class="unused widjet">
                                            <div class="name">Случайныe товары</div> 
                                            <div class="title">Случайные товары</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(29)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(29)">X</span>
                                            </div>
                                        </div>
                                </div>
                             <div class="label">(14)Костишин Я.Г.</div>
                                <div class="position ui-droppable ui-sortable" id="bottom_counters">
                                    <div id="widjet-31" widjet="0" class="unused widjet">
                                            <div class="name">Текст</div> 
                                            <div class="title">Счетчики</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(31)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(31)">X</span>
                                            </div>
                                    </div>
                                 </div>
                                                                            
                    </td>
                    <td width="25%">
                              <div class="label">(3)Федик В.М.</div>
                                <div class="position ui-droppable ui-sortable" id="search">
                                                                </div>
                               <div class="label">(8)Риснюк В.П.</div>
                                <div class="position ui-droppable ui-sortable" id="main_menu">
                                          <div id="widjet-2" widjet="1" class="unused widjet">
                                            <div class="name">Меню</div> 
                                            <div class="title">Главное меню</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(2)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(2)">X</span>
                                            </div>
                                        </div>
                                </div>
                                                                            
                    </td>
                    <td width="25%">
                               <div class="label">(32)Гишта Ж.М.</div>
                                <div class="position ui-droppable ui-sortable" id="bottom">
                                                                </div>
                              <div class="label">(11)Рабарський С.Й.</div>
                                <div class="position ui-droppable ui-sortable" id="bottom_menu">
                                    <div id="widjet-33" widjet="2" class="unused widjet">
                                            <div class="name">Меню</div> 
                                            <div class="title">Нижнее меню</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(33)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(33)">X</span>
                                            </div>
                                        </div>
                                </div>
                                                                            
                    </td>
                    <td width="25%">
                             <div class="label">(13)Онуфрейчук Я.В.</div>
                                <div class="position ui-droppable ui-sortable" id="bottom_1">
                                       <div id="widjet-30" widjet="3" class="unused widjet">
                                            <div class="name">Текст</div> 
                                            <div class="title">Текст футера</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(30)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(30)">X</span>
                                            </div>
                                        </div>
                                </div>
                             <div class="label">(22)Грабчук А.Б.</div>
                                <div class="position ui-droppable ui-sortable" id="left">
                                      <div id="widjet-26" widjet="3" class="unused widjet">
                                            <div class="name">Каталог</div> 
                                            <div class="title">Каталог</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(26)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(26)">X</span>
                                            </div>
                                        </div>
                                                                            <div id="widjet-32" widjet="3" class="unused widjet">
                                            <div class="name">HTML</div> 
                                            <div class="title">Распродажа кожзама</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(32)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(32)">X</span>
                                            </div>
                                        </div>
                                                                            <div id="widjet-40" widjet="3" class="unused widjet">
                                            <div class="name">Текст</div> 
                                            <div class="title">АКЦИЯ</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(40)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(40)">X</span>
                                            </div>
                                        </div>
                                                                            <div id="widjet-38" widjet="3" class="unused widjet">
                                            <div class="name">Опрос</div> 
                                            <div class="title">Опрос</div>
                                            <div class="sub-menu">
                                                <span class="edit" onclick="editWidjet(38)">редактировать</span>
                                                <span class="remove" onclick="removeWidjet(38)">X</span>
                                            </div>
                                        </div>
                                                                                                    </div>
                                                                            
                    </td>
                                    </tr>            
            </tbody></table>
            
            
              
        </td>
    </tr>
</tbody></table>
</div></div><div id="footer"><div id="benckmark">Время генерации страницы: 0.0846 сек.<br>Выполнено запросов в БД: 17</div></div>
<script type="text/javascript">    
function removeWidjet(widjet_id)    {
        if( confirm('Вы действительно хотите удалить виджет?') )        
		{
		var widjet = $("#widjet-"+widjet_id);            
		$.ajax({
		url: "http://"+location.host+"/backend/main/widjets/delete_widjet/"+widjet_id,
		type: "POST",
		cache: false,
		async: false,
		success: function(response)
		{                     
		var json = eval("(" + response +  ")");
		if( json.status == 'ok' )
		{
		widjet.fadeOut("slow", function(){
		widjet.remove();
		});
		}
		}
		});
		}
		}
function saveWidjet(form, widjet_id) {
        var data = $(form).serialize();
		$("#message").remove();
        $.fancybox.showActivity();
        $.ajax({
		type: "POST",
		cache: false,
		url: "/backend/main/widjets/save_widjet/"+widjet_id,
		data    : data,
		success: function(response)
		{
		$.fancybox.hideActivity();
		var json = eval("(" + response +  ")");
		if( typeof(json) == 'object' )                {
		if( json.widjet )                    {
		var widjet_id = json.widjet.widjet_id;
		var title = json.widjet.title;
		$("#widjet-" + widjet_id + " .title").text(title);
		}
		var string = '';
		if( json.error )                    {
		for(var id in json.error)                         {
		string += '<div class="redMessage">' + json.error[id] + '</div>';                         
		}                     
		}
		if( json.message )                    {
		for(var id in json.message)                         {
		string += '<div class="greenMessage">' + json.message[id] + '</div>';
		}
		}
		string = '<div id="message">' + string + '</div>';
		$(form).before(string);                                   }    }    });
		}                    
function editWidjet(widjet_id)    {
		$.fancybox.showActivity();                
		$.fancybox({
		type : 'ajax',
		href : "/backend/main/widjets/edit_widjet/"+widjet_id,                    
		onCleanup : function(a){
		if ( window.tinyMCE != undefined )                        {
		if ((tinyMCE != undefined) && (tinyMCE.activeEditor != undefined))                            
		{
		try
		{                                    
		tinyMCE.activeEditor.remove();
		}
		catch (e)
		{
		console.debug(e);
		}
		location.reload();
		}
		}                                           
		}
		});
		return false;
		}
		$(function() {
        var sort = false;
        var move = false;
        var clone;
		$('#widjets .widjet').draggable({
		cursor: "move",
		cursorAt: { top: 0, left: 0 },
		helper:'clone',
		opacity: 0.7,
		zIndex: 2700,
        });
		$('.position').droppable({
		hoverClass: 'dropHere',
		drop: function(event, ui)
		{                
		if( move == true )                {
		var el = ui.draggable.parent().html();
		ui.draggable.remove();
		$(this).append(clone.css("opacity",1));
		move = false;
		return true;
		}
		if( sort == false )                {
		var widjet = ui.draggable.attr("widjet");
		var pos = $(this);
		var position = pos.attr("id");
		var data = new Object();
		var sort_order = new Object();
		data.position = position;
		data.widjet = widjet;
		data.sort_order = parseInt($(this).find("div.widjet").length);
		$.ajax({                        
		url: "http://"+location.host+"/backend/main/widjets/add_widjet/",
		type: "POST",
		cache: false,
		data: data,
		success: function(response)                        {
		var json = eval("(" + response +  ")");
		if( typeof(json) == 'object' )
		{
		var widjet_id = json.widjet_id;
		var string = '<div id="widjet-'+widjet_id+'" widjet="'+widjet+'" class="widjet">';
		string += '<div class="name">' + ui.draggable.html() + '</div>';
		string += '<div class="title"></div>';
		string += '<div class="sub-menu">';
		string += '<span class="edit" onclick="editWidjet('+widjet_id+')">редактировать</span>';
		string += '<span class="remove" onclick="removeWidjet('+widjet_id+')">X</span>';
		string += '</div>';
		string += '</div>';
		pos.append(string);
		editWidjet(widjet_id);
		}
		}
		});
		}
		}
        });
		$('.position').sortable({
		connectWith: ".position",
		start: function(event, ui) {sort = true;},
		stop: function(event, ui)             {
		sort = false;
		var data = new Object();
		var sort_order = new Object();
		var positions = $('.position');
		var key = 0;
		for( i = 0; i < positions.length; i++ )                {
		position = $(positions[i]).attr("id");
		var widjets = $(positions[i]).find("div.widjet");
		var s = new Object();
		for( j = 0; j < widjets.length; j++ )                    {
		var id = $(widjets[j]).attr("id").replace(/widjet-/, "");
		sort_order[key++] =  {'position':position, 'sort_order':j, 'widjet_id':id};                    
		}
		}
		data.sort_order = sort_order;
		$.ajax({
		url: "http://"+location.host+"/backend/main/widjets/sort_order/",
		type: "POST",
		cache: false,
		data: data,
		success: function(response){}
		});
		}    
		});
		});
</script>
<div id="fancybox-tmp"></div><div id="fancybox-loading"><div></div></div><div id="fancybox-overlay"></div><div id="fancybox-wrap"><div id="fancybox-outer"><div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div><div id="fancybox-content"></div><a id="fancybox-close"></a><div id="fancybox-title"></div><a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a><a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a></div></div>
</body></html>