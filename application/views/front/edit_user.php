<?php $this->load->view('front/header'); ?>
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />

<div id="container">
	<h1>�������</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/edit_user").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">�����</a></div>
	<div id="body">

<form action="<? echo site_url("main/user_update") ?>" method="post">
<div class="perDiv">
	<table class="perTab">
		<tr>
			<td>ϲ�:</td>
			<td><? echo $user->name; ?></td>
		</tr>
		<tr>
			<td>�������:</td>
			<td><? echo $user->tel; ?></td>
		</tr>
		<tr>
			<td>���. �����:</td>
			<td><? echo $user->tab_nomer; ?></td>
		</tr>
		<tr>
			<td>ϳ������:</td>
			<td><? echo $user->description; ?></td>
		</tr>
		<tr>
			<td>������:</td>
			<td><? echo $user->posada; ?></td>
		</tr>
		<tr>
			<td>������� ������:</td>
			<td><input type="text" name="product_posadu" value="<? echo $user->product_posadu; ?>" class="form-control" /></td>
		</tr>
</table>
	<div style="height: 30px;margin: 0px 20px 0px 20px;text-align:center;">
		<input type="submit" class="but orange" style="float:none;" value="��������" />
	</div>	  
</div>	
</form>	

	</div>

<?php $this->load->view('front/footer'); ?>