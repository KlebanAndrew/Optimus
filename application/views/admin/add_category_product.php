<?php $this->load->view('admin/header'); ?>


  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/black-tie/jquery-ui.css" type="text/css" />
  <script type="text/javascript" src="<? echo base_url() ?>application/views/files/jquery-1.5.min.js"></script>
  <script type="text/javascript" src="<? echo base_url() ?>application/views/files/jquery-ui-1.8.5.custom.min.js"></script>
		
<script type="text/javascript">
$(document).ready(function(){
$('img').click(function(){
	$('input[name=images]').val($(this).attr('title'));
	$('img').attr('border', '0px');
	$(this).attr('border', '3px');
});

$("#add").click(function(){
	if($('input[name=nazva]').val()!='' && $('input[name=images]').val()!='') {
		$('#modalform').trigger('submit');	// підтвердження форми
	} else {
		alert('Заполните название категории и выберете картинку внизу !');
}
});

	
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
			<h3>Новая категория продуктов</h3>
			
			<div id="example" class="flora">
<!-- Main content -->
			
			<table id="zcontent" border="0" cellpadding="10" cellspacing="10" width="90%">
			<tbody>
<?php 
$attributes = array('id' => 'modalform');
echo form_open('admin/add_category_product_true', $attributes);
?>
			<tr>
				<td colspan="2" align="center">&nbsp;<font color="#ff0000"><?php echo $title; ?></font></td>
			</tr>
			<tr>
				<td>Название</td>
				<td><input class="inptext" size="30" name="nazva" type="text" value=""></td>
			</tr>
			<tr>
				<td>Рисунок</td>
				<td><input class="inptext" size="30" name="images" type="text" value="" readonly="readonly"></td>
			</tr>
			<tr>
				<td><input value="Добавить" id="add" type="button"></td>
			</tr>
			</form>
		
			</tbody>
			</table>
			
<h3>Галерея изображений категорий (<a href="<? echo site_url("admin/upload_images") ?>" class="dialog_link">загрузить</a>) - оптимальный размер 160 x 160px</h3>			
<table width="100%" border="0" cellspacing="0" cellpadding="0"  align="center">
	<tr>

	<p>&nbsp;</p>


<?php
//Повертає список файлів в папці
$files = scandir(realpath(APPPATH."../images/category"));
//print_r($files);

$a=0;
//перебір всіх малюнків в папці
for ($i=1; $i<=count($files)-1; $i++) {
	if ($i==1) {
		continue;
	}
	$a++;
	echo "<td align='center'>
    <img src='".base_url()."images/category/".$files[$i]."' width='150' height='150' border='0' title='".$files[$i]."' /><br />
	<div style='font-family:Tahoma; font-size:9px'>".$files[$i]."</div>
    <br />
    <a href='".site_url("admin/del_img_from_category/".$files[$i])."' title='Видалити малюнок'>
    <img src='".base_url()."application/views/admin/files/publish0.png' border='0' /></a><br /><br />
	</td>";
	if ($a==4) { $a=0;
		echo "</tr>";
	}
}


?>
	</tr>
</table>



<!-- ui-dialog -->
<div id="dialog" title="Загрузка картинки">
<p>&nbsp;</p> 
<?php 
$attributes = array('id' => 'upload');
echo form_open_multipart('admin/do_upload_into_category', $attributes);?>
<input type="file" name="userfile" size="20" />
</form>
</div>
<!-- ui-dialog -->		
		

<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>