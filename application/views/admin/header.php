<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=win-1251" />
<!--<script src="http://pay.oe.if.ua/FrameWorks/jquery-1.6.2.min.js" type="text/javascript"></script>-->
<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/front/style.css">
<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/front/tables.css">
<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/admin/css/message.css">
    <script src="<? echo base_url() ?>application/views/js/jquery-1.6.2.min.js" type="text/javascript"></script>
<title>Планування</title>
</head>
<script>
    $(function(){
        $('.select_menu li').click(function(){//обробник меню секретарів
            $('#open').click();
            $(".select_menu").toggleClass("hide");
            var $t = $(this),
                $f = $('.field')
            text = $t.text(),
                icon = $t.find('i').attr('class');
            $f.find('label').text(text);
            $f.find('i').attr('class',icon);
            $.ajax({
                type: "POST",
                url: "<? echo site_url("ajax/change_user") ?>",
                data: {
                    "id" : $(this).attr("data-id")
                },
                dataType: "text",
                success: function(msg){
                    //alert(msg);
                    window.location.reload();
                }
            });

        });
        $('.field').click(function(e){
            $(".select_menu").toggleClass("hide");
            e.preventDefault();
            $('#open').click();
        });
    });
<?php //блок зчитування інформації для меню секретарів
        $main_user_id = $this->session->userdata('main_user_id');//id попереднього юзера(формується в ajax.php/change_user()
        $main_user_name = $this->session->userdata('main_user_name');
        $user_id = $this->session->userdata('user_id');
         $zast_user = $this->session->userdata('zast_user');
        //кінець блоку ?>
</script>
<body style="height: 100%;">
<div style="height:50px;text-align:center;margin-right:204px;">
	<div id="logo">Optimus</div>
	<br/>
    <div class="search-select"><span class="field">
                <label class="seltext" for="open"><?echo $this->session->userdata('user_name');?></label>
                     </span><input id="open" type="checkbox" />
        <ul class="select_menu hide" >
            <?php if($main_user_id != $user_id){
                echo '<li data-id="'.$main_user_id.'">'.$main_user_name.' ('.$this->session->userdata('main_user_login').')</li>';
            }?>
            <?php
            if(isset($zast_user)){
            foreach($zast_user as $z){
               echo '<li data-id="'.$z['user_id'].'">'.$z['name'].' (us'.$z['tab_nomer'].')</li>';
            }
            }?>

        </ul>
    </div>
	<div id="menu">
        <ul>
		    <li id="active"><a class="light" href="<? echo site_url("main/index") ?>">Поточний тиждень</a></li>
		    <li><a class="light" href="<? echo site_url("main/plan_next") ?>">Наступний тиждень</a></li>
		    <li><a class="light" href="<? echo site_url("main/year_plan") ?>">Річний план</a></li>
		    <li><?php if($this->session->userdata('permission') == 1) { echo '<a class="light" href="'.site_url("admin/plans").'"><strong>Інтерфейс керівника</strong></a>'; } ?></li>
        </ul>
    </div>
</div>



