document.addEventListener("DOMContentLoaded", function() {

	const $ = jQuery.noConflict(),
			win = $(window),
			body_height = $('body .wp-site-blocks').height(),
			win_height = win.height(),
			pageUrl = window.location.href;

	function isTouchDevice(){
		return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
	}

	//$('.wpcf7-select, .acf-form select').select2( { minimumResultsForSearch: 20 } );

	/**
	 * Mega Menu - Sumbenu image items
	 */
	$("li.menu-item-has-children ul.dropdown-menu > li.menu-item-img:first-child").addClass("active");
	$("ul.dropdown-menu li.menu-item-img").bind("mouseover mouseenter", function() {
		$("ul.dropdown-menu li.menu-item-img").removeClass("active");		
		$(this).addClass("active");
	});	
	$("ul.dropdown-menu li.menu-item-img").bind("mouseover", function() {
		$("li.menu-item-has-children ul.dropdown-menu > li.menu-item-img:first-child").addClass("active");
    });

    /**
	 * Mobile Menu Toggle
	 */
	$(document).on('click','.navbar-toggler-btn', function() {
		$('body').toggleClass("menu-open");
	});

	$('.header-nav-overlay').on('click', function() {
		$('.navbar-toggler-btn').trigger('click');
	});
''
	$(window).resize(function () {
		$('.navbar-nav input').prop('checked', false);
	});

	if ($("body").hasClass("menu-open")) {
		$('body').removeClass("menu-open");
	} else {
		$('body').toggleClass("menu-open");
	}

	$('body').removeClass("menu-open");


	$('.header-nav-overlay').on('click', function() {
		$('body').removeClass("menu-open");
	});

	$('.wp-header-menu-container .navbar-nav>.menu-item a').each(function() {
		const target = $(this).attr('href');
		if( pageUrl === target ) {
			$(this).parent('.nav-item').addClass('menu-item-current');
		} else {
			$(this).parent('.nav-item').removeClass('menu-item-current');
		}
	});

	/**
	 * Switcher Dark Mode
	 */
	const darkTheme = "dark-theme";
	const darkThemeSetUp = () => {
		if (getCurrentTheme() === "dark") {
			document.getElementById("toggleBtn").checked = true;
		} else {
			document.getElementById("toggleBtn").checked = false;
		}
	};
	const getCurrentTheme = () => document.body.classList.contains(darkTheme) ? "dark" : "light";
	const selectedTheme = localStorage.getItem("selected-theme");
	if (selectedTheme === "dark") {
		document.body.classList[selectedTheme === "dark" ? "add" : "remove"](
			darkTheme
		);
		darkThemeSetUp();
	}
	const themeButton = document.getElementById("toggleBtn");

	if ($("body").hasClass("light-dark")) {
		// console.log('btn active');
		$(".switcher-darkmode").css("display","block");

		themeButton.addEventListener("change", () => {
			document.body.classList.toggle(darkTheme);
			localStorage.setItem("selected-theme", getCurrentTheme());
			darkThemeSetUp();
		});
		
	} else {
		// console.log('btn not active');
		$(".switcher-darkmode").css("display","none");
	}

	if ($("body").hasClass("wp-search")) {
		$(".top-searchbox").css("display","block");
	} else {
		$(".top-searchbox").css("display","none");
	}

	/**
	 * Switching Cart
	 */
	// if ($("body").hasClass("woo-cart")) {
	// 	$(".wp-block-woocommerce-mini-cart").css({"display":"flex","opacity":"1"});
	// } else {
	// 	$(".wp-block-woocommerce-mini-cart").css("display","none");
	// }

	/**
	 * Switching Account Btn
	 */
	if ($("body").hasClass("woo-account")) {
		$(".block-account").css({"display":"flex","opacity":"1"});
	} else {
		$(".block-account").css("display","none");
	}

	/**
	 * Switching Flags Btn
	 */
	if ($("body").hasClass("menu-flags")) {
		$(".block-polylang").css({"display":"flex","opacity":"1"});
	} else {
		$(".block-polylang").css("display","none");
	}

	if ( $("body nav.header-nav").hasClass("wp-nav-menu__megamenu") ) { 
		$(".navbar-toggler-btn").css({"display":"none"});
	} else {
		$(".navbar-toggler-btn").css({"display":"block"}); 
	}

	// Scroll Right & Back Horizontal scroll menu responsive
	if ( window.innerWidth < 1024 ) {
		$('#wp-header-top-menu').append('<div class="arr-l arr"><em></em></div><div class="arr-r arr"><em></em></div>');
		$('#wp-header-top-menu .arr-r').hide();
				
		if ( $('#wp-header-top-menu').innerWidth() <  $('#menu-header-top-menu').innerWidth() ) {
			$('#wp-header-top-menu .arr-r').show();
			$('#wp-header-top-menu .arr').click(function() {
				var box = $('#wp-header-top-menu'), x;
				if ($(this).hasClass('arr-r')) {
					x = ((box.innerWidth() / 2)) + box.scrollLeft();
					box.animate({scrollLeft: x})
					$('#wp-header-top-menu .arr-l').show();
				} else {
					x = ((box.innerWidth() / 2)) - box.scrollLeft();
					box.animate({scrollLeft: -x})
				}
			});
		}
	}

});