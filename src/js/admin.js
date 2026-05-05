// import './components/_editor-toolbar';
/*
 * WP Admin custom scripts
 */
const $ = jQuery.noConflict(),
	d = new Date(),
	page = $('html, body');

document.addEventListener("DOMContentLoaded", function () {

	setTimeout(function () {
		$('body.wp-admin .row-wrap').each(function () {
			const l = $(this).find('.col-wrap').length;
			if (l == 2) {
				$(this).addClass('col-wrap--2col');
			}
		});
	}, 2500);
	
});
