document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.wpbb-swiper-block[data-swiper="1"], .wpbb-testimonials[data-swiper="1"]').forEach(function (block) {
    if (!window.Swiper) return;
    var el = block.querySelector('.swiper');
    if (!el) return;
    try {
      new window.Swiper(el, {
        slidesPerView: parseInt(block.dataset.slides || '1', 10),
        spaceBetween: parseInt(block.dataset.space || '20', 10),
        speed: parseInt(block.dataset.speed || '600', 10),
        loop: block.dataset.loop === '1',
        autoplay: block.dataset.autoplay === '1' ? { delay: 3000 } : false,
        breakpoints: {
          0: { slidesPerView: parseInt(block.dataset.slidesMobile || '1', 10) },
          768: { slidesPerView: parseInt(block.dataset.slidesTablet || block.dataset.slides || '2', 10) },
          992: { slidesPerView: parseInt(block.dataset.slides || '3', 10) }
        },
        pagination: block.querySelector('.swiper-pagination') ? { el: block.querySelector('.swiper-pagination'), clickable: true } : false,
        navigation: (block.querySelector('.swiper-button-next') && block.querySelector('.swiper-button-prev')) ? { nextEl: block.querySelector('.swiper-button-next'), prevEl: block.querySelector('.swiper-button-prev') } : false
      });
    } catch (e) {}
  });
});
