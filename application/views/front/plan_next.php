<?php $this->load->view('front/header'); ?>

<div id="container">
	<h1>Мої плани (<? echo $title_dates; ?>) наступний тиждень</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/settings").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">Вихід</a></div>
	<div id="body">

    <script src="<? echo base_url() ?>application/views/js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/js/jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
	
    <script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	
	<link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.14.custom.css">
	<link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-timepicker-addon.css">
	<script type="text/javascript" src="<? echo base_url() ?>application/views/js/DatePickerScript.js"></script>

	<!-- Підказки -->
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

        // ініціалізація плагіна підказок після виконання ajax запитів
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
        $(".DatePicker").datepicker("setDate", "+7d");
        //активний елемент меню
        $('#menu ul li').removeAttr("id");
        $('#menu ul li:nth-child(2)').attr("id","active");
        //
    });
function zatverduty() {
	$.ajax({
		type: "POST",
		url: "<? echo site_url("ajax/ajax_zatv_pl2") ?>",
		data: { 
			"j_stroka_id_pot":'<? echo $j_stroka_id_pot; ?>',
			"j_stroka_id_poz":'<? echo $j_stroka_id_poz; ?>',
			"period" :'next'	// флаг періода (потрібний при затвердженні)
		},
		dataType: "html",
		success: function(msg){
			if (parseInt(msg)!=0) {
				//alert(msg);
				if(msg == "ok") {
					$().el7r_notify({'text':"План відправлено керівнику на затвердження !", 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});				
					$(".img_t>img").attr("src","<? echo base_url().'application/views/front/files/'; ?>/timeIcon.png");
					$(".img_t").removeAttr('href');
					$(".img_t").removeAttr('onclick');
					setInterval(function(){ 
						window.location.reload(0);
					},1000); // 10sec (10000)
				}
				if(msg == "error") {
					$().el7r_notify({'text':"Помилка! у вас немає поточних завдань", 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});				
				}
			}
		}
	});
}

function del_zavd() {
	if (confirm("Видалити це завдання ?")) {
		return true;
	} else {
		return false;
	}
}
</script>	
<a href="<? echo site_url("main/faq") ?>"><img src="<? echo base_url() ?>application/views/front/files/question.png" style="float:right; margin-right: -150px;" /></a>
Фільтр по даті:
<input type="text" name="Period" form="export" class="DatePicker" style="font-size:14px; font-weight:bold; width: 150px;text-align: center;"/><p />
	
<div id="load" style="visibility:hidden;">
	<!--<div class="load_background">-->
		<img src="<? echo base_url() ?>application/views/front/files/ajax-loader.gif" class="ajax-loader"/>
	<!--</div>-->	
</div>	

<?php
if($status) {
// повідомлення в залежності від статусів
if($status->flag == 0) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification error">
		<div><b>План повернено на доопрацювання, причина: </b>'.$status->comment.'</div>
		</div>';
}
if($status->flag == 1) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification success">
		<div><b>План на затвердженні в керівника ! </b></div>
		</div>';
}
if($status->flag == 2) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification success">
		<div><b>План затверджено </b></div>
		</div>';
}
if($status->flag == 3) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification success">
		<div><b>Звіт на затвердженні в керівника ! </b></div>
		</div>';
}
if($status->flag == 4) {
	echo '<link rel="stylesheet" type="text/css" href="'.base_url().'application/views/admin/css/message.css">
		<div class="notification success">
		<div><b>Факт затверджено ! </b></div>
		</div>';
}
}
?>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5">Ред</th>
			<th width="17">Страт. плани</th>
			<th width="100">Назва завдання</th>
			<th width="200">Результат тижня</th>
			<th width="50">Дата початку завдання</th>
			<th width="50">Запл. дата заверш.</th>
			<th width="50">Запл. час на викон.</th>
			<th width="50">Фактична дата заверш.</th>
			<th width="50">Факт. затр. час</th>
			<th width="5">Статус</th>
		</tr>
    </thead>
	<?php $this->load->view('front/ajax_pl'); ?>
</tbody>	
</table>	
	<div style="height: 30px;margin: 20px 20px 0px 20px;">
		<form action="<? echo base_url() ?>export_excel/1.php" method="POST" id="export" style="float:left;">
			<input type="button" class="but orange" id="but_1" onclick="location.href='<?php echo site_url("main/create_zavd_plan") ?>'" value="Створити завдання">
			<input type="button" class="but orange" id="but_3" onclick="zatverduty()" value="Відправити на затвердження" />

			<input type="hidden" name="user_id" value="<?php echo $this->session->userdata('user_id');?>" />
			<input type="hidden" name="period" value="<?php echo $this->session->userdata('next_date_begin');?>" />
			<input type="submit" name="exportSubmit" class="but orange" value="Експорт в Excel" style="float:none;"/>
		</form>
	</div>

	</div>
<?php $this->load->view('front/footer'); ?>