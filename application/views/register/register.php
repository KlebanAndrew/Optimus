<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>��������� � ������ ����������</title>
	<meta charset="UTF-8" />
	<link href="<? echo base_url() ?>application/views/register/style.css" rel="stylesheet" />
	<link href="<? echo base_url() ?>application/views/register/register.css" rel="stylesheet" />	
	<link type="text/css" href="<? echo base_url() ?>application/views/front/files/modal_window/yellow.css" rel="stylesheet" />	

	<script type="text/javascript" src="<? echo base_url() ?>application/views/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/register/my_register.js"></script>
</head>
<body>

<div id="main">
	<div id="header">
		<a href="<? echo site_url("main/index") ?>"><img src="<? echo base_url() ?>application/views/register/img/logo.png" alt="�������" /></a>
	</div>
	<div id="content">
		<h2>��������� � ������ ����������</h2>
		<a href="<? echo site_url("main/index") ?>">�����������</a>
		<div id="mes_1">���� 1 - ������ ��� ����� ��� Windows</div>        

		<?php $attributes = array('id' => 'modalform'); echo form_open('register/do_registered', $attributes); ?>

		<div class="row">
			<label>���� Windows</label>
			<input type="text" name="login_w" class="text" />
		</div>
		<div class="row">
			<label>������ Windows</label>
			<input type="password" name="pass_w" class="text" />&nbsp;&nbsp;&nbsp;<b><a id="chek_user" style="cursor: pointer;">�����</a></b>
			<img src="<? echo base_url() ?>application/views/front/files/load.gif" id="load_1" style="visibility: hidden" />
		</div>

		<div id="reg_form">
			<div class="row">
				<label>�.�.�.</label>
				<input name="user" type="text" value="" maxlength="3" style="border:1px solid red;" /><br />
				<div id="search">
					<a onClick="loadUser()">������</a>
				</div>
			</div>
			<div class="row">
				<label>������</label>
				<input name="posada" type="text" value="" readonly="readonly" />
			</div>
			<div class="row">
				<label>����</label>
				<input name="description" type="text" value="" readonly="readonly" />
			</div>
			<div class="row">
				<label>��������� �����</label>
				<input name="tab_no" type="text" value="" readonly="readonly" />
			</div>
			<div class="row">
				<label>����� �������</label>
				<input name="deb_no" type="text" value="" readonly="readonly" />
			</div>
			<div class="row">
				<label>�������</label>
				<input name="tel" type="text" value="" />
			</div>
			<div class="row">
				<label>Email</label>
				<input name="email" type="text" value="" readonly="readonly" />
			</div>
			<p><a href="#" id="do_reg" class="ui-state-default ui-corner-all" style="visibility: hidden">��������������!</a></p>
		</div>
		</form>      

		<div id="mes_2">���� 2 - ������ ����� 3 ����� ����� �������, �� �������� ������ ������</div>	


		
		
<!-- ui-dialog 2 -->
<div id="dialog2" title="���������� ������">
	<div id="personSearch_loader">
		<div>��� ����� �����</div>
		<div>
			<img src="<? echo base_url() ?>application/views/register/img/loader.gif" alt="������������ �����"/>
		</div>
	</div>
<div id="rez_search" style="height:250px"></div> 
</div>		
		
		
		
		
	</div>
	<div id="footer">
		<a href="<? echo site_url("main/index") ?>">�� �������</a>
		<!--<a class="copy" href="#"><span>� 2013 � <strong>phpist</strong>�</span>
		<em>Design</em>-->
		</a>
	</div>
</div>

</body>
</html>