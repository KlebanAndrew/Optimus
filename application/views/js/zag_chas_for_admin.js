function zag_chas() {
	chas_summ = 0;
	$('.chas_plan').each(function(){
		chas = parseFloat($(this).text());
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('#zag_chas_plan').text($('#zag_chas_plan').text()+" ("+chas_summ+" год.)");
	
	chas_summ = 0;
	$('.chas_pot').each(function(){
		//chas = parseInt($(this).text());
		chas = parseFloat($(this).text());
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('#zag_chas_pot').text($('#zag_chas_pot').text()+" ("+chas_summ+" год.)");
	
	chas_summ = 0;
	$('.chas_poz').each(function(){
		//chas = parseInt($(this).text());
		chas = parseFloat($(this).text());
		if($(this).text() == '') { chas = 0; }
		chas_summ += chas;
	});
	$('#zag_chas_poz').text($('#zag_chas_poz').text()+" ("+chas_summ+" год.)");
}