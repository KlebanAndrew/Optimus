<?php $this->load->view('admin/header'); ?>



  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/black-tie/jquery-ui.css" type="text/css" />
  <script type="text/javascript" src="<? echo base_url() ?>application/views/files/jquery-1.5.min.js"></script>
  <script type="text/javascript" src="<? echo base_url() ?>application/views/files/jquery-ui-1.8.5.custom.min.js"></script>

		
		
<script type="text/javascript">
$(document).ready(function(){

$('#dialog').dialog({
	autoOpen: false,
	width: 300,
	modal: true,
	buttons: {
		"Подтвердить": function() { 
			$('#upload').trigger('submit');	// підтвердження форми
			return false;
		}, 
		"Закрыть": function() { 
			$(this).dialog("close"); 
		} 
	}
});

$('.dialog_link').click(function(){
	$('#dialog').dialog('open');
	return false;
});


});

</script>
		
	  <td id="zcenter">
		<span id="dle-info"></span>

		<div id="dle-content">
			<h3>Слайдер (<a href="<? echo site_url("admin/upload_images") ?>" class="dialog_link">загрузить</a>)</h3>
			Максимум 4, оптимальный размер - 945 x 340px
			
			
			<div id="example" class="flora">
<!-- Main content -->
			

<table width="100%" border="0" cellspacing="0" cellpadding="0"  align="center">
	


	<p>&nbsp;</p>


<?php
//Повертає список файлів в папці
$files = scandir(realpath(APPPATH."../images/slide"));
//print_r($files);

$a=0;
//перебір всіх малюнків в папці
for ($i=1; $i<=count($files)-1; $i++) {
	if ($i==1) {
		continue;
	}
	$a++;
	echo "<tr><td align='center'>
    <a href='".base_url()."images/slide/".$files[$i]."'>
	<img src='".base_url()."images/slide/".$files[$i]."' width='600px' border='0' /><br />
	<div style='font-family:Tahoma; font-size:9px'>".$files[$i]."</div></a>
    <br />
    <a href='".site_url("admin/del_img_from_slide/".$files[$i])."' title='Видалити малюнок'>
    <img src='".base_url()."application/views/admin/files/publish0.png' border='0' /></a><br /><br />
	</td></tr>";
	}


?>

</table>



<!-- ui-dialog -->
<div id="dialog" title="Загрузка картинки">
<p>&nbsp;</p> 
<?php 
$attributes = array('id' => 'upload');
echo form_open_multipart('admin/do_upload_into_slide', $attributes);?>
<input type="file" name="userfile" size="20" />
</form>
</div>
<!-- ui-dialog -->		


					
			
<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>