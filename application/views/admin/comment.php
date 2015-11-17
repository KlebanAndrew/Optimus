<?php $this->load->view('admin/header'); ?>
		
	  <td id="zcenter">
		<span id="dle-info"></span>

		<div id="dle-content">
			<h3>Отзывы</h3>
			
			<div id="example" class="flora">
<!-- Main content -->
			

<table width='100%' border='1' cellpadding='0' cellspacing='0'>
  <tr>
    <td width="2%" align="center"><b>№</b></td>
    <td width="5%" align="center"><b>Дата</b></td>
    <td width="15%" align="center"><b>Имя</b></td>
    <td width="15%" align="center"><b>email</b></td>
    <td width="50%" align="center"><b>Коментарий</b></td>
    <td width="5%" align="center"><b>Опубликовано</b></td>
    <td width="5%" align="center"><b>Удалить</b></td>
  </tr>

<?php
for($i=0; $i<count($main); $i++) {
	echo 
	"<tr>
    <td align='center'>".$main[$i]['id']."</td>
    <td align='center'>".$main[$i]['date']."</td>
    <td>".$main[$i]['name']."</td>
    <td>".$main[$i]['email']."</td>
    <td>".$main[$i]['text']."</td>";
	if($main[$i]['public']==1) { echo "<td align='center'><a href='".site_url("admin/deactevate_comment/".$main[$i]['id'])."'><img border='0' src='".base_url()."application/views/admin/files/check3.gif' /></a></td>"; }
	else { echo "<td align='center'><a href='".site_url("admin/deactevate_comment/".$main[$i]['id'])."'><img border='0' src='".base_url()."application/views/admin/files/delete3.png' /></a></td>"; }
	echo "<td align='center'><a href='".site_url("admin/del_comment/".$main[$i]['id'])."'><img border='0' src='".base_url()."application/views/admin/files/publish0.png' /></a></td>
	</tr>";
}	
?>	
	

</table>
		
					
			
<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>