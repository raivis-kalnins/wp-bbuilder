document.addEventListener("DOMContentLoaded", function() {
	const $ = jQuery.noConflict(),
			win = $(window).width(),
			d = new Date(),
			n = d.getFullYear(),
			page = $('html, body'),
			pageUrl = window.location.href,
			i = 0;

	// WP Remove <p> tags
	$('div.row p:empty').hide();
	$('div.row p').each(function() {
		var $this = $(this);
		if ($this.html() === "&nbsp;") {
			$this.remove();
		}
	});

	/**
	* Menu Scroll
	*/
	$('.site-header').removeClass('sticky-header');
	const lastScrollTop = 320;
	function stickyHeader() { 
		const scroll = $(window).scrollTop();
		const st = $(this).scrollTop();
		lastScrollTop = st;
		if (scroll >= 30) {
			$('.wp-nav-menu__sticky-header').addClass('sticky-header');
		} else {
			$('.wp-nav-menu__sticky-header').removeClass('sticky-header');
		}
		if (scroll >= 200) {
			$('.scroll-up').removeClass('hidden');
		} else {			
			$('.scroll-up').addClass('hidden');
		}
	}
	$(window).on('scroll', stickyHeader);
	$(window).on('resize', stickyHeader);
	$(document).ready(stickyHeader);

	$('.wp-block-query-pagination-previous, .wp-block-query-pagination-next').addClass("btn btn-primary");

	// WooCommerce
	$('.products .product.outofstock .wp-element-button, .product.outofstock .wp-block-woocommerce-product-button .wp-block-button__link span').text('Out Of Stock');
	// User Registration
	$('.woocommerce-form-register #reg_password_field').prepend('<span class="error-psw" style="color:red;display:none">Please enter a valid password!</span>');
	$('.woocommerce-form-register #reg_password').on('keyup', function() {
		const input = $('#reg_password').val();
		if (input === "" || input.length < 8) {
			$('.woocommerce-form-register .woocommerce-form-register__submit').css({'pointer-events':'none','opacity':'0.4'});
			$(".woocommerce-form-register").bind("keydown", function(e) {if (e.keyCode === 13) return false;});
			$('.error-psw').show();
		} else {
			$('.woocommerce-form-register .woocommerce-form-register__submit').removeAttr('style');
			$('.error-psw').hide();
		}
	});

	if (document.querySelectorAll('.product .summary').length > 0) {

		$('form.variations_form').on('show_variation', function(event, data){
			$('.woocommerce.single-product .summary .price, .woocommerce.single-product .product-total-price_caption').show('fast');
		});
		$('form.variations_form').on('hide_variation', function(){
			$('.woocommerce.single-product .summary .price, .woocommerce.single-product .product-total-price_caption').hide('fast');
		});
	}

	$(".quote_btn.btn").each(function() {
		const url = $(this).attr('href'), path = window.location.pathname, prod_id = path.split('/').filter(Boolean).pop();
		$(this).attr('href', url + '#' + prod_id);
	});

	const prod_name = document.URL.substr(document.URL.indexOf('#')+1).replace(/%20/g,' ');
	$('form .wpcf7-textarea').val('Quote - "' + prod_name + '"');
	if(window.location.hash) {
		setTimeout(function() {
			$('form .wpcf7-textarea').val('Quote - "' + prod_name + '"');
		}, 3500 );
	} else {
		$('.wpcf7-textarea').val('');
	}
});
