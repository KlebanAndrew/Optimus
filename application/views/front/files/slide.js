$(document).ready(function() {
	/* slide and show */
	$(".show-info").click(function() {	
		if ($(this).hasClass("active"))
		{
			$(".show-info-block").fadeOut(200).removeClass("active");
			$(this).parents(".block").removeClass("active").animate({width:'100%'});
			$(this).removeClass("active");
		
		} else {
			$(".show-info").removeClass("active");
			$(this).addClass("active");
			$(".bubble-arrow").css("top",$(this).position().top);
			
			if ($(this).parents(".block").hasClass("active"))
			{
				alert("Ajax запроса информации");
			} else {
				var sizeSlide = $(".show-info-block").width()+20;				
				var thisOffset = $(this).parents(".block").offset();		
				$(this).parents(".block").animate({width:'-='+sizeSlide}, 200, function() {			
					$(".show-info-block").css("top",thisOffset.top).fadeIn(400).addClass("active");
				}).addClass("active");	
			}	
		}			
	});

	/* change slides */
	$(".show-info-tabs li").click(function() {
		var numTab = $(this).index();
		$(".show-info-tabs li, .show-info-levels li").removeClass("active");
		$(this).addClass("active");
		$(".show-info-levels li:eq("+numTab+")").addClass("active");
	});
});