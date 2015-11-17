<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>����������</title>

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
 		//�������� � ����� ��� ��������� �����
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
 			$("#mt").html("������������ ��������");
 			$("#mm").html($("#dlg_cont_m > h3").html()+" ����������� ����� ��������. ³���� �����:_    _. ����� ������ ����!");
 			mail_to = "anna.ivanova@if.energy.gov.ua";
 		});
	 	$('.bada').click(function(){
	 		$("#mail_dlg").show();
	 		$("#mt").html("������������ ���������� ������");
	 		$("#mm").html($("#dlg_cont_m > h3").html()+" ���������� ��������� ������. ³��� ������:_  _. ����� ������ ����!");
	 		mail_to = "roman.savchak@if.energy.gov.ua";
	 	});
 		$(".row").click(function(){
 			$(".current").removeClass('current');
   			$(this).addClass('current');
   			$("#dlg_cont").html($(this).children('td:first').children('div').html());
   			//�������� � ����� ��� ��������� �����
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
   	 			$("#mt").html("������������ ��������");
   	 			$("#mm").html($("#dlg_cont_m > h3").html()+" ����������� ����� ��������, ����� ������ ����!");
   	 			mail_to = "anna.ivanova@if.energy.gov.ua";
   	 		});
   		 	$('.bada').click(function(){
   		 		$("#mail_dlg").show();
   		 		$("#mt").html("������������ ���������� ������");
   		 		$("#mm").html($("#dlg_cont_m > h3").html()+" ���������� ��������� ������, ����� ������ ����!");
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
			    	   alert('��� ���������!');   
			       },
			       error:function (xhr, ajaxOptions, thrownError){AJAXErro (xhr, ajaxOptions, thrownError);}
			});	
	}
</script>






<body>






<div id="sidetree">
<div class="treeheader">&nbsp;</div>
<div id="sidetreecontrol"><a href="?#">&nbsp;�������� ��</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?#">���������� ��</a></div>
<ul id="tree" class="treeview"><!-- <li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_00000103" class="">��� "��������������������</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000101" class="">�����������</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000001" class="">������i���</span><ul style="display: none;"><li><span id="sp_50003009">������ ������ ��������</span></li><li class="last"><span id="sp_60100000">����������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000004" class="">���������� ���������i�</span><ul style="display: none;"><li><span id="sp_07875494">������������ �����</span></li><li><span id="sp_07894284">����� �� �����  ���������</span></li><li><span id="sp_07912075">����� �� ����� ���������� ��������</span></li><li><span id="sp_07933902">���������� �����</span></li><li><span id="sp_09473457">����� ����� �������� ������</span></li><li class="last"><span id="sp_50004814">��������� ���������� ���������</span></li></ul></li><li><span id="sp_00000006">���������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000009" class="">������ ������� �����</span><ul style="display: none;"><li><span id="sp_50003289">����� ������������ ��������</span></li><li class="last"><span id="sp_50003290">����� ������ � ������������ �����������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50002273" class="">������������-�������������� ������</span><ul style="display: none;"><li class="last"><span id="sp_50002277">����� �� ����� �� ����������� ��������</span></li></ul></li><li><span id="sp_50003193">³��� ������������ �������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003664" class="">�������� � ��������� ������</span><ul style="display: none;"><li><span id="sp_50003662">³��� � ��������-�������� ������</span></li><li><span id="sp_50003663">³��� � ����������-������� ������</span></li><li class="last"><span id="sp_50004815">��������� �������� � ��������� ������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003677" class="">�������� � ��������� ������</span><ul style="display: none;"><li><span id="sp_00000005">�i��i� �����i�����-����i����� ����������</span></li><li><span id="sp_09605583">³��� ���� ���������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003214" class="">�������� �����</span><ul style="display: none;"><li class="last"><span id="sp_50003215">�������� �����</span></li></ul></li><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span id="sp_50003683" class="">��������� ��������</span><ul style="display: block;"><li><span id="sp_00000019">��������-���i������� ���</span></li><li class="last"><span id="sp_00000020">������ �����i���ii i ����������</span></li></ul></li><li><span id="sp_50004818">��������� �������� � ��������� ������</span></li><li class="last"><span id="sp_50005949">³��� �������������� �� ���.����������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003678" class="">�������� �������</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000008">���������-����i��� ������</span><ul style="display: none;"><li><span id="sp_50003125">����� � ������������������ ������</span></li><li class="last"><span id="sp_50003131">����� �������������� ��������</span></li></ul></li><li><span id="sp_07310013">³��� ����������� ����������</span></li><li><span id="sp_50000780">������ ����������� �����</span></li><li><span id="sp_50002600">³��� ������� ���������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003684">��������� ��������</span><ul style="display: none;"><li><span id="sp_00000010">����������-������������� ������</span></li><li><span id="sp_00000011">������ i�����ii</span></li><li><span id="sp_00000015">��� �� ������� ����������</span></li><li><span id="sp_00000016">������ �i�i� ��������������</span></li><li><span id="sp_00000017">������ �i������i�</span></li><li class="last"><span id="sp_50004911">������ ��������� ������� � ����������</span></li></ul></li><li class="last"><span id="sp_60100002">��������� �������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003679" class="">�������� � �����������</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000018">³���  �����������</span><ul style="display: none;"><li><span id="sp_50005207">����� �� ����� � ���������������</span></li><li><span id="sp_50005208">����� �� ������.������.� �����������</span></li><li class="last"><span id="sp_50005209">����� �� ����� � ������</span></li></ul></li><li><span id="sp_00000043">������ �������������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50000627">³��� �� ����� � �����.�����������</span><ul style="display: none;"><li><span id="sp_50000628">����� � ����.������ ����.������� ���</span></li><li class="last"><span id="sp_50000629">����� ������ �� ����������</span></li></ul></li><li><span id="sp_50000741">³��� �������� �� ��������</span></li><li><span id="sp_50000742">���������-�������� ���� (����)</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003685">��������� ��������</span><ul style="display: none;"><li><span id="sp_50000113">³��� �������������� ���</span></li><li class="last"><span id="sp_50000785">������ ������� �����</span></li></ul></li><li><span id="sp_50005917">������ ������ ����� �����������㳿</span></li><li class="last"><span id="sp_60100003">��������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003681" class="">�������� � ���������</span><ul style="display: none;"><li><span id="sp_09777340">³��� ���������� ����� � ��������</span></li><li><span id="sp_50003191">³��� ����� ���������</span></li><li class="last"><span id="sp_50003192">����� ������㳿 � �������� ���������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50003682">�������� � ������ �Ų</span><ul style="display: none;"><li><span id="sp_00000041">³��� �������</span></li><li><span id="sp_50000154">����� ������������ ��������</span></li><li class="last"><span id="sp_50004819">��������� �������� � ������ �Ų</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50005500" class="">Գ������� ��������</span><ul style="display: none;"><li><span id="sp_00000046">�i�������� �i��i�</span></li><li><span id="sp_04419396">³��� ���������� ���������� �� ��������</span></li><li class="last"><span id="sp_50005501">��������� ��������� ��������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50005504" class="">�������� � ����������, �������� �� ��</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_09511836" class="">������ ����</span><ul style="display: none;"><li><span id="sp_50002333">����� ���������������� ��"���� (��)</span></li><li><span id="sp_50002334">����� ������"���� �� ��.�������� (��)</span></li><li><span id="sp_50002335">����� ������������ ����� ��"���� (���)</span></li><li><span id="sp_50002337">����� ����������� ��"����</span></li><li class="last"><span id="sp_50002338">����� ���������� ��"����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50000594">³��� ��������� � �������� ���</span><ul style="display: none;"><li><span id="sp_50000595">����� �������</span></li><li><span id="sp_50000596">����� ��������� ������������ ��������</span></li><li><span id="sp_50000597">����� ���������</span></li><li><span id="sp_50000598">����� ����</span></li><li><span id="sp_50000599">����� ��������� ����������</span></li><li class="last"><span id="sp_50000600">����� ����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50004336">������ �������� ����������� ������������</span><ul style="display: none;"><li><span id="sp_50004342">����� �������� �������� ������</span></li><li class="last"><span id="sp_50004343">����� �������� �� �������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50004337">������ �����</span><ul style="display: none;"><li><span id="sp_50004344">���������� � ������� �� ����.������.��</span></li><li class="last"><span id="sp_50004345">���������� � �����.������-������.�����.</span></li></ul></li><li><span id="sp_50005507">��������� �������� � ������.,����.�� ��</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_50005508">������ �������������� ��</span><ul style="display: none;"><li><span id="sp_50005518">����� ��������� ������������</span></li><li><span id="sp_50005519">����� �������� ����������� ������������</span></li><li class="last"><span id="sp_50005520">����� ��������� ����.�����������. ��</span></li></ul></li><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span id="sp_50005509" class="">������ �������������� ��������</span><ul style="display: block;"><li><span id="sp_50005552">����� �������. �� ��������. ���������</span></li><li><span id="sp_50005553">����� � �����</span></li><li class="last"><span id="sp_50005554">����� � ����������� �����-�������</span></li></ul></li><li><span id="sp_50005510">����� � ���������� ������ � �볺�����</span></li><li><span id="sp_50005511">����� � ��������� � ������������</span></li><li class="last"><span id="sp_50005968">����� ������� �������</span></li></ul></li><li class="last"><span id="sp_50005569">³��� � ������������ ������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000102" class="">������ ����������� �����</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000021">Գ�� ��� "��������������� ���"</span><ul style="display: none;"><li><span id="sp_07979757">���� � 1</span></li><li><span id="sp_07981674">���� � 2 (�����)</span></li><li><span id="sp_07983744">�� ��. ���������</span></li><li><span id="sp_07985853">���</span></li><li><span id="sp_07987660">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_07989830">³������� �������������</span><ul style="display: none;"><li><span id="sp_09889584">����������� �����</span></li><li><span id="sp_50005486">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005594">����� �������������� ����������</span></li></ul></li><li><span id="sp_09601796">��������� ��������</span></li><li><span id="sp_09791586">����� ��������������� �����</span></li><li><span id="sp_50004008">������� ��������������� �������</span></li><li class="last"><span id="sp_50004086">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000022">Գ�� ��� "������������� ���"</span><ul style="display: none;"><li><span id="sp_07982596">����� ��������������� �����</span></li><li><span id="sp_08131120">�� ��. ���������</span></li><li><span id="sp_08132576">���</span></li><li><span id="sp_08134756">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08166778">³������� �������������</span><ul style="display: none;"><li><span id="sp_10722914">����������� �����</span></li><li><span id="sp_50005488">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005605">����� �������������� ����������</span></li></ul></li><li><span id="sp_08181734">���� � 2</span></li><li><span id="sp_08184761">���� � 1</span></li><li><span id="sp_10754183">��������� ��������</span></li><li><span id="sp_50004093">���������-������� �����</span></li><li class="last"><span id="sp_50005422">������� ��������������� �������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000023">Գ�� ��� "��������� ���"</span><ul style="display: none;"><li><span id="sp_07790601">���</span></li><li><span id="sp_08136025">�� ���������� �� ����������</span></li><li><span id="sp_08199827">���� � 1</span></li><li><span id="sp_08202057">���� � 2</span></li><li><span id="sp_08204127">���� � 3</span></li><li><span id="sp_08449092">����</span></li><li><span id="sp_08752416">����� ��������������� �����</span></li><li><span id="sp_10240867">�� ��. ���������</span></li><li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span id="sp_10690315" class="">³������� �������������</span><ul style="display: block;"><li><span id="sp_10771808">����������� �����</span></li><li><span id="sp_50005490">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005616">����� �������������� ����������</span></li></ul></li><li><span id="sp_10762312">��������� ��������</span></li><li class="last"><span id="sp_50004097">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000024">Գ�� ��� "��������i������ ���"</span><ul style="display: none;"><li><span id="sp_08013536">�� ��. ���������</span></li><li><span id="sp_08136679">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08170557">³������� �������������</span><ul style="display: none;"><li><span id="sp_10796761">����������� �����</span></li><li><span id="sp_50005492">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005628">����� �������������� ����������</span></li></ul></li><li><span id="sp_08215239">���� � 1</span></li><li><span id="sp_08217524">���� � 3</span></li><li><span id="sp_08628990">����</span></li><li><span id="sp_08779840">����� ��������������� �����</span></li><li><span id="sp_09152351">���</span></li><li><span id="sp_10792751">��������� ��������</span></li><li><span id="sp_50002901">���� � 2</span></li><li class="last"><span id="sp_50004103">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000025">Գ�� ��� "���������� ���"</span><ul style="display: none;"><li><span id="sp_08137580">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08171172">³������� �������������</span><ul style="display: none;"><li><span id="sp_10843975">����������� �����</span></li><li><span id="sp_50005494">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005639">����� �������������� ����������</span></li></ul></li><li><span id="sp_08235830">���� � 1</span></li><li><span id="sp_08237906">���� � 2</span></li><li><span id="sp_08242169">���</span></li><li><span id="sp_08501347">��������� ��������</span></li><li><span id="sp_08506538">�������� ������ ��</span></li><li><span id="sp_08754565">����</span></li><li><span id="sp_08836573">����� ��������������� �����</span></li><li><span id="sp_10003446">�� ��. ���������</span></li><li><span id="sp_50004027">������� ��������������� �������</span></li><li class="last"><span id="sp_50004108">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000026">Գ�� ��� "I����-�����i������ ���"</span><ul style="display: none;"><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08171804">³������� �������������</span><ul style="display: none;"><li><span id="sp_08447648">����������� �����</span></li><li><span id="sp_08458518">������� �� ����������</span></li><li><span id="sp_08467333">����� ������� �����</span></li><li><span id="sp_50005498">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005505">����� � ��������</span></li></ul></li><li><span id="sp_08288136">����</span></li><li><span id="sp_08432755">�� � ������. �� �����</span></li><li><span id="sp_08434726">�� ������� ��</span></li><li><span id="sp_08437165">����</span></li><li><span id="sp_08439219">���</span></li><li><span id="sp_08440966">�� �� ������������� � ������</span></li><li><span id="sp_09352196">�� ��.���������</span></li><li><span id="sp_09631028">����� ��������������� �����</span></li><li><span id="sp_11214692">�� ���������� �� ����������</span></li><li><span id="sp_11219729">��������� ��������</span></li><li><span id="sp_50004114">���������-������� �����</span></li><li class="last"><span id="sp_50005496">����� � ����������� ������������</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000027">Գ�� ��� "��������� ���"</span><ul style="display: none;"><li><span id="sp_08138870">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08172430">³������� �������������</span><ul style="display: none;"><li><span id="sp_10875777">����������� �����</span></li><li><span id="sp_50005528">����� � ���������� � ����.�����������</span></li><li><span id="sp_50005532">����� � ��������</span></li><li class="last"><span id="sp_50005653">����� �������������� ����������</span></li></ul></li><li><span id="sp_08448359">����</span></li><li><span id="sp_08452308">���� � 1</span></li><li><span id="sp_08454511">���� � 2</span></li><li><span id="sp_08456400">���� � 4</span></li><li><span id="sp_08458581">����</span></li><li><span id="sp_09058472">���</span></li><li><span id="sp_09655190">����� ��������������� �����</span></li><li><span id="sp_10059229">�� ��. ���������</span></li><li><span id="sp_10775157">��������� ��������</span></li><li class="last"><span id="sp_50004120">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000028">Գ�� ��� "������������ ����"</span><ul style="display: none;"><li><span id="sp_08008298">����� ��������������� �����</span></li><li><span id="sp_08139557">�� ���������� �� ����������</span></li><li><span id="sp_08153667">����</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08173034">³������� �������������</span><ul style="display: none;"><li><span id="sp_08064679">����������� �����</span></li><li><span id="sp_50005536">����� � ���������� � ����.�����������</span></li><li><span id="sp_50005537">����� � ��������</span></li><li class="last"><span id="sp_50005668">����� �������������� ����������</span></li></ul></li><li><span id="sp_08466435">���� � 1</span></li><li><span id="sp_08468401">���� � 3 (�����)</span></li><li><span id="sp_08470499">���� � 2 (�����)</span></li><li><span id="sp_08471999">���</span></li><li><span id="sp_08522949">��������� ��������</span></li><li><span id="sp_50001180">����</span></li><li><span id="sp_50001181">�� ��.���������</span></li><li class="last"><span id="sp_50004126">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000029">Գ�� ��� "������������ ���"</span><ul style="display: none;"><li><span id="sp_08074850">�� ��. ���������</span></li><li><span id="sp_08142792">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08173644">³������� �������������</span><ul style="display: none;"><li><span id="sp_08512713">����������� �����</span></li><li><span id="sp_50005539">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005682">����� �������������� ����������</span></li></ul></li><li><span id="sp_08478760">���� � 4</span></li><li><span id="sp_08480924">���� � 1</span></li><li><span id="sp_08483725">���� � 2</span></li><li><span id="sp_08485659">���� � 3</span></li><li><span id="sp_08491239">���</span></li><li><span id="sp_08528908">��������� ��������</span></li><li><span id="sp_09070848">����</span></li><li><span id="sp_10503322">����� ��������������� �����</span></li><li class="last"><span id="sp_50004131">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000030">Գ�� ��� "���i������ ���"</span><ul style="display: none;"><li><span id="sp_08143435">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08174308">³������� �������������</span><ul style="display: none;"><li><span id="sp_10940045">����������� �����</span></li><li><span id="sp_50005541">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005694">����� �������������� ����������</span></li></ul></li><li><span id="sp_08501944">���� � 1</span></li><li><span id="sp_08503488">���� � 4</span></li><li><span id="sp_08506212">���� � 2</span></li><li><span id="sp_08507821">���� � 3</span></li><li><span id="sp_08509365">���</span></li><li><span id="sp_08534038">��������� ��������</span></li><li><span id="sp_09524797">����� ��������������� �����</span></li><li><span id="sp_10264370">�� ��. ���������</span></li><li><span id="sp_10331653">����</span></li><li><span id="sp_50003407">������� ��������������� �������</span></li><li><span id="sp_50003408">������� �� ���� �����</span></li><li class="last"><span id="sp_50004140">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000031">Գ�� ��� "��������� ���"</span><ul style="display: none;"><li><span id="sp_07546024">����� ��������������� �����</span></li><li><span id="sp_08144160">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08175632">³������� �������������</span><ul style="display: none;"><li><span id="sp_07786729">����������� �����</span></li><li><span id="sp_50005543">����� � ���������� � ����.�����������</span></li><li><span id="sp_50005544">����� � ��������</span></li><li class="last"><span id="sp_50005723">����� �������������� ����������</span></li></ul></li><li><span id="sp_08522486">���� � 1</span></li><li><span id="sp_08524282">���� � 2</span></li><li><span id="sp_08526705">���</span></li><li><span id="sp_08540624">��������� ��������</span></li><li><span id="sp_09413021">�� ��. ���������</span></li><li><span id="sp_09848264">����</span></li><li class="last"><span id="sp_50004153">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000032">Գ�� ��� "����i��������� ���"</span><ul style="display: none;"><li><span id="sp_08144835">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08174973">³������� �������������</span><ul style="display: none;"><li><span id="sp_10982601">����������� �����</span></li><li><span id="sp_50005546">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005753">����� �������������� ����������</span></li></ul></li><li><span id="sp_08538519">���� � 1</span></li><li><span id="sp_08540398">���� � 3</span></li><li><span id="sp_08542358">���</span></li><li><span id="sp_08543995">���� � 2</span></li><li><span id="sp_09495332">�� ��. ���������</span></li><li><span id="sp_09552710">����� ��������������� �����</span></li><li><span id="sp_09911154">����</span></li><li><span id="sp_11151062">��������� ��������</span></li><li><span id="sp_50004032">������� ��������������� �������</span></li><li class="last"><span id="sp_50004167">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000033">Գ�� ��� "������������ ���"</span><ul style="display: none;"><li><span id="sp_08145511">�� ���������� �� ����������</span></li><li><span id="sp_08156979">����</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08176258">³������� �������������</span><ul style="display: none;"><li><span id="sp_07725536">����������� �����</span></li><li><span id="sp_50005548">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005778">����� �������������� ����������</span></li></ul></li><li><span id="sp_08559457">���� � 1</span></li><li><span id="sp_08562362">���� � 2</span></li><li><span id="sp_08564323">���</span></li><li><span id="sp_09553355">�� ��. ���������</span></li><li><span id="sp_09566123">����� ��������������� �����</span></li><li><span id="sp_11373087">��������� ��������</span></li><li class="last"><span id="sp_50004173">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000034">Գ�� ��� "������i������ ���"</span><ul style="display: none;"><li><span id="sp_08146115">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08176862">³������� �������������</span><ul style="display: none;"><li><span id="sp_07651453">����������� �����</span></li><li><span id="sp_50005550">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005788">����� �������������� ����������</span></li></ul></li><li><span id="sp_08572524">���� � 1</span></li><li><span id="sp_08574539">���� � 2 (�����)</span></li><li><span id="sp_08576577">���</span></li><li><span id="sp_08580751">��������� ��������</span></li><li><span id="sp_09570929">����� ��������������� �����</span></li><li><span id="sp_09663536">�� ��. ���������</span></li><li><span id="sp_10115801">����</span></li><li class="last"><span id="sp_50004162">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000035">Գ�� ��� "����������� ���"</span><ul style="display: none;"><li><span id="sp_08146774">�� ���������� �� ����������</span></li><li><span id="sp_08158226">����</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08177472">³������� �������������</span><ul style="display: none;"><li><span id="sp_07628582">����������� �����</span></li><li><span id="sp_50005555">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005767">����� �������������� ����������</span></li></ul></li><li><span id="sp_08590984">���� � 1</span></li><li><span id="sp_08592736">���� � 2</span></li><li><span id="sp_08594691">���</span></li><li><span id="sp_09575932">����� ��������������� �����</span></li><li><span id="sp_09733709">�� ��. ���������</span></li><li><span id="sp_11074881">��������� ��������</span></li><li><span id="sp_50004017">������� ��������������� �������</span></li><li class="last"><span id="sp_50004149">���������-������� �����</span></li></ul></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_00000036">Գ�� ��� "���������� ���"</span><ul style="display: none;"><li><span id="sp_08147598">�� ���������� �� ����������</span></li><li><span id="sp_08158841">���� � 3</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08178071">³������� �������������</span><ul style="display: none;"><li><span id="sp_07599027">����������� �����</span></li><li><span id="sp_50005557">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005738">����� �������������� ����������...</span></li></ul></li><li><span id="sp_08601694">���� � 1</span></li><li><span id="sp_08604117">���� � 2</span></li><li><span id="sp_08606962">���</span></li><li><span id="sp_09588302">����� ��������������� �����</span></li><li><span id="sp_10403403">�� ��. ���������</span></li><li><span id="sp_11302905">��������� ��������</span></li><li><span id="sp_50004145">���������-������� �����</span></li><li class="last"><span id="sp_50004183">����</span></li></ul></li><li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_00000037">Գ�� ��� "������������ ���"</span><ul style="display: none;"><li><span id="sp_08148241">�� ���������� �� ����������</span></li><li class="expandable"><div class="hitarea expandable-hitarea"></div><span id="sp_08178702">³������� �������������</span><ul style="display: none;"><li><span id="sp_07540405">����������� �����</span></li><li><span id="sp_50005559">����� � ���������� � ����.�����������</span></li><li class="last"><span id="sp_50005713">����� �������������� ����������</span></li></ul></li><li><span id="sp_08612482">���� � 1</span></li><li><span id="sp_08614542">���� � 2</span></li><li><span id="sp_08616601">���</span></li><li><span id="sp_09593734">����� ��������������� �����</span></li><li><span id="sp_09830878">�� ��. ���������</span></li><li><span id="sp_10791932">��������� ��������</span></li><li><span id="sp_50004132">���������-������� �����</span></li><li class="last"><span id="sp_50004446">����</span></li></ul></li></ul></li><li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_11092328" class="">������������� ��������</span><ul style="display: none;"><li><span id="sp_00000039">���������-�������� ����i���</span></li><li><span id="sp_00000044">��������</span></li><li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_50003601" class="">������ ���������-���������� ������������</span><ul style="display: none;"><li><span id="sp_50003602">������������� ����</span></li><li class="expandable lastExpandable"><div class="hitarea expandable-hitarea lastExpandable-hitarea"></div><span id="sp_50003603" class="">³��� �����.������.����. � ���������</span><ul style="display: none;"><li><span id="sp_09695062">������� �����-���������</span></li><li><span id="sp_09705641">������� ������</span></li><li><span id="sp_10070718">����</span></li><li><span id="sp_50001781">���� ��������� "�������"</span></li><li><span id="sp_50003630">���������� ��������</span></li><li><span id="sp_50004426">���� �����-���������</span></li><li><span id="sp_50005263">���� ������</span></li><li class="last"><span id="sp_50005823">����� ������</span></li></ul></li></ul></li></ul></li></ul></li> --></ul>
</div>




		



	
	
</body>
</html>	
	