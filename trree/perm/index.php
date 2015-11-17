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
<div class="position ui-droppable ui-sortable" id="ludi">


           <div id="widjet-1" widjet="html" class="unused widjet">
                <div class="name">апапапапап</div> 
                <div class="title">Працівник</div>
                <div class="sub-menu">
                    <span class="edit" onclick="editWidjet(1)">ред</span>
                    <span class="remove" onclick="removeWidjet(1)">X</span>
                </div>
            </div>
           <div id="widjet-2" widjet="html" class="unused widjet">
                <div class="name">апапапапап2222</div> 
                <div class="title">Працівник</div>
                <div class="sub-menu">
                    <span class="edit" onclick="editWidjet(2)">ред</span>
                    <span class="remove" onclick="removeWidjet(2)">X</span>
                </div>
            </div>		
           <div id="widjet-3" widjet="html" class="unused widjet">
                <div class="name">апапапапап3333</div> 
                <div class="title">Працівник</div>
                <div class="sub-menu">
                    <span class="edit" onclick="editWidjet(3)">ред</span>
                    <span class="remove" onclick="removeWidjet(3)">X</span>
                </div>
            </div>

</div>
            
        </td>
        <td valign="top">
            <h2>Доступные позиции</h2>
            
            <table class="positions" border="1" width="100%">
                <tbody><tr>                    
                    <td width="25%">
                             <div class="label">(41)Семенчук О.В.</div>
                                <div class="position ui-droppable ui-sortable" id="scroller">
                                </div>
                             <div class="label">(14)Костишин Я.Г.</div>
                                <div class="position ui-droppable ui-sortable" id="bottom_counters">
                                 </div>
                                                                            
                    </td>
                    <td width="25%">
                              <div class="label">(3)Федик В.М.</div>
                                <div class="position ui-droppable ui-sortable" id="search">
                               </div>
                               <div class="label">(8)Риснюк В.П.</div>
                                <div class="position ui-droppable ui-sortable" id="main_menu">

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
		alert(sort_order.toSource());
		$.ajax({
		url: "http://"+location.host+"/backend/main/widjets/sort_order/",
		type: "POST",
		cache: false,
		data: data,
		success: function(response){ }
		});
		}    
		});
		});
</script>
<div id="fancybox-tmp"></div><div id="fancybox-loading"><div></div></div><div id="fancybox-overlay"></div><div id="fancybox-wrap"><div id="fancybox-outer"><div class="fancybox-bg" id="fancybox-bg-n"></div><div class="fancybox-bg" id="fancybox-bg-ne"></div><div class="fancybox-bg" id="fancybox-bg-e"></div><div class="fancybox-bg" id="fancybox-bg-se"></div><div class="fancybox-bg" id="fancybox-bg-s"></div><div class="fancybox-bg" id="fancybox-bg-sw"></div><div class="fancybox-bg" id="fancybox-bg-w"></div><div class="fancybox-bg" id="fancybox-bg-nw"></div><div id="fancybox-content"></div><a id="fancybox-close"></a><div id="fancybox-title"></div><a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a><a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a></div></div>
</body></html>