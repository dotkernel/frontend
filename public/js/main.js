$( document ).ready(function() {
    
	// sidebar menu slowly slide on mobile
	$( window ).resize(function() {
		if ($(window).width() > 767)
		{
			$(".sidebar-container > .sidebar").css("display", "block");
		}
	});
	$(".sidebar-menu-header").on("click", function(){
    	$(".sidebar-container > .sidebar").slideToggle( "slow", function() {
    		// Animation complete.
    	});
    });
	
});