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
  

var message="!";

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
	$("#no_captcha").text('!');
}
else {
	if(val_e=="y") { 
		//$('#modalform').trigger('submit');	// підтвердження форми
		//return false;

		$.ajax({
			type: "POST",
			url: "<? echo site_url("main/ajax_send_from_contact") ?>",
			data: {
			"name":$('input[name=name]').val(),
			"email":$('input[name=email]').val(), 
			"comment":$('textarea#comment').val()
			},
			dataType: "html",
			success: function(html) {
				alert('Письмо отправлено !');
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
					<h1><span><?php echo $main[0]['title'] ?></span></h1>
					<section class="text-section"><?php echo $main[0]['texts'] ?></section>
					<section class="form">
						<?php 
						$attributes = array('id' => 'modalform');
						echo form_open('main/add_comment', $attributes);
						?>					
						<table width="100%" border="0">
							<tr>
							<td>
							<h2>Обратная связь</h2>
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
							</td>
							<td><iframe width="425" height="280" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.ru/?ie=UTF8&amp;t=m&amp;ll=55.920552,37.998501&amp;spn=0.001683,0.00457&amp;z=17&amp;output=embed"></iframe><br /><small><a href="http://maps.google.ru/?ie=UTF8&amp;t=m&amp;ll=55.920552,37.998501&amp;spn=0.001683,0.00457&amp;z=17&amp;source=embed" style="color:#0000FF;text-align:left">Просмотреть увеличенную карту</a></small></td>
							</tr>
						</table>
						</form>
					</section>					
				</article>
			</div>
<?php $this->load->view('front/footer'); ?>