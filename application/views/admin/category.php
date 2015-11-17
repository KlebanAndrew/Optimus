<?php $this->load->view('admin/header'); ?>
		
	  <td id="zcenter">
		<span id="dle-info"></span>

		<div id="dle-content">

			
			<div id="example" class="flora">
<!-- Main content -->


<table width='100%' border='0' bgcolor="#CCCCCC">
		<tr>
			<td width='8%' align='center'>&nbsp;</td>
			<td width="59%">
		    <b>Категория продуктов</b>
			
			
			<?php
			echo
			form_open('admin/edit_category')."
				<input type='text' name='new_name' value='".$category[0]['nazva']."' size='45' /> Порядок
				<input type='text' name='sort' value='".$category[0]['sort']."' size='3' />
				<input type='hidden' name='id_category' value='".$category[0]['id']."' /><br /><br />";
				if($category[0]['public'] == 1) {
					echo "<label><input type='checkbox' name='active' checked='checked' />";
				} else {
					echo "<label><input type='checkbox' name='active' />";
				}
				echo "&nbsp;Активная</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='submit' value='Сохранить' />
			</form>";
			?>
			<br />
			(<?php echo "<strong><a href='".site_url("admin/add_product/".$category[0]['id'])."'>Добавить продукт</a></strong>"; ?>)
			
			
			</td>
			<td width='26%' align='center'><img src="<?php echo base_url().'images/category/'.$category[0]['images']; ?>" alt="" /></td>
			<td width='7%' align='center'><a href="<?php echo site_url("admin/del_category/".$category[0]['id']); ?>" title="Удалить категорию"><img src="<?php echo base_url().'application/views/admin/files/delete3.png'; ?>" /></a></td>
		</tr>
</table>
<p>&nbsp;</p>
			

<?php			
echo "<table width='100%' border='1' cellpadding='0' cellspacing='0'>
		<tr>
			<td width='2%' align='center'><b>id</b></td>
			<td width='10%' align='center'><b>Название</b></td>
			<td width='30%' align='center'><b>Описание</b></td>
			<td width='5%' align='center'><b>Цена</b></td>
			<td width='5%' align='center'><b>Вес</b></td>
			<td width='20%' align='center'><b>Картинка</b></td>
			<td width='2%' align='center'><b>Удалить</b></td>
		</tr>";

for($i=0; $i<count($produkts); $i++) {		
	echo "<tr class='tablici'>
		<td align='center'><a href='".site_url("admin/product/".$produkts[$i]['id'])."'>".$produkts[$i]['id']."</a></td>
		<td>&nbsp;<a href='".site_url("admin/product/".$produkts[$i]['id'])."'>".$produkts[$i]['name']."</a>&nbsp;</td>
		<td>&nbsp;".$produkts[$i]['descript']."</td>
		<td align='center'>".$produkts[$i]['cena_1']."&nbsp;</td>
		<td align='center'>".$produkts[$i]['vaga_1']."&nbsp;</td>
		<td align='center'><img border='0' src='".base_url()."images/produkt/".$produkts[$i]['images']."' width='100px' /></td>
		<td align='center'><a href='".site_url("admin/del_product/".$produkts[$i]['id'])."' title='Удалить продукт'><img border='0' src='".base_url()."application/views/admin/files/delete3.png' /></a></td>	
		</td>
	</tr>";
}
echo "</table>";
			

?>			
					
			
<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>