<?php $this->load->view('admin/header'); ?>



  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/black-tie/jquery-ui.css" type="text/css" />
  <script type="text/javascript" src="<? echo base_url() ?>application/views/files/jquery-1.5.min.js"></script>
  <script type="text/javascript" src="<? echo base_url() ?>application/views/files/jquery-ui-1.8.5.custom.min.js"></script>

		
		
<script type="text/javascript">
$(document).ready(function(){

$('#add_dop_sv_1').click(function(){
	$('input[name=dop_svoystvo_1]').val("<div class='diametr'><span>Диаметр (см):</span><div class='d-num'>35</div></div>");
});

$('#add_dop_sv_2').click(function(){
	$('input[name=dop_svoystvo_2]').val("<div class='diametr'><span>Диаметр (см):</span><div class='d-num'>35</div></div>");
});


$('#primenit').click(function(){
	tt=$("#modalform").attr("action", "<? echo site_url("admin/save_product") ?>");
	$('#modalform').trigger('submit');	// підтвердження форми
});

$('#save').click(function(){
	tt=$("#modalform").attr("action", "<? echo site_url("admin/update_product") ?>");
	$('#modalform').trigger('submit');	// підтвердження форми
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

$('#dialog_2').dialog({
	autoOpen: false,
	width: 300,
	modal: true,
	buttons: {
		"Подтвердить": function() { 
			$('#upload_2').trigger('submit');	// підтвердження форми
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

$('.dialog_link_2').click(function(){
	$('#dialog_2').dialog('open');
	return false;
});

});

</script>



		
	  <td id="zcenter">
		<span id="dle-info"></span>

		<div id="dle-content">
			<h3>Товар №<?php echo $main[0]['id'] ?></h3>
			
			<div id="example" class="flora">
<!-- Main content -->
			
			<table id="zcontent" border="0" cellpadding="10" cellspacing="10" width="90%">
			<tbody>
<?php
$attributes = array('id' => 'modalform');
echo form_open('admin/save_product', $attributes);
?>
			<tr>
				<td colspan="2" align="center"><font color="#ff0000"></font></td>
			</tr>
			<tr>
				<td>Название</td>
				<td><input class="inptext" size="30" name="name" type="text" value="<?php echo $main[0]['name'] ?>"></td>
			</tr>
			<tr>
				<td>Описание</td>
				<td><textarea name="descript" cols="55" rows="5"><?php echo $main[0]['descript'] ?></textarea></td>
			</tr>
			<tr>
				<td>Состав</td>
				<td><textarea name="sostav" cols="55" rows="5"><?php echo $main[0]['sostav'] ?></textarea></td>
			</tr>
			<tr>
				<td>Цена</td>
				<td><input class="inptext" size="30" name="cena_1" type="text" value="<?php echo $main[0]['cena_1'] ?>"></td>
			</tr>
<?php
if($main[0]['cena_2']!='0') {
echo '			
			<tr>
				<td><span style="color:#F00">Цена 2</span></td>
				<td><input class="inptext" size="30" name="cena_2" type="text" value="'.$main[0]['cena_2'].'"></td>
			</tr>'; } ?>
			<tr>
				<td>Вес</td>
				<td><input class="inptext" size="30" name="vaga_1" type="text" value="<?php echo $main[0]['vaga_1'] ?>"></td>
			</tr>
<?php
if($main[0]['cena_2']!='0') {
echo '	
			<tr>
				<td><span style="color:#F00">Вес 2</span></td>
				<td><input class="inptext" size="30" name="vaga_2" type="text" value="'.$main[0]['vaga_2'].'"></td>
			</tr>'; } ?>
			<tr>
				<td>Дополнительное свойство</td>
				<td><input class="inptext" size="30" name="dop_svoystvo_1" type="text" value="<?php echo $main[0]['dop_svoystvo_1'] ?>">&nbsp;<a id="add_dop_sv_1">ADD</a></td>
			</tr>
<?php
if($main[0]['cena_2']!='0') {
echo '	
			<tr>
				<td><span style="color:#F00">Дополнительное свойство 2</span></td>
				<td><input class="inptext" size="30" name="dop_svoystvo_2" type="text" value="'.$main[0]['dop_svoystvo_2'].'">&nbsp;<a id="add_dop_sv_2">ADD</a></td>
			</tr>'; } ?>
			<tr>
				<td>Картинка (<a href="<? echo site_url("admin/upload_images") ?>" class="dialog_link">загрузить</a>)</td>
				<td><input class="inptext" size="30" name="images" type="text" value="<?php echo $main[0]['images'] ?>"></td>
			</tr>
			<tr>
				<td>Большая картинка (<a href="<? echo site_url("admin/upload_images") ?>" class="dialog_link_2">загр.</a>)</td>
				<td><input class="inptext" size="30" name="big_images" type="text" value="<?php echo $main[0]['big_images'] ?>"></td>
			</tr>
			<tr>
				<td>Категория</td>
				<td>
					<select size="1" name="id_category" class="inptext_drop">
					<?php
					for($i=0; $i<count($category); $i++) {
					if($main[0]['id_category']==$category[$i]['id']) {
						echo "<option selected='selected' value='".$category[$i]['id']."'>".$category[$i]['nazva']."</option>";
					} else { 
						echo "<option value='".$category[$i]['id']."'>".$category[$i]['nazva']."</option>";
					}
					}

					?>					
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="hidden" name="id" value="<?php echo $main[0]['id'] ?>" /></td>
				<td><input value="Применить" type="button" id="primenit">&nbsp;&nbsp;&nbsp;&nbsp;<input value="Сохранить" type="button" id="save"></td>
			</tr>
			</form>
		
			</tbody>
			</table>
		

<!-- ui-dialog -->
<div id="dialog" title="Загрузка картинки">
<p>&nbsp;</p> 
<?php 
$attributes = array('id' => 'upload');
echo form_open_multipart('admin/do_upload', $attributes);?>
<input type="file" name="userfile" size="20" />
<input type="hidden" name="id_zapisi" value="<?php echo $main[0]['id'] ?>" />
<input type="hidden" name="small_or_big" value="small" />
</form>
</div>
<!-- ui-dialog -->		


<!-- ui-dialog 2 -->
<div id="dialog_2" title="Загрузка большой картинки">
<p>&nbsp;</p> 
<?php 
$attributes = array('id' => 'upload_2');
echo form_open_multipart('admin/do_upload', $attributes);?>
<input type="file" name="userfile" size="20" />
<input type="hidden" name="id_zapisi" value="<?php echo $main[0]['id'] ?>" />
<input type="hidden" name="small_or_big" value="big" />
</form>
</div>
<!-- ui-dialog 2 -->
		

<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>