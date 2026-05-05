document.addEventListener("DOMContentLoaded", function() {

	const 	$ = jQuery.noConflict(),
			page = $('html, body');	

	// Scroll Up button
	$('.scroll-up').click(function(e) {
		page.animate({
			scrollTop : 0,
		}, 100);
		e.preventDefault();
	});

	// Scroll Down button
	$('.scroll-down').click(function(e) {
		const SlideHeight = $('.section__home-hero').height();
		// /console.log(SlideHeight);
		page.animate({
			scrollTop : page.scrollTop() + SlideHeight,
		}, 1000);
		e.preventDefault();
	});

	// Slow scroll #
	$(function() {
		$('.menu-item').on('click', 'a[href*="#"]:not([href="#"])', function() {
			if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
				var target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
				if (target.length) {
					$('html, body').animate({
						scrollTop: target.offset().top - 85
					}, 500);
					return false;
				}
			}
		});
	});
	
});