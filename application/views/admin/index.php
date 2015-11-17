<?php $this->load->view('admin/header'); ?>

<div id="container">
	<div style="border-bottom: solid 1px #D0D0D0;">
		<div id="zagolovok">Структура дирекції</div>
		<?php $this->load->view('admin/menu'); ?>
	</div><br/>
	<div id="body">

		<div id="addUsers">
			<form  action="<? echo site_url("admin/addDerevoUsers")?>" method="POST">
				<p><select name="parentUsers" id="parentUsers">
					<option>Керівник</option>
					<?php
						foreach($allUsers as $row){
							echo "<option value=".$row->id.">".$row->name."</option>";
						}
					?>
				</select></p>
				<p><select name="Users" id="Users">
					<option>Підлеглий</option>
				</select></p>
				<!--<input type="submit" class="button" value="Добавити" />-->
			</form>
		</div>

<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
<script>
	$('#parentUsers').change(function(){
		var parentUsersId = $(this).val();
		$.ajax({
		url: "<? echo site_url("admin/ajaxUsers") ?>",
		type: 'POST',
		data:{"ajaxParentUserId":parentUsersId},
		success: function(data) {
				$('#Users').html(data);
		}	
	});
	});

</script>	
			
			
<?php
	function getSubCustomers($idUserParent, $ret) { 
		$ret .= "<ul>";
		$query = mysql_query("
			SELECT d.id as incr, u.id, u.name FROM derevo d
				JOIN users u ON d.idUser=u.id
			WHERE d.idUserParent = $idUserParent
		") or die("function view_tree ERROR DATABASE");
		while ($row = mysql_fetch_array($query)) {
			//$ret.='<li id="'.$row['id'].'">'.$row['name'].'(<a href="'.site_url("admin/delete_from_derevo/".$row['incr']).'">del</a>)</li>';
			$ret.='<li id="'.$row['id'].'">'.$row['name'].'</li>';
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
			$ret.="<li id=".$row['id'].">".$row['name']."</li>";
			$ret .= str_replace("<ul></ul>", "", getSubCustomers($idUser, ""));
		}
		return $ret."</ul>";	
	}
	echo getParentCustomer(1);
?>			
			



	</div>

<?php $this->load->view('admin/footer'); ?>