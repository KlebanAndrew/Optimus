<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Siamo felisi voi</title>
<meta charset=utf-8>
<!--script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script-->
<script>
document.createElement('header');
document.createElement('nav');
document.createElement('section');
document.createElement('article');
document.createElement('aside');
document.createElement('footer');
</script>

<!--GALLERY-->
<link rel="stylesheet" href="<? echo base_url() ?>application/views/front/gallery/css/screen.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<? echo base_url() ?>application/views/front/gallery/css/lightbox.css" type="text/css" media="screen" />
<script src="<? echo base_url() ?>application/views/front/gallery/js/jquery-1.7.2.min.js"></script>
<script src="<? echo base_url() ?>application/views/front/gallery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="<? echo base_url() ?>application/views/front/gallery/js/jquery.smooth-scroll.min.js"></script>
<script src="<? echo base_url() ?>application/views/front/gallery/js/lightbox.js"></script>
<!--GALLERY-->
<link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/css/all.css"/>
<!--[if IE]><link rel="stylesheet" type="text/css" href="css/ie.css" media="screen"/><![endif]-->
<link rel="stylesheet" href="<? echo base_url() ?>application/views/front/stylesheet.css" type="text/css" />
<link rel="stylesheet" href="<? echo base_url() ?>application/views/front/css/popup_windows.css" type="text/css" />
<script type="text/javascript" src="<? echo base_url() ?>application/views/front/js/jquery.jshowoff.min.js"></script>
<script>
$(document).ready(function(){
	$('#features').jshowoff();
	//$(".litebox-all").draggable({ containment: 'parent' });
// клік на корзині	
	$('.order').click(function(){
		$('.popup_window').fadeIn();
		$('#sum_popup').html($('#sum').text());		// при відкритті вікна встановлюємо в нього суму корзини
		$.ajax({
			type: "GET",
			url: "<? echo site_url("main/show_session") ?>",
			dataType: "html",
			success: function(msg){
				//alert(msg);
				$('#litebox > ul').html(msg);
				$('.cancel').click(function(){
					$(this).parent().parent().parent().remove();
				});
			}
		});
	});
	$('.close').click(function(){
		$('.popup_window').fadeOut();
		$('.popup_window2').fadeOut();
	});

// GALLERY
	$('a').smoothScroll({ speed: 1000, 	easing: 'easeInOutCubic' });
	$('.showOlderChanges').on('click', function(e){ $('.changelog .old').slideDown('slow'); $(this).fadeOut(); e.preventDefault(); });

});

function cancel_session(prod_id) {
		var cina = parseInt($('#id_'+prod_id).text());
		var Sum = parseInt($('#sum').text());
		var rez = Sum-cina;
		$('#sum').html(rez);
		if(rez==0){
			$(".order").css("visibility", "hidden");
			$('.popup_window').fadeOut();
		}
		$.ajax({
			type: "GET",
			url: "<? echo site_url("main/cancel_session") ?>"+"/"+prod_id,
			dataType: "html",
			async:false,
			success: function(msg){
				//alert(msg);
			}
		});
		set_suma_zakaza(rez);		
}

// функція для встановлення в сесію загальної суми заказу
function set_suma_zakaza(suma) {
		$.ajax({
			type: "GET",
			url: "<? echo site_url("main/set_suma_zakaza_session") ?>"+"/"+suma,
			dataType: "html",
			success: function(msg){
				//alert(msg);
			}
		});
		var Sum = parseInt($('#sum').text());
		$('#sum_popup').html(Sum);
}

</script>

</head>
<body>
	<div class="top">
		<div class="main">
			<header>
				<div class="logo"><a href="/">Siamo felisi voi - Кафе-пиццерия, традиции вкуса</a></div>
				<div class="footer-content">
					<div class="phones">
						<?php echo $this->model_front->show_tel(); ?>
					</div>
					<div class="call-order">
						<div class="call-me">
							Звоните<br />нам!
						</div>
						<?php if($this->session->userdata('massiv')) { $visibility=''; } else { $visibility='style="visibility: hidden"'; } ?>
						<div class="order" <?php echo $visibility; ?> >
							<a href="#">Стоимость Вашего заказа</a>
							<div class="price">
								<span id="sum"><?php if($this->session->userdata('suma_zakaza')) { echo $this->session->userdata('suma_zakaza'); } else { echo "0"; } ?></span> руб.
							</div>
						</div>
					</div>
				</div>
				<ul>
					<li><a href="<? echo site_url("main/about") ?>">О ресторане</a></li>
					<li><a href="<? echo site_url("main/index") ?>">Меню</a></li>
					<li><a href="<? echo site_url("main/dostavka") ?>">Доставка</a></li>
					<li><a href="<? echo site_url("main/otzivu") ?>">Отзывы</a></li>
					<li><a href="<? echo site_url("main/gallery") ?>">Фотогалерея</a></li>
					<li><a href="<? echo site_url("main/for_kids") ?>">Для детей</a></li>
					<li><a href="<? echo site_url("main/specpredlozhenie") ?>">Спецпредложение</a></li>
					<li><a href="<? echo site_url("main/vacancy") ?>">Вакансии</a></li>
					<li><a href="<? echo site_url("main/contact") ?>">Контакты</a></li>
				</ul>
			</header>
			<div class="slide-rep">
				<div class="slide-bot">
					<div id="features">
						<!-- слайды (то что внутри div и будет слайдом, можно размещать и текст и все что угодно) 
							 только в зависимости от количества слайдов надо регулировать в css ширину .jshowoff p.jshowoff-slidelinks , иначе 
							 навигационные точки могут быть не по центру
						<div><img src="<? //echo base_url() ?>images/slide/slide-1.jpg" alt="" /></div>
						<div><img src="<? //echo base_url() ?>images/slide/slide-2.jpg" alt="" /></div>
						<div><img src="<? //echo base_url() ?>images/slide/slide-3.jpg" alt="" /></div>
						<div><img src="<? //echo base_url() ?>images/slide/slide-4.jpg" alt="" /></div>
						 -->
<?php
$files = scandir(realpath(APPPATH."../images/slide"));
for ($i=1; $i<=count($files)-1; $i++) { if ($i==1) { continue; } echo '<div><img src="'.base_url().'images/slide/'.$files[$i].'" alt="" /></div>'; }
?>
					</div>
				</div>
			</div>
			<div class="content">
				<article>
					<h1><span>Галерея</span></h1>

<div class="section" id="example" style="padding-left:75px">

  <div class="imageRow">
  
<?php
//Повертає список файлів в папці
$files = scandir(realpath(APPPATH."../images/gallery"));
$a=0;
//перебір всіх малюнків в папці
for ($i=1; $i<=count($files)-2; $i++) {
	if ($i==1) {
		continue;
	}
	$a++;
	echo '
      <div class="single">
  		  <a href="'.base_url().'images/gallery/'.$files[$i].'" rel="lightbox[plants]" title=""><img src="'.base_url().'images/gallery/thumbs/'.$files[$i].'" alt="Plants: image 1 0f 4 thumb" /></a>
      </div>';	
}
?>  

  </div>
	
</div>
					
				</article>
			</div>
<?php $this->load->view('front/footer'); ?>