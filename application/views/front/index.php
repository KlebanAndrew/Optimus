<?php $this->load->view('front/header'); ?>

<div id="container">
	<h1>�� ����� (<? echo $title_dates; ?>) ��������  �������</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/settings").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">�����</a></div>
	<div id="body">

    <script src="<? echo base_url() ?>application/views/js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/js/jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
	
    <script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	
	<link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.14.custom.css">
	<link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-timepicker-addon.css">
	<script type="text/javascript" src="<? echo base_url() ?>application/views/js/DatePickerScript.js"></script>

	<!-- ϳ������ -->
	<script src="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.jq.js" type="text/javascript"></script>
	<link href="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.min.jq.css" rel="stylesheet" type="text/css" />
        <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
        <link type="text/css" href="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip_index.css" rel="stylesheet" />
        <script src="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip.js"></script>
	
<?php
$stroka_id_pot = array();
if($potocni_zavd) {
	for($i=0; $i<count($potocni_zavd); $i++) {
		$stroka_id_pot[] = $potocni_zavd[$i]['id'];
	}
}
$j_stroka_id_pot = json_encode($stroka_id_pot);
$stroka_id_poz = array();
if($pozachergovi_zavd) {
	for($i=0; $i<count($pozachergovi_zavd); $i++) {
		$stroka_id_poz[] = $pozachergovi_zavd[$i]['id'];
	}
}
$j_stroka_id_poz = json_encode($stroka_id_poz);
?>
	
	
<script>
   $(function(){
       $('.tooltiper a').tooltip({
           track: false,
           delay: 0,
           showURL: false,
           fade: 200
       });
       $('.tooltiper img').tooltip({
           track: false,
           delay: 0,
           showURL: false,
           fade: 200
       });
// ����������� ������ ������� ���� ��������� ajax ������
       $.ajaxSetup({
           complete: function(){
               $('.tooltiper img').tooltip({
                   track: false,
                   delay: 0,
                   showURL: false,
                   fade: 200
               });
           }
       });
       //       $(".DatePicker").datepicker("setDate", "+0d");
       $('.DatePicker').mouseover(function(){
           $(this).animate({ backgroundColor: "#f4a62a",
               color: 'white'
           }, "fast");
       });
       $('.DatePicker').mouseout(function(){
           $(this).animate({ backgroundColor: "white",
               color: 'black'}, "fast");
       });
   });


function zatverduty() {
	$.ajax({
		type: "POST",
		url: "<? echo site_url("ajax/ajax_zatv_pl2") ?>",
		data: { 
			"j_stroka_id_pot":'<? echo $j_stroka_id_pot; ?>',
			"j_stroka_id_poz":'<? echo $j_stroka_id_poz; ?>',
			"period" :'now'		// ���� ������ (�������� ��� �����������)
		},
		dataType: "html",
		success: function(msg){
			if (parseInt(msg)!=0) {
				//alert(msg);
				if(msg == "ok") {
					$().el7r_notify({'text':"���� ���������� �������� �� ������������ !", 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});				
					$(".img_t>img").attr("src","<? echo base_url().'application/views/front/files/'; ?>/timeIcon.png");
					$(".img_t").removeAttr('href');
					$(".img_t").removeAttr('onclick'); 
					setInterval(function(){ 
						window.location.reload(0);
					},1000); // 10sec (10000)
				}
				if(msg == "error") {
					$().el7r_notify({'text':"�������! � ��� ���� �������� �������", 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});				
				}
			}
		}
	});
}

// ��������� �� ��������� �������
function report_date() {
	//alert($('input[name=json_stroka_pot]').val());
	//alert($('input[name=json_stroka_poz]').val());
	//alert($('input[name=date_vid]').val());
	//alert($('input[name=date_do]').val());
	var myForm = document.createElement("form");
    myForm.style.visibility = "hidden";
    myForm.method = "POST";
    myForm.action = "<? echo site_url("main/report_date") ?>";
    var par1 = document.createElement("input");
    par1.setAttribute("type", "hidden");
    par1.setAttribute("name", "j_stroka_pot");
    par1.setAttribute("value", $('input[name=json_stroka_pot]').val());
    myForm.appendChild(par1);
    var par2 = document.createElement("input");
    par2.setAttribute("type", "hidden");
    par2.setAttribute("name", "j_stroka_poz");
    par2.setAttribute("value", $('input[name=json_stroka_poz]').val());
    myForm.appendChild(par2);
    var par3 = document.createElement("input");
    par3.setAttribute("type", "hidden");
    par3.setAttribute("name", "date_vid");
    par3.setAttribute("value", $('input[name=date_vid]').val());
    myForm.appendChild(par3);
    var par4 = document.createElement("input");
    par4.setAttribute("type", "hidden");
    par4.setAttribute("name", "date_do");
    par4.setAttribute("value", $('input[name=date_do]').val());
    myForm.appendChild(par4);
    document.body.appendChild(myForm);
    myForm.submit();
}

function del_zavd() {
	if (confirm("�������� �� �������� ?")) {
		return true;
	} else {
		return false;
	}
}
</script>	
<a href="<? echo site_url("main/faq") ?>"><img src="<? echo base_url() ?>application/views/front/files/question.png" style="float:right; margin-right: -150px;" /></a>
Գ���� �� ���:
<input type="text" name="Period" form="export" class="DatePicker" style="font-size:14px; font-weight:bold; width: 150px;text-align: center;"/><p />
	
<div id="load" style="visibility:hidden;">
	<!--<div class="load_background">-->
		<img src="<? echo base_url() ?>application/views/front/files/ajax-loader.gif" class="ajax-loader"/>
	<!--</div>-->	
</div>	

<?php
if($status) {
// ����������� � ��������� �� �������
if($status->flag == 0) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification error">
		<div><b>���� ��������� �� �������������, �������: </b>'.$status->comment.'</div>
		</div>';
}
if($status->flag == 1) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification success">
		<div><b>���� �� ����������� � �������� ! </b></div>
		</div>';
}
if($status->flag == 2) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification success">
		<div><b>���� ����������� </b></div>
		</div>';
}
if($status->flag == 3) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification success">
		<div><b>��� �� ����������� � �������� ! </b></div>
		</div>';
}
if($status->flag == 4) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification success">
		<div><b>���� ����������� ! </b></div>
		</div>';
}
}
// ��������� ����� (������� ������)
if($product_posadu == '') {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification attention">
		<div><b>�������� ���� "<a href="'.site_url("main/edit_user").'">������� ������</a>"</b></div>
		</div>';
}
?>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5">���</th>
			<th width="17">���. ��.</th>
			<th width="100">����� ��������</th>
			<th width="200">��������� �����</th>
			<th width="50">���� ������� ��������</th>
			<th width="50">����. ���� ������.</th>
			<th width="50">����. ��� �� �����.</th>
			<th width="50">�������� ���� ������.</th>
			<th width="50">����. ����. ���</th>
			<th width="5">������</th>
		</tr>
    </thead>
	<?php $this->load->view('front/ajax_pl'); ?>
</tbody>	
</table>
	<div style="height: 30px;margin: 20px 20px 0px 20px;">
		<form action="<?php echo base_url() ?>export_excel/1.php" method="POST" id="export" style="float:left;">
<? if(@$status->flag != 4) { ?>
			<input type="button" class="but orange" id="but_1" onclick="location.href='<?php echo site_url("main/create_zavd") ?>'" value="�������� ��������">
          <?if(@$status->flag > 0){?>  <input type="button" class="  but orange" id="but_3" onclick="zatverduty()" value="³�������� �� ������������" disabled="disabled" /><?}
    else {?>
        <input type="button" class="  but orange" id="but_3" onclick="zatverduty()" value="³�������� �� ������������" />
    <?}?>
<? }else{?>
    <input type="button" class="  but orange" id="but_1" onclick="location.href='<?php echo site_url("main/create_zavd") ?>'" value="�������� ��������" disabled="disabled">
    <input type="button" class="  but orange" id="but_3" onclick="zatverduty()" value="³�������� �� ������������" disabled="disabled"/>
<?} ?>

<? if(@$status->flag != 4) { ?>
			<input type="button" class="but orange" id="but_2" onclick="location.href='<?php echo site_url("main/report") ?>'" value="³�����������" />
<? }
else{?>
    <input type="button" class="but orange" id="but_2" onclick="location.href='<?php echo site_url("main/report") ?>'" value="³�����������" disabled="disabled"/>
<?} ?>
			<input type="button" class="but orange " id="but_4" onclick="report_date()" value="³�����������" style="visibility:hidden;" />
			<input type="hidden" name="user_id" value="<?php echo $this->session->userdata('user_id');?>" />
			<input type="hidden" name="period" value="<?php echo $this->session->userdata('date_begin');?>" />
			<input type="submit" name="exportSubmit" class="but orange" value="������� � Excel" style="float:none;"/>

		</form>
        <input style="margin-left: 25px;" type="button" class="but orange" id="SD" onclick="location.href='<?php echo site_url("servicedesk/index") ?>'" value="������ � SD"/>
	</div>

	</div>
<?php $this->load->view('front/footer'); ?>