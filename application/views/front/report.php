<?php $this->load->view('front/header'); ?>

	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	<link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip.css" rel="stylesheet" />
    <script src="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip.js"></script>

    <script>
        function first_sum(){ //функція початкової суми планових і фактичних годин
            var first_sum =0;
                $(".chas_fakt").each(function(){
                    var temp_sum = parseFloat($(this).val().replace(",", ".")) || 0;
                    first_sum = parseFloat(first_sum) + temp_sum;

                });
            $("#zag_chas_fakt").text(first_sum);
            first_sum =0;
                $(".chas").each(function(){
                    var temp_sum = parseFloat($(this).text().replace(",", ".")) || 0;
                    first_sum = parseFloat(first_sum) + temp_sum;

                });
            $("#zag_chas_plan").text(first_sum);
            $("#zag_chas_fakt").css('color','green');
        }


	$(function() {
        first_sum();
        $(".chas_fakt").keyup(function(){// функція динамічної зміни суми фактичних годин
            var sum =0;
            $(".chas_fakt").each(function(){
               var temp_sum = parseFloat($(this).val().replace(",", ".")) || 0;
                sum = parseFloat(sum) + temp_sum;

            });
            $("#zag_chas_fakt").text(sum);
            var t_s = parseFloat($("#zag_chas_plan").text().replace(",", ".")) || 0;
            if(sum>40){$("#zag_chas_fakt").css('color','red');}
            else if(sum>t_s){$("#zag_chas_fakt").css('color','#ffa500');}
            else{
                $("#zag_chas_fakt").css('color','green');
            }
            });

        $('.tooltiper a').tooltip({
            track: false,
            delay: 0,
            showURL: false,
            fade: 200
        });

        $(".chas_fakt").keypress(function (event) {
            if (event.which < 44
                || event.which > 57  || event.which ==47 || event.which ==45) {
                alert("Дозволено вводити тільки числа, наприклад 40 або 40.5");
                event.preventDefault();
            }
        });
		$( ".datepicker" ).datepicker({
			dateFormat: "dd.mm.yy",
			minDate: '<? echo date('d.m.Y', strtotime($d_v)) ?>',
			maxDate: '<? echo date('d.m.Y', strtotime($d_do))?>'
		});	
		$(".ui-datepicker").css("font-size", "0.9em");
		
		$('#send_zvit').click(function(){
			error = 0;
			$("form#myfrm :input").each(function(){
				$(this).css("background-color", "#FFF");
				if($(this).val() == '') {
					$(this).css("background-color", "red");
					error = 1;
				} 
			});
			if(error == 1) {	
				alert('Заповніть всі поля !');
			} else {	
				$('#myfrm').trigger('submit');	// підтвердження форми
			}
		});
		
		
		// Dialog
		$('#dialog').dialog({
			autoOpen: false,
			width: 420,
			buttons: {
				"Зберегти": function() {
					$(this).dialog("close");
					$.ajax({
						type: "POST",
						url: "<? echo site_url("ajax/save_prymitky") ?>",
						data: { "id_zavd":id_zavd, "is_planovi":is_planovi, "prymitka":$('#prymitka').val() },
						dataType: "text",
						success: function(msg){
							//alert(msg);
						}
					});
				},
				"Закрити": function() {
					$(this).dialog("close");
				}
			}
		});
		
		// Dialog Link
		$('.dialog_link').click(function(){
			id_zavd = $(this).attr('id_zavd');
			is_planovi = $(this).attr('is_planovi');
			$.ajax({
				type: "POST",
				url: "<? echo site_url("ajax/get_prymitky") ?>",
				data: { "id_zavd":id_zavd, "is_planovi":is_planovi },
				dataType: "text",
				success: function(msg){
					$('#prymitka').val(msg);
				}
			});
			$('#dialog').dialog('open');
			return false;
		});			

	});
	</script>

	

 <style>
.datepicker { width:75px; }
.chas_fakt { width:40px; }
form { display: block; }
 </style>


<div id="container">
	<h1>Звіт (<? echo @$title_dates; ?>)</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/edit_user").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">Вихід</a></div>
	<div id="body">

<form action="<?php echo site_url("main/reporting") ?>" method="POST" id="myfrm">
	

    <table class="adminlist" cellspacing="1">
        <thead>
        <tr>
            <th width="5">Ред</th>
            <th width="17">Стр. пл.</th>
            <th width="100">Назва завдання</th>
            <th width="200">Результат тижня</th>
            <th width="50">Дата початку завдання</th>
            <th width="50">Запл. дата заверш.</th>
            <th width="50">Запл. час на викон.</th>
            <th width="50">Фактична дата заверш.</th>
            <th width="50">Факт. затр. час</th>
            <th width="5">Завдання виконане</th>
            <th width="5">Прим.</th>
        </tr>
        </thead>
	
<?php
echo '<tfoot>
	  <tr>
		  <td colspan="6"><input type="hidden" id="title_dates" value="'.date('d.m.Y', strtotime($d_v)).' - '.date('d.m.Y', strtotime($d_do)).'" /></td>
		  <td colspan="1" id="zag_chas_plan" style="text-align: center; font-size:18px;"></td>
		  <td colspan="1"></td>
		  <td colspan="1" id="zag_chas_fakt" style="text-align: center; font-size:18px;"></td>
		  <td colspan="1"></td>
	  </tr>
    </tfoot>';	

if($planovi_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17">Планові</td>
	  </tr>
    </tfoot>';
	
	echo '<tbody>';	
	for($i=0; $i<count($planovi_zavd); $i++) {
		echo '
		<tr class="row0">
			<td align="center">&nbsp;</td>
			<td align="center">&nbsp;</td>
			<td><a href="'.site_url("main/detalize/".$planovi_zavd[$i]['id']).'">'.$planovi_zavd[$i]['title'].'</a></td>
			<td>'.$planovi_zavd[$i]['result_detail'].'</td>	
			<td align="center">'.(($planovi_zavd[$i]['d_v'])?date('d.m.Y', strtotime($planovi_zavd[$i]['d_v'])):'&nbsp;').'</td>
			<td align="center">'.(($planovi_zavd[$i]['d_do'])?date('d.m.Y', strtotime($planovi_zavd[$i]['d_do'])):'&nbsp;').'</td>
			<td align="center" class="chas">'.$planovi_zavd[$i]['chas_plan'].'</td>';
		if($planovi_zavd[$i]['uniq']) {
                echo '<td align="center"><input type="text" name="planovi_data_fakt['.$planovi_zavd[$i]['uniq'].']" value="'.(($planovi_zavd[$i]['d_do'])?date('d.m.Y', strtotime($planovi_zavd[$i]['d_do'])):'').'" class="datepicker" /></td>'; // Фактична дата заверш підставляється планова
                if($planovi_zavd[$i]['chas_fakt']){
                    echo '<td align="center"><input type="text" name="planovi_chas_fakt['.$planovi_zavd[$i]['uniq'].']" value="'.$planovi_zavd[$i]['chas_fakt'].'" class="chas_fakt" /></td>';}
                else{
                    echo '<td align="center"><input type="text" name="planovi_chas_fakt['.$planovi_zavd[$i]['uniq'].']" value="'.$planovi_zavd[$i]['chas_plan'].'" class="chas_fakt" /></td>';
                }
			//echo '<td align="center">&nbsp;</td>';
			echo '<td align="center"><input type="checkbox" name="plan_zavd_zaversh['.$planovi_zavd[$i]['uniq'].']" checked=on /></td>';
            if(!isset($planovi_zavd[$i]['prymitky'])){$planovi_zavd[$i]['prymitky']='';}
			echo '<td class="tooltiper" align="center"><a href="#" title="'.$planovi_zavd[$i]['prymitky'].'"><img src="'.base_url().'application/views/front/files/information.png" class="dialog_link" id_zavd="'.$planovi_zavd[$i]['uniq'].'" is_planovi="1" /></a></td>';
		} else {
			echo '<td align="center">&nbsp;</td>
				<td align="center">&nbsp;</td>
				<td align="center">&nbsp;</td>
				<td align="center">&nbsp;</td>';
		}
		echo '
		</tr>';
	}
	echo '</tbody>';
}		


if($potocni_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17" id="zag_chas">Поточні</td>
	  </tr>
    </tfoot>';
	
	echo '<tbody>';
	for($i=0; $i<count($potocni_zavd); $i++) {
		if($potocni_zavd[$i]['strateg'] == 1) { $strateg="Так"; } else { $strateg="Ні"; }
		echo '
		<tr class="row0">';
			if($potocni_zavd[$i]['mitky'] == 0) {
				echo '<td align="center"><a href="'.site_url("main/edit_zavd/".$potocni_zavd[$i]['id']).'"><img src="'.base_url().'application/views/front/files/pencil.png" /></a></td>';
			} else {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/pencil.png" /></td>';		
			}
			echo '
			<td align="center">'.$strateg.'</td>
			<td><a href="'.site_url("main/pereglad_zavd/".$potocni_zavd[$i]['id']).'">'.$potocni_zavd[$i]['nazva'].'</a></td>
			<td>'.$potocni_zavd[$i]['rezult'].'</td>        
			<td align="center">'.date('d.m.Y', strtotime($potocni_zavd[$i]['date_begin'])).'</td>
			<td align="center">'.date('d.m.Y', strtotime($potocni_zavd[$i]['date_zapl_zaversh'])).'</td>
			<td align="center" class="chas">'.$potocni_zavd[$i]['zapl_chas'].'</td>
			<td align="center"><input type="text" name="data_fakt['.$potocni_zavd[$i]['id'].']" value="'.(($potocni_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($potocni_zavd[$i]['data_fakt'])):date('d.m.Y', strtotime($potocni_zavd[$i]['date_zapl_zaversh']))).'" class="datepicker" readonly="readonly" /></td>';
        if($potocni_zavd[$i]['chas_fakt']){
        echo '<td align="center"><input type="text" name="chas_fakt['.$potocni_zavd[$i]['id'].']" value="'.$potocni_zavd[$i]['chas_fakt'].'" class="chas_fakt" /></td>';
        }else{
            echo '<td align="center"><input type="text" name="chas_fakt['.$potocni_zavd[$i]['id'].']" value="'.$potocni_zavd[$i]['zapl_chas'].'" class="chas_fakt" /></td>';
        }
		echo '<td align="center"><input type="checkbox" name="zavd_zaversh['.$potocni_zavd[$i]['id'].']" checked=on /></td>
			<td align="center" class="tooltiper"><a href="#" title="'.$potocni_zavd[$i]['prymitky'].'"><img src="'.base_url().'application/views/front/files/information.png" class="dialog_link" id_zavd="'.$potocni_zavd[$i]['id'].'" is_planovi="0" /></a></td>
		</tr>';
	}
	echo '</tbody>';	
}


if($pozachergovi_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17">Позачергові</td>
	  </tr>
    </tfoot>';

	echo '<tbody>';
	for($i=0; $i<count($pozachergovi_zavd); $i++) {
		if($pozachergovi_zavd[$i]['strateg'] == 1) { $strateg="Так"; } else { $strateg="Ні"; }
		echo '
		<tr class="row0">
			<td align="center"><a href="'.site_url("main/edit_zavd/".$pozachergovi_zavd[$i]['id']).'"><img src="'.base_url().'application/views/front/files/pencil.png" /></a></td>
			<td align="center">'.$strateg.'</td>
			<td><a href="'.site_url("main/pereglad_zavd/".$pozachergovi_zavd[$i]['id']).'">'.$pozachergovi_zavd[$i]['nazva'].'</a></td>
			<td>'.$pozachergovi_zavd[$i]['rezult'].'</td>   
			<td align="center">'.(($pozachergovi_zavd[$i]['date_begin'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['date_begin'])):'&nbsp;').'</td>
			<td align="center">'.(($pozachergovi_zavd[$i]['date_zapl_zaversh'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['date_zapl_zaversh'])):'&nbsp;').'</td>
			<td align="center">'.$pozachergovi_zavd[$i]['zapl_chas'].'</td>
			<td align="center"><input type="text" name="data_fakt['.$pozachergovi_zavd[$i]['id'].']" value="'.(($pozachergovi_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['data_fakt'])):date('d.m.Y', strtotime($pozachergovi_zavd[$i]['date_zapl_zaversh']))).'" class="datepicker" readonly="readonly" /></td>';
        if($pozachergovi_zavd[$i]['chas_fakt']){
			echo '<td align="center"><input type="text" name="chas_fakt['.$pozachergovi_zavd[$i]['id'].']" value="'.$pozachergovi_zavd[$i]['chas_fakt'].'" class="chas_fakt" /></td>';
        }else{
                echo '<td align="center"><input type="text" name="chas_fakt['.$pozachergovi_zavd[$i]['id'].']" value="'.$pozachergovi_zavd[$i]['zapl_chas'].'" class="chas_fakt" /></td>';
        }
        echo '<td align="center"><input type="checkbox" name="zavd_zaversh['.$pozachergovi_zavd[$i]['id'].']" checked=on/></td>
			<td align="center"><a href="#"><img src="'.base_url().'application/views/front/files/information.png" class="dialog_link" id_zavd="'.$pozachergovi_zavd[$i]['id'].'" is_planovi="0" /></a></td>
		</tr>';
	}
}	
?>	
	
	
</tbody>	
</table>	
	
<div style="height: 30px;margin: 20px 20px 0px 20px;">
	<!--<input type="submit" class="button" value="Відзвітуватись" />-->
	<!-- Звітування за попередній тиждень -->
		<input type="hidden" name="d_v" value="<? echo $d_v ?>" />
		<input type="hidden" name="d_do" value="<? echo $d_do ?>" />
       <? /*<input type="hidden" name="j_stroka_id_pot" value="<? echo htmlspecialchars($j_stroka_pot); ?>" />
    <input type="hidden" name="j_stroka_id_poz" value="<? echo htmlspecialchars($j_stroka_poz); ?>" /> */?>
    <input type="hidden" name="date_vid" value="<? echo $d_v; ?>" />
    <input type="hidden" name="date_do" value="<? echo $d_do; ?>" />
	<!-- Звітування за попередній тиждень -->
	<input type="button" class="but orange" id="send_zvit" value="Відзвітуватись" />
</div>
</form>


		<!-- ui-dialog -->
		<div id="dialog" title="Примітка">
		<p><strong>Внесіть примітку:</strong></p>
		<textarea id="prymitka" style="width: 370px; height: 80px;" class="form-control"></textarea>
		<input type="hidden" name="id_zavd" value="" />
		</div>
		<!-- ui-dialog -->	



	</div>

<?php $this->load->view('front/footer'); ?>