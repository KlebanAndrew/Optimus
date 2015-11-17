<?php $this->load->view('admin/header'); ?>

<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/front/style.css">
<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/front/tables.css">
<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/admin/css/message.css">

	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/js/DatePickerScript_for_admin.js"></script>
	<link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip.css" rel="stylesheet" />
    <script src="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip.js"></script>

	<script>

        $(document).ready(function(){
            <?

            if($check == 1){

             echo '$(".NavigateButtons").remove();';
            }
            ?>
        })
	$(function() {
        $('.tooltiper a').tooltip({
            track: false,
            delay: 0,
            showURL: false,
            fade: 200
        });

		$(".DatePicker").datepicker("setDate", "+0d");
		
		$(".DatePicker").datepicker().change(function() {
			$('#change_data').trigger('submit');	// підтвердження форми
		});	
	
		// Dialog
		$('#dialog').dialog({
			autoOpen: false,
			width: 480,
			buttons: {
				"Відправити": function() {
					$(this).dialog("close");
					$('#modalform').trigger('submit');	// підтвердження форми
				},
				"Закрити": function() {
					$(this).dialog("close");
					$('input[name=date_end_povtor]').val('');
				}
			}
		});
		
		// Dialog Link
		$('#dialog_link').click(function(){
			$('#dialog').dialog('open');
			return false;
		});		

		
		
	});
	</script>


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
$stroka_id_plan = array();
if($planovi_zavd) {
    for($i=0; $i<count($planovi_zavd); $i++) {
        $stroka_id_plan[] = $planovi_zavd[$i]['uniq'];
    }
}
$j_stroka_id_plan = json_encode($stroka_id_plan);
?>	
	

<div id="container">
	<div id="zagolovok" style="width:auto;"><?php echo $user_data->name; ?></div>
	<?php $this->load->view('admin/menu'); ?>
	<div id="body">

Фільтр по даті:
<form action="<? echo site_url("admin/show_user_plan_na_zatverd_date"); ?>" id="change_data" method="post" >
<input type="text" name="period" class="DatePicker" style="font-size:14px; font-weight:bold; width: 150px;text-align: center;"/>
<input type="hidden" name="user_id" value="<? echo $user_data->id; ?>" />
<? echo $title_dates; ?> (<a href="<? echo site_url("admin/show_status_plan/".$user_data->id); ?>">Статуси планів</a>)<p />
</form>


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
			<th width="5">Прим.</th>
		</tr>
    </thead>


<?php
echo '<tfoot>
	  <tr>
		  <td colspan="3">&nbsp;</td>
		  <td colspan="1"style="text-align: center; font-size:18px;">Загальна сума</td>
		  <td></td>
		  <td></td>
		  <td colspan="1"style="text-align: center; font-size:18px;" id="zag_chas"></td>
		  <td colspan="1"></td>
		  <td colspan="1"></td>
		  <td colspan="1"></td>
	  </tr>
    </tfoot>';	
	
if($planovi_zavd) {
	echo '
	<tfoot>
	  <tr>
	      <td colspan="6" style="text-align: center; font-size:18px;">Завдання з річного плану</td>
		  <td colspan="1" id="zag_chas_plan" style="text-align: center; font-size:18px;"></td>
		  <td colspan="1"></td>
		  <td colspan="1" ></td>
		  <td colspan="1"></td>
	  </tr>
    </tfoot>';
	
	echo '<tbody>';	
	for($i=0; $i<count($planovi_zavd); $i++) {
		echo '
		<tr class="row0">
			<td align="center">'.(($planovi_zavd[$i]['task_end'] == 1)?'<span style="color:#009900;">Заверш.</span>':'<span style="color:red;">Не заверш.</span>').'</td>
			<td align="center">&nbsp;</td>
			<td><a href="'.site_url("main/detalize/".$planovi_zavd[$i]['id']).'">'.$planovi_zavd[$i]['title'].'</a></td>
			<td>'.$planovi_zavd[$i]['result_detail'].'</td>			
			<td align="center">'.(($planovi_zavd[$i]['d_v'])?date('d.m.Y', strtotime($planovi_zavd[$i]['d_v'])):'&nbsp;').'</td>
			<td align="center">'.(($planovi_zavd[$i]['d_do'])?date('d.m.Y', strtotime($planovi_zavd[$i]['d_do'])):'&nbsp;').'</td>
			<td align="center" class="chas_plan">'.$planovi_zavd[$i]['chas_plan'].'</td>';
			if($planovi_zavd[$i]['uniq']) {
				echo '<td align="center">'.(($planovi_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($planovi_zavd[$i]['data_fakt'])):'&nbsp;').'</td>';
				echo '<td align="center" class="f_chas_plan">'.$planovi_zavd[$i]['chas_fakt'].'</td>';
			} else {
				echo '<td align="center">&nbsp;</td>';
				echo '<td align="center">&nbsp;</td>';
			}
			echo '
			<td align="center">&nbsp;</td>			
			
		</tr>';
	}
	echo '</tbody>';
}		

if($potocni_zavd) {
	echo '
	<tfoot>
	  <tr>
	      <td colspan="6" style="text-align: center; font-size:18px;">Завдання тижневого плану</td>
		  <td colspan="1" id="zag_chas_pot" style="text-align: center; font-size:18px;"></td>
		  <td colspan="1"></td>
		  <td colspan="1"></td>
		  <td colspan="1"></td>
	  </tr>
    </tfoot>';
	}		

echo '<tbody>';
for($i=0; $i<count($potocni_zavd); $i++) {
	if($potocni_zavd[$i]['strateg'] == 1) { $strateg="Так"; } else { $strateg="Ні"; }
	echo '
    <tr class="row0">
        <td align="center"></td>
        <td align="center">'.$strateg.'</td>
        <td>'.$potocni_zavd[$i]['nazva'].'</td>
		<td>'.$potocni_zavd[$i]['rezult'].'</td>        
		<td align="center">'.(($potocni_zavd[$i]['date_begin'])?date('d.m.Y', strtotime($potocni_zavd[$i]['date_begin'])):'&nbsp;').'</td>
        <td align="center">'.(($potocni_zavd[$i]['date_zapl_zaversh'])?date('d.m.Y', strtotime($potocni_zavd[$i]['date_zapl_zaversh'])):'&nbsp;').'</td>
        <td align="center" class="chas_pot">'.$potocni_zavd[$i]['zapl_chas'].'</td>
		<td align="center">'.(($potocni_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($potocni_zavd[$i]['data_fakt'])):'&nbsp;').'</td>
		<td align="center" class="f_chas_pot">'.$potocni_zavd[$i]['chas_fakt'].'</td>
		<!--<td align="center"><a href="'.site_url("admin/delete_zavd/".$potocni_zavd[$i]['id'].'-'.$user_data->id).'"><img src="'.base_url().'application/views/front/files/cross.png" /></a></td>-->
	';
	if($potocni_zavd[$i]['prymitky']) {
		echo '<td align="center" class="tooltiper"><a href="#" title="'.$potocni_zavd[$i]['prymitky'].'"><img src="'.base_url().'application/views/front/files/exclamation.png" /></a></td>';
	} else {
		echo '<td align="center">&nbsp;</td>';
	}
	echo '
	</tr>';
}
echo '</tbody>';	



if($pozachergovi_zavd) {
	echo '
	<tfoot>
	  <tr>
	      <td colspan="6" style="text-align: center; font-size:18px;">Позачергові завдання</td>
		  <td colspan="1" id="zag_chas_poz" style="text-align: center; font-size:18px;"></td>
		  <td colspan="1"></td>
		  <td colspan="1" ></td>
		  <td colspan="1"></td>
	  </tr>
    </tfoot>';
	}		
		
echo '<tbody>';
for($i=0; $i<count($pozachergovi_zavd); $i++) {
	if($pozachergovi_zavd[$i]['strateg'] == 1) { $strateg="Так"; } else { $strateg="Ні"; }
	echo '
    <tr class="row0">
        <td align="center"></td>
        <td align="center">'.$strateg.'</td>
        <td>'.$pozachergovi_zavd[$i]['nazva'].'</td>
		<td>'.$pozachergovi_zavd[$i]['rezult'].'</td>   
        <td align="center">'.(($pozachergovi_zavd[$i]['date_begin'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['date_begin'])):'&nbsp;').'</td>
        <td align="center">'.(($pozachergovi_zavd[$i]['date_zapl_zaversh'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['date_zapl_zaversh'])):'&nbsp;').'</td>
        <td align="center" class="chas_poz">'.$pozachergovi_zavd[$i]['zapl_chas'].'</td>
		<td align="center">'.(($pozachergovi_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['data_fakt'])):'&nbsp;').'</td>
		<td align="center" class="f_chas_poz">'.$pozachergovi_zavd[$i]['chas_fakt'].'</td>
		<!--<td align="center"><a href="'.site_url("admin/delete_zavd/".$pozachergovi_zavd[$i]['id'].'-'.$user_data->id).'"><img src="'.base_url().'application/views/front/files/cross.png" /></a></td>-->
		<td>&nbsp;</td>	
	</tr>';
}
?>
</tbody>	
	
	
</table>

    <div style="margin: 20px 0px;overflow:auto;">
		<input type="button" class="button but orange" onclick="location.href='<? echo site_url("admin/plans") ?>'" value="Повернутись">
<? if($status) {//перевірка чи є відправлені на затвердження плани ?>
    <? if($status->flag == 1) {	?>
        <form class="NavigateButtons" action="<?php echo site_url("admin/zatverduty_plan"); ?>" method="POST">
            <input type="hidden" name="user_id" value='<?php echo $user_data->id; ?>' />
            <input type="hidden" name="j_stroka_id_pot" value='<?php echo $j_stroka_id_pot; ?>' />
            <input type="hidden" name="j_stroka_id_poz" value='<?php echo $j_stroka_id_poz; ?>' />
            <input type="hidden" name="j_stroka_id_plan" value='<?php echo $j_stroka_id_plan; ?>' />
            <input type="hidden" name="period" value='<?php echo $period ?>' />

            <input type="submit" class="button but orange" value="Затвердити план">
        </form>
    <? } ?>
    <? if($status->flag != 2) {	?>
        <input  type="button" class="button but orange NavigateButtons" id="dialog_link" value="Відправити на доопрацювання">
    <? } ?>
<? if($status->flag == 0) echo '<span style="color:#FF0000; padding-left:15px;">План повернений на доопрацювання !</span>'; ?>
<? if($status->flag == 2) echo '<span style="color:#009900; padding-left:15px;">План затверджений !</span>'; ?>
<? if($status->flag == 4) echo '<span style="color:#009900; padding-left:15px;">Факт затверджений !</span>'; ?>
<? } ?>
    </div>

		<!-- ui-dialog -->
		<div id="dialog" title="Коментар">
			<p><strong>Внесіть пояснення:</strong></p>
			<?php
				$attributes = array('id' => 'modalform');
				echo form_open('admin/ne_zatverduty_plan', $attributes);
			?>	
			<textarea name="comment" id="textarea" cols="65" rows="5" class="form-control"></textarea>
			<input type="hidden" name="user_id" value='<?php echo $user_data->id; ?>' />
			<input type="hidden" name="j_stroka_id_pot" value='<?php echo $j_stroka_id_pot; ?>' />
			<input type="hidden" name="j_stroka_id_poz" value='<?php echo $j_stroka_id_poz; ?>' />
            <input type="hidden" name="j_stroka_id_plan" value='<?php echo $j_stroka_id_plan; ?>' />
			<input type="hidden" name="period" value='<?php echo $period ?>' />
			</form>
		</div>
		<!-- ui-dialog -->

		
	</div>

<?php $this->load->view('front/footer'); ?>