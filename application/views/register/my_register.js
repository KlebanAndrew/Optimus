host = window.location.protocol+"//"+window.location.host;
var url_reg = host+"/optimus/script/ldap.php";
var url_my = host+"/optimus/script/add_user_from_admin.php";

$(document).ready(function(){
// Ентер на "Увійти"-----------------------------------------------------------
	if ($.browser.mozilla) {
        $('input[name=pass_w]').keypress(function(event) {
            if (event.which || event.keyCode)
                if ((event.which == 13) || (event.keyCode == 13)) {
                    $("#chek_user").click();
            }
            return 0;
        });
    } else {
        $('input[name=pass_w]').keydown(function(event) {
            if (event.which || event.keyCode)
                if ((event.which == 13) || (event.keyCode == 13)) {
                    $("#chek_user").click();
            }
            return 0;
        });
    }

// Опис діалогу----------------------------------------------------------------	
	$('#dialog2').dialog({
		autoOpen: false,
		width: 400,
		height: 550,
		modal: true,
		buttons: {
			"Відмінити": function() { 
				$('input[name=user]').val('');
				$('input[name=posada]').val('');
				$('input[name=email]').val('');
				$('input[name=description]').val('');
				$('input[name=tel]').val('');
				$('input[name=tab_no]').val('');
				$('input[name=login]').val('');
				$('input[name=pass]').val('');
				$(this).dialog("close"); 
			}, 
			"Підтвердити": function() { 
				$(this).dialog("close"); 
				$("#mes_2").css("color", "#090");
			} 
		}	
	});
	
// Перевірка чи зареєстрований юзер--------------------------------------------
	$("#mes_2").hide();
	$("#reg_form").hide();
	$("#do_reg").click(function(){
		$.ajax({
			type: "POST",
			url: url_my,
			data: { "login": $('input[name=login_w]').val()  },
			dataType: "html",
			success: function(html) {
				if(html == 2) { 
					$('#modalform').submit();
				} 
				if(html == 1) { 
					alert('Користувач з таким логіном вже зареєстрований !');
				}
			}
		}); 
	});

// Авторизація через LDAP------------------------------------------------------	
	$("#chek_user").click(function() {
		$("#load_1").css("visibility", "visible");
		$.ajax({
			type: "POST",
			url: url_reg,
			data: {
				"login": $('input[name=login_w]').val(),
				"pass": $('input[name=pass_w]').val()
			},
			dataType: "html",
			success: function(html) {
                alert(html);
				$("#load_1").css("visibility", "hidden");
				if(html == "TRUE") { 
					$("#reg_form").css("visibility", "visible");
					$("#reg_form").show("slow");
					$("#mes_2").show("slow");
					//$('input[name=login_w]').attr('disabled','disabled');
					//$('input[name=pass_w]').attr('disabled','disabled');
					$('input[name=login_w]').attr('readonly', true);
					$('input[name=pass_w]').attr('readonly', true);
					$("#mes_1").css("color", "#090");
					$("#chek_user").remove();
				} else {
					alert('Невірно введені дані');
				}
			}
		}); 
		
	});


});		

// Пошук в SAP-----------------------------------------------------------------	
function loadUser() {
	host = window.location.protocol+"//"+window.location.host;
		$("#personSearch_loader").show();
		$("#rez_search").empty();
		$('#dialog2').dialog('open');
		var user = $('input[name=user]').val();
		$.ajax({
			url: host+"/optimus/script/soap.php",
			type: 'POST',
			data:{"user":user, "task":'search_user'},
			dataType: 'json',
			success: function(data) {			
				if(data.length > 0) {
					for (var i = 0; i < data.length; i++) {
						$("#rez_search").append('<div class="person">'+
                                                    '<div class="name"><b>'+data[i].Family+" "+data[i].Name+" "+data[i].Father+'</b></div>'+
													'<div class="tabno">'+data[i].Tabno+'</div>'+
                                                    '<div class="posada" org_txt="'+data[i].Orgtxt+'" deb_no="'+data[i].Customer+'" tel="'+data[i].TelAts+'" email="'+data[i].Email+'">'+data[i].Jobtxt+'</div>'+													
													'</div>');   
					}
				} else {
					$("#rez_search").append('<div class="emptyResult">Пошук по заданих критеріях не дав результату!</div>');
				}
				
				$(".person").click(function(){
					$('input[name=user]').val($(this).find('.name').text());
					$('input[name=tab_no]').val($(this).find('.tabno').text());
					$('input[name=posada]').val($(this).find('.posada').text());
					$('input[name=description]').val($(this).find('.posada').attr("org_txt"));
					$('input[name=deb_no]').val($(this).find('.posada').attr("deb_no"));					
					$('input[name=tel]').val($(this).find('.posada').attr("tel"));	
					$('input[name=email]').val($(this).find('.posada').attr("email"));						
					//$("#dialog2").dialog("close");
					$(".person").css({color: '#333333'});
					$(this).css({color: 'red'});
					
					$("#do_reg").css("visibility", "visible");
					
				})
				$("#personSearch_loader").hide();
			},
			error: function() {
				$("#rez_search").append('<div class="emptyResult">Помилка отримання даних!</div>');
				$("#personSearch_loader").hide();
			}
		});
}