<?php $this->load->view('admin/header'); ?>
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
<div id="container">
	<div style="border-bottom: solid 1px #D0D0D0;">
		<div id="zagolovok">Статуси планів</div>
		<?php $this->load->view('admin/menu'); ?>
	</div><br/>
	<div id="body">
		<h3><? echo $user_data->name ?></h3>

<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
<script>
$(document).ready(function() {
	$('.status').click(function(){
		$('input[name=period]').val($(this).attr('date'));
		$('#change_data').trigger('submit');	// підтвердження форми
	});
});
</script>


<form action="<? echo site_url("admin/show_user_plan_na_zatverd_date"); ?>" id="change_data" method="post" >
<input type="hidden" name="period" />
<input type="hidden" name="user_id" value="<? echo $user_data->id; ?>" />
</form>

	
<?php
	foreach($status->result() as $row) {
		if($row->flag == 0) { $text = '<span style="color:#03F;">Повернений на доопрацювання</span>'; }
		if($row->flag == 1) { $text = '<span style="color:#FF0000;">Поданий на затвердження (план)</span>'; }
		if($row->flag == 2) { $text = '<span style="color:#F90;">Затверджений план</span>'; }
		if($row->flag == 3) { $text = '<span style="color:#FF0000;">Поданий на затвердження (факт)</span>'; }
		if($row->flag == 4) { $text = '<span style="color:#009900;">Затверджений факт</span>'; }
		echo '<a href="#" class="status" date="'.$row->begin.'">'.date('d.m.Y', strtotime($row->begin)).' - '.date('d.m.Y', strtotime($row->end)).'</a> - '.$text.'<br>';
	}
?>			
			


	</div>
    <div style="padding-left:20px; margin-top:25px;"><input type="button" class="but orange" onclick="location.href='<? echo site_url("admin/plans") ?>'" value="Повернутись"></div>
<?php $this->load->view('admin/footer'); ?>