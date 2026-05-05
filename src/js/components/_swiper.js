document.addEventListener("DOMContentLoaded", function() {

	var $ = jQuery.noConflict();	

	// Hero
	swiper = new Swiper('.hero-slider', {
		updateOnWindowResize: true,		
		slidesPerColumnFill: 'row',
		allowTouchMove: false,
		centeredSlides: true,
		slidesPerView: 1,
		spaceBetween: 0,
		draggable: false,
		speed: 2000,
		loop: true,
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
		keyboard: {
			enabled: true,
			onlyInViewport: false,
		},
		pagination: {
			el: '.hero-slider .swiper-pagination',
			clickable: true,
		},
		navigation: {
			prevEl: '.hero-slider .swiper-button-prev',
			nextEl: '.hero-slider .swiper-button-next'
		},
		grabCursor: true
	});

	// Testimonials slider 
	swiper = new Swiper('.posts-testimonials-slider', {
      	observeSlideChildren: true,
		slideToClickedSlide: true,
		updateOnWindowResize: true,
		slidesPerView: 3,
		spaceBetween: 20,
		loop: true,
		speed: 5000,
		autoplay: {
			disableOnInteraction: false,
			pauseOnMouseEnter: true,
			delay: 0,
		},
		breakpoints: {
			320: {
				slidesPerView: 1
			},
			760: {
				slidesPerView: 2
			},
			992: {
				slidesPerView: 3
			}
		},
		keyboard: {
			enabled: true,
			onlyInViewport: false,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		grabCursor: true
    });

	// Related pages & posts
	swiper = new Swiper('.related-pages.swiper-slider, .related-posts.swiper-slider', {
		observeSlideChildren: true,
		slideToClickedSlide: true,
		updateOnWindowResize: true,
		slidesPerView: 3,
		spaceBetween: 30,
		loop: true,
		breakpoints: {
			320: {
				slidesPerView: 1
			},
			760: {
				slidesPerView: 2
			},
			992: {
				slidesPerView: 3
			}
		},
		autoplay: {
			delay: 7000,
			disableOnInteraction: false,
		},
		keyboard: {
			enabled: true,
			onlyInViewport: false,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		grabCursor: true
	});

	// Default Gallery section
	setTimeout(function() {
		swiper = new Swiper('.photo-gallery.swiper-slider', {
			observeSlideChildren: true,
			slideToClickedSlide: true,
			updateOnWindowResize: true,
			slidesPerView: 1,
			spaceBetween: 20,
			loop: false,
			breakpoints: {
				320: {
					slidesPerView: 1
				},
				760: {
					slidesPerView: 2
				},
				992: {
					slidesPerView: 3
				}
			},
			keyboard: {
				enabled: true,
				onlyInViewport: false,
			},
			pagination: {
				el: '.swiper-slider-section-nav .swiper-pagination',
				clickable: true,
			},
			navigation: {
				nextEl: '.swiper-slider-section-nav .swiper-button-next',
				prevEl: '.swiper-slider-section-nav .swiper-button-prev',
			},
			grabCursor: true
		});

	}, 500);
});
