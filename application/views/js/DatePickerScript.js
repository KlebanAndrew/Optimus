$(document).ready(function() {

	$(".DatePicker").datepicker({
		//dateFormat: "yy-mm-dd",
		dateFormat: "dd.mm.yy",
		changeYear: true,
        changeMonth:true,
		yearRange: '-1:+1',
		beforeShow: function(input, inst) {
			$(".ui-datepicker").css("font-size", "0.9em");
		},
		showOtherMonths: true,
		selectOtherMonths: true,
		firstDay: 0
	});
	$('.ui-datepicker-calendar tr').live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); });
	$('.ui-datepicker-calendar tr').live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });

	$(".DatePicker").datepicker("setDate", "+0d");
	
	$(".DatePicker").datepicker().change(function() {
		host = window.location.protocol+"//"+window.location.host;
		jQuery.ajax({
			type: "POST",
			url: host+"/optimus_test/ajax/ajax_form/",
			cache: false,
			async: true,
			data: "date=" + $(".DatePicker").val(),
			dataType: "html",
			success: function(data){
                console.log(data);
				//$('#load').css('visibility','hidden');
				$('.adminlist tbody').remove();
				$('.adminlist tfoot').remove();
				$('.adminlist').append(data);
				$('#but_1').css('visibility','hidden');
				$('#but_2').css('visibility','hidden');
				$('#but_3').css('visibility','hidden');
				$('#but_4').css('visibility','visible');				
				$('h1').text("Мої плани ("+$("#title_dates").val()+")");
				$('input[name=period]').val($(".DatePicker").val());
				$('.notification, .success').hide("slow");
				zag_chas();
			}
		});
    });

	zag_chas();
});

function zag_chas() {
    zag_summ_plan =0;
    zag_summ_fakt =0;
    chas_summ = 0;
	f_chas_summ = 0;
	$('.chas_plan').each(function(){
		chas = parseFloat($(this).text().replace(",","."));
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('.f_chas_plan').each(function(){
		f_chas = parseFloat($(this).text().replace(",","."));
		if($(this).text() == '') { f_chas = 0; }
		f_chas_summ += f_chas;
	});
	$('#zag_chas_plan').text($('#zag_chas_plan').text()+chas_summ+" год");
    $('#zag_chas_plan_fakt').text($('#zag_chas_plan_fakt').text()+f_chas_summ+" год");

	     zag_summ_plan = zag_summ_plan +chas_summ;
        zag_summ_fakt = zag_summ_fakt +f_chas_summ;

	chas_summ = 0;
	f_chas_summ = 0;
	$('.chas_pot').each(function(){
		chas = parseFloat($(this).text().replace(",","."));
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('.f_chas_pot').each(function(){
		f_chas = parseFloat($(this).text().replace(",","."));
		if($(this).text() == '') { f_chas = 0; }
		f_chas_summ += f_chas;
	});
	$('#zag_chas_pot').text($('#zag_chas_pot').text()+chas_summ+" год");
    $('#zag_chas_pot_fakt').text($('#zag_chas_pot_fakt').text()+f_chas_summ+" год");

         zag_summ_plan = zag_summ_plan +chas_summ;
        zag_summ_fakt = zag_summ_fakt +f_chas_summ;

	chas_summ = 0;
	f_chas_summ = 0;
	$('.chas_poz').each(function(){
		chas = parseFloat($(this).text().replace(",","."));
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('.f_chas_poz').each(function(){
		f_chas = parseFloat($(this).text().replace(",","."));
		if($(this).text() == '') { f_chas = 0; }
		f_chas_summ += f_chas;
	});

    zag_summ_plan = zag_summ_plan +chas_summ;
    zag_summ_fakt = zag_summ_fakt +f_chas_summ;

	$('#zag_chas_poz').text($('#zag_chas_poz').text()+chas_summ+" год");
    $('#zag_chas_poz_fakt').text($('#zag_chas_poz_fakt').text()+f_chas_summ+" год");

    $('#zag_chas').text($('#zag_chas').text()+zag_summ_plan+"год");
    $('#zag_chas_fakt').text($('#zag_chas_fakt').text()+zag_summ_fakt+"год");
}

