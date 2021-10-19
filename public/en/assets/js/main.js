// $.noConflict();

jQuery(document).ready(function($) {

	"use strict";

	[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {
		new SelectFx(el);
	} );

	jQuery('.selectpicker').selectpicker;


	$('#menuToggle').on('click', function(event) {
		$('body').toggleClass('open');
// 		if($('body').hasClass('open')){
// 		    var css = {
//                     'display': 'none !important',
//                     'left': 'inherit',
//                     'right': '-180px',
//                     'top': '0',
//                 }
//             $('.open aside.left-panel .navbar .navbar-nav li.menu-item-has-children .sub-menu').css(css);    
// 		}
// 		$('ul.sub-menu.children.dropdown-menu').css('display','block !important'); 
	});

	$('.search-trigger').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('.search-trigger').parent('.header-left').addClass('open');
	});

	$('.searchClose').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('.search-trigger').parent('.header-left').removeClass('open');
	});

	$('.the-notification-area').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		// $('.menuMenu').toggleClass('display-none');
		$('.menuMenu').toggle();
		// if ($('.menuMenu').is(':visible')) {
		// 	$('.search-trigger').hide();
		// } else{
		// 	$('.search-trigger').show();
		// }
		
	});

	// $('.user-area> a').on('click', function(event) {
	// 	event.preventDefault();
	// 	event.stopPropagation();
	// 	$('.user-menu').parent().removeClass('open');
	// 	$('.user-menu').parent().toggleClass('open');
	// });


});