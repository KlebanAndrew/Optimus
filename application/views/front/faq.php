<?php $this->load->view('front/header'); ?>

<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

$('#one').click(function(){
	if ($(".one").is(":hidden")) {
		$(".one").show("slow");
		$(".two").hide();
		$(".tree").hide();
		$(".four").hide();
    } else {
        $(".one").hide();
    }
});

$('#two').click(function(){
	if ($(".two").is(":hidden")) {
		$(".two").show("slow");
		$(".one").hide();
		$(".tree").hide();
		$(".four").hide();
    } else {
        $(".two").hide();
    }
});

$('#tree').click(function(){
	if ($(".tree").is(":hidden")) {
		$(".tree").show("slow");
		$(".one").hide();
		$(".two").hide();
		$(".four").hide();
    } else {
        $(".tree").hide();
    }
});

$('#four').click(function(){
	if ($(".four").is(":hidden")) {
		$(".four").show("slow");
		$(".one").hide();
		$(".two").hide();
		$(".tree").hide();
    } else {
        $(".four").hide();
    }
});

function hid_all() {
$(".one").hide();
$(".two").hide();
$(".tree").hide();
$(".four").hide();

}

hid_all();
});
</script>






<div id="container">
	<h1>Справка</h1>
	<div id="body">


	<div style="height:400px; margin:50px;">
		
<a href="#" id="one">Я вперше заповнюю плани</a><br />
<div class="one">		
<p style="margin-left: 20px;">1. Заповніть план на наступний тиждень вкладка "<a href="<? echo site_url("main/plan_next") ?>">Планування</a>", поточними завданнями</p>
<p style="margin-left: 20px;">2. Натисніть кнопку &quot;Затвердити план&quot;</p>
<p style="margin-left: 20px;">3. Після затвердження плану вашим керівником з'явиться повідомлення що план затверджено</p>
</div>

<a href="#" id="two">Моє планування затведжене, минулого тижня, що далі?</a><br />
<div class="two">		
<p style="margin-left: 20px;">1. Вкладку "<a href="<? echo site_url("main/index") ?>">Поточний тиждень</a>" ви дозаповнюєте на протязі тижня позачерговими завданнями</p>
<p style="margin-left: 20px;">2. В пятницю ви звітуєтесь відповідною кнопкою (проставляєте дату факт і час факт)</p>
<p style="margin-left: 20px;">3. Також в пятницю плануєтесь на наступний тиждень (вкладка "<a href="<? echo site_url("main/plan_next") ?>">Планування</a>")</p>
</div>

<a href="#" id="tree">Як заповнювати річний план і що таке деталізація</a><br />
<div class="tree">		
<p style="margin-left: 20px;">1. Річний план заповнюється завданнями які потім падають в планові роботи</p>
<p style="margin-left: 20px;">2. При створенні завдання необхідно вказати № процесу, задіяних працівників, та період.</p>
<p style="margin-left: 20px;">3. Після створення завдання, коли воно попало в категорію "планові", кожного тижня є можливість вносити деталізацію. Для цього натисніть на завдання, внесіть назву роботи, час план, та оберіть потрібний тиждень (дати понеділка-пятниці проставляються автоматично).</p>
</div>

<a href="#" id="four">В мене кожного тижня є однакові завдання, як автоматизувати?</a><br />
<div class="four">		
<p style="margin-left: 20px;">1. При створенні поточного завдання натисніть кнопку "Повторювати завдання"</p>
<p style="margin-left: 20px;">2. У випадаючому вікні оберіть кінцеву дату повторення.</p>
<p style="margin-left: 20px;">3. Після створення завдання, воно автоматично продублюється в наступних тижнях до вказаного вами періоду. Повторювати можна тільки поточне завдання.</p>
</div>


	</div>
	
	
	

	</div>

<?php $this->load->view('front/footer'); ?>