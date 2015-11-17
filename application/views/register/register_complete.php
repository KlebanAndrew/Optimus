<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Реєстрація в системі планування</title>
	<meta charset="UTF-8" />
	<link href="<? echo base_url() ?>application/views/register/style.css" rel="stylesheet" />
	<link href="<? echo base_url() ?>application/views/register/register.css" rel="stylesheet" />
	
	<link href="<? echo base_url() ?>application/views/register/src/jquery.counter-analog.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="<? echo base_url() ?>application/views/register/src/jquery.counter-analog2.css" media="screen" rel="stylesheet" type="text/css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/register/src/jquery.counter.js" type="text/javascript"></script>
</head>
<body>
<div id="main">
	<div id="header">
		<a href="<? echo site_url("main/index") ?>"><img src="<? echo base_url() ?>application/views/register/img/logo.png" alt="Головна" /></a>
	</div>
	<div id="content">
		<h2>Реєстрація успішна!</h2>
		<br>	
		<a href="<? echo site_url("main/index") ?>">Увійдіть</a> в систему під своїм логіном та паролем 
		<!--Система планування в стадії розробки, дякуємо за проходження процедури реєстрації.-->

<div style="width:450px; margin:0 auto; margin-top:80px">
    <h2>Ви будете автоматично перенаправлені через:</h2><br />
	<span style="margin-left:160px;" class="counter counter-analog" data-direction="down" data-format="00:20">00:20</span>
</div>


	</div>
	<div id="footer">
		<a href="<? echo site_url("main/index") ?>">На головну</a>
		<a class="copy" href="#"><span>© 2014 « <strong>phpist</strong>»</span>
		<em>Design</em>
		</a>
	</div>
</div>

<script>
	$('.counter').counter();
	$('.counter').on('counterStop', function() {
		location.href="<?php echo site_url("main/index") ?>";
	});
</script>


</body>
</html>