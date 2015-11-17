<?php
$stroka_id_pot = array();
//var_dump($planovi_zavd);
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


echo '<tfoot>
	  <tr>
		  <td colspan="3">
			<!-- для заголовків дат при кліку по календарику -->
			<input type="hidden" id="title_dates" value="'.date('d.m.Y', strtotime($d_v)).' - '.date('d.m.Y', strtotime($d_do)).'" />
<!-- new -->
<input type="hidden" name="json_stroka_pot" value=\''.$j_stroka_id_pot.'\' />
<input type="hidden" name="json_stroka_poz" value=\''.$j_stroka_id_poz.'\' />
<input type="hidden" name="date_vid" value=\''.$d_v.'\' />
<input type="hidden" name="date_do" value=\''.$d_do.'\' />
		  &nbsp;</td>
		  <td style="text-align: center; font-size:18px;">Загальна тривалість завдань</td>
		  <td></td>
		  <td style="text-align: center; font-size:18px;"></td>
		  <td colspan="1"style="text-align: center; font-size:18px;" id="zag_chas"> </td>
		  <td ></td>
		  <td colspan="1" style="text-align: center; font-size:18px;" id="zag_chas_fakt"></td>
		  <td colspan="10"></td>
	  </tr>
    </tfoot>';

if($planovi_zavd) {
	echo '
	<tfoot>
	  <tr>
	      <td colspan="3"></td>
		  <td style="text-align: center; font-size:18px;">Завдання з річного плану</td>
		  <td></td>
		  <td></td>
		  <td colspan="1" style="text-align: center; font-size:18px;" id="zag_chas_plan"></td>
		  <td ></td>
		  <td colspan="1" style="text-align: center; font-size:18px;" id="zag_chas_plan_fakt"></td>
		  <td colspan="10"></td>
	  </tr>
    </tfoot>';
	
	echo '<tbody>';	
	for($i=0; $i<count($planovi_zavd); $i++) {
		echo '
		<tr class="row0">';
			if(is_null($planovi_zavd[$i]['data_fakt']) and $planovi_zavd[$i]['uniq'] > 0) {
			
				echo '<td align="center" class="tooltiper"><a title="Редагувати завдання" href="'.site_url("main/edit_plan_zavd/".$planovi_zavd[$i]['uniq']).'"><img src="'.base_url().'application/views/front/files/pencil.png" /></a></td>';
			} else {
				echo '<td align="center">&nbsp;</td>';		
			}
			echo '
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
    if (isset($planovi_zavd[$i]['mitky'])){
        if ( $planovi_zavd[$i]['mitky'] == 0){
            //тестова функція видалення завдання
            echo '<td align="center" class="tooltiper"><a href="'.site_url("main/delete_plan_zavd/".$planovi_zavd[$i]['uniq']).'" class="img_t" onclick="return del_zavd();" title="Видалити завдання"><img src="'.base_url().'application/views/front/files/cross.png" /></a></td>';
        }
        if($planovi_zavd[$i]['mitky'] == 1) {
            echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника" /></td>';
        }
        if($planovi_zavd[$i]['mitky'] == 2) {
            echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/success_time.png" title="Завдання затверджене" /></td>';
        }
        if($planovi_zavd[$i]['mitky'] == 3) {
            echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника (факт)" /></td>';
        }
        if($planovi_zavd[$i]['mitky'] == 4) {
            echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене (факт)" /></td>';
        }
        if($planovi_zavd[$i]['mitky'] == 5) {
            echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/moved.png" title="Завдання перенесено" /></td>';
        }
    }
        else{echo '<td align="center"></td>';//Створіть завдання - напис для користувачів
        }


        //	echo '<td align="center">&nbsp;</td>';
        /* Блок для оформлення статтусів планових завдань  річного плану
//=========================================================================================================================================================
        // треба добавити в таблицю поле mitky для керування процесом затвердження
        // знайти або зробити метод для видалення планових завдань річного плану
        if($planovi_zavd[$i]['mitky'] == 0) {
				echo '<td align="center"><a href="'.site_url("main/delete_zavd/".$potocni_zavd[$i]['id']).'" class="img_t" onclick="return del_zavd();"><img src="'.base_url().'application/views/front/files/cross.png" /></a></td>';
			}
			if($planovi_zavd[$i]['mitky'] == 1) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника" /></td>';
			}
			if($planovi_zavd[$i]['mitky'] == 2) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене" /></td>';
			}
			if($planovi_zavd[$i]['mitky'] == 3) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника (факт)" /></td>';
			}
			if($planovi_zavd[$i]['mitky'] == 4) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене (факт)" /></td>';
			}
//=======================================================================================================================================================
        */
	echo'	</tr>';
	}
	echo '</tbody>';
}	//var_dump($planovi_zavd);


if($potocni_zavd) {
	echo '
	<tfoot>
	  <tr>
	      <td colspan="3"></td>
		  <td style="text-align: center; font-size:18px;">Завдання тижневого плану</td>
		  <td></td>
		  <td></td>
		  <td colspan="1" style="text-align: center; font-size:18px;" id="zag_chas_pot"></td>
		  <td></td>
		  <td colspan="1" style="text-align: center; font-size:18px;" id="zag_chas_pot_fakt"></td>
		  <td colspan="10"></td>
	  </tr>
    </tfoot>';
	
	echo '<tbody>';
	for($i=0; $i<count($potocni_zavd); $i++) {
		if($potocni_zavd[$i]['strateg'] == 1) { $strateg="Так"; } else { $strateg="Ні"; }
		echo '
		<tr class="row0">';
			if($potocni_zavd[$i]['mitky'] == 0) {
				echo '<td align="center" class="tooltiper"><a title="Редагувати завдання" href="'.site_url("main/edit_zavd/".$potocni_zavd[$i]['id']).'"><img src="'.base_url().'application/views/front/files/pencil.png" /></a></td>';
			} else {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/pencil.png" /></td>';		
			}
			echo '
			<td align="center">'.$strateg.'</td>
			<td><a href="'.site_url("main/pereglad_zavd/".$potocni_zavd[$i]['id']).'">'.$potocni_zavd[$i]['nazva'].'</a></td>
			<td>'.$potocni_zavd[$i]['rezult'].'</td>        
			<td align="center">'.date('d.m.Y', strtotime($potocni_zavd[$i]['date_begin'])).'</td>
			<td align="center">'.date('d.m.Y', strtotime($potocni_zavd[$i]['date_zapl_zaversh'])).'</td>
			<td align="center" class="chas_pot">'.$potocni_zavd[$i]['zapl_chas'].'</td>
			<td align="center">'.(($potocni_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($potocni_zavd[$i]['data_fakt'])):'&nbsp;').'</td>
			<td align="center" class="f_chas_pot">'.$potocni_zavd[$i]['chas_fakt'].'</td>';
			if($potocni_zavd[$i]['mitky'] == 0) {
				echo '<td align="center" class="tooltiper"><a href="'.site_url("main/delete_zavd/".$potocni_zavd[$i]['id']).'" class="img_t" onclick="return del_zavd();" title="Видалити завдання"><img src="'.base_url().'application/views/front/files/cross.png" /></a></td>';
			}
			if($potocni_zavd[$i]['mitky'] == 1) {
				echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника" /></td>';
			}
			if($potocni_zavd[$i]['mitky'] == 2) {
				echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/success_time.png" title="Завдання затверджене" /></td>';
			}
			if($potocni_zavd[$i]['mitky'] == 3) {
				echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника (факт)" /></td>';
			}
			if($potocni_zavd[$i]['mitky'] == 4) {
				echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене (факт)" /></td>';
			}
        if($potocni_zavd[$i]['mitky'] == 5) {
            echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/moved.png" title="Завдання перенесено" /></td>';
        }
		echo '</tr>';
	}
	echo '</tbody>';	
}


if($pozachergovi_zavd) {
	echo '
	<tfoot>
	  <tr>
		  <td colspan="3"></td>
		  <td style="text-align: center; font-size:18px;">Позачергові завдання</td>
		  <td></td>
		  <td></td>
		  <td colspan="1" style="text-align: center; font-size:18px;" id="zag_chas_poz"></td>
		  <td></td>
		  <td colspan="1" style="text-align: center; font-size:18px;" id="zag_chas_poz_fakt"></td>
		  <td colspan="10"></td>
	  </tr>
    </tfoot>';

	echo '<tbody>';
	for($i=0; $i<count($pozachergovi_zavd); $i++) {
		if($pozachergovi_zavd[$i]['strateg'] == 1) { $strateg="Так"; } else { $strateg="Ні"; }
		echo '
		<tr class="row0">
			<td align="center" class="tooltiper"><a title="Редагувати завдання" href="'.site_url("main/edit_zavd/".$pozachergovi_zavd[$i]['id']).'"><img src="'.base_url().'application/views/front/files/pencil.png" /></a></td>
			<td align="center">'.$strateg.'</td>
			<td><a href="'.site_url("main/pereglad_zavd/".$pozachergovi_zavd[$i]['id']).'">'.$pozachergovi_zavd[$i]['nazva'].'</a></td>
			<td>'.$pozachergovi_zavd[$i]['rezult'].'</td>   
			<td align="center">'.(($pozachergovi_zavd[$i]['date_begin'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['date_begin'])):'&nbsp;').'</td>
			<td align="center">'.(($pozachergovi_zavd[$i]['date_zapl_zaversh'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['date_zapl_zaversh'])):'&nbsp;').'</td>
			<td align="center" class="chas_poz">'.$pozachergovi_zavd[$i]['zapl_chas'].'</td>
			<td align="center">'.(($pozachergovi_zavd[$i]['data_fakt'])?date('d.m.Y', strtotime($pozachergovi_zavd[$i]['data_fakt'])):'&nbsp;').'</td>
			<td align="center" class="f_chas_poz">'.$pozachergovi_zavd[$i]['chas_fakt'].'</td>';
			
			if($pozachergovi_zavd[$i]['mitky'] == 0) {
				echo '<td align="center" class="tooltiper"><a href="'.site_url("main/delete_zavd/".$pozachergovi_zavd[$i]['id']).'" class="img_t" onclick="return del_zavd();" title="Видалити завдання"><img src="'.base_url().'application/views/front/files/cross.png" /></a></td>';
			}
			if($pozachergovi_zavd[$i]['mitky'] == 1) {
				echo '<td align="center"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника (факт)" /></td>';
			}
			if($pozachergovi_zavd[$i]['mitky'] == 2) {
				echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене" /></td>';
			}
			if($pozachergovi_zavd[$i]['mitky'] == 3) {
				echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/timeIcon.png" title="Завдання на затвердженні в керівника (факт)" /></td>';
			}
			if($pozachergovi_zavd[$i]['mitky'] == 4) {
				echo '<td align="center" class="tooltiper"><img src="'.base_url().'application/views/front/files/success.png" title="Завдання затверджене (факт)" /></td>';
			}
		echo '</tr>';
	}
}	
?>