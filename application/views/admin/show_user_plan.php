<?php $this->load->view('admin/header'); ?>

<div id="container">
	<div id="zagolovok"><?php echo $user_data->name; ?></div>
	<?php $this->load->view('admin/menu'); ?>
	<div id="body">

	
<? echo 'Поточний тиждень ('.date('d.m.Y', strtotime($this->date_begin)).' - '.date('d.m.Y', strtotime($this->date_end)).')'; ?>	
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
		</tr>
    </thead>


<?php
echo '<tfoot>
	  <tr>
		  <td colspan="17">&nbsp;</td>
	  </tr>
    </tfoot>';	

if($potocni_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17">Поточні</td>
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
        <td align="center">'.$potocni_zavd[$i]['zapl_chas'].'</td>
		<td align="center">'.(($potocni_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($potocni_zavd[$i]['data_fakt'])):'&nbsp;').'</td>
		<td align="center">'.$potocni_zavd[$i]['chas_fakt'].'</td>
	</tr>';
}
echo '</tbody>';	



if($pozachergovi_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17">Позачергові</td>
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
        <td align="center">'.$pozachergovi_zavd[$i]['zapl_chas'].'</td>
		<td align="center">'.(($pozachergovi_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['data_fakt'])):'&nbsp;').'</td>
		<td align="center">'.$pozachergovi_zavd[$i]['chas_fakt'].'</td>
	</tr>';
}
?>
</tbody>
</table>

<p>&nbsp;</p>

<? echo 'Наступний тиждень ('.date('d.m.Y', strtotime($this->next_date_begin)).' - '.date('d.m.Y', strtotime($this->next_date_end)).')'; ?>	
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
		</tr>
    </thead>


<?php
echo '<tfoot>
	  <tr>
		  <td colspan="17">&nbsp;</td>
	  </tr>
    </tfoot>';	

if($next_potocni_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17">Поточні</td>
	  </tr>
    </tfoot>';
	}		

echo '<tbody>';
for($i=0; $i<count($next_potocni_zavd); $i++) {
	if($next_potocni_zavd[$i]['strateg'] == 1) { $strateg="Так"; } else { $strateg="Ні"; }
	echo '
    <tr class="row0">
        <td align="center"></td>
        <td align="center">'.$strateg.'</td>
        <td>'.$next_potocni_zavd[$i]['nazva'].'</td>
		<td>'.$next_potocni_zavd[$i]['rezult'].'</td>        
        <td align="center">'.(($next_potocni_zavd[$i]['date_begin'])?date('d.m.Y', strtotime($next_potocni_zavd[$i]['date_begin'])):'&nbsp;').'</td>
        <td align="center">'.(($next_potocni_zavd[$i]['date_zapl_zaversh'])?date('d.m.Y', strtotime($next_potocni_zavd[$i]['date_zapl_zaversh'])):'&nbsp;').'</td>
        <td align="center">'.$next_potocni_zavd[$i]['zapl_chas'].'</td>
		<td align="center">'.(($next_potocni_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($next_potocni_zavd[$i]['data_fakt'])):'&nbsp;').'</td>
		<td align="center">'.$next_potocni_zavd[$i]['chas_fakt'].'</td>
	</tr>';
}
echo '</tbody>';	



if($next_pozachergovi_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17">Позачергові</td>
	  </tr>
    </tfoot>';
	}		
		
echo '<tbody>';
for($i=0; $i<count($next_pozachergovi_zavd); $i++) {
	if($next_pozachergovi_zavd[$i]['strateg'] == 1) { $strateg="Так"; } else { $strateg="Ні"; }
	echo '
    <tr class="row0">
        <td align="center"></td>
        <td align="center">'.$strateg.'</td>
        <td>'.$next_pozachergovi_zavd[$i]['nazva'].'</td>
		<td>'.$next_pozachergovi_zavd[$i]['rezult'].'</td>   
        <td align="center">'.(($next_pozachergovi_zavd[$i]['date_begin'])?date('d.m.Y', strtotime($next_pozachergovi_zavd[$i]['date_begin'])):'&nbsp;').'</td>
        <td align="center">'.(($next_pozachergovi_zavd[$i]['date_zapl_zaversh'])?date('d.m.Y', strtotime($next_pozachergovi_zavd[$i]['date_zapl_zaversh'])):'&nbsp;').'</td>
        <td align="center">'.$next_pozachergovi_zavd[$i]['zapl_chas'].'</td>
		<td align="center">'.(($next_pozachergovi_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($next_pozachergovi_zavd[$i]['data_fakt'])):'&nbsp;').'</td>
		<td align="center">'.$next_pozachergovi_zavd[$i]['chas_fakt'].'</td>
	</tr>';
}
?>
</tbody>
</table>


	
	<div style="margin: 20px 0px;overflow:auto;">
		<input type="button" class="button" onclick="location.href='<? echo site_url("admin/plans") ?>'" value="Повернутись">		
	</div>

	</div>

<?php $this->load->view('front/footer'); ?>