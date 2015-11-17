<?php $this->load->view('admin/header'); ?>

	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	<link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip_index.css" rel="stylesheet" />
    <script src="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip.js"></script>
		
<script>
$(function() {
     //активний елемент меню
    $('#menu ul li').removeAttr("id");
    $('#menu ul li:nth-child(3)').attr("id","active");

	var wurl="<? echo site_url("main/ajax_add_to_richniy_plan") ?>";

    $('.tooltiper a').tooltip({
        track: false,
        delay: 0,
        showURL: false,
        fade: 200
    });

	$(".datepicker").datepicker({
		//dateFormat: "yy-mm-dd",
		dateFormat: "dd.mm.yy",
		changeYear: true,
		yearRange: '-1:+1',
		beforeShow: function(input, inst) {
			$(".ui-datepicker").css("font-size", "0.9em");
		},
		showOtherMonths: true,
		selectOtherMonths: true,
		firstDay: 0
	}
    );

	
// Dialog2
	$('#dialog2').dialog({
		autoOpen: false,
		width: 600,
		buttons: {
			"Зберегти": function() {
				if($.trim($('input[name=nazva]').val()) != '' & $('input[name=plan_vid]').val() != '' & $('input[name=plan_do]').val() != '') {
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
							"plan_do":$('input[name=plan_do]').val(),
                            "vlasnyk": <?echo $vlasnyk;?>
						},
						dataType: "text",
						success: function(msg){
							window.location.reload();
						}
					});
				} else {
					alert('Заповніть назву роботи, дати початку і кінця !');
				}
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
		wurl="<? echo site_url("main/ajax_add_to_richniy_plan") ?>";
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
		wurl="<? echo site_url("main/ajax_edit_richniy_plan") ?>"+'/'+$(this).attr("id_zap");
		$('#dialog2').dialog('open');
		$("input[name='detalize']").removeAttr("checked");
		if($(this).attr("detalize") == "0") $("input[name='detalize']").first().attr("checked", "checked");
		if($(this).attr("detalize") == "1") $("input[name='detalize']").last().attr("checked", "checked");
		return false;
	});
	
	
	
	$('#filtr_pidlegli').click(function(){
		$("#users").empty();
		$.ajax({
			type: "POST",
			url: "<? echo site_url("main/ajax_select_pidlegli") ?>",
			data: { "user":'none' },
			dataType: "text",
			success: function(msg){
				$("#users").append(msg);
			}
		});
	});
	
	$('#filtr_all').click(function(){
		$("#users").empty();
		$.ajax({
			type: "POST",
			url: "<? echo site_url("main/ajax_select_pidlegli") ?>",
			data: { "user":'all' },
			dataType: "text",
			success: function(msg){
				$("#users").append(msg);
			}
		});
	});


    //функція вибору працівників зі multiple select без зажимання кнопки ctrl
    $("select").mousedown(function(e){
        e.preventDefault();

        var scroll = this.scrollTop;

        e.target.selected = !e.target.selected;

        this.scrollTop = scroll;

        $(this).focus();
    }).mousemove(function(e){e.preventDefault()});
});

function del_zavd(e) {
	if (confirm("Видалити себе з цього завдання ?")) {
		e = e || window.event;
		var id_zavd = $(e).parent().siblings().find('a').attr('id_zap');
		$.ajax({
			type: "POST",
			url: "<? echo site_url("main/ajax_del_from_richniy_plan") ?>",
			data: { "id_zavd":id_zavd },
			dataType: "text",
			success: function(msg){
				//alert(msg);
				window.location.reload(0);
			}
		});
	} else {
		return false;
	}
}
</script>

<div id="container">
	<h1>Річний план</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/settings").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">Вихід</a></div>
	<div id="body">

<a href="<? echo site_url("main/year_plan") ?>">З моєю участю</a>&nbsp;&nbsp;&nbsp;
<a href="<? echo site_url("main/year_plan_all") ?>">Всі</a><p />
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
						<td align="center">'.$a++.'</td>';
			if($flag == 'my' and $row->vlasnyk == $vlasnyk) {//and $row->vlasnyk == $vlasnyk додаткова умова для можливості редагування планів тільки власниками

				echo   '<td><a href="#" class="dialog_link" usera="'.$row->users.'" id_zap="'.$row->id.'" detalize="'.$row->detalize.'" title="'.$title.'">'.$row->nazva.'</a></td>';
			} else {

				echo   '<td>'.$row->nazva.'</td>';
			} echo '
					<td align="center" >'.$row->nom_proces.'</td><td class="tooltiper">';
					echo $this->model_admin->show_users_z_massiva2($row->users);
					echo '</td>
					<td align="center">'.date('d.m.Y', strtotime($row->plan_vid)).'</td>
					<td align="center">'.date('d.m.Y', strtotime($row->plan_do)).'</td>
					</tr>';
				}
			}
            if($flag == "my"){
            echo '<tr>
                        <td colspan="6" style="background-color: rgba(135, 131, 130, 0.26);"></td>
                    </tr>';
            }
            //Блок виведення на екран рычних планыв за атрибутом vlasnyk
            if(isset($plan_vlasnyk)) {
                foreach ($plan_vlasnyk as $row) {
                    //перевірка дублювання річних планів у двох списках(є працівником і власником || є тільки власником в плані)
                    foreach ($plan->result() as $temp){
                        if($temp->id == $row['id']){ continue(2);}//continue(2) вихід як з ітерації перевірки входжень,так і з ітерації поточного річного плану
                    }
                    ////////////////////////////////////
                    if($row['detalize'] == 1) { $title="З детілізацією"; } else { $title="Без детілізації"; }
                    echo
                        '<tr class="row0">
                            <td align="center">'.$a++.'</td>';
                    if($flag == 'my' and $row['vlasnyk'] == $vlasnyk) {//and $row->vlasnyk == $vlasnyk додаткова умова для можливості редагування планів тільки власниками

                        echo   '<td><a href="#" class="dialog_link" usera="'.$row['users'].'" id_zap="'.$row['id'].'" detalize="'.$row['detalize'].'" title="'.$title.'">'.$row['nazva'].'</a></td>';
                    } else {

                        echo   '<td>'.$row['users'].'</td>';
                    } echo '
					<td align="center">'.$row['nom_proces'].'</td><td>';
                    echo $this->model_admin->show_users_z_massiva2($row['users']);
                    echo '</td>
					<td align="center">'.date('d.m.Y', strtotime($row['plan_vid'])).'</td>
					<td align="center">'.date('d.m.Y', strtotime($row['plan_do'])).'</td>
					</tr>';
                }
            }
            /////////////////////////////////////////////////////////////////
			?>
			</tbody>	
		</table>	
<? if($flag == 'my') { ?>
		<div style="margin: 20px 0px;overflow:auto;">
			<input type="button" class="but orange" id="dialog_link2" value="Створити роботу">
		</div>
<? } ?>
		<!-- ui-dialog -->
		<div id="dialog2" title="Створити роботу">
			<br />
			<table width="500" border="0">
				<tr>
				  <td width="200">Назва роботи:</td>
				  <td width="300" colspan="2"><input type="text" name="nazva" style="width:280px"></td>
				</tr>
				<tr>
				  <td>№ процесу 1, 2, 3, 4 рівнів:</td>
				  <td colspan="2"><input type="text" name="nom_proces"></td>
				</tr>
				<tr>
				  <td>Задіяні працівники:</td>
				  <td>
					<select name="users" id="users"  size="10" multiple>
						<?php
						foreach ($users->result() as $user) { echo "<option value='".$user->id."'>".$user->name."</option>"; }
						?>
					</select></td>
				  <td><p><a href="#" id="filtr_pidlegli">Свої підлеглі</a></p>
					  <p><a href="#" id="filtr_all">Всі працівники</a></p></td>
				</tr>
				<!--<tr>
				  <td><br />Механізм деталізації:</td>
				  <td colspan="2"><br />
					<label><input type="radio" name="detalize" value="0" />Завдання без деталізації</label><br />
					<label><input type="radio" name="detalize" value="1" />Завдання з відомими етапами</label>
				  </td>
				</tr>-->
				<tr>
				  <td>Початок:</td>
				  <td colspan="2"><input type="text" name="plan_vid" class="datepicker" value="" /></td>
				</tr>
				<tr>
				  <td>Кінець:</td>
				  <td colspan="2"><input type="text" name="plan_do" class="datepicker" value="" /></td>
				</tr>
			</table>
		</div>
		<!-- ui-dialog -->


	</div>

<?php $this->load->view('admin/footer'); ?>