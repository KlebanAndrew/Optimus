<?php $this->load->view('admin/header'); ?>



	
	<script src="<? echo base_url() ?>application/views/js/tooltip/jquery-1.8.3.min.js"></script>
	<script src="<? echo base_url() ?>application/views/js/tooltip/jQuery.pTooltip.js"></script>	
	<script src="<? echo base_url() ?>application/views/js/tooltip/jquery-ui.min.js"></script>	
	<link type="text/css" href="<? echo base_url() ?>application/views/js/tooltip/jquery-ui.css" rel="stylesheet" />
	<script>
$(document).ready(function() {
    //Активний елемент меню
    $('#menu ul li').removeAttr("id");
    $('#menu ul li:nth-child(4)').attr("id","active");
    //
function setClickableTooltip(target, content){
    $( target ).tooltip({
        show: null, // show immediately 
        position: { my: "right top", at: "right top" },
        content: content, //from params
        hide: { effect: "" }, //fadeOut
        close: function(event, ui){
            ui.tooltip.hover(
                function () {
                    $(this).stop(true).fadeTo(400, 1); 
                },
                function () {
                    $(this).fadeOut("400", function(){
                        $(this).remove(); 
                    })
                }
            );
        }  
    });
}
//setClickableTooltip('#t1', "some basic content");

<?php
for($i=0; $i<count($users1); $i++) {
	echo "setClickableTooltip('#t".$users1[$i]['id']."', '<a href=\"".site_url("admin/show_status_plan/".$users1[$i]['id'])."\">статуси</a>');";

}
?>


});
</script>





<div id="container">
	<div style="border-bottom: solid 1px #D0D0D0;">
		<div id="zagolovok">Плани працівників</div>
		<?php $this->load->view('admin/menu'); ?>
	</div><br/>
	<div id="body">
<table width="100%" border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td width="50%" valign="top"><h3>Перегляд планів</h3>

<?php
	function getSubCustomers($idUserParent, $ret) { 
		$ret .= "<ul>";
		$query = mysql_query("
			SELECT d.id as incr, u.id, u.name FROM derevo d
				JOIN users u ON d.idUser=u.id
			WHERE d.idUserParent = $idUserParent
		") or die("function view_tree ERROR DATABASE");
		while ($row = mysql_fetch_array($query)) {
			$ret.='<li><a href="'.site_url("admin/show_user_plan_na_zatverd/".$row['id']).'">'.$row['name'].'</a></li>'; //місце для вставки методу показу завдань
			$ret = getSubCustomers($row['id'], $ret);
		}
		return $ret."</ul>";
	}
	function getParentCustomer($idUser){
		$ret = "<ul>";
		$query = mysql_query("
			SELECT u.id, u.name FROM users u WHERE u.id = $idUser
		") or die("function view_tree ERROR DATABASE");
		while ($row = mysql_fetch_array($query)) {
			$ret.="<li>".$row['name']."</li>";
			$ret .= str_replace("<ul></ul>", "", getSubCustomers($idUser, ""));
		}
		return $ret."</ul>";	
	}
	//echo getParentCustomer(41);
	echo getParentCustomer($user_id);
?>		
	</td>
    <td width="25%" valign="top"><h3>Затвердження (поточний тиждень)</h3>
<?php
for($i=0; $i<count($users1); $i++) {
    $status_count = 0; //лічильник для незатверджених статусів попередніх періодів
    //цикл перевірки наявності незатверджених тижневих планів
    foreach($status as $s){
        if($s['user_id'] == $users1[$i]['id']){
            $status_count = $s['kilk'];
        }
    }
    //////////////////////////////////////////////
	if($users1[$i]['kilk'] != 0) {//перевірка на наявність незатверджених завдань(незалежно від статусів планів)
		echo '	
		<div class="notification error" id="t'.$users1[$i]['id'].'" title="">
		<div><a href="'.site_url("admin/show_user_plan_na_zatverd/".$users1[$i]['id']).'">'.$users1[$i]['name'].'</a>&nbsp;&nbsp; 
		Незатверджені плани ('.$users1[$i]['kilk'].')</div>
		</div>';
	} else {
        if($status_count != 0){//перевірка чи є незатверджені тижневі плани
            echo '
		<div class="notification error" id="t'.$users1[$i]['id'].'" title="">
		<div><a href="'.site_url("admin/show_user_plan_na_zatverd/".$users1[$i]['id']).'">'.$users1[$i]['name'].'</a>&nbsp;&nbsp;
		Існують незатверджені плани ('.$status_count.')</div>
		</div>';
        }
        else{//все затверджено
            echo '
            <div class="notification success" id="t'.$users1[$i]['id'].'" title="">
            <div><a href="'.site_url("admin/show_user_plan_na_zatverd/".$users1[$i]['id']).'">'.$users1[$i]['name'].'</a></div>
            </div>';
        }
	}
}
?>	
	</td>
    <td width="25%" valign="top"><h3>Затвердження (наступний тиждень)</h3>
<?php
for($i=0; $i<count($users2); $i++) {
	if($users2[$i]['kilk'] != 0) { 	
		echo '	
		<div class="notification error">
		<div><a href="'.site_url("admin/show_user_plan_na_zatverd_next/".$users2[$i]['id']).'">'.$users2[$i]['name'].'</a>&nbsp;&nbsp; 
		Незатверджені плани ('.$users2[$i]['kilk'].')</div>
		</div>';	
	} else {
		echo '	
		<div class="notification success">
		<div><a href="'.site_url("admin/show_user_plan_na_zatverd_next/".$users2[$i]['id']).'">'.$users2[$i]['name'].'</a></div>
		</div>';	
	}
}
?>	
	</td>
  </tr>
</table>


	
<!--
<div class="notification success">
<a class="close" href="#"><img title="Close" src="<? //echo base_url() ?>application/views/admin/css/cross_grey_small.png"></a>
<div><a href="vbvbvbvbv">Error notification. </a></div>
</div>
-->

	</div>

<?php $this->load->view('admin/footer'); ?>