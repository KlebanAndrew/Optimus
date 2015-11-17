$(document).ready(function() {

	$(".DatePicker").datepicker({
		dateFormat: "yy-mm-dd",
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

	zag_chas();
});


function zag_chas() {
    zag_summ_plan =0;
    zag_summ_fakt =0;
    chas_summ = 0;
	f_chas_summ = 0;
	$('.chas_plan').each(function(){
		chas = parseFloat($(this).text());
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('.f_chas_plan').each(function(){
		f_chas = parseFloat($(this).text());
		if($(this).text() == '') { f_chas = 0; }
		f_chas_summ += f_chas;
	});
    zag_summ_plan = zag_summ_plan +chas_summ;
    zag_summ_fakt = zag_summ_fakt +f_chas_summ;
	$('#zag_chas_plan').text($('#zag_chas_plan').text()+chas_summ+" год.");
    $('#zag_chas_plan_fakt').text($('#zag_chas_plan_fakt').text()+f_chas_summ+" год.");
	
	chas_summ = 0;
	f_chas_summ = 0;
	$('.chas_pot').each(function(){
		chas = parseFloat($(this).text());
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('.f_chas_pot').each(function(){
		f_chas = parseFloat($(this).text());
		if($(this).text() == '') { f_chas = 0; }
		f_chas_summ += f_chas;
	});
    zag_summ_plan = zag_summ_plan +chas_summ;
    zag_summ_fakt = zag_summ_fakt +f_chas_summ;
	$('#zag_chas_pot').text($('#zag_chas_pot').text()+chas_summ+" год");
    $('#zag_chas_pot_fakt').text($('#zag_chas_pot_fakt').text()+f_chas_summ+" год");
	
	chas_summ = 0;
	f_chas_summ = 0;
	$('.chas_poz').each(function(){
		chas = parseFloat($(this).text());
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('.f_chas_poz').each(function(){
		f_chas = parseFloat($(this).text());
		if($(this).text() == '') { f_chas = 0; }
		f_chas_summ += f_chas;
	});
    zag_summ_plan = zag_summ_plan +chas_summ;
    zag_summ_fakt = zag_summ_fakt +f_chas_summ;
	$('#zag_chas_poz').text($('#zag_chas_poz').text()+chas_summ+" год.");
    $('#zag_chas_poz_fakt').text($('#zag_chas_poz_fakt').text()+f_chas_summ+" год.");
    $('#zag_chas').text($('#zag_chas').text()+zag_summ_plan+" год");
    $('#zag_chas_fakt').text($('#zag_chas_fakt').text()+zag_summ_fakt+" год");
}

