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
<link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/css/all.css"/>
<!--[if IE]><link rel="stylesheet" type="text/css" href="css/ie.css" media="screen"/><![endif]-->
<link rel="stylesheet" href="<? echo base_url() ?>application/views/front/stylesheet.css" type="text/css" />
<link rel="stylesheet" href="<? echo base_url() ?>application/views/front/css/popup_windows.css" type="text/css" />
<link rel="stylesheet" href="<? echo base_url() ?>application/views/front/css/scrollable.css" type="text/css" />
<!--script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script --> 
<!--script type="text/javascript" src="<? echo base_url() ?>application/views/front/js/jquery-1.5.min.js"></script-->
<script type="text/javascript" src="<? echo base_url() ?>application/views/front/js/jquery.tools.min3.js"></script>
<script type="text/javascript" src="<? echo base_url() ?>application/views/front/js/jquery.jshowoff.min.js"></script>
<script src="<? echo base_url() ?>application/views/front/js/jquery-ui-1.8.5.custom.min.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(){
	$("#navigator").scrollable().navigator({navi:'.pagination'});
	$('#features').jshowoff();
	$(".litebox-all").draggable({ containment: 'parent' });
// берем перший в списку продукт, підставляєм справа
	$('.id_produkt').html($(".slide_menu > li:first").children('input[name="id"]').attr('value'));					// id			
	$('.product-left > h2').html($(".slide_menu > li:first").children('input[name="name"]').attr('value'));			// назва
	$('.prod-detail > .img-box > img').attr('src', $(".slide_menu > li:first").children('input[name="images"]').attr('value'));	// картинка
	$('.prod-detail > p').html($(".slide_menu > li:first").children('input[name="descript"]').attr('value'));		// описание
	$('.prod-content').html("<strong>Состав:</strong> "+$(".slide_menu > li:first").children('input[name="sostav"]').attr('value'));			// состав
	$('.w-num:first').html($(".slide_menu > li:first").children('input[name="vaga_1"]').attr('value'));				// вага 1
	$('.w-num:last').html($(".slide_menu > li:first").children('input[name="vaga_2"]').attr('value'));				// вага 2 (якщо є)
	$('#cena1').html($(".slide_menu > li:first").children('input[name="cena_1"]').attr('value')+' <font size="3px">руб.</font>');			// ціна 1
	$('#cena2').html($(".slide_menu > li:first").children('input[name="cena_2"]').attr('value')+' <font size="3px">руб.</font>');			// ціна 2 (якщо є)
	$('.diametr').remove();																							// додаткові властивості
	$('.info-left:first').prepend($(".slide_menu > li:first").children('input[name="dop_svoystvo_1"]').attr('value'));
	$('.info-left:last').prepend($(".slide_menu > li:first").children('input[name="dop_svoystvo_2"]').attr('value'));
	if($('input[name="cena_2"]').attr('value') == '0') {															// якшо ціни 2 немає
		$(".prod-detail > ul > li:last").hide();
	} else { $(".prod-detail > ul > li:last").show(); }	
	big_images=$('input[name="big_images"]').attr('value');
//--

	var Val;
	var cena_nomer;

	$('#add_1').click(function(){
		$('.popup_window2').fadeIn();
		cena_nomer=1;
	});

	$('#add_2').click(function(){
		$('.popup_window2').fadeIn();
		cena_nomer=2;
	});
	
	$('#y_ord').click(function(){
		$('.popup_window2').fadeOut();
		$(".order").css("visibility", "visible");
		
		var id_produkt = parseInt($('#add_'+cena_nomer).children('div').text());
		var Val = parseInt($('#cena'+cena_nomer).html());
		var Sum = parseInt($('#sum').text());
		$('#sum').html(Sum+Val);
		
		$.ajax({
			type: "GET",
			url: "<? echo site_url("main/set_session") ?>/"+id_produkt+"/"+Val,
			dataType: "html",
			async:false,
			success: function(msg){
				//alert(msg);
			}
		});
		set_suma_zakaza(Sum+Val);
		
	});
	$('#n_ord').click(function(){
		$('.popup_window2').fadeOut();
	});
	
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

// клік на превюшці продукту
	$(".slide_menu > li").click(function(){
		$('.id_produkt').html($(this).children('input[name="id"]').attr('value'));					// id			
		$('.product-left > h2').html($(this).children('input[name="name"]').attr('value'));			// назва
		$('.prod-detail > .img-box > img').attr('src', $(this).children('input[name="images"]').attr('value'));	// картинка
		$('.prod-detail > p').html($(this).children('input[name="descript"]').attr('value'));		// описание
		$('.prod-content').html("<strong>Состав:</strong> "+$(this).children('input[name="sostav"]').attr('value'));			// состав
		$('.w-num:first').html($(this).children('input[name="vaga_1"]').attr('value'));				// вага 1
		$('.w-num:last').html($(this).children('input[name="vaga_2"]').attr('value'));				// вага 2 (якщо є)
		$('#cena1').html($(this).children('input[name="cena_1"]').attr('value')+' <font size="3px">руб.</font>');			// ціна 1
		$('#cena2').html($(this).children('input[name="cena_2"]').attr('value')+' <font size="3px">руб.</font>');			// ціна 2 (якщо є)
		$('.diametr').remove();																		// додаткові властивості
		$('.info-left:first').prepend($(this).children('input[name="dop_svoystvo_1"]').attr('value'));
		$('.info-left:last').prepend($(this).children('input[name="dop_svoystvo_2"]').attr('value'));
		if($(this).children('input[name="cena_2"]').attr('value') == '0') {							// якшо ціни 2 немає
			$(".prod-detail > ul > li:last").hide();
		} else { $(".prod-detail > ul > li:last").show(); }
		big_images=$(this).children('input[name="big_images"]').attr('value');
		
	});

// клік для збільшення малюнку
	$(".img-box").click(function(){
		//alert(big_images);
		if(big_images) {
		//window.open('/images/produkt/'+big_images, 'contacts', 'location,width=800,height=600,top=50, left=400'); popupWin.focus(); return false;
		window.open('<? echo base_url() ?>/images/produkt/'+big_images,'gener','width=800,height=600,top='+((screen.height-600)/2)+',left='+((screen.width-800)/2)+',toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no'); popupWin.focus(); return false;
		} else { alert('Нет большого изображения !'); }
	});
	
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
						<?php echo @$this->tel; ?>
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
<?php
$files = scandir(realpath(APPPATH."../images/slide"));
for ($i=1; $i<=count($files)-1; $i++) { if ($i==1) { continue; } echo '<div><img src="'.base_url().'images/slide/'.$files[$i].'" alt="" /></div>'; }
?>	
					</div>
				</div>
			</div>
			<div class="content">
				<article>
					<ul class="breadcrambs">
						<?php
						if(uri_string() != "main/category/5") { echo '<li><a href="'.site_url("main/index").'">Меню</a></li>'; }
						if(uri_string() == "main/category/5") { echo '<li><a href="'.site_url("main/for_kids").'">Для детей</a></li>'; } ?>
						<li><span><?php echo @$main[0]['title_cat']; ?></span></li>
					</ul>
					<h1><span><?php echo @$main[0]['title_cat']; ?></span></h1>
					<section class="products clearfix">
						<div class="product-left">
							<h2>Пицца Пепперони</h2>
							<div class="prod-detail-border">
								<div class="prod-detail">
									<div class="img-box">
										<img src="<? echo base_url() ?>application/views/front/images/prod-detail.png" alt="" width="160px" height="160px" />					
									</div>
									<p>Необычная пицца</p>
									<div class="prod-content"><strong>Состав:</strong> пицца-соус, сыр Моцарелла, болгарский перец, шампиньоны, пепперони, зелень.</div>
									<ul>
										<li>
											<div class="info-left">
												<div class="diametr">
													<span>Диаметр (см):</span>
													<div class="d-num">35</div>
												</div>
												<div class="price">
													<span>Стоимость:</span>
													<div class="p-num"><font id="cena1">750 руб.</font></div>
												</div>
											</div>
											<div class="info-right">
												<div class="weight">
													<span>Вес (г):</span>
													<div class="w-num">1,015</div>
												</div>
												<div class="add" id="add_1"><a>Добавить<br /> в список доставки</a><div class="id_produkt" style="display:none">1</div></div>
											</div>
										</li>
										<li>
											<div class="info-left">
												<div class="diametr">
													<span>Диаметр (см):</span>
													<div class="d-num">45</div>
												</div>
												<div class="price">
													<span>Стоимость:</span>
													<div class="p-num"><font id="cena2">850 руб.</font></div>
												</div>
											</div>
											<div class="info-right">
												<div class="weight">
													<span>Вес (г):</span>
													<div class="w-num">1,785</div>
												</div>
												<div class="add" id="add_2"><a>Добавить<br /> в список доставки</a><div class="id_produkt" style="display:none">1</div></div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="scroller">
							<div class="pagination">
								<!--<a class="active"  href="#">1</a>
								<a  href="#">2</a>
								<a  href="#">3</a>
								<a  href="#">4</a>-->
							<?php 
							// розбивка на сторінки (по 6 картинок)
								$count_pages=ceil(count($main)/6); // кількість сторінок (завкруглення до більшого
								$pag=1;
								while($pag<=$count_pages) {
									if($pag==1) {
									echo '<a class="active" href="#">'.$pag.'</a>
									';
									} else {
									echo '<a href="#">'.$pag.'</a>
									';
									}
									$pag++;
								}
							?>	
							</div>
							<div class="scrollable" id="navigator">
								<div class="items">
									<div>
							<ul class="menu clearfix slide_menu" >
<?php
$cnt=0;
for($i=0; $i<count($main); $i++) {
$cnt++;	
echo '							<li>
								<input type="hidden" name="id" value="'.$main[$i]['id'].'" />
								<input type="hidden" name="name" value="'.$main[$i]['name'].'" />
								<input type="hidden" name="images" value="'.base_url().'images/produkt/'.$main[$i]['images'].'" />
								<input type="hidden" name="descript" value="'.$main[$i]['descript'].'" />
								<input type="hidden" name="sostav" value="'.$main[$i]['sostav'].'" />
								<input type="hidden" name="vaga_1" value="'.$main[$i]['vaga_1'].'" />
								<input type="hidden" name="vaga_2" value="'.$main[$i]['vaga_2'].'" />
								<input type="hidden" name="cena_1" value="'.$main[$i]['cena_1'].'" />
								<input type="hidden" name="cena_2" value="'.$main[$i]['cena_2'].'" />
								<input type="hidden" name="dop_svoystvo_1" value="'.$main[$i]['dop_svoystvo_1'].'" />
								<input type="hidden" name="dop_svoystvo_2" value="'.$main[$i]['dop_svoystvo_2'].'" />
								<input type="hidden" name="big_images" value="'.$main[$i]['big_images'].'" />									
									<a><img src="'.base_url().'images/produkt/'.$main[$i]['images'].'" alt="" /></a>
									<h3><a>«'.$main[$i]['name'].'»</a></h3>
								</li>';
if($cnt==6 and $i<count($main)-1) {		// умова "and $i<count($main)-1" для того щоб не появлялась пуста листалка коли 6, 12, 18.. продуктів
echo '							</ul>
									</div>
									<div>
										<ul class="menu clearfix slide_menu">';
$cnt=0;
}		
}
?>								
							</ul>
								</div>
								</div>
								</div>
							
							
							<a class="prew prev "><img src="<? echo base_url() ?>application/views/front/images/prew.png" alt="" /></a>
							<a class="next "><img src="<? echo base_url() ?>application/views/front/images/next.png" alt="" /></a>
						</div>
					</section>
				</article>
			</div>
<?php $this->load->view('front/footer'); ?>