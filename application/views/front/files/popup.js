function  textWidth(text){ 
	
	
	 
	  var html = $('<div style="position:absolute;width:auto;height:auto;left:-9999px" class="uppoptest">' + text + '</div>'); 
	  $('body').append(html);
	  var width = html.width();
	  var height = html.height();
	  html.remove();
	  var hw = new Array()
	  hw[0] = width;
	  hw[1] = height;
	  return hw;
	}
$.fn.textWidth = function(text){
	  var org = $(this)
	  var html = $('<span style="position:absolute;width:auto;left:-9999px">' + (text || org.html()) + '</span>');
	  
	 
	  if (!text) {
	    html.css("font-family", org.css("font-family"));
	    html.css("font-size", org.css("font-size"));
	  }
	  $('body').append(html);
	  
	  html.remove();
	  var hw = new Array()
	  hw[0] = width;
	  hw[1] = height;
	  return hw;
	}

$(document).ready(function() {
	
	$(".stpop").mouseover(function() { 
		
		var offsetH=3;
		var offsetW=3;
		var leftcorner=true; 
		if ($(this).hasClass('indicator-block'))
		{
		var text='<div class="indicator-legend"><ul><li class="green">Данные корректны</li><li class="yellow">Данные еще не проверены</li><li class="gray">Данные еще не получены</li><li class="red">Данные некорректны</li></ul><div class="legend"></div></div>';
		leftcorner=false;
		
		} 
		else if ($(this).parent().hasClass('time'))
		{
			var text='<div class="in_st"><table><tr><td class="time_label"><span>Активирована:</span></td><td class="time_value"><span>09:00 12.05.2012</span></td></tr>'
				+'<tr><td class="time_label"><span>Нормативное время:</span></td><td class="time_value"><span>1 д : 4 ч :30 м</span></td></tr>'
				+'<tr><td class="time_label"><span>Потраченное время:</span></td><td class="time_value"><span>2 д : 14 ч :28 м</span></td></tr><tr>'
				+'<td colspan="2" class="status_history"><span >История статусов</span></td></tr></table></div>';
			 
		}
	else
		{
		var text='<div class="status-popup"><ul><li><b>Выполняет:</b> <span><a href="#">Сергей Ликашин</a></span><div class="label-button red label-popup">Снять</div></li><li><b>Задача взята:</b> <span>Сергей Ликашин</span></li></ul></div>';
			
		}
		
		var offset = $(this).offset();
		
		var hw=textWidth(text);
		
		var w=hw[0];
		var h=hw[1];
			/*
		    var thisW=$(this).width()+parseInt($(this).css("padding-left"), 10) + parseInt($(this).css("padding-right"), 10)
	    	+ parseInt($(this).css("margin-left"), 10) + parseInt($(this).css("margin-right"), 10)+
	    	parseInt($(this).css("borderLeftWidth"), 10) + parseInt($(this).css("borderRightWidth"), 10);
		    var thisH=$(this).height()+parseInt($(this).css("padding-top"), 10) + parseInt($(this).css("padding-bottom"), 10)
	    	+ parseInt($(this).css("margin-top"), 10) + parseInt($(this).css("margin-bottom"), 10)+
	    	parseInt($(this).css("borderLeftWidth"), 10) + parseInt($(this).css("borderRightWidth"), 10);
	    	*/
		    var thisW=$(this).outerWidth(true);
		    var thisH=$(this).outerHeight(true);
		    
		    
		 
		    $("div.downpop").height(thisH+offsetH);
		    $("div.downpop").width(thisW+offsetW*2);
		    
		    
		    if (w < $("div.downpop").width()+20)
	    	{
	    	w=$("div.downpop").width()+20;
	    	}
		    if (h < 50)
		    {
		    	h=60;
		    }
		    else
		    {
		    	if (!$(this).hasClass('indicator-block'))
				{
		    	h=h+10;
				}
	    	}
		   //alert(w+':'+h);
		    
		    $("div.uppop").html(text);
		  //  alert($("div.uppop").width()+':'+h);
		    $("div.uppop").width(w);
		    $("div.uppop").height(h);
		    if (!leftcorner)
		    	{
		    	$("div.uppop").addClass('right90');
		    	$("div.uppop").removeClass('left90');
		    	$("div.uppop").css('top',offset.top-$("div.uppop").height());
		    	$("div.uppop").css('left',offset.left-w+thisW-9);  
		    	$("div.downpop").css('top',offset.top);
		    	$("div.downpop").css('left',offset.left+offsetW-9);
			 	
		    	}
		    else
		    	{
		    	$("div.uppop").addClass('left90');
		    	$("div.uppop").removeClass('right90');
		    	$("div.uppop").css('top',offset.top-$("div.uppop").height()+0);
		    	$("div.uppop").css('left',offset.left+1-offsetW); 
		    	$("div.downpop").css('top',offset.top);
		    	$("div.downpop").css('left',offset.left-offsetW);
		    	}
		    
		    $("div.uppop").css( 'z-index', 7000);
		    $("div.uppop").html(text);
		    
		    $(this).css('z-index', 9000);
		    $(this).children().css('z-index', 9001); 
		    $("div.downpop").css( 'z-index', 7001);
		    $("div.uppop").show();
		    $("div.downpop").show(); 
		/*    $(this).hide();*/
		
		
	}).mouseout(function(){
		 
	 
		$("div.uppop").hide();
		$("div.downpop").hide();
		 
		  $(this).css( 'z-index',10);
		  $(this).children().css('z-index', 11);
		 
		
	});
	
	$("div.dt").mouseover(function() { 
		if ($(this).hasClass('i_legend'))
			{
			var text='<img src="/i/legend.png"/>';
			
			}
		else
			{
			var text="<br><b>jshgdjashdjhasd<br/><table><tr><td>Проверка строкda</td><td>fadfsdf</td></tr><tr><td>Проверка строкda</td><td>fadfsdf</td></tr><tr><td>Проверка строкda</td><td>fadfsdf</td></tr></table></b>";
				
			}
		var offset = $(this).offset();
	    $(this).addClass("dton");
	   
	    var hw=textWidth(text);
	    var w=hw[0];
	    var h=hw[1];
	    if (w < $(this).width()+20)
	    	{
	    	w=$(this).width()+20;
	    	}
	    if (h < 50)
    	{ 	h=60;   	}
	    else
	    {  	h=h+20; }
	    $("div.st1").width(w);
	    $("div.st1").height(h);
	    if ($(this).hasClass('r90'))
	    	{
	    	$("div.st1").addClass('right90');
	    	$("div.st1").removeClass('left90');
	    	$("div.st1").css('top',offset.top-$("div.st1").height()+0);
	    	  $("div.st1").css('left',offset.left-w+$(this).width()-9); 
		 	
	    	}
	    else
	    	{
	    	$("div.st1").addClass('left90');
	    	$("div.st1").removeClass('right90');
	    	 $("div.st1").css('top',offset.top-$("div.st1").height()+0);
	    	 $("div.st1").css('left',offset.left+1); 
	    	}
	    	 
	  
	   
	    $("div.st1").css( 'z-index', 7000);
	    $("div.st1").html(text);
	   
	    $(this).css('z-index', 9000);
	    $(this).children().css('z-index', 9001);
	    $("div.st1").show();
	  }).mouseout(function(){
		  $("div.st1").hide();
		  $(this).css( 'z-index',10);
		  $(this).children().css('z-index', 11);
		  $(this).removeClass("dton");
	  });
	});