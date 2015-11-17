<?php $this->load->view('front/header'); ?>
<style>
    #validEmail
    {
    margin-top: 8px;
    margin-left: 5px;
    position: absolute;
    width: 16px;
    height: 16px;
    }
</style>	
	
<script type="text/javascript">
$(document).ready(function(){

var val_e="n";

//$("#validate").keyup(function(){
$("#validate").bind('change click keyup', function(){
    var email = $("#validate").val();
    if(email != 0) {
		if(isValidEmailAddress(email)) {
			$("#validEmail").css({ "background-image": "url('<? echo base_url() ?>application/views/front/images/validyes.png')" });
			val_e="y";
		} else {
			$("#validEmail").css({ "background-image": "url('<? echo base_url() ?>application/views/front/images/validno.png')" });
			val_e="n";
		}
    } else {
		$("#validEmail").css({ "background-image": "none" }); 
    }
});
  
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}
  

var message="Обязательное поле";

$(".send").click(function(){
	$("#no_email").text('');
	$("#no_name").text('');
	$("#no_comment").text('');
	$("#no_captcha").text('');
	
if($("#name").val()=='') {
	$("#no_name").text(message);
} 
if($("#comment").val()=='') {
	$("#no_comment").text(message);
}
if($("#captcha").val()!='<?php echo $captcha_cod; ?>') {
	$("#no_captcha").text('Код не верный');
}
else {
	if(val_e=="y") { 
		//$('#modalform').trigger('submit');	// підтвердження форми
		//return false;

		$.ajax({
			type: "POST",
			url: "<? echo site_url("main/ajax_add_otziv") ?>",
			data: {
			"name":$('input[name=name]').val(),
			"email":$('input[name=email]').val(), 
			"comment":$('textarea#comment').val()
			},
			dataType: "html",
			success: function(html) {
				alert('Ваш отзыв отправлен !');
				document.location.href = "<? echo site_url("main/index") ?>";
			}
		});

		
	}
}

});
  
});
</script>	

			<div class="content">
				<article>
					<h1><span>Отзывы</span></h1>
					<section class="text-section">

<table width="100%" border="0">

<?php
for($i=0; $i<count($main); $i++) {
	echo "<b>".$main[$i]['name']."</b>&nbsp;<font color='#666666'>(".$main[$i]['date'].")</font><br />".$main[$i]['text']."</p><hr noshade size='1' /><br />";
}
?>	

</table>
					</section>
					<section class="form">
						<?php 
						$attributes = array('id' => 'modalform');
						echo form_open('main/add_comment', $attributes);
						?>
							<h2>Добавить отзыв</h2>
							<div class="row">
								<label>Имя <font color="#FF0000">*</font></label>
								<input type="text" name="name" id="name" />&nbsp;&nbsp;&nbsp;<font id="no_name" color="#FF0000"></font>
							</div>
							<div class="row">
								<label>E-mail <font color="#FF0000">*</font></label>
								<input type="text" name="email" id="validate" /><span id="validEmail"></span>
							</div>
							<div class="row">
								<label>Сообщение <font color="#FF0000">*</font></label>
								<textarea cols="20" rows="8" name="text" id="comment"></textarea>&nbsp;&nbsp;&nbsp;<font id="no_comment" color="#FF0000"></font>
							</div>
							<div class="row capcha">
								<label>Введите код</label>
								<? echo $captcha ?>
								<input type="text" id="captcha" value="" />&nbsp;&nbsp;&nbsp;<font id="no_captcha" color="#FF0000"></font>
							</div>
							<div class="send"><input type="button" value="Отправить" /></div>							
						</form>
					</section>
				</article>
			</div>
<?php $this->load->view('front/footer'); ?>