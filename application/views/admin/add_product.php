<?php $this->load->view('admin/header'); ?>


  <script type="text/javascript" src="<? echo base_url() ?>application/views/files/jquery-1.5.min.js"></script>
		
<script type="text/javascript">
$(document).ready(function(){
	$('input[name=cena_2]').hide();
	$('input[name=vaga_2]').hide();	
	$('input[name=dop_svoystvo_2]').hide();
	$('#cena_2').hide();
	$('#vaga_2').hide();	
	$('#dop_svoystvo_2').hide();

$('#one').click(function(){
	//$('input[name=dop_svoystvo_2]').attr('disabled','disabled');
	//$("#sel2").removeAttr('disabled'); 	
	$('input[name=cena_2]').hide().val('');
	$('input[name=vaga_2]').hide().val('');
	$('input[name=dop_svoystvo_2]').hide().val('');
	$('#cena_2').hide();
	$('#vaga_2').hide();	
	$('#dop_svoystvo_2').hide()
});
$('#two').click(function(){
	$('input[name=cena_2]').show();
	$('input[name=vaga_2]').show();
	$('input[name=dop_svoystvo_2]').show();
	$('#cena_2').show();
	$('#vaga_2').show();
	$('#dop_svoystvo_2').show();
});


$('#add_dop_sv_1').click(function(){
	$('input[name=dop_svoystvo_1]').val("<div class='diametr'><span>Диаметр (см):</span><div class='d-num'>35</div></div>");
});

$('#add_dop_sv_2').click(function(){
	$('input[name=dop_svoystvo_2]').val("<div class='diametr'><span>Диаметр (см):</span><div class='d-num'>35</div></div>");
});

$("#send").click(function(){

if($("#two:checked").val()) {
	if($('input[name=name]').val()!='' && $('input[name=cena_2]').val()!='' && $('input[name=vaga_2]').val()!='' && $('input[name=cena_1]').val()!='' && $('input[name=vaga_1]').val()!='') {
		$('#modalform').trigger('submit');	// підтвердження форми
		//return false;
	} else {
		alert('Заполните все синие и красные поля !');
}	

} else {
	if($('input[name=name]').val()!='' && $('input[name=cena_1]').val()!='' && $('input[name=vaga_1]').val()!='') {
		$('#modalform').trigger('submit');	// підтвердження форми
		//return false;
	} else {
		alert('Заполните все синие поля !');
}

}

});



});

</script>
	
	  <td id="zcenter">
		<span id="dle-info"></span>

		<div id="dle-content">
			<h3>Новый товар <?php echo $id_category; ?></h3>
			
			<div id="example" class="flora">
<!-- Main content -->
			
			<table id="zcontent" border="0" cellpadding="10" cellspacing="10" width="90%">
			<tbody>
<?php 
$attributes = array('id' => 'modalform');
echo form_open('admin/add_product_true', $attributes);
?>
			<tr>
				<td colspan="2" align="center"><font color="#ff0000"></font></td>
			</tr>
			<tr>
				<td><span style="color:#00F">Название</span></td>
				<td><input class="inptext" size="30" name="name" type="text" value=""></td>
			</tr>
			<tr>
				<td>Описание</td>
				<td><textarea name="descript" cols="55" rows="5"></textarea></td>
			</tr>
			<tr>
				<td>Состав</td>
				<td><textarea name="sostav" cols="55" rows="5"></textarea></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>В товаре:&nbsp;&nbsp;
					<label><input type="radio" name="radio" id="one"  checked="checked" /> Одно свойство</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type="radio" name="radio" id="two" /> Два свойства</label>
				</td>
			</tr>
			<tr>
				<td><span style="color:#00F">Цена</span></td>
				<td><input class="inptext" size="30" name="cena_1" type="text" value=""></td>
			</tr>
			<tr>
				<td><span id="cena_2" style="color:#F00">Цена 2</span></td>
				<td><input class="inptext" size="30" name="cena_2" type="text" value=""></td>
			</tr>
			<tr>
				<td><span style="color:#00F">Вес</span></td>
				<td><input class="inptext" size="30" name="vaga_1" type="text" value=""></td>
			</tr>
			<tr>
				<td><span id="vaga_2" style="color:#F00">Вес 2</span></td>
				<td><input class="inptext" size="30" name="vaga_2" type="text" value=""></td>
			</tr>
			<tr>
				<td>Дополнительное свойство</td>
				<td><input class="inptext" size="30" name="dop_svoystvo_1" type="text" value="">&nbsp;<a id="add_dop_sv_1">ADD</a></td>
			</tr>
			<tr>
				<td>Дополнительное свойство 2</td>
				<td><input class="inptext" size="30" name="dop_svoystvo_2" type="text" value=""></td>
			</tr>
			<tr>
				<td>Картинка</td>
				<td><input class="inptext" size="30" name="images" type="text" value=""></td>
			</tr>
			<tr>
				<td>Большая картинка</td>
				<td><input class="inptext" size="30" name="big_images" type="text" value=""></td>
			</tr>
			<tr>
				<td>Категория</td>
				<td>
					<select size="1" name="id_category" class="inptext_drop">
					<?php
					for($i=0; $i<count($category); $i++) {
						if($category[$i]['id']==$id_category) {
							echo '<option selected="selected" value="'.$category[$i]['id'].'">'.$category[$i]['nazva'].'</option>';
						} else { 
							echo '<option value="'.$category[$i]['id'].'">'.$category[$i]['nazva'].'</option>';
						}
					}
					?>					
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" id="send" value="Добавить" /></td>
			</tr>
			</form>
		
			</tbody>
			</table>
		

<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>