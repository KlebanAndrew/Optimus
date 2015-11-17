<?php $this->load->view('admin/header'); ?>

	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	<link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />

		
<script>
$(function() {
	var wurl="<? echo site_url("admin/ajax_add_to_richniy_plan") ?>";
	$( ".datepicker" ).datepicker();
	$( "#format" ).change(function() {
	$( ".datepicker" ).datepicker( $.datepicker.regional[ "uk" ] );
		$( ".datepicker" ).datepicker( "option", "dateFormat", $( this ).val() );
	});
		
// Dialog2
	$('#dialog2').dialog({
		autoOpen: false,
		width: 600,
		buttons: {
			"Зберегти": function() {
				$(this).dialog("close");
				$.ajax({
					type: "POST",
					url: wurl,
					data: { 
						"nazva":$('input[name=nazva]').val(),
						"nom_proces":$('input[name=nom_proces]').val(),
						"users":$('#users').val(),
						"detalize":$("input:checked").val(),
						"plan_vid":$('input[name=plan_vid]').val(),
						"plan_do":$('input[name=plan_do]').val()
					},
					dataType: "text",
					success: function(msg){
						window.location.reload();
					}
				});
			},
			"Закрити": function() {
				$(this).dialog("close");
			}
		}
	});

// Dialog Link Add
	$('#dialog_link2').click(function(){
		$('input[name=nazva]').val('');
		$('input[name=nom_proces]').val('');
		$('#users option:selected').each(function(){
			this.selected=false;			// робимо всі елементи невибрані
		});			
		wurl="<? echo site_url("admin/ajax_add_to_richniy_plan") ?>";
		$('#dialog2').dialog('open');
		return false;
	});	
// Dialog Link Edit		
	$('.dialog_link').click(function(){
		$('#users option:selected').each(function(){
			this.selected=false;			// робимо всі елементи невибрані
		});
		arr = $(this).attr("usera").split(',');
		for(var i=0; i<arr.length; i++) {
			$("#users option[value='"+arr[i]+"']").attr("selected", "selected");
		}
		$('input[name=nazva]').val($(this).text());
		$('input[name=nom_proces]').val($(this).parent().next('td').text());
		$('input[name=plan_vid]').val($(this).parent().next('td').next('td').next('td').text());
		$('input[name=plan_do]').val($(this).parent().next('td').next('td').next('td').next('td').text());
		wurl="<? echo site_url("admin/ajax_edit_richniy_plan") ?>"+'/'+$(this).attr("id_zap");
		$('#dialog2').dialog('open');
		$("input[name='detalize']").removeAttr("checked");
		if($(this).attr("detalize") == "0") $("input[name='detalize']").first().attr("checked", "checked");
		if($(this).attr("detalize") == "1") $("input[name='detalize']").last().attr("checked", "checked");
		return false;
	});	
});
</script>

<div id="container">
	<div style="border-bottom: solid 1px #D0D0D0;">
		<div id="zagolovok">Річний план</div>
		<?php $this->load->view('admin/menu'); ?>
	</div><br/>
	<div id="body">

	
		<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5">№</th>
					<th width="100">Назва роботи</th>
					<th width="5">№ процесу 1, 2, 3, 4 рівнів</th>
					<th width="20">ПІБ працівника</th>
					<th width="2">Початок</th>
					<th width="2">Кінець</th>
				</tr>
			</thead>
			<tfoot>
			  <tr>
				  <td colspan="6">&nbsp;</td>
			  </tr>
			</tfoot>

			<tbody>
			
			<?php
			$a=1;
			if($plan->result()) {
				foreach ($plan->result() as $row) {
					if($row->detalize == 1) { $title="З детілізацією"; } else { $title="Без детілізації"; }
					echo 
					'<tr class="row0">
						<td align="center">'.$a++.'</td>
						<td><a href="#" class="dialog_link" usera="'.$row->users.'" id_zap="'.$row->id.'" detalize="'.$row->detalize.'" title="'.$title.'">'.$row->nazva.'</a></td>
						<td>'.$row->nom_proces.'</td><td>';
					echo $this->model_admin->show_users_z_massiva2($row->users);
					echo '</td>
					<td align="center">'.$row->plan_vid.'</td>
					<td align="center">'.$row->plan_do.'</td>
					</tr>';
				}
			}
			?>		
			
			</tbody>	
		</table>	

		<div style="margin: 20px 0px;overflow:auto;">
			<input type="button" class="button" id="dialog_link2" value="Створити роботу">
		</div>

		<!-- ui-dialog -->
		<div id="dialog2" title="Створити роботу">
			<br />
			<table width="500" border="0">
				<tr>
				  <td width="200">Назва роботи:</td>
				  <td width="300"><input type="text" name="nazva" style="width:280px"></td>
				</tr>
				<tr>
				  <td>№ процесу 1, 2, 3, 4 рівнів:</td>
				  <td><input type="text" name="nom_proces"></td>
				</tr>
				<tr>
				  <td>Задіяні працівники:</td>
				  <td>
					<select name="users" id="users" size="10" multiple>
						<?php
						foreach ($users->result() as $user) { echo "<option value='".$user->id."'>".$user->name."</option>"; }
						?>
					</select>
				  </td>
				</tr>
				<tr>
				  <td><br />Механізм деталізації:</td>
				  <td><br />
					<label><input type="radio" name="detalize" value="0" />Завдання без деталізації</label><br />
					<label><input type="radio" name="detalize" value="1" />Завдання з відомими етапами</label>
				  </td>
				</tr>
				<tr>
				  <td>Початок:</td>
				  <td><input type="text" name="plan_vid" class="datepicker" value="" /></td>
				</tr>
				<tr>
				  <td>Кінець:</td>
				  <td><input type="text" name="plan_do" class="datepicker" value="" /></td>
				</tr>
			</table>
		</div>
		<!-- ui-dialog -->


	</div>

<?php $this->load->view('admin/footer'); ?>