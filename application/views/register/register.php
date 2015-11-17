<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Реєстрація в системі планування</title>
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
		<a href="<? echo site_url("main/index") ?>"><img src="<? echo base_url() ?>application/views/register/img/logo.png" alt="Головна" /></a>
	</div>
	<div id="content">
		<h2>Реєстрація в системі планування</h2>
		<a href="<? echo site_url("main/index") ?>">Повернутись</a>
		<div id="mes_1">Крок 1 - введіть дані входу для Windows</div>        

		<?php $attributes = array('id' => 'modalform'); echo form_open('register/do_registered', $attributes); ?>

		<div class="row">
			<label>Логін Windows</label>
			<input type="text" name="login_w" class="text" />
		</div>
		<div class="row">
			<label>Пароль Windows</label>
			<input type="password" name="pass_w" class="text" />&nbsp;&nbsp;&nbsp;<b><a id="chek_user" style="cursor: pointer;">Увійти</a></b>
			<img src="<? echo base_url() ?>application/views/front/files/load.gif" id="load_1" style="visibility: hidden" />
		</div>

		<div id="reg_form">
			<div class="row">
				<label>П.І.Б.</label>
				<input name="user" type="text" value="" maxlength="3" style="border:1px solid red;" /><br />
				<div id="search">
					<a onClick="loadUser()">Знайти</a>
				</div>
			</div>
			<div class="row">
				<label>Посада</label>
				<input name="posada" type="text" value="" readonly="readonly" />
			</div>
			<div class="row">
				<label>Опис</label>
				<input name="description" type="text" value="" readonly="readonly" />
			</div>
			<div class="row">
				<label>Табельний номер</label>
				<input name="tab_no" type="text" value="" readonly="readonly" />
			</div>
			<div class="row">
				<label>Номер дебітора</label>
				<input name="deb_no" type="text" value="" readonly="readonly" />
			</div>
			<div class="row">
				<label>Телефон</label>
				<input name="tel" type="text" value="" />
			</div>
			<div class="row">
				<label>Email</label>
				<input name="email" type="text" value="" readonly="readonly" />
			</div>
			<p><a href="#" id="do_reg" class="ui-state-default ui-corner-all" style="visibility: hidden">Зареєструватись!</a></p>
		</div>
		</form>      

		<div id="mes_2">Крок 2 - введіть перші 3 літери свого прізвища, та натисніть зсилку знайти</div>	


		
		
<!-- ui-dialog 2 -->
<div id="dialog2" title="Результати пошуку">
	<div id="personSearch_loader">
		<div>Йде пошук даних</div>
		<div>
			<img src="<? echo base_url() ?>application/views/register/img/loader.gif" alt="завантаження даних"/>
		</div>
	</div>
<div id="rez_search" style="height:250px"></div> 
</div>		
		
		
		
		
	</div>
	<div id="footer">
		<a href="<? echo site_url("main/index") ?>">На головну</a>
		<!--<a class="copy" href="#"><span>© 2013 « <strong>phpist</strong>»</span>
		<em>Design</em>-->
		</a>
	</div>
</div>

</body>
</html>