<?php
echo '<tfoot>
	  <tr>
		  <td colspan="17"><input type="hidden" id="title_dates" value="'.date('d.m.Y', strtotime($d_v)).' - '.date('d.m.Y', strtotime($d_do)).'" />&nbsp;</td>
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

			<td colspan="2">';
			if($planovi_zavd[$i]['detalize'] == 1) {
				echo '<a href="'.site_url("main/detalize/".$planovi_zavd[$i]['id']).'">'.$planovi_zavd[$i]['title'].'</a>';
			} else {
				echo $planovi_zavd[$i]['title'];			
			}
			echo '
			</td>			
			<td align="center">'.$planovi_zavd[$i]['d_v'].'</td>
			<td align="center">'.$planovi_zavd[$i]['d_do'].'</td>
			<td align="center">'.$planovi_zavd[$i]['chas_plan'].'</td>
			<td align="center">&nbsp;</td>			
			<td align="center">&nbsp;</td>
			<td align="center">&nbsp;</td>			
			
		</tr>';
	}
	echo '</tbody>';
}		


if($potocni_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17" id="zag_chas_pot">Поточні</td>
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
			<td align="center">'.$potocni_zavd[$i]['date_begin'].'</td>
			<td align="center">'.$potocni_zavd[$i]['date_zapl_zaversh'].'</td>
			<td align="center" class="chas_pot">'.$potocni_zavd[$i]['zapl_chas'].'</td>
			<td align="center">'.$potocni_zavd[$i]['data_fakt'].'</td>
			<td align="center">'.$potocni_zavd[$i]['chas_fakt'].'</td>';
			if($potocni_zavd[$i]['mitky'] == 0) {
				echo '<td align="center"><a href="'.site_url("main/delete_zavd/".$potocni_zavd[$i]['id']).'" class="img_t" onclick="return del_zavd();"><img src="'.base_url().'application/views/front/files/cross.png" /></a></td>';
			}
			if($potocni_zavd[$i]['mitky'] == 1) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника" /></td>';
			}
			if($potocni_zavd[$i]['mitky'] == 2) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене" /></td>';
			}
			if($potocni_zavd[$i]['mitky'] == 3) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника (факт)" /></td>';
			}
			if($potocni_zavd[$i]['mitky'] == 4) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене (факт)" /></td>';
			}
		echo '</tr>';
	}
	echo '</tbody>';	
}


if($pozachergovi_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="17" id="zag_chas_poz">Позачергові</td>
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
			<td align="center">'.$pozachergovi_zavd[$i]['date_begin'].'</td>
			<td align="center">'.$pozachergovi_zavd[$i]['date_zapl_zaversh'].'</td>
			<td align="center" class="chas_poz">'.$pozachergovi_zavd[$i]['zapl_chas'].'</td>
			<td align="center">'.$pozachergovi_zavd[$i]['data_fakt'].'</td>
			<td align="center">'.$pozachergovi_zavd[$i]['chas_fakt'].'</td>';
			
			if($pozachergovi_zavd[$i]['mitky'] == 0) {
				echo '<td align="center"><a href="'.site_url("main/delete_zavd/".$pozachergovi_zavd[$i]['id']).'" class="img_t" onclick="return del_zavd();"><img src="'.base_url().'application/views/front/files/cross.png" /></a></td>';
			}
			if($pozachergovi_zavd[$i]['mitky'] == 1) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника (факт)" /></td>';
			}
			if($pozachergovi_zavd[$i]['mitky'] == 2) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене (факт)" /></td>';
			}
			if($pozachergovi_zavd[$i]['mitky'] == 3) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника (факт)" /></td>';
			}
			if($pozachergovi_zavd[$i]['mitky'] == 4) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене (факт)" /></td>';
			}
		echo '</tr>';
	}
}	
?>