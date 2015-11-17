<?php $this->load->view('front/header'); ?>

    <script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
    <script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
    <script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
    <link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <script src="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.jq.js" type="text/javascript"></script>
    <link href="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.min.jq.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />

<div id="container">
	<h1>Повідомлення</h1>
	<div id="body">


	<div style="height:300px; margin:50px;">
		<h2><? echo $message ?></h2>



        <table class="adminlist" cellspacing="1">
            <thead>
            <tr>
                <th width="100">Назва завдання</th>
                <th width="200">Результат тижня</th>
                <th width="50">Дата початку завдання</th>
                <th width="50">Запл. дата заверш.</th>
                <th width="50">Запл. час на викон.</th>
                <th width="50"></th>
            </tr>
            </thead>
            <tbody>
            <? //var_dump($zavd);
            if(isset($zavd)){
            for($i=0; $i<count($zavd); $i++) {
            echo '
		<tr class="row0">';
			echo '<td><a href="'.site_url("main/pereglad_zavd/".$zavd[$i]['id']).'">'.$zavd[$i]['nazva'].'</a></td>
			<td>'.$zavd[$i]['rezult'].'</td>
			<td align="center">'.(($zavd[$i]['date_begin'])?date('d.m.Y', strtotime($zavd[$i]['date_begin'])):'&nbsp;').'</td>
			<td align="center">'.(($zavd[$i]['date_zapl_zaversh'])?date('d.m.Y', strtotime($zavd[$i]['date_zapl_zaversh'])):'&nbsp;').'</td>
			<td align="center">'.$zavd[$i]['zapl_chas'].'</td>
			<td align="center"><input type="button" class="but orange closeButton" id ='.$zavd[$i]['id'].' value="Закрити завдання"></td>';

            }

            }?>
            </tbody>
            </table>



    </div>


	</div>

<div style ="height: 30px;margin: 20px 20px 0px 20px; margin-left: 60px;">

    <form action="<? echo site_url("main/index") ?>">
        <button type="submit" class="button">Повернутись</button>
       </form>
</div>
    <script>
        $(document).ready(function(){
         $(".but").click(function(){
                $(this).prop('disabled', true);
             var i=$(this).attr('id');
             $.ajax({
                 type: "POST",
                 url: "<? echo site_url("main/close_old_task") ?>",
                 data: {
                     "id_zavd" : i
                 },
                 dataType: "text",
                 success: function(msg){
                     alert(msg);
                     $().el7r_notify({'text':msg, 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});


                 }
             });
             alert($(this).attr('id'));
         });


        });


    </script>

<?php $this->load->view('front/footer'); ?>