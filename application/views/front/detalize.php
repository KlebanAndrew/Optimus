<?php $this->load->view('front/header'); ?>
<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
<link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
<!-- Підказки -->
<script src="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.jq.js" type="text/javascript"></script>
<link href="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.min.jq.css" rel="stylesheet" type="text/css" />

<script>
    <?php
    echo 'var check = false';
    for($i=0; $i<count($users); $i++) {
        if($users[$i]['id']== $user_id){
        echo 'check = true';
        };
    }
    echo ';';
    ?>
$(document).ready(function(){
    $(".zapl_chas").keypress(function (event) {
        if (event.which < 44
            || event.which > 57  || event.which ==47 || event.which ==45) {
            alert("Дозволено вводити тільки числа, наприклад 40 або 40.5");
            event.preventDefault();
        }
    });

	$( ".datepicker" ).datepicker();
	$( "#format" ).change(function() {
		$( ".datepicker" ).datepicker( $.datepicker.regional[ "uk" ] );
		$( ".datepicker" ).datepicker( "option", "dateFormat", $( this ).val() );
	});

    ////////////////////////////////////////////////////////////////
    $("textarea").focus(function(){
        $(this).css({position:"absolute"});
        $(this).animate({ height:"200px"}, 300);
    }).blur(function(){
        var id = $(this).attr('id');
        $(this).animate({ height:"25px"}, 200);
    setTimeout('$('+id+').css({position:"static"})',200);
    });
////////////////////////////////////////////////////////////////////////////////////////

	$(".DatePicker").datepicker({
		//dateFormat: "yy-mm-dd",
		dateFormat: "dd.mm.yy",
		changeYear: true,
        changeMonth:true,
		yearRange: '-1:+1',
		beforeShow: function(input, inst) {
			$(".ui-datepicker").css("font-size", "0.9em");
		},
		showOtherMonths: true,
		selectOtherMonths: true,
		firstDay: 1
	});
	$('.ui-datepicker-calendar td').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
	$('.ui-datepicker-calendar td').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
	$(".DatePicker").datepicker("setDate", "+0d");

	
	
	$('#add').click(function(){
		if(($('input[name=text_detail]').val()!='') & ($('#main_result_detail').val()!='') & ($('input[name=chas_plan]').val()!='') &  ($('input[name=date_begin]').val()!='')) {
			$.ajax({
				type: "POST",
				url: "<? echo site_url("main/detalize_add") ?>",
				data: { 
					"text_detail":$('input[name=text_detail]').val(),
					"result_detail":$('#main_result_detail').val(),
					"chas_plan":$('input[name=chas_plan]').val(),					
					"date_begin":$('input[name=date_begin]').val(),
                    "date_end":$('input[name=date_end]').val(),
					"id_pl_zavd":$('input[name=id_pl_zavd]').val()
				},
				dataType: "text",
				success: function(msg){
					//console.log(msg);
					$().el7r_notify({'text':msg, 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});
					setInterval(function(){ 
						window.location.reload(0);
					},1000); // 10sec (10000)
					
				}
			});
		} else { 
			//alert('Заповніть всі поля !');
			$().el7r_notify({'text':"Заповніть всі поля !", 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});
		}
	});

    //блок переврірки
    $( ".element" ).prop( "readonly", true );
    $( ".editButton" ).prop( "disabled", true );

    $('.adminlist tr').dblclick(function () {
        if(check){
            $( ".element" ).prop( "readonly", true );
            $( ".editButton" ).prop( "disabled", true );
        $(this).children().children().each(function(){
            $(this).prop("readonly", false);
           // $(this).prop("disabled", false);
        })
        $(this).children().children().last().prop("disabled", false);

        }
    });
    //функція обробки кнопки редагування
    $('.editButton').click(function(){
        var n=false;//тригер для перевірки пустих полів
        var i = $(this).attr("data-id"); //змінна id редагованого завдання
        $(this).parent().parent().children().children().each(function(){//функція перевірки всіх полів рядка таблиці
            if($(this).val().trim()==""){
                if($(this).text().trim()==""){
                    n=true;
                }

            }
            $(this).prop("readonly", true);
            console.log(i);
        });
        $(this).prop("disabled", true);
        if(n){alert("Заповніть всі поля")
        }else{


            $.ajax({
                type: "POST",
                url: "<? echo site_url("main/detalize_update") ?>",
                data: {
                    "text_detail":$('#text_detail_'+i).val(),
                    "result_detail":$('#result_detail_'+i).val(),
                    "chas_plan":$('#chas_plan_'+i).val(),
                    "date_begin":$('#data_first_'+i).val(),
                    "date_end":$('#data_last_'+i).val(),
                    "id_pl_zavd":$('input[name=id_pl_zavd]').val(),
                    "id_zavd" : i
                },
                dataType: "text",
                success: function(msg){
                    //alert(msg);
                    $().el7r_notify({'text':msg, 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});
                    setInterval(function(){
                       // window.location.reload(0);
                    },1000); // 10sec (10000)

                }
            });

        }

    });
    //+++++++++++++++++++++++++++++++++++++++
    $('.dublicate').click(function(){
        var id = $(this).attr("data-id");
        $('#main_text_detail').val($('#text_detail_'+id).val());
        $('#main_result_detail').val($('#result_detail_'+id).val());
        $('#main_zapl_chas').val($('#chas_plan_'+id).val());

    });
});
</script>

<div id="container">
	<h1>Деталізація планового завдання (з річного плану)</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/edit_user").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">Вихід</a></div>
	<div id="body">

	<br />
<h3><? echo $title ?>:&nbsp;&nbsp;<a href="" onClick="window.location.reload()" title="Обновити"><img src="<? echo base_url() ?>application/views/front/files/refreshgreen.png" /></a></h3>	


<div style="margin-left: 50px; width:800px; float: left;">
<table class="adminlist" id="12" cellspacing="1">
	<thead>
		<tr>
			<th width="5">№</th>
			<th width="100">Назва роботи</th>
			<th width="100">Результат</th>
			<th width="100">Час план</th>
			<th width="20" colspan="2">Період</th>
            <th width="25"></th>

		</tr>
    </thead>

	<tbody>
		<tr>
			<td align="center">#</td>
			<td>
				<input type="text" name="text_detail" id="main_text_detail" size="20" value="" />
				<input type="hidden" name="id_pl_zavd" value="<? echo $id_pl_zavd ?>" />
				<input type="hidden" name="user_id" value="" />
			</td>
			<td><textarea  id="main_result_detail" style="width: 280px;"></textarea></td>
			<td align="center"><input type="text" class="zapl_chas" id="main_zapl_chas" name="chas_plan" size="5" value="" /></td>
			<td>
				<input type="text" name="date_begin" class="DatePicker" style="font-size:14px; font-weight:bold; text-align: center; width: 150px;"/>
			</td>
            <td colspan="2">
                <input type="text" name="date_end" class="DatePicker" style="font-size:14px; font-weight:bold; text-align: center; width: 150px;"/>
            </td>
		</tr>
		<tr>
			<td colspan="7">
				<input type="button" class="but orange" id="add" value="Додати" />
			</td>
		</tr>

		<?php
        $chasSum = 0;
        echo '<tr class="attention"><td colspan="6"><hr></td><td>Власні завдання</td></tr>';
        foreach ($main->result() as $row) {
            if($row->id_user == $user_id){
                $chasSum = $chasSum+$row->chas_plan;
                echo '

                        <tr class="row0 '.$row->id.'">
                            <td align="center" class="dublicate" data-id="'.$row->id.'"><a href=#>'.$row->id.'</a></td>
                            <td><input  class="element" type="text" id="text_detail_'.$row->id.'" size="20" value="'.$row->text_detail.'"/></td>
                            <td><textarea  class="element" id="result_detail_'.$row->id.'" style="width: 280px;">'.$row->result_detail.'</textarea></td>
                            <td ><input  class="element zapl_chas" type="text" size="5" id="chas_plan_'.$row->id.'" value="'.$row->chas_plan.'"/></td>
                            <td align="center"><input  type="text" id="data_first_'.$row->id.'" value="'.$row->d_v.'" class="datepicker element"  /></td>
                            <td align="center"><input  type="text" id="data_last_'.$row->id.'" value="'.$row->d_do.'" class="datepicker element" /></td>
                            <td><input class="editButton but orange" type="button" data-id="'.$row->id.'" id = "idButton_'.$row->id.'" value="Редагувати"></td>
                        </tr>
                        ';
            }
        }
        echo '<tr class="attention"><td colspan="6"><hr></td><td>Завдання інших працівників</td></tr>';
        foreach ($main->result() as $row) {
            if($row->id_user != $user_id){
                $chasSum = $chasSum+$row->chas_plan;
                echo '

                        <tr class="row0 '.$row->id.'">
                            <td align="center" class="dublicate" data-id="'.$row->id.'"><a href=#>'.$row->id.'</a></td>
                            <td><input  class="element" type="text" id="text_detail_'.$row->id.'" size="20" value="'.$row->text_detail.'"/></td>
                            <td><textarea  class="element" id="result_detail_'.$row->id.'" style="width: 280px;">'.$row->result_detail.'</textarea></td>
                            <td ><input  class="element zapl_chas" type="text" size="5" id="chas_plan_'.$row->id.'" value="'.$row->chas_plan.'"/></td>
                            <td align="center"><input  type="text" id="data_first_'.$row->id.'" value="'.$row->d_v.'" class="datepicker element"  /></td>
                            <td align="center"><input  type="text" id="data_last_'.$row->id.'" value="'.$row->d_do.'" class="datepicker element" /></td>
                            <td><input class="editButton but orange" type="button" data-id="'.$row->id.'" id = "idButton_'.$row->id.'" value="Редагувати"></td>
                        </tr>
                        ';
            }
        }
       /* <td>'.date('d.m.Y', strtotime($row->d_v)).'</td>
					<td>'.date('d.m.Y', strtotime($row->d_do)).'</td> */
		?>
	</tbody>
    <tfoot>
    <tr>
        <td colspan="3"></td>
        <td colspan="2"><div style="text-align: left; font-size:18px;">Сума планових годин: <?echo $chasSum;?></div></td>
        <td colspan="1"></td>
    </tr>
    </tfoot>
</table>

</div>


<div style="overflow: auto; padding-left: 275px;"><b>Задіяні працівники:</b><br />
<?php
for($i=0; $i<count($users); $i++) {
	echo $users[$i]['name'].'<br>';
}
?>	

</div>
<div style="clear: both;">&nbsp;</div>

<p>&nbsp;</p>
<p>&nbsp;</p>

	</div>

<?php $this->load->view('front/footer'); ?>