<?php $this->load->view('front/header'); ?>

	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/moment.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	<link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />

	<script>


	var min_date;//глобальна змінна для початкової дати
	function initDatepicker(){ //функція обробки дати при зміні значення ліст боксу vud
        /////////////////////////////////////////////////////////////////
        var next_week_selector = <?php echo $next_week; ?>;
                //////////////////////////////////////////////////////////////////////
        var max_Date1;
        var selectedVal= $('#select').val();
		var prev_week = new Date();// порожня змінна понеділку попереднього тижня (ініціалізужться сьогоднішня дата)
		var this_week = new Date();// порожня понеділку поточного тижня
		var max_date = new Date();//порожня пятниці поточного тижня
        var next_week = new Date(); //порожня змінна понеділку наступного дня
        var next_week_end = new Date(); //порожня змінна пятниці наступного дня
			var prev_date = prev_week.getDate();// змінна яка містить номер дня поточного місяця

			var numberofweek = prev_week.getDay();// змінна яка містить номер сьогоднішнього  дня поточного тижня 
				var b = prev_date - numberofweek+1;//змінна яка містить номер дня  понеділка поточного тижня 
				var a  = prev_date - (6 +numberofweek);//змінна яка містить номер дня  понеділка попереднього тижня 
				var c= prev_date +(5-numberofweek);//змінна яка містить номер дня  пятниці поточного тижня
                var d =prev_date +(8-numberofweek);//змінна яка містить номер дня  понеділка наступного тижня
                var e=prev_date +(12-numberofweek);//змінна яка містить номер дня  пятниці наступного тижня
                        max_date.setDate(c);//ініціалізація дати
                        this_week.setDate(b);//ініціалізація дати
                        prev_week.setDate(a);//ініціалізація дати
                        next_week.setDate(d);//ініціалізація дати
                        next_week_end.setDate(e);//ініціалізація дати
        this_week = ((moment(this_week)).format("DD.MM.YYYY")); //приведення дати до формату DD.MM.YYYY
        prev_week = ((moment(prev_week)).format("DD.MM.YYYY"));//приведення дати до формату DD.MM.YYYY
        max_date = ((moment(max_date)).format("DD.MM.YYYY"));//приведення дати до формату DD.MM.YYYY
        next_week = ((moment(next_week)).format("DD.MM.YYYY"));//приведення дати до формату DD.MM.YYYY
        next_week_end = ((moment(next_week_end)).format("DD.MM.YYYY"));//приведення дати до формату DD.MM.YYYY


			
			
		 minDate = (selectedVal == 3) ? prev_week :this_week;  /*'<? echo date('d.m.Y', strtotime($d_v)) ?>'*/   //оператор перевірки для позачергових завданнь
																													//для чергових (selectedVal == 3) встановлюється дата поточного тижня
		        if(next_week_selector==0){ //оператор перевірки умови завдань для поточного чи наступного тижня
		                    (selectedVal == 3) ? $('input[name=date_begin]').val(this_week) :$('input[name=date_begin]').val(this_week); //встановлення дати для текстових полів календаря (дата початку завдання)
                            $('input[name=date_zapl_zaversh]').val(max_date);
                            max_Date1=max_date;
                            min_date = minDate;
                        }
                else{
                            $('input[name=date_begin]').val(next_week) ; //встановлення дати для текстових полів календаря (дата початку завдання)
                            $('input[name=date_zapl_zaversh]').val(next_week_end);
                            max_Date1=next_week_end;
                            minDate=next_week;
                }




        $( ".datepicker" ).datepicker("destroy");
		$( ".datepicker" ).datepicker({
			dateFormat: "dd.mm.yy",
			minDate: minDate, 
			maxDate: max_Date1										//'<? echo date('d.m.Y', strtotime($d_do))?>'
		});
		};
		function addDays(days){ //тестова функція (можна видалити)
				var today = new Date();
				var next = new Date(today);
				tomorrow.setDate(today.getDate()+1);
		}
	$(function() {
        $("#zapl_chas").keypress(function (event) {
            if (event.which < 44
                || event.which > 57  || event.which ==47 || event.which ==45) {
                alert("Дозволено вводити тільки числа, наприклад 40 або 40.5");
                event.preventDefault();
            }
        });

		initDatepicker();//ініціалізація функції встановлення дати

		$( ".datepicker2" ).datepicker({
			dateFormat: "dd.mm.yy",
			minDate:  min_date                                      /*'<?echo date("d.m.Y", strtotime($d_v))?>'*/
		});
		$(".ui-datepicker").css("font-size", "0.9em");
		
		// Dialog
		$('#dialog').dialog({
			autoOpen: false,
			width: 300,
			buttons: {
				"Зберегти": function() {
					$(this).dialog("close");
					$('input[name=date_end_povtor]').val($('#repeat_date_end').val());
					if($("#povtor_result").is(':checked')) {
						$('input[name=povtor_result]').val('1');
					} else {
						$('input[name=povtor_result]').val('0');	
					}
				},
				"Закрити": function() {
					$(this).dialog("close");
					$('input[name=date_end_povtor]').val('');
					$('input[name=povtor_result]').val('');
				}
			}
		});
		
		// Dialog Link
		$('#dialog_link').click(function(){
			$('#dialog').dialog('open');
			return false;
		});		

		$('#create').click(function(){
			if($('input[name=nazva]').val()=='') {  
				alert('Заповніть назву завдання !');
			}
			if($('input[name=zapl_chas]').val()=='') {  
				alert('Заповніть запланований час !');
			} 
			if(($('input[name=nazva]').val()!='') & ($('input[name=zapl_chas]').val()!='')) {
				$('#modalform').trigger('submit');	// підтвердження форми
				return false;
			}
		});					
		$('#select').click(function(){//виклик функції встановлення дати при зміні значення списку vud

			initDatepicker();
		});

	});
	</script>

<div id="container">
	<h1>Створити завдання <?php if($next_week == 1) { echo 'на наступний тиждень'; }  ?></h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/edit_user").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">Вихід</a></div><br />
	<div id="body">


<div id="inner">
<?php
$attributes = array('id' => 'modalform');
echo form_open('main/create_zavd_true', $attributes);
?>
<input type="hidden" name="next_week" value="<?php echo $next_week; ?>" />
<table border="0" align="center" class="createZavd">
  <tr>
    <td>Вид завдання</td>
	<td>
		<select name="vud" id="select" class="form-control">
			<!--<option value="2">Поточні</option>
			<option value="3">Позачергові</option>-->
<?php if($next_week == 1 or $lock_potochni == 0) { echo '<option value="2" >Поточні</option>'; }  ?>
<?php if($next_week == 0) { echo '<option value="3">Позачергові</option>'; }  ?>
		</select>
    </td>
    <td style="text-align: right; padding-right: 20px;">
		<label>Згідно стратегічного плану <input type="checkbox" name="strateg" style="vertical-align: middle; margin: 0px;"  /></label>
	</td>
  </tr>
  <tr>
    <td>Назва завдання</td>
    <td colspan="2"><label>
      <input type="text" name="nazva" id="textfield" class="form-control" />
    </label></td>
  </tr>
  <tr>
    <td>Результат завдання</td>
    <td colspan="2"><label>
      <textarea name="rezult" id="textarea" cols="65" rows="5" class="form-control"></textarea>
    </label></td>
  </tr>
  <tr>
    <td>Дата початку завдання</td>
    <td><input type="text" name="date_begin" class="form-control datepicker" value="<?php /*echo date('d.m.Y', strtotime($d_v))*/ ?>" /></td>
    <td style="text-align:right;">Запланована дата завершення <input type="text" name="date_zapl_zaversh" class="form-control datepicker" value="<?php /*echo date('d.m.Y', strtotime($d_do))*/ ?>" /></td>
  </tr>
  <tr>
    <td>Запланований час (в год.)</td>
    <td><input id="zapl_chas" type="text" name="zapl_chas" class="form-control"/></td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td colspan="3" style="text-align: center; padding-top: 5px;">
		<!-- повтореня завдання -->
		<input type="hidden" name="date_end_povtor" value="" />
		<input type="hidden" name="povtor_result" value="" />
		<input type="button" class="but orange" style="float:none;" name="button4" value="Створити" id="create" />
		<input type="button" class="but orange" style="float:none;" id="dialog_link" value="Повторювати завдання" />
		<input type="button" class="but orange" style="float:none;" onclick="location.href='<? echo site_url("main/index") ?>'" value="Скасувати" />
	</td>
  </tr>
</table>



		<!-- ui-dialog -->
		<div id="dialog" title="Повторювати завдання щотижня">
		<p><strong>Кінцева дата повторення</strong></p><input type="text" id="repeat_date_end" class="datepicker2">
		<p><label><input type="checkbox" id="povtor_result" style="vertical-align: middle; margin: 0px;" /> Повторювати результат</label></p>
		</div>
		<!-- ui-dialog -->		
</form>	

		
<br /><br />
</div>
	</div>
<?php $this->load->view('front/footer');?>