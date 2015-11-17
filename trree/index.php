<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Планування</title>

	<script type="text/javascript" src="http://10.93.1.52/zvity/application/views/files/datapicer2/js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="http://10.93.1.52/zvity/application/views/files/datapicer2/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="http://10.93.1.52/zvity/application/views/files/datapicer/jquery.ui.datepicker.js"></script>



</head>





<script type="text/javascript">
	var sap_url= "http://10.93.1.56:8084/SAPEmployee/Employee";
	//var sap_url= "/Employee";
	var mail_to = "Vasyl.Synenko@if.energy.gov.ua";
	$(document).ready(function() {
		jQuery.ajax({
			type: "POST",
		       url: sap_url,
		       data:  {"task":"ORG_UNIT_LIST"},
		       dataType: "html",
		       success: function(xml){
		    	   tree_txt=xml;
		    	   $('#tree').html(tree_txt);
		    	   $('span').click(function(){
						var org_name = $(this).attr('id').substring(3,11);
						$('span').attr('style','');
						$(this).attr('style','background:#00BFFF;');
						AJAXGetRezSerch('','',org_name,'','*');
					});
		    	   $("#tree").treeview({
		   			collapsed: true,
		   			animated: "medium",
		   			control:"#sidetreecontrol",
		   			persist: "cookie"
		   			});
		    	   $("#progresbar").hide();
		       },
                error:function (xhr, ajaxOptions, thrownError){
                    alert(xhr.status);
                    alert(thrownError);
               } 
		});
		$('input').keydown(function(e) {
			if(e.keyCode == 13) {
				var pip=$("input[name='pip']").attr("value")+'*';
				var posada=$("input[name='posada']").attr("value");
				var tel='*'+$("input[name='tel']").attr("value")+'*';
				AJAXGetRezSerch('',pip,'',posada,tel);
			}
		});
   	});
	
	function AJAXGetRezSerch(tabn,pip,org_name,posada,tel){
		$("#progresbar").show();
		chinp=$("input[name='ch']").attr("value");
		jQuery.ajax({
			type: "POST",
		       url: sap_url,
		       data:  {"task":"COMMUNIC", "employee_id":tabn,"last_name":pip,"first_name":"","org_name":"","job_name":posada,"org_id":org_name,"job_id":"","only_communic":chinp,"tel_ats":tel},
		       dataType: "html",
		       success: function(xml){
		    	   myparse(xml);
		    	   $("#progresbar").hide();
		    	},
                error:function (xhr, ajaxOptions, thrownError){AJAXErro (xhr, ajaxOptions, thrownError);} 
		});		
	};
	function AJAXErro (xhr, ajaxOptions, thrownError){
		$("#progresbar").hide();
		alert(xhr.status);
        alert(thrownError);
	};
	function myparse(xml){
		$('#mtabl').html('');
		$("#dlg_cont").html('');
		$('#mtabl').append(xml);
 		$("#dlg_cont").html($(".row:first > td:first > div").html());
 		//виділення в дереві орг поточного юзера
 		var ps_id = $(".row:first > td:first > div").attr('id');
		$('span').attr('style','');
		$('#sp_'+ps_id).attr('style','background:#00BFFF;');
		$('#sp_'+ps_id).parent('li').parent('ul').attr('style','display: block;');
		$('#sp_'+ps_id).parent('li').parent('ul').parent('li').parent('ul').attr('style','display: block;');
		$('#sp_'+ps_id).parent('li').parent('ul').parent('li').parent('ul').parent('li').parent('ul').attr('style','display: block;');
		$('#sp_'+ps_id).parent('li').parent('ul').parent('li').parent('ul').parent('li').parent('ul').parent('li').parent('ul').attr('style','display: block;');
 		
 		$(".row:first").addClass('current');
 		$('.badt').click(function(){
 			$("#mail_dlg").show();
 			$("#mt").html("Невідповідність телефонів");
 			$("#mm").html($("#dlg_cont_m > h3").html()+" невідповідний номер телефону. Вірний номер:_    _. Прошу внести зміни!");
 			mail_to = "anna.ivanova@if.energy.gov.ua";
 		});
	 	$('.bada').click(function(){
	 		$("#mail_dlg").show();
	 		$("#mt").html("Невідповідність електронної адреси");
	 		$("#mm").html($("#dlg_cont_m > h3").html()+" невідповідна електронн адреса. Вірна адреса:_  _. Прошу внести зміни!");
	 		mail_to = "roman.savchak@if.energy.gov.ua";
	 	});
 		$(".row").click(function(){
 			$(".current").removeClass('current');
   			$(this).addClass('current');
   			$("#dlg_cont").html($(this).children('td:first').children('div').html());
   			//виділення в дереві орг поточного юзера
   			var ps_id = $(this).children('td:first').children('div').attr('id');
   			$('span').attr('style','');
			$('#sp_'+ps_id).attr('style','background:#00BFFF;');
			$('#sp_'+ps_id).parent('li').parent('ul').attr('style','display: block;');
			$('#sp_'+ps_id).parent('li').parent('ul').parent('li').parent('ul').attr('style','display: block;');
			$('#sp_'+ps_id).parent('li').parent('ul').parent('li').parent('ul').parent('li').parent('ul').attr('style','display: block;');
			$('#sp_'+ps_id).parent('li').parent('ul').parent('li').parent('ul').parent('li').parent('ul').parent('li').parent('ul').attr('style','display: block;');
			//var sp_class = $('#sp_'+ps_id).parent('li').attr('class');
			//if(sp_class=='expandable'){
			//	$('#sp_'+ps_id).parent('li').removeClass('expandable');
			//	$('#sp_'+ps_id).parent('li').addClass('collapsable');
			//}
   			
   			
   			$('.badt').click(function(){
   	 			$("#mail_dlg").show();
   	 			$("#mt").html("Невідповідність телефонів");
   	 			$("#mm").html($("#dlg_cont_m > h3").html()+" невідповідний номер телефону, прошу внести зміни!");
   	 			mail_to = "anna.ivanova@if.energy.gov.ua";
   	 		});
   		 	$('.bada').click(function(){
   		 		$("#mail_dlg").show();
   		 		$("#mt").html("Невідповідність електронної адреси");
   		 		$("#mm").html($("#dlg_cont_m > h3").html()+" невідповідна електронн адреса, прошу внести зміни!");
   		 		mail_to = "roman.savchak@if.energy.gov.ua";
   		 	});
   			
  		});
	};
	function savefunck(){
		var eml = 	$("#dlg_cont > #dlg_cont_m > input[name='email']").attr('value');
			var at =	$("#dlg_cont > #dlg_cont_t > span > input[name='tel_ats']").attr('value');
			var mis =	$("#dlg_cont > #dlg_cont_t > span > input[name='tel_MISTO']").attr('value');
			var m1 =	$("#dlg_cont > #dlg_cont_t > span > input[name='mob1']").attr('value');
			var m2 =	$("#dlg_cont > #dlg_cont_t > span > input[name='mob2']").attr('value');
			var hom =	$("#dlg_cont > #dlg_cont_t > span > input[name='tel_home']").attr('value');
			var tabln =	$("#dlg_cont > #dlg_cont_f > div:first").html();
			//alert(eml +at+mis+m1+m2+hom+tabln);
			
			jQuery.ajax({
				type: "POST",
			       url: sap_url,
			       data:  {"task":"COMMUNIC_UPDATE","email":eml,"ats":at,"misto":mis,"mob1":m1,"mob2":m2,"home":hom,"employee_id":tabln},
			       dataType: "xml",
			       success: function(xml){
			    	   alert('Дані збережено!');   
			       },
			       error:function (xhr, ajaxOptions, thrownError){AJAXErro (xhr, ajaxOptions, thrownError);}
			});	
	}
</script>






<body>






<div id="sidetree">
<div class="treeheader">&nbsp;</div>
<div id="sidetreecontrol"><a href="?#">&nbsp;Згорнути всі</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?#">Розгорнути всі</a></div>
<ul id="tree" class="treeview"><!-- <li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_00000103" class="">ПАТ "Прикарпаттяобленерго</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000101" class="">Адміністрація</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000001" class="">Управлiння</span><ul style="display: none;"><li><span id="sp_50003009">Апарат Голови Правління</span></li><li class="last"><span id="sp_60100000">Керівництво</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000004" class="">Центральна бухгалтерiя</span><ul style="display: none;"><li><span id="sp_07875494">Розрахункова група</span></li><li><span id="sp_07894284">Група по обліку  реалізації</span></li><li><span id="sp_07912075">Група по обліку фінансових операцій</span></li><li><span id="sp_07933902">Матеріальна група</span></li><li><span id="sp_09473457">Група обліку основних засобів</span></li><li class="last"><span id="sp_50004814">Управління центральної бухгалтерії</span></li></ul></li><li><span id="sp_00000006">Канцелярія</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000009" class="">Служба охорони праці</span><ul style="display: none;"><li><span id="sp_50003289">Група оперативного контролю</span></li><li class="last"><span id="sp_50003290">Група аналізу і профілактики травматизму</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50002273" class="">Інформаційно-консультаційна служба</span><ul style="display: none;"><li class="last"><span id="sp_50002277">Група по роботі із зверненнями громадян</span></li></ul></li><li><span id="sp_50003193">Відділ інформаційної політики</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003664" class="">Дирекція з юридичних питань</span><ul style="display: none;"><li><span id="sp_50003662">Відділ з загально-правових питань</span></li><li><span id="sp_50003663">Відділ з претензійно-позовної роботи</span></li><li class="last"><span id="sp_50004815">Управління дирекції з юридичних питань</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003677" class="">Дирекція з загальних питань</span><ul style="display: none;"><li><span id="sp_00000005">Вiддiл матерiально-технiчного постачання</span></li><li><span id="sp_09605583">Відділ іншої реалізації</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003214" class="">Сервісний центр</span><ul style="display: none;"><li class="last"><span id="sp_50003215">Проектна група</span></li></ul></li><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span id="sp_50003683" class="">Виробничі підрозділи</span><ul style="display: block;"><li><span id="sp_00000019">Ремонтно-будiвельний цех</span></li><li class="last"><span id="sp_00000020">Служба механiзацii i транспорту</span></li></ul></li><li><span id="sp_50004818">Управління дирекції з загальних питань</span></li><li class="last"><span id="sp_50005949">Відділ інвентаризації та обл.нерухомості</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003678" class="">Дирекція технічна</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000008">Виробничо-технiчна служба</span><ul style="display: none;"><li><span id="sp_50003125">Група з загальновиробничих питань</span></li><li class="last"><span id="sp_50003131">Група перспективного розвитку</span></li></ul></li><li><span id="sp_07310013">Відділ капітального будівництва</span></li><li><span id="sp_50000780">Служба розподільних мереж</span></li><li><span id="sp_50002600">Відділ технічної інспекції</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003684">Виробничі підрозділи</span><ul style="display: none;"><li><span id="sp_00000010">Оперативно-диспетчерська служба</span></li><li><span id="sp_00000011">Служба iзоляцii</span></li><li><span id="sp_00000015">Цех по ремонту обладнання</span></li><li><span id="sp_00000016">Служба лiнiй електропередач</span></li><li><span id="sp_00000017">Служба пiдстанцiй</span></li><li class="last"><span id="sp_50004911">Служба релейного захисту і автоматики</span></li></ul></li><li class="last"><span id="sp_60100002">Управління технічних служб</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003679" class="">Дирекція з енергозбуту</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000018">Відділ  енергозбуту</span><ul style="display: none;"><li><span id="sp_50005207">Група по роботі з постачальниками</span></li><li><span id="sp_50005208">Група по забезп.розрах.зі споживачами</span></li><li class="last"><span id="sp_50005209">Група по роботі з філіями</span></li></ul></li><li><span id="sp_00000043">Служба енергонагляду</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50000627">Відділ по роботі з побут.споживачами</span><ul style="display: none;"><li><span id="sp_50000628">Група з конт.роботи абон.відділень РЕМ</span></li><li class="last"><span id="sp_50000629">Група аналізу та планування</span></li></ul></li><li><span id="sp_50000741">Відділ розподілу та контролю</span></li><li><span id="sp_50000742">Виробничо-технічний відділ (збут)</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003685">Виробничі підрозділи</span><ul style="display: none;"><li><span id="sp_50000113">Відділ обслуговування ПТК</span></li><li class="last"><span id="sp_50000785">Служба приладів обліку</span></li></ul></li><li><span id="sp_50005917">Служба аналізу втрат електроенергії</span></li><li class="last"><span id="sp_60100003">Управління збуту</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003681" class="">Дирекція з персоналу</span><ul style="display: none;"><li><span id="sp_09777340">Відділ організації праці і зарплати</span></li><li><span id="sp_50003191">Відділ обліку персоналу</span></li><li class="last"><span id="sp_50003192">Група стратегії і розвитку персоналу</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003682">Дирекція з питань ЗЕІ</span><ul style="display: none;"><li><span id="sp_00000041">Відділ охорони</span></li><li><span id="sp_50000154">Група оперативного контролю</span></li><li class="last"><span id="sp_50004819">Управління дирекції з питань ЗЕІ</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50005500" class="">Фінансова дирекція</span><ul style="display: none;"><li><span id="sp_00000046">Фiнансовий вiддiл</span></li><li><span id="sp_04419396">Відділ бюджетного планування та контролю</span></li><li class="last"><span id="sp_50005501">Управління фінансової дирекції</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50005504" class="">Дирекція з інвестицій, розвитку та ІТ</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_09511836" class="">Служба ЗДТУ</span><ul style="display: none;"><li><span id="sp_50002333">Група високочастотного зв"язку (ВЧ)</span></li><li><span id="sp_50002334">Група радіозв"язку та ел.живлення (РЗ)</span></li><li><span id="sp_50002335">Група центрального вузла зв"язку (ЦВЗ)</span></li><li><span id="sp_50002337">Група телефонного зв"язку</span></li><li class="last"><span id="sp_50002338">Група кабельного зв"язку</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50000594">Відділ супроводу і розвитку СУП</span><ul style="display: none;"><li><span id="sp_50000595">Група фінанси</span></li><li><span id="sp_50000596">Група управління матеріальними потоками</span></li><li><span id="sp_50000597">Група контролінг</span></li><li><span id="sp_50000598">Група збут</span></li><li><span id="sp_50000599">Група управління персоналом</span></li><li class="last"><span id="sp_50000600">Група ТОРО</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50004336">Служба розробки програмного забезпечення</span><ul style="display: none;"><li><span id="sp_50004342">Група розробки білінгових систем</span></li><li class="last"><span id="sp_50004343">Група розробки ПЗ технічних служб</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50004337">Служба АСКТП</span><ul style="display: none;"><li><span id="sp_50004344">Лабораторія з ремонту та обсл.обладн.ТМ</span></li><li class="last"><span id="sp_50004345">Лабораторія з експл.операт-інформ.компл.</span></li></ul></li><li><span id="sp_50005507">Управління дирекції з інвест.,розв.та ІТ</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50005508">Служба обслуговування ІТ</span><ul style="display: none;"><li><span id="sp_50005518">Група системних адміністраторів</span></li><li><span id="sp_50005519">Група підтримки програмного забезпечення</span></li><li class="last"><span id="sp_50005520">Група сервісного обсл.інфраструкт. ІТ</span></li></ul></li><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span id="sp_50005509" class="">Служба перспективного розвитку</span><ul style="display: block;"><li><span id="sp_50005552">Група стратег. та оператив. управління</span></li><li><span id="sp_50005553">Група з якості</span></li><li class="last"><span id="sp_50005554">Група з моделювання бізнес-процесів</span></li></ul></li><li><span id="sp_50005510">Група з організації роботи з клієнтами</span></li><li><span id="sp_50005511">Група з управління з інвестиціями</span></li><li class="last"><span id="sp_50005968">Центр обробки викликів</span></li></ul></li><li class="last"><span id="sp_50005569">Відділ з регуляторних питань</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000102" class="">Райони електричних мереж</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000021">Філія ПАТ "Богородчанський РЕМ"</span><ul style="display: none;"><li><span id="sp_07979757">ВДЕМ № 1</span></li><li><span id="sp_07981674">ВДЕМ № 2 (гірські)</span></li><li><span id="sp_07983744">ВД гр. підстанцій</span></li><li><span id="sp_07985853">ОВБ</span></li><li><span id="sp_07987660">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_07989830">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_09889584">Абонентська група</span></li><li><span id="sp_50005486">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005594">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_09601796">Допоміжний персонал</span></li><li><span id="sp_09791586">Група бухгалтерського обліку</span></li><li><span id="sp_50004008">Бригада централізованого ремонту</span></li><li class="last"><span id="sp_50004086">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000022">Філія ВАТ "Верховинський РЕМ"</span><ul style="display: none;"><li><span id="sp_07982596">Група бухгалтерського обліку</span></li><li><span id="sp_08131120">ВД гр. підстанцій</span></li><li><span id="sp_08132576">ОВБ</span></li><li><span id="sp_08134756">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08166778">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_10722914">Абонентська група</span></li><li><span id="sp_50005488">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005605">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08181734">ВДЕМ № 2</span></li><li><span id="sp_08184761">ВДЕМ № 1</span></li><li><span id="sp_10754183">Допоміжний персонал</span></li><li><span id="sp_50004093">Виробничо-технічна група</span></li><li class="last"><span id="sp_50005422">Бригада централізованого ремонту</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000023">Філія ВАТ "Галицький РЕМ"</span><ul style="display: none;"><li><span id="sp_07790601">ОВБ</span></li><li><span id="sp_08136025">ВД механізації та транспорту</span></li><li><span id="sp_08199827">ВДЕМ № 1</span></li><li><span id="sp_08202057">ВДЕМ № 2</span></li><li><span id="sp_08204127">ВДЕМ № 3</span></li><li><span id="sp_08449092">ВДЕО</span></li><li><span id="sp_08752416">Група бухгалтерського обліку</span></li><li><span id="sp_10240867">ВД гр. підстанцій</span></li><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span id="sp_10690315" class="">Відділення енергонагляду</span><ul style="display: block;"><li><span id="sp_10771808">Абонентська група</span></li><li><span id="sp_50005490">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005616">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_10762312">Допоміжний персонал</span></li><li class="last"><span id="sp_50004097">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000024">Філія ВАТ "Городенкiвський РЕМ"</span><ul style="display: none;"><li><span id="sp_08013536">ВД гр. підстанцій</span></li><li><span id="sp_08136679">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08170557">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_10796761">Абонентська група</span></li><li><span id="sp_50005492">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005628">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08215239">ВДЕМ № 1</span></li><li><span id="sp_08217524">ВДЕМ № 3</span></li><li><span id="sp_08628990">ВДЕО</span></li><li><span id="sp_08779840">Група бухгалтерського обліку</span></li><li><span id="sp_09152351">ОВБ</span></li><li><span id="sp_10792751">Допоміжний персонал</span></li><li><span id="sp_50002901">ВДЕМ № 2</span></li><li class="last"><span id="sp_50004103">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000025">Філія ВАТ "Долинський РЕМ"</span><ul style="display: none;"><li><span id="sp_08137580">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08171172">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_10843975">Абонентська група</span></li><li><span id="sp_50005494">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005639">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08235830">ВДЕМ № 1</span></li><li><span id="sp_08237906">ВДЕМ № 2</span></li><li><span id="sp_08242169">ОВБ</span></li><li><span id="sp_08501347">Допоміжний персонал</span></li><li><span id="sp_08506538">Персонал складу ЦО</span></li><li><span id="sp_08754565">ВДЕО</span></li><li><span id="sp_08836573">Група бухгалтерського обліку</span></li><li><span id="sp_10003446">ВД гр. підстанцій</span></li><li><span id="sp_50004027">Бригада централізованого ремонту</span></li><li class="last"><span id="sp_50004108">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000026">Філія ВАТ "Iвано-Франкiвський РЕМ"</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08171804">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_08447648">Абонентська група</span></li><li><span id="sp_08458518">Бригада по вимкненнях</span></li><li><span id="sp_08467333">Група приладів обліку</span></li><li><span id="sp_50005498">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005505">Група з договорів</span></li></ul></li><li><span id="sp_08288136">ВДЕО</span></li><li><span id="sp_08432755">ВД з обслуг. ВБ мереж</span></li><li><span id="sp_08434726">ВД повітряні лінії</span></li><li><span id="sp_08437165">ВДКЛ</span></li><li><span id="sp_08439219">ОВБ</span></li><li><span id="sp_08440966">ВД по випробуваннях і вимірах</span></li><li><span id="sp_09352196">ВД гр.підстанцій</span></li><li><span id="sp_09631028">Група бухгалтерського обліку</span></li><li><span id="sp_11214692">ВД механізації та транспорту</span></li><li><span id="sp_11219729">Допоміжний персонал</span></li><li><span id="sp_50004114">Виробничо-технічна група</span></li><li class="last"><span id="sp_50005496">Група з програмного забезпечення</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000027">Філія ВАТ "Калуський РЕМ"</span><ul style="display: none;"><li><span id="sp_08138870">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08172430">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_10875777">Абонентська група</span></li><li><span id="sp_50005528">Група з розрахунків з юрид.споживачами</span></li><li><span id="sp_50005532">Група з договорів</span></li><li class="last"><span id="sp_50005653">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08448359">ВДЕО</span></li><li><span id="sp_08452308">ВДЕМ № 1</span></li><li><span id="sp_08454511">ВДЕМ № 2</span></li><li><span id="sp_08456400">ВДЕМ № 4</span></li><li><span id="sp_08458581">ВДКЛ</span></li><li><span id="sp_09058472">ОВБ</span></li><li><span id="sp_09655190">Група бухгалтерського обліку</span></li><li><span id="sp_10059229">ВД гр. підстанцій</span></li><li><span id="sp_10775157">Допоміжний персонал</span></li><li class="last"><span id="sp_50004120">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000028">Філія ВАТ "Коломийський МРЕМ"</span><ul style="display: none;"><li><span id="sp_08008298">Група бухгалтерського обліку</span></li><li><span id="sp_08139557">ВД механізації та транспорту</span></li><li><span id="sp_08153667">ВДЕО</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08173034">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_08064679">Абонентська група</span></li><li><span id="sp_50005536">Група з розрахунків з юрид.споживачами</span></li><li><span id="sp_50005537">Група з договорів</span></li><li class="last"><span id="sp_50005668">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08466435">ВДЕМ № 1</span></li><li><span id="sp_08468401">ВДЕМ № 3 (гірські)</span></li><li><span id="sp_08470499">ВДЕМ № 2 (гірські)</span></li><li><span id="sp_08471999">ОВБ</span></li><li><span id="sp_08522949">Допоміжний персонал</span></li><li><span id="sp_50001180">ВДКЛ</span></li><li><span id="sp_50001181">ВД гр.підстанцій</span></li><li class="last"><span id="sp_50004126">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000029">Філія ВАТ "Коломийський РЕМ"</span><ul style="display: none;"><li><span id="sp_08074850">ВД гр. підстанцій</span></li><li><span id="sp_08142792">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08173644">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_08512713">Абонентська група</span></li><li><span id="sp_50005539">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005682">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08478760">ВДЕМ № 4</span></li><li><span id="sp_08480924">ВДЕМ № 1</span></li><li><span id="sp_08483725">ВДЕМ № 2</span></li><li><span id="sp_08485659">ВДЕМ № 3</span></li><li><span id="sp_08491239">ОВБ</span></li><li><span id="sp_08528908">Допоміжний персонал</span></li><li><span id="sp_09070848">ВДЕО</span></li><li><span id="sp_10503322">Група бухгалтерського обліку</span></li><li class="last"><span id="sp_50004131">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000030">Філія ВАТ "Косiвський РЕМ"</span><ul style="display: none;"><li><span id="sp_08143435">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08174308">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_10940045">Абонентська група</span></li><li><span id="sp_50005541">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005694">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08501944">ВДЕМ № 1</span></li><li><span id="sp_08503488">ВДЕМ № 4</span></li><li><span id="sp_08506212">ВДЕМ № 2</span></li><li><span id="sp_08507821">ВДЕМ № 3</span></li><li><span id="sp_08509365">ОВБ</span></li><li><span id="sp_08534038">Допоміжний персонал</span></li><li><span id="sp_09524797">Група бухгалтерського обліку</span></li><li><span id="sp_10264370">ВД гр. підстанцій</span></li><li><span id="sp_10331653">ВДЕО</span></li><li><span id="sp_50003407">Бригада централізованого ремонту</span></li><li><span id="sp_50003408">Бригада по заміні вводів</span></li><li class="last"><span id="sp_50004140">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000031">Філія ВАТ "Лисецький РЕМ"</span><ul style="display: none;"><li><span id="sp_07546024">Група бухгалтерського обліку</span></li><li><span id="sp_08144160">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08175632">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_07786729">Абонентська група</span></li><li><span id="sp_50005543">Група з розрахунків з юрид.споживачами</span></li><li><span id="sp_50005544">Група з договорів</span></li><li class="last"><span id="sp_50005723">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08522486">ВДЕМ № 1</span></li><li><span id="sp_08524282">ВДЕМ № 2</span></li><li><span id="sp_08526705">ОВБ</span></li><li><span id="sp_08540624">Допоміжний персонал</span></li><li><span id="sp_09413021">ВД гр. підстанцій</span></li><li><span id="sp_09848264">ВДЕО</span></li><li class="last"><span id="sp_50004153">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000032">Філія ВАТ "Надвiрнянський РЕМ"</span><ul style="display: none;"><li><span id="sp_08144835">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08174973">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_10982601">Абонентська група</span></li><li><span id="sp_50005546">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005753">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08538519">ВДЕМ № 1</span></li><li><span id="sp_08540398">ВДЕМ № 3</span></li><li><span id="sp_08542358">ОВБ</span></li><li><span id="sp_08543995">ВДЕМ № 2</span></li><li><span id="sp_09495332">ВД гр. підстанцій</span></li><li><span id="sp_09552710">Група бухгалтерського обліку</span></li><li><span id="sp_09911154">ВДЕО</span></li><li><span id="sp_11151062">Допоміжний персонал</span></li><li><span id="sp_50004032">Бригада централізованого ремонту</span></li><li class="last"><span id="sp_50004167">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000033">Філія ВАТ "Рогатинський РЕМ"</span><ul style="display: none;"><li><span id="sp_08145511">ВД механізації та транспорту</span></li><li><span id="sp_08156979">ВДЕО</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08176258">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_07725536">Абонентська група</span></li><li><span id="sp_50005548">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005778">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08559457">ВДЕМ № 1</span></li><li><span id="sp_08562362">ВДЕМ № 2</span></li><li><span id="sp_08564323">ОВБ</span></li><li><span id="sp_09553355">ВД гр. підстанцій</span></li><li><span id="sp_09566123">Група бухгалтерського обліку</span></li><li><span id="sp_11373087">Допоміжний персонал</span></li><li class="last"><span id="sp_50004173">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000034">Філія ВАТ "Рожнятiвський РЕМ"</span><ul style="display: none;"><li><span id="sp_08146115">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08176862">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_07651453">Абонентська група</span></li><li><span id="sp_50005550">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005788">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08572524">ВДЕМ № 1</span></li><li><span id="sp_08574539">ВДЕМ № 2 (гірські)</span></li><li><span id="sp_08576577">ОВБ</span></li><li><span id="sp_08580751">Допоміжний персонал</span></li><li><span id="sp_09570929">Група бухгалтерського обліку</span></li><li><span id="sp_09663536">ВД гр. підстанцій</span></li><li><span id="sp_10115801">ВДЕО</span></li><li class="last"><span id="sp_50004162">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000035">Філія ВАТ "Снятинський РЕМ"</span><ul style="display: none;"><li><span id="sp_08146774">ВД механізації та транспорту</span></li><li><span id="sp_08158226">ВДЕО</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08177472">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_07628582">Абонентська група</span></li><li><span id="sp_50005555">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005767">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08590984">ВДЕМ № 1</span></li><li><span id="sp_08592736">ВДЕМ № 2</span></li><li><span id="sp_08594691">ОВБ</span></li><li><span id="sp_09575932">Група бухгалтерського обліку</span></li><li><span id="sp_09733709">ВД гр. підстанцій</span></li><li><span id="sp_11074881">Допоміжний персонал</span></li><li><span id="sp_50004017">Бригада централізованого ремонту</span></li><li class="last"><span id="sp_50004149">Виробничо-технічна група</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000036">Філія ВАТ "Тлумацький РЕМ"</span><ul style="display: none;"><li><span id="sp_08147598">ВД механізації та транспорту</span></li><li><span id="sp_08158841">ВДЕМ № 3</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08178071">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_07599027">Абонентська група</span></li><li><span id="sp_50005557">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005738">Група обслуговування споживачів...</span></li></ul></li><li><span id="sp_08601694">ВДЕМ № 1</span></li><li><span id="sp_08604117">ВДЕМ № 2</span></li><li><span id="sp_08606962">ОВБ</span></li><li><span id="sp_09588302">Група бухгалтерського обліку</span></li><li><span id="sp_10403403">ВД гр. підстанцій</span></li><li><span id="sp_11302905">Допоміжний персонал</span></li><li><span id="sp_50004145">Виробничо-технічна група</span></li><li class="last"><span id="sp_50004183">ВДЕО</span></li></ul></li><li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_00000037">Філія ВАТ "Яремчанський РЕМ"</span><ul style="display: none;"><li><span id="sp_08148241">ВД механізації та транспорту</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08178702">Відділення енергонагляду</span><ul style="display: none;"><li><span id="sp_07540405">Абонентська група</span></li><li><span id="sp_50005559">Група з розрахунків з юрид.споживачами</span></li><li class="last"><span id="sp_50005713">Група обслуговування споживачів</span></li></ul></li><li><span id="sp_08612482">ВДЕМ № 1</span></li><li><span id="sp_08614542">ВДЕМ № 2</span></li><li><span id="sp_08616601">ОВБ</span></li><li><span id="sp_09593734">Група бухгалтерського обліку</span></li><li><span id="sp_09830878">ВД гр. підстанцій</span></li><li><span id="sp_10791932">Допоміжний персонал</span></li><li><span id="sp_50004132">Виробничо-технічна група</span></li><li class="last"><span id="sp_50004446">ВДЕО</span></li></ul></li></ul></li><li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_11092328" class="">Непромисловий персонал</span><ul style="display: none;"><li><span id="sp_00000039">Навчально-курсовий комбiнат</span></li><li><span id="sp_00000044">Медпункт</span></li><li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_50003601" class="">Служба соціально-побутового забезпечення</span><ul style="display: none;"><li><span id="sp_50003602">Господарський відділ</span></li><li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_50003603" class="">Відділ орган.громад.харч. і відпочинку</span><ul style="display: none;"><li><span id="sp_09695062">Їдальня Івано-Франківськ</span></li><li><span id="sp_09705641">Їдальня Лисець</span></li><li><span id="sp_10070718">Кафе</span></li><li><span id="sp_50001781">База відпочинку "Кремінці"</span></li><li><span id="sp_50003630">Оздоровчий комплекс</span></li><li><span id="sp_50004426">Кафе Івано-Франківськ</span></li><li><span id="sp_50005263">Кафе Лисець</span></li><li class="last"><span id="sp_50005823">Склад торгівлі</span></li></ul></li></ul></li></ul></li></ul></li> --></ul>
</div>




		



	
	
</body>
</html>	
	