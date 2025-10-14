/* Theme Name: The Project - Responsive Website Template
 * Author:HtmlCoder
 * Author URI:http://www.htmlcoder.me
 * Author e-mail:htmlcoder.me@gmail.com
 * Version:1.4.0
 * Created:March 2015
 * License URI:http://support.wrapbootstrap.com/
 * File Description: Initializations of plugins
 */

(function($){
	$(document).ready(function(){

		if ($('.boxed .fullscreen-bg').length>0) {
			$("body").addClass("transparent-page-wrapper");
		};

		$(window).load(function() {
			$("body").removeClass("no-trans");
		});
		// Enable Smooth Scroll only on Chrome and only on Win and Linux Systems
		var platform = navigator.platform.toLowerCase();
		if ((platform.indexOf('win') == 0 || platform.indexOf('linux') == 0) && !Modernizr.touch) {
			if ($.browser.webkit) {
				$.webkitSmoothScroll();
				console.log("hello webkit");
			}
		};
		//Show dropdown on hover only for desktop devices
		//-----------------------------------------------
		var delay=0, setTimeoutConst;
		if ((Modernizr.mq('only all and (min-width: 768px)') && !Modernizr.touch) || $("html.ie8").length>0) {
			$('.main-navigation:not(.onclick) .navbar-nav>li.dropdown, .main-navigation:not(.onclick) li.dropdown>ul>li.dropdown').hover(
			function(){
				var $this = $(this);
				setTimeoutConst = setTimeout(function(){
					$this.addClass('open').slideDown();
					$this.find('.dropdown-toggle').addClass('disabled');
				}, delay);

			},	function(){
				clearTimeout(setTimeoutConst );
				$(this).removeClass('open');
				$(this).find('.dropdown-toggle').removeClass('disabled');
			});
		};

		//Show dropdown on click only for mobile devices
		//-----------------------------------------------
		if (Modernizr.mq('only all and (max-width: 767px)') || Modernizr.touch || $(".main-navigation.onclick").length>0 ) {
			$('.header [data-toggle=dropdown], .header-top [data-toggle=dropdown]').on('click', function(event) {
			// Avoid following the href location when clicking
			event.preventDefault();
			// Avoid having the menu to close when clicking
			event.stopPropagation();
			// close all the siblings
			$(this).parent().siblings().removeClass('open');
			// close all the submenus of siblings
			$(this).parent().siblings().find('[data-toggle=dropdown]').parent().removeClass('open');
			// opening the one you clicked on
			$(this).parent().toggleClass('open');
			});
		};

		//Transparent Header Calculations
		var timer_tr;
		if ($(".transparent-header").length>0) {
			$(window).load(function() {
				trHeaderHeight = $("header.header").outerHeight();
				$(".transparent-header .tp-bannertimer").css("marginTop", (trHeaderHeight)+"px");
			});
			$(window).resize(function() {
				if ($(this).scrollTop()  < headerTopHeight + headerHeight -5) {
					trHeaderHeight = $("header.header").outerHeight();
					$(".transparent-header .tp-bannertimer").css("marginTop", (trHeaderHeight)+"px");
				}
			});
			$(window).scroll(function() {
				if ($(this).scrollTop() == 0 ) {
					if(timer_tr) {
						window.clearTimeout(timer_tr);
					};
					timer_tr = window.setTimeout(function() {
						trHeaderHeight = $("header.header").outerHeight();
						$(".transparent-header .tp-bannertimer").css("marginTop", (trHeaderHeight)+"px");
					}, 300);
				};
			});
		}

		if ($(".transparent-header .slideshow").length>0) {
			$(".header-container header.header").addClass("transparent-header-on");
		} else {
			$(".header-container header.header").removeClass("transparent-header-on");
		}

		//Full Width Slider with Transparent Header Calculations
		if ($(".transparent-header .slider-banner-fullwidth-big-height").length>0) {
			if (Modernizr.mq('only all and (max-width: 991px)')) {
				$("body").removeClass("transparent-header");
				$(".header-container header.header").removeClass("transparent-header-on");
				$(".tp-bannertimer").css("marginTop", "0px");
				$("body").addClass("slider-banner-fullwidth-big-height-removed");
			} else {
				$("body").addClass("transparent-header");
				$(".header-container header.header").addClass("transparent-header-on");
				$("body").removeClass("slider-banner-fullwidth-big-height-removed");
			}
		};

		if ($(".transparent-header .slider-banner-fullwidth-big-height").length>0 || $(".slider-banner-fullwidth-big-height-removed").length>0) {
			$(window).resize(function() {
				if (Modernizr.mq('only all and (max-width: 991px)')) {
					$("body").removeClass("transparent-header");
					$(".header-container header.header").removeClass("transparent-header-on");
					$(".tp-bannertimer").css("marginTop", "0px");
				} else {
					$("body").addClass("transparent-header");
					$(".header-container header.header").addClass("transparent-header-on");
				}
			});
		};

		//Revolution Slider 5
		if ($(".slider-revolution-5-container").length>0) {
			$(".tp-bannertimer").show();

			$('body:not(.transparent-header) .slider-revolution-5-container .slider-banner-fullscreen').revolution({
				sliderType:"standard",
				sliderLayout:"fullscreen",
				delay:9000,
				autoHeight:"on",
				responsiveLevels:[1199,991,767,480],
				fullScreenOffsetContainer: ".header-container, .offset-container",
				navigation: {
					onHoverStop: "off",
					arrows: {
						style: "hebe",
						enable:true,
						tmp: '<div class="tp-title-wrap"><span class="tp-arr-titleholder">{{title}}</span></div>',
						left : {
							h_align:"left",
							v_align:"center",
							h_offset:0,
							v_offset:0,
						},
						right : {
							h_align:"right",
							v_align:"center",
							h_offset:0,
							v_offset:0
						}
					},
					bullets:{
						style:"",
						enable:true,
						hide_onleave:true,
						direction:"horizontal",
						space: 3,
						h_align:"center",
						v_align:"bottom",
						h_offset:0,
						v_offset:20
					}
				},
				gridwidth:1140,
				gridheight:750
			});
			$('.transparent-header .slider-revolution-5-container .slider-banner-fullscreen').revolution({
				sliderType:"standard",
				sliderLayout:"fullscreen",
				delay:9000,
				autoHeight:"on",
				responsiveLevels:[1199,991,767,480],
				fullScreenOffsetContainer: ".header-top, .offset-container",
				navigation: {
					onHoverStop: "off",
					arrows: {
						style: "hebe",
						enable:true,
						tmp: '<div class="tp-title-wrap"><span class="tp-arr-titleholder">{{title}}</span></div>',
						left : {
							h_align:"left",
							v_align:"center",
							h_offset:0,
							v_offset:0,
						},
						right : {
							h_align:"right",
							v_align:"center",
							h_offset:0,
							v_offset:0
						}
					},
					bullets:{
						style:"",
						enable:true,
						hide_onleave:true,
						direction:"horizontal",
						space: 3,
						h_align:"center",
						v_align:"bottom",
						h_offset:0,
						v_offset:20
					}
				},
				gridwidth:1140,
				gridheight:750
			});
			$('.slider-revolution-5-container .slider-banner-fullwidth').revolution({
				sliderType:"standard",
				sliderLayout:"fullwidth",
				delay:8000,
				gridwidth:1140,
				gridheight:450,
				responsiveLevels:[1199,991,767,480],
				navigation: {
					onHoverStop: "off",
					arrows: {
						style: "hebe",
						enable:true,
						tmp: '<div class="tp-title-wrap"><span class="tp-arr-titleholder">{{title}}</span></div>',
						left : {
							h_align:"left",
							v_align:"center",
							h_offset:0,
							v_offset:0,
						},
						right : {
							h_align:"right",
							v_align:"center",
							h_offset:0,
							v_offset:0
						}
					},
					bullets:{
						style:"",
						enable:true,
						hide_onleave:true,
						direction:"horizontal",
						space: 3,
						h_align:"center",
						v_align:"bottom",
						h_offset:0,
						v_offset:20
					}
				}
			});
			$('.slider-revolution-5-container .slider-banner-fullwidth-big-height').revolution({
				sliderType:"standard",
				sliderLayout:"fullwidth",
				delay:8000,
				gridwidth:1140,
				gridheight:650,
				responsiveLevels:[1199,991,767,480],
				navigation: {
					onHoverStop: "off",
					arrows: {
						style: "hebe",
						enable:true,
						tmp: '<div class="tp-title-wrap"><span class="tp-arr-titleholder">{{title}}</span></div>',
						left : {
							h_align:"left",
							v_align:"center",
							h_offset:0,
							v_offset:0,
						},
						right : {
							h_align:"right",
							v_align:"center",
							h_offset:0,
							v_offset:0
						}
					},
					bullets:{
						style:"",
						enable:true,
						hide_onleave:true,
						direction:"horizontal",
						space: 3,
						h_align:"center",
						v_align:"bottom",
						h_offset:0,
						v_offset:20
					}
				}
			});
			$('.slider-revolution-5-container .slider-banner-boxedwidth').revolution({
				sliderType:"standard",
				sliderLayout:"auto",
				delay:8000,
				gridwidth:1140,
				gridheight:450,
				responsiveLevels:[1199,991,767,480],
				shadow: 1,
				navigation: {
					onHoverStop: "off",
					arrows: {
						style: "hebe",
						enable:true,
						tmp: '<div class="tp-title-wrap"><span class="tp-arr-titleholder">{{title}}</span></div>',
						left : {
							h_align:"left",
							v_align:"center",
							h_offset:0,
							v_offset:0,
						},
						right : {
							h_align:"right",
							v_align:"center",
							h_offset:0,
							v_offset:0
						}
					},
					bullets:{
						style:"",
						enable:true,
						hide_onleave:true,
						direction:"horizontal",
						space: 3,
						h_align:"center",
						v_align:"bottom",
						h_offset:0,
						v_offset:20
					}
				}
			});
			$('.slider-revolution-5-container .slider-banner-fullscreen-hero:not(.dotted)').revolution({
				sliderType:"hero",
				sliderLayout:"fullscreen",
				autoHeight:"on",
				gridwidth:1140,
				gridheight:650,
				responsiveLevels:[1199,991,767,480],
				delay: 9000,
				fullScreenOffsetContainer: ".header-top, .offset-container"
			});
			$('.slider-revolution-5-container .slider-banner-fullscreen-hero.dotted').revolution({
				sliderType:"hero",
				sliderLayout:"fullscreen",
				autoHeight:"on",
				gridwidth:1140,
				gridheight:650,
				dottedOverlay:"twoxtwo",
				delay: 9000,
				responsiveLevels:[1199,991,767,480],
				fullScreenOffsetContainer: ".header-top, .offset-container"
			});
			$('.slider-revolution-5-container .slider-banner-fullwidth-hero:not(.dotted)').revolution({
				sliderType:"hero",
				sliderLayout:"fullwidth",
				gridwidth:1140,
				gridheight:650,
				responsiveLevels:[1199,991,767,480],
				delay: 9000
			});
			$('.slider-revolution-5-container .slider-banner-fullwidth-hero.dotted').revolution({
				sliderType:"hero",
				sliderLayout:"fullwidth",
				gridwidth:1140,
				gridheight:650,
				responsiveLevels:[1199,991,767,480],
				delay: 9000,
				dottedOverlay:"twoxtwo"
			});
			$('.slider-revolution-5-container .slider-banner-carousel').revolution({
				sliderType:"carousel",
				sliderLayout:"fullwidth",
				dottedOverlay:"none",
				delay:5000,
				navigation: {
					keyboardNavigation:"off",
					keyboard_direction: "horizontal",
					mouseScrollNavigation:"off",
					mouseScrollReverse:"default",
					onHoverStop:"off",
					arrows: {
						style:"erinyen",
						enable:true,
						hide_onmobile:false,
						hide_onleave:false,
						tmp:'<div class="tp-title-wrap">  	<div class="tp-arr-imgholder"></div>    <div class="tp-arr-img-over"></div>	<span class="tp-arr-titleholder">{{title}}</span> </div>',
							left: {
								h_align:"left",
								v_align:"center",
								h_offset:30,
								v_offset:0
							},
							right: {
								h_align:"right",
								v_align:"center",
								h_offset:30,
								v_offset:0
							}
					},
					thumbnails: {
						style:"",
						enable:true,
						width:160,
						height:120,
						min_width:100,
						wrapper_padding:30,
						wrapper_color:"#373737",
						wrapper_opacity:"1",
						tmp:'<span class="tp-thumb-img-wrap">  <span class="tp-thumb-image"></span></span>',
						visibleAmount:9,
						hide_onmobile:false,
						hide_onleave:false,
						direction:"horizontal",
						span:true,
						position:"outer-bottom",
						space:20,
						h_align:"center",
						v_align:"bottom",
						h_offset:0,
						v_offset:0
					}
				},
				carousel: {
					maxRotation: 65,
					vary_rotation: "on",
					minScale: 55,
					vary_scale: "off",
					horizontal_align: "center",
					vertical_align: "center",
					fadeout: "on",
					vary_fade: "on",
					maxVisibleItems: 5,
					infinity: "off",
					space: -150,
					stretch: "off"
				},
				visibilityLevels:[1240,1024,778,480],
				gridwidth:600,
				gridheight:600,
				lazyType:"none",
				spinner:"off",
				stopLoop:"off",
				stopAfterLoops:-1,
				stopAtSlide:-1,
				shuffle:"off",
				autoHeight:"off",
				disableProgressBar:"on",
				hideThumbsOnMobile:"off",
				hideSliderAtLimit:0,
				hideCaptionAtLimit:0,
				hideAllCaptionAtLilmit:0,
				shadow: 0,
				fallbacks: {
					simplifyAll:"off",
					nextSlideOnWindowFocus:"off",
					disableFocusListener:false,
				}
			});
		};

		//Full Page
		if ($(".fullpage-site").length>0 || $(".fullpage-site-with-menu").length>0) {
			$('.fullpage-site').fullpage({
				anchors: ['firstPage', 'secondPage', 'thirdPage', 'fourthPage', 'fifthPage'],
				navigation: true,
				navigationPosition: 'right',
				navigationTooltips: ['Intro', 'About', 'Portfolio', 'Clients', 'Contact Us'],
				fixedElements: '.header-container, .subfooter',
				responsiveWidth: 992,
				responsiveHeight: 600
			});
			$('.fullpage-site-with-menu').fullpage({
				anchors: ['firstPage', 'secondPage', 'thirdPage', 'fourthPage', 'fifthPage'],
				navigation: true,
				navigationPosition: 'right',
				navigationTooltips: ['Intro', 'About', 'Menu', 'Reviews', 'Contact Us'],
				fixedElements: '.header-container, .subfooter',
				responsiveWidth: 992,
				responsiveHeight: 600,
				menu: '#fullpage-menu',
			});
		};

		//Owl carousel
		//-----------------------------------------------
		if ($('.owl-carousel').length>0) {
			$("*[dir='ltr'] .owl-carousel.carousel").owlCarousel({
				items:1,
				dots: false,
				nav: true,
				loop: true,
				navText: false,
				responsive:{
					479:{
						items:2
					},
					768:{
						items:2
					},
					992:{
						items:4
					},
					1200:{
						items:4
					}
				}
			});
			$("*[dir='rtl'] .owl-carousel.carousel").owlCarousel({
				items:1,
				rtl: true,
				dots: false,
				nav: true,
				loop: true,
				navText: false,
				responsive:{
					479:{
						items:2
					},
					768:{
						items:2
					},
					992:{
						items:4
					},
					1200:{
						items:4
					}
				}
			});
			$("*[dir='ltr'] .owl-carousel.carousel-autoplay").owlCarousel({
				items:1,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				dots: false,
				nav: true,
				navText: false,
				responsive:{
					479:{
						items:2
					},
					768:{
						items:2
					},
					992:{
						items:4
					},
					1200:{
						items:4
					}
				}
			});
			$("*[dir='rtl'] .owl-carousel.carousel-autoplay").owlCarousel({
				items:1,
				rtl: true,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				dots: false,
				nav: true,
				navText: false,
				responsive:{
					479:{
						items:2
					},
					768:{
						items:2
					},
					992:{
						items:4
					},
					1200:{
						items:4
					}
				}
			});
			$("*[dir='ltr'] .owl-carousel.clients").owlCarousel({
				items:2,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				dots: false,
				responsive:{
					479:{
						items:3
					},
					768:{
						items:4
					},
					992:{
						items:4
					},
					1200:{
						items:6
					}
				}
			});
			$("*[dir='rtl'] .owl-carousel.clients").owlCarousel({
				items:2,
				rtl: true,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				dots: false,
				responsive:{
					479:{
						items:3
					},
					768:{
						items:4
					},
					992:{
						items:4
					},
					1200:{
						items:6
					}
				}
			});
			$("*[dir='ltr'] .owl-carousel.content-slider").owlCarousel({
				items: 1,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				nav: false,
				navText: false,
				dots: false
			});
			$("*[dir='rtl'] .owl-carousel.content-slider").owlCarousel({
				items: 1,
				rtl: true,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				nav: false,
				navText: false,
				dots: false
			});
			$("*[dir='ltr'] .owl-carousel.content-slider-with-controls").owlCarousel({
				items: 1,
				loop: true,
				autoplay: false,
				nav: true,
				dots: true
			});
			$("*[dir='rtl'] .owl-carousel.content-slider-with-controls").owlCarousel({
				items: 1,
				loop: true,
				rtl: true,
				autoplay: false,
				nav: true,
				dots: true
			});
			$("*[dir='ltr'] .owl-carousel.content-slider-with-large-controls").owlCarousel({
				items: 1,
				loop: true,
				autoplay: false,
				nav: true,
				dots: true
			});
			$("*[dir='rtl'] .owl-carousel.content-slider-with-large-controls").owlCarousel({
				items: 1,
				loop: true,
				rtl: true,
				autoplay: false,
				nav: true,
				dots: true
			});
			$("*[dir='ltr'] .owl-carousel.content-slider-with-controls-autoplay").owlCarousel({
				items: 1,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				nav: true,
				dots: true
			});
			$("*[dir='rtl'] .owl-carousel.content-slider-with-controls-autoplay").owlCarousel({
				items: 1,
				autoplay: true,
				rtl: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				nav: true,
				dots: true
			});
			$("*[dir='ltr'] .owl-carousel.content-slider-with-large-controls-autoplay").owlCarousel({
				items: 1,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				nav: true,
				dots: true
			});
			$("*[dir='rtl'] .owl-carousel.content-slider-with-large-controls-autoplay").owlCarousel({
				items: 1,
				autoplay: true,
				rtl: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				nav: true,
				dots: true
			});
			$("*[dir='ltr'] .owl-carousel.content-slider-with-controls-autoplay-hover-stop").owlCarousel({
				items: 1,
				autoplay: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				nav: true,
				dots: true,
				autoplayHoverPause: true
			});
			$("*[dir='rtl'] .owl-carousel.content-slider-with-controls-autoplay-hover-stop").owlCarousel({
				items: 1,
				autoplay: true,
				rtl: true,
				autoplayTimeout: 5000,
				autoplaySpeed: 700,
				loop: true,
				nav: true,
				dots: true,
				autoplayHoverPause: true
			});

			var sync1 = $(".owl-carousel.content-slider-with-thumbs");
			var sync2 = $(".owl-carousel.content-slider-thumbs");
			var slidesPerPage = 4; //globaly define number of elements per page
			var syncedSecondary = true;

			if ($("*[dir='ltr']").length>0) {
				sync1.owlCarousel({
					items : 1,
					slideSpeed : 700,
					nav: true,
					autoplay: false,
					dots: false,
					loop: true,
					responsiveRefreshRate : 200
				}).on('changed.owl.carousel', syncPosition);

				sync2.on('initialized.owl.carousel', function () {
					sync2.find(".owl-item").eq(0).addClass("current");
				}).owlCarousel({
					items : slidesPerPage,
					dots: false,
					nav: false,
					smartSpeed: 200,
					slideSpeed : 500,
					slideBy: slidesPerPage,
					responsiveRefreshRate : 100
				}).on('changed.owl.carousel', syncPosition2);
			} else {
				sync1.owlCarousel({
					items : 1,
					slideSpeed : 700,
					nav: true,
					autoplay: false,
					rtl: true,
					dots: false,
					loop: true,
					responsiveRefreshRate : 200
				}).on('changed.owl.carousel', syncPosition);

				sync2.on('initialized.owl.carousel', function () {
					sync2.find(".owl-item").eq(0).addClass("current");
				}).owlCarousel({
					items : slidesPerPage,
					dots: false,
					nav: false,
					rtl: true,
					smartSpeed: 200,
					slideSpeed : 500,
					slideBy: slidesPerPage,
					responsiveRefreshRate : 100
				}).on('changed.owl.carousel', syncPosition2);
			}
			function syncPosition(el) {
				//if you set loop to false, you have to restore this next line
				//var current = el.item.index;

				//if you disable loop you have to comment this block
				var count = el.item.count-1;
				var current = Math.round(el.item.index - (el.item.count/2) - .5);

				if(current < 0) {
					current = count;
				}
				if(current > count) {
					current = 0;
				}

				//end block
				sync2.find(".owl-item").removeClass("current").eq(current).addClass("current");
				var onscreen = sync2.find('.owl-item.active').length - 1;
				var start = sync2.find('.owl-item.active').first().index();
				var end = sync2.find('.owl-item.active').last().index();

				if (current > end) {
					sync2.data('owl.carousel').to(current, 100, true);
				}
				if (current < start) {
					sync2.data('owl.carousel').to(current - onscreen, 100, true);
				}
			}

			function syncPosition2(el) {
				if(syncedSecondary) {
					var number = el.item.index;
					sync1.data('owl.carousel').to(number, 100, true);
				}
			}

			sync2.on("click", ".owl-item", function(e){
				e.preventDefault();
				var number = $(this).index();
				sync1.data('owl.carousel').to(number, 300, true);
			});
		};

		// Fixed header
		//-----------------------------------------------
		headerTopHeight = $(".header-top").outerHeight(),
		headerHeight = $("header.header.fixed").outerHeight();
		$(window).resize(function() {
			if(($(this).scrollTop() < headerTopHeight + headerHeight -5 ) && ($(window).width() > 767)) {
				headerTopHeight = $(".header-top").outerHeight(),
				headerHeight = $("header.header.fixed").outerHeight();
			}
		});

		$(window).scroll(function() {
			if (($(".header.fixed:not(.fixed-before)").length > 0)  && !($(".transparent-header .slideshow").length>0)) {
				if (($(this).scrollTop() > headerTopHeight + headerHeight) && ($(window).width() > 767)) {
					$("body").addClass("fixed-header-on");
					$(".header.fixed:not(.fixed-before)").addClass('animated object-visible fadeInDown');
					$(".header-container").css("paddingBottom", (headerHeight)+"px");
				} else {
					$("body").removeClass("fixed-header-on");
					$(".header-container").css("paddingBottom", (0)+"px");
					$(".header.fixed:not(.fixed-before)").removeClass('animated object-visible fadeInDown');
				}
			} else if ($(".header.fixed:not(.fixed-before)").length > 0) {
				if (($(this).scrollTop() > headerTopHeight + headerHeight) && ($(window).width() > 767)) {
					$("body").addClass("fixed-header-on");
					$(".header.fixed:not(.fixed-before)").addClass('animated object-visible fadeInDown');
				} else {
					$("body").removeClass("fixed-header-on");
					$(".header.fixed:not(.fixed-before)").removeClass('animated object-visible fadeInDown');
				}
			};
		});

		$(window).scroll(function() {
			if (($(".header.fixed.fixed-before").length > 0)  && !($(".transparent-header .slideshow").length>0)) {
				if (($(this).scrollTop() > headerTopHeight) && ($(window).width() > 767)) {
					$("body").addClass("fixed-header-on");
					$(".header.fixed.fixed-before").addClass('object-visible');
					$(".header-container").css("paddingBottom", (headerHeight)+"px");
				} else {
					$("body").removeClass("fixed-header-on");
					$(".header-container").css("paddingBottom", (0)+"px");
					$(".header.fixed.fixed-before").removeClass('object-visible');
				}
			} else if ($(".header.fixed.fixed-before").length > 0) {
				if (($(this).scrollTop() > headerTopHeight) && ($(window).width() > 767)) {
					$("body").addClass("fixed-header-on");
					$(".header.fixed.fixed-before").addClass('object-visible');
				} else {
					$("body").removeClass("fixed-header-on");
					$(".header.fixed.fixed-before").removeClass('object-visible');
				}
			};
		});

		// Charts
		//-----------------------------------------------
		if ($(".graph").length>0) {
			// Creates random numbers you don't need this for real graphs
			var randomScalingFactor = function(){ return Math.round(Math.random()*500)};

			if ($(".graph.line").length>0) {
				// Data for line charts
				var lineChartData = {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					datasets: [
					{
						label: "My First dataset",
						fill: false,
						lineTension: 0.1,
						backgroundColor: "rgba(75,192,192,0.4)",
						borderColor: "rgba(75,192,192,1)",
						borderCapStyle: 'butt',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgba(75,192,192,1)",
						pointBackgroundColor: "#fff",
						pointBorderWidth: 1,
						pointHoverRadius: 5,
						pointHoverBackgroundColor: "rgba(75,192,192,1)",
						pointHoverBorderColor: "rgba(220,220,220,1)",
						pointHoverBorderWidth: 2,
						pointRadius: 1,
						pointHitRadius: 10,
						data: [65, 59, 80, 81, 56, 55, 40],
						spanGaps: false,
					}
					]
				};

				// Line Charts Initialization
				$(window).load(function() {
					var ctx = document.getElementById("lines-graph").getContext("2d");
					var LineChart = new Chart(ctx, {
						type: 'line',
						data: lineChartData,
						responsive: true,
						bezierCurve : false
					});
				});
			}
			if ($(".graph.bar").length>0) {
				// Data for bar charts
				var barChartData = {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					datasets: [
					{
						label: "My First dataset",
						backgroundColor: [
						'rgba(255, 99, 132, 0.2)',
						'rgba(54, 162, 235, 0.2)',
						'rgba(255, 206, 86, 0.2)',
						'rgba(75, 192, 192, 0.2)',
						'rgba(153, 102, 255, 0.2)',
						'rgba(255, 159, 64, 0.2)'
						],
						borderColor: [
						'rgba(255,99,132,1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)'
						],
						borderWidth: 1,
						data: [65, 59, 80, 81, 56, 55, 40],
					}
					]
				};

				// Bar Charts Initialization
				$(window).load(function() {
					var ctx = document.getElementById("bars-graph").getContext("2d");
					var BarChart = new Chart(ctx, {
						type: 'bar',
						data: barChartData,
						responsive : true
					});
				});
			}
			if ($(".graph.pie").length>0) {
				// Data for pie chart
				var pieData = {
					labels: [
					"Red",
					"Blue",
					"Yellow"
					],
					datasets: [
					{
						data: [300, 50, 100],
						backgroundColor: [
						"#FF6384",
						"#36A2EB",
						"#FFCE56"
						],
						hoverBackgroundColor: [
						"#FF6384",
						"#36A2EB",
						"#FFCE56"
						]
					}]
				};

				// Pie Chart Initialization
				$(window).load(function() {
					var ctx = document.getElementById("pie-graph").getContext("2d");
					var PieChart = new Chart(ctx,{
						type: 'pie',
						data: pieData
					});
				});
			}
			if ($(".graph.doughnut").length>0) {
				// Data for doughnut chart
				var doughnutData = {
					labels: [
					"Red",
					"Blue",
					"Yellow"
					],
					datasets: [
					{
						data: [300, 50, 100],
						backgroundColor: [
						"#FF6384",
						"#36A2EB",
						"#FFCE56"
						],
						hoverBackgroundColor: [
						"#FF6384",
						"#36A2EB",
						"#FFCE56"
						]
					}]
				};

				// Doughnut Chart Initialization
				$(window).load(function() {
					var ctx = document.getElementById("doughnut-graph").getContext("2d");
					var DoughnutChart = new Chart(ctx, {
						type: 'doughnut',
						data: doughnutData,
						responsive : true
					});
				});
			}
		};

		// Magnific popup
		//-----------------------------------------------
		if (($(".popup-img").length > 0) || ($(".popup-iframe").length > 0) || ($(".popup-img-single").length > 0)) {
			$(".popup-img").magnificPopup({
				type:"image",
				gallery: {
					enabled: true,
				}
			});
			$(".popup-img-single").magnificPopup({
				type:"image",
				gallery: {
					enabled: false,
				}
			});
			$('.popup-iframe').magnificPopup({
				disableOn: 700,
				type: 'iframe',
				preloader: false,
				fixedContentPos: false
			});
		};

		// Animations
		//-----------------------------------------------
		if ($("[data-animation-effect]").length>0) {
			$("[data-animation-effect]").each(function() {
				if(Modernizr.csstransitions) {
					var waypoints = $(this).waypoint(function(direction) {
						var appearDelay = $(this.element).attr("data-effect-delay"),
						animatedObject = $(this.element);
						setTimeout(function() {
							animatedObject.addClass('animated object-visible ' + animatedObject.attr("data-animation-effect"));
						}, appearDelay);
						this.destroy();
					},{
						offset: '95%'
					});
				} else {
					$(this).addClass('object-visible');
				}
			});
		};

		// Text Rotators
		//-----------------------------------------------
		if ($(".text-rotator").length>0) {
			$(".text-rotator").each(function() {
				var tr_animationEffect = $(this).attr("data-rotator-animation-effect");
				$(this).Morphext({
					animation: ""+tr_animationEffect+"", // Overrides default "bounceIn"
					separator: ",", // Overrides default ","
					speed: 3000 // Overrides default 2000
				});
			});
		};

		// Stats Count To
		//-----------------------------------------------
		if ($(".stats [data-to]").length>0) {
			$(".stats [data-to]").each(function() {
				var stat_item = $(this),
				offset = stat_item.offset().top;
				if($(window).scrollTop() > (offset - 800) && !(stat_item.hasClass('counting'))) {
					stat_item.addClass('counting');
					stat_item.countTo();
				};
				$(window).scroll(function() {
					if($(window).scrollTop() > (offset - 800) && !(stat_item.hasClass('counting'))) {
						stat_item.addClass('counting');
						stat_item.countTo();
					}
				});
			});
		};

		// Isotope filters
		//-----------------------------------------------
		if ($('.isotope-container').length>0 || $('.masonry-grid').length>0 || $('.masonry-grid-fitrows').length>0 || $('.isotope-container-fitrows').length>0) {
			$(window).load(function() {
				$('.masonry-grid').isotope({
					itemSelector: '.masonry-grid-item',
					layoutMode: 'masonry'
				});
				$('.masonry-grid-fitrows').isotope({
					itemSelector: '.masonry-grid-item',
					layoutMode: 'fitRows'
				});
				$('.isotope-container').fadeIn();
				var $container = $('.isotope-container').isotope({
					itemSelector: '.isotope-item',
					layoutMode: 'masonry',
					transitionDuration: '0.6s',
					filter: "*"
				});
				$('.isotope-container-fitrows').fadeIn();
				var $container_fitrows = $('.isotope-container-fitrows').isotope({
					itemSelector: '.isotope-item',
					layoutMode: 'fitRows',
					transitionDuration: '0.6s',
					filter: "*"
				});
				// filter items on button click
				$('.filters').on( 'click', 'ul.nav li a', function() {
					var filterValue = $(this).attr('data-filter');
					$(".filters").find("li.active").removeClass("active");
					$(this).parent().addClass("active");
					$container.isotope({ filter: filterValue });
					$container_fitrows.isotope({ filter: filterValue });
					return false;
				});
			});
			$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				$('.tab-pane .masonry-grid-fitrows').isotope({
					itemSelector: '.masonry-grid-item',
					layoutMode: 'fitRows'
				});
			});
		};

		// Animated Progress Bars
		//-----------------------------------------------
		if ($("[data-animate-width]").length>0) {
			$("[data-animate-width]").each(function() {
				if (Modernizr.touch || !Modernizr.csstransitions) {
					$(this).find("span").hide();
				};
				var waypoints = $(this).waypoint(function(direction) {
					$(this.element).animate({width: $(this.element).attr("data-animate-width")}, 800 );
					this.destroy();
					if (Modernizr.touch || !Modernizr.csstransitions) {
						$(this.element).find("span").show('slow');
					};
				},{
					offset: '90%'
				});
			});
		};

		// Animated Circular Progress Bars
		//-----------------------------------------------
		if ($(".knob").length>0) {

			$(".knob").knob();
			$(".knob").each(function() {
				var animateVal = $(this).attr("data-animate-value");
				$(this).animate({animatedVal: animateVal}, {
					duration: 2000,
					step: function() {
						$(this).val(Math.ceil(this.animatedVal)).trigger("change");
					}
				});
			});
		}

		//Video Background
		//-----------------------------------------------
		if($(".video-background").length>0) {
			if (Modernizr.touch) {
				$(".video-background").vide({
					mp4: "videos/background-video.mp4",
					webm: "videos/background-video.webm",
					poster: "videos/video-fallback.jpg"
				}, {
					volume: 1,
					playbackRate: 1,
					muted: true,
					loop: true,
					autoplay: true,
					position: "50% 100%", // Similar to the CSS `background-position` property.
					posterType: "jpg", // Poster image type. "detect" — auto-detection; "none" — no poster; "jpg", "png", "gif",... - extensions.
					resizing: true
				});
			} else {
				$(".video-background").vide({
					mp4: "videos/background-video.mp4",
					webm: "videos/background-video.webm",
					poster: "videos/video-poster.jpg"
				}, {
					volume: 1,
					playbackRate: 1,
					muted: true,
					loop: true,
					autoplay: true,
					position: "50% 100%", // Similar to the CSS `background-position` property.
					posterType: "jpg", // Poster image type. "detect" — auto-detection; "none" — no poster; "jpg", "png", "gif",... - extensions.
					resizing: true
				});
			};

		};
		if($(".video-background-banner").length>0) {
			if (Modernizr.touch) {
				$(".video-background-banner").vide({
					mp4: "videos/background-video-banner.mp4",
					webm: "videos/background-video-banner.webm",
					poster: "videos/video-fallback.jpg"
				}, {
					volume: 1,
					playbackRate: 1,
					muted: true,
					loop: true,
					autoplay: true,
					position: "50% 50%", // Similar to the CSS `background-position` property.
					posterType: "jpg", // Poster image type. "detect" — auto-detection; "none" — no poster; "jpg", "png", "gif",... - extensions.
					resizing: true
				});
			} else {
				$(".video-background-banner").vide({
					mp4: "videos/background-video-banner.mp4",
					webm: "videos/background-video-banner.webm",
					poster: "videos/video-banner-poster.jpg"
				}, {
					volume: 1,
					playbackRate: 1,
					muted: true,
					loop: true,
					autoplay: true,
					position: "50% 50%", // Similar to the CSS `background-position` property.
					posterType: "jpg", // Poster image type. "detect" — auto-detection; "none" — no poster; "jpg", "png", "gif",... - extensions.
					resizing: true
				});
			};
		};
		//Scroll totop
		//-----------------------------------------------
		$(window).scroll(function() {
			if($(this).scrollTop() != 0) {
				$(".scrollToTop").addClass("fadeToTop");
				$(".scrollToTop").removeClass("fadeToBottom");
			} else {
				$(".scrollToTop").removeClass("fadeToTop");
				$(".scrollToTop").addClass("fadeToBottom");
			}
		});

		$(".scrollToTop").click(function() {
			$("body,html").animate({scrollTop:0},800);
		});

		//Modal
		//-----------------------------------------------
		if($(".modal").length>0) {
			$(".modal").each(function() {
				$(".modal").prependTo( "body" );
			});
		}

		// Pricing tables popovers
		//-----------------------------------------------
		if ($(".pricing-tables").length>0) {
			$(".plan .pt-popover").popover({
				trigger: 'hover',
				container: 'body'
			});
		};

		// Contact forms validation
		//-----------------------------------------------
		if($("#contact-form").length>0) {
			$("#contact-form").validate({
				submitHandler: function(form) {
					$('.submit-button').button("loading");
					$.ajax({
						type: "POST",
						url: "php/email-sender.php",
						data: {
							"name": $("#contact-form #name").val(),
							"email": $("#contact-form #email").val(),
							"subject": $("#contact-form #subject").val(),
							"message": $("#contact-form #message").val()
						},
						dataType: "json",
						success: function (data) {
							if (data.sent == "yes") {
								$("#MessageSent").removeClass("hidden");
								$("#MessageNotSent").addClass("hidden");
								$(".submit-button").removeClass("btn-default").addClass("btn-success").prop('value', 'Message Sent');
								$("#contact-form .form-control").each(function() {
									$(this).prop('value', '').parent().removeClass("has-success").removeClass("has-error");
								});
							} else {
								$("#MessageNotSent").removeClass("hidden");
								$("#MessageSent").addClass("hidden");
							}
						}
					});
				},
				errorPlacement: function(error, element) {
					error.insertBefore( element );
				},
				onkeyup: false,
				onclick: false,
				rules: {
					name: {
						required: true,
						minlength: 2
					},
					email: {
						required: true,
						email: true
					},
					subject: {
						required: true
					},
					message: {
						required: true,
						minlength: 10
					}
				},
				messages: {
					name: {
						required: "Please specify your name",
						minlength: "Your name must be longer than 2 characters"
					},
					email: {
						required: "We need your email address to contact you",
						email: "Please enter a valid email address e.g. name@domain.com"
					},
					subject: {
						required: "Please enter a subject"
					},
					message: {
						required: "Please enter a message",
						minlength: "Your message must be longer than 10 characters"
					}
				},
				errorElement: "span",
				highlight: function (element) {
					$(element).parent().removeClass("has-success").addClass("has-error");
					$(element).siblings("label").addClass("hide");
				},
				success: function (element) {
					$(element).parent().removeClass("has-error").addClass("has-success");
					$(element).siblings("label").removeClass("hide");
				}
			});
		};

		if($("#contact-form-with-recaptcha").length>0) {
			$("#contact-form-with-recaptcha").validate({
				submitHandler: function(form) {
					$('.submit-button').button("loading");
					$.ajax({
						type: "POST",
						url: "php/email-sender-recaptcha.php",
						data: {
							"name": $("#contact-form-with-recaptcha #name").val(),
							"email": $("#contact-form-with-recaptcha #email").val(),
							"subject": $("#contact-form-with-recaptcha #subject").val(),
							"message": $("#contact-form-with-recaptcha #message").val(),
							"g-recaptcha-response": $("#g-recaptcha-response").val()
						},
						dataType: "json",
						success: function (data) {
							if (data.sent == "yes") {
								$("#MessageSent").removeClass("hidden");
								$("#MessageNotSent").addClass("hidden");
								$(".submit-button").removeClass("btn-default").addClass("btn-success").prop('value', 'Message Sent');
								$("#contact-form-with-recaptcha .form-control").each(function() {
									$(this).prop('value', '').parent().removeClass("has-success").removeClass("has-error");
								});
							} else {
								$("#MessageNotSent").removeClass("hidden");
								$("#MessageSent").addClass("hidden");
							}
						}
					});
				},
				errorPlacement: function(error, element) {
					error.insertBefore( element );
				},
				onkeyup: false,
				onclick: false,
				rules: {
					name: {
						required: true,
						minlength: 2
					},
					email: {
						required: true,
						email: true
					},
					subject: {
						required: true
					},
					message: {
						required: true,
						minlength: 10
					}
				},
				messages: {
					name: {
						required: "Please specify your name",
						minlength: "Your name must be longer than 2 characters"
					},
					email: {
						required: "We need your email address to contact you",
						email: "Please enter a valid email address e.g. name@domain.com"
					},
					subject: {
						required: "Please enter a subject"
					},
					message: {
						required: "Please enter a message",
						minlength: "Your message must be longer than 10 characters"
					}
				},
				errorElement: "span",
				highlight: function (element) {
					$(element).parent().removeClass("has-success").addClass("has-error");
					$(element).siblings("label").addClass("hide");
				},
				success: function (element) {
					$(element).parent().removeClass("has-error").addClass("has-success");
					$(element).siblings("label").removeClass("hide");
				}
			});
		};

		if($("#footer-form").length>0) {
			$("#footer-form").validate({
				submitHandler: function(form) {
					$('.submit-button').button("loading");
					$.ajax({
						type: "POST",
						url: "php/email-sender.php",
						data: {
							"name": $("#footer-form #name2").val(),
							"email": $("#footer-form #email2").val(),
							"subject": "Message from contact form",
							"message": $("#footer-form #message2").val()
						},
						dataType: "json",
						success: function (data) {
							if (data.sent == "yes") {
								$("#MessageSent2").removeClass("hidden");
								$("#MessageNotSent2").addClass("hidden");
								$(".submit-button").removeClass("btn-default").addClass("btn-success").prop('value', 'Message Sent');
								$("#footer-form .form-control").each(function() {
									$(this).prop('value', '').parent().removeClass("has-success").removeClass("has-error");
								});
							} else {
								$("#MessageNotSent2").removeClass("hidden");
								$("#MessageSent2").addClass("hidden");
							}
						}
					});
				},
				errorPlacement: function(error, element) {
					error.insertAfter( element );
				},
				onkeyup: false,
				onclick: false,
				rules: {
					name2: {
						required: true,
						minlength: 2
					},
					email2: {
						required: true,
						email: true
					},
					message2: {
						required: true,
						minlength: 10
					}
				},
				messages: {
					name2: {
						required: "Please specify your name",
						minlength: "Your name must be longer than 2 characters"
					},
					email2: {
						required: "We need your email address to contact you",
						email: "Please enter a valid email address e.g. name@domain.com"
					},
					message2: {
						required: "Please enter a message",
						minlength: "Your message must be longer than 10 characters"
					}
				},
				errorElement: "span",
				highlight: function (element) {
					$(element).parent().removeClass("has-success").addClass("has-error");
					$(element).siblings("label").addClass("hide");
				},
				success: function (element) {
					$(element).parent().removeClass("has-error").addClass("has-success");
					$(element).siblings("label").removeClass("hide");
				}
			});
		};

		if($("#sidebar-form").length>0) {

			$("#sidebar-form").validate({
				submitHandler: function(form) {
					$('.submit-button').button("loading");
					$.ajax({
						type: "POST",
						url: "php/email-sender.php",
						data: {
							"name": $("#sidebar-form #name3").val(),
							"email": $("#sidebar-form #email3").val(),
							"subject": "Message from FAQ page",
							"category": $("#sidebar-form #category").val(),
							"message": $("#sidebar-form #message3").val()
						},
						dataType: "json",
						success: function (data) {
							if (data.sent == "yes") {
								$("#MessageSent3").removeClass("hidden");
								$("#MessageNotSent3").addClass("hidden");
								$(".submit-button").removeClass("btn-default").addClass("btn-success").prop('value', 'Message Sent');
								$("#sidebar-form .form-control").each(function() {
									$(this).prop('value', '').parent().removeClass("has-success").removeClass("has-error");
								});
							} else {
								$("#MessageNotSent3").removeClass("hidden");
								$("#MessageSent3").addClass("hidden");
							}
						}
					});
				},
				errorPlacement: function(error, element) {
					error.insertAfter( element );
				},
				onkeyup: false,
				onclick: false,
				rules: {
					name3: {
						required: true,
						minlength: 2
					},
					email3: {
						required: true,
						email: true
					},
					message3: {
						required: true,
						minlength: 10
					}
				},
				messages: {
					name3: {
						required: "Please specify your name",
						minlength: "Your name must be longer than 2 characters"
					},
					email3: {
						required: "We need your email address to contact you",
						email: "Please enter a valid email address e.g. name@domain.com"
					},
					message3: {
						required: "Please enter a message",
						minlength: "Your message must be longer than 10 characters"
					}
				},
				errorElement: "span",
				highlight: function (element) {
					$(element).parent().removeClass("has-success").addClass("has-error");
				},
				success: function (element) {
					$(element).parent().removeClass("has-error").addClass("has-success");
				}
			});

		};

		if($("#rsvp").length>0) {
			$("#rsvp").validate({
				submitHandler: function(form) {
					$('.submit-button').button("loading");
					$.ajax({
						type: "POST",
						url: "php/email-sender.php",
						data: {
							"name": $("#rsvp #name").val(),
							"email": $("#rsvp #email").val(),
							"guests": $("#rsvp #guests").val(),
							"subject": "RSVP",
							"events": $("#rsvp #events").val()
						},
						dataType: "json",
						success: function (data) {
							if (data.sent == "yes") {
								$("#MessageSent").removeClass("hidden");
								$("#MessageNotSent").addClass("hidden");
								$(".submit-button").removeClass("btn-default").addClass("btn-success").prop('value', 'Message Sent');
								$("#rsvp .form-control").each(function() {
									$(this).prop('value', '').parent().removeClass("has-success").removeClass("has-error");
								});
							} else {
								$("#MessageNotSent").removeClass("hidden");
								$("#MessageSent").addClass("hidden");
							}
						}
					});
				},
				errorPlacement: function(error, element) {
					error.insertAfter( element );
				},
				onkeyup: false,
				onclick: false,
				rules: {
					name: {
						required: true,
						minlength: 2
					},
					email: {
						required: true,
						email: true
					},
					guests: {
						required: true
					},
					events: {
						required: true
					}
				},
				messages: {
					name: {
						required: "Please specify your name",
						minlength: "Your name must be longer than 2 characters"
					},
					email: {
						required: "We need your email address to contact you",
						email: "Please enter a valid email address e.g. name@domain.com"
					}
				},
				errorElement: "span",
				highlight: function (element) {
					$(element).parent().removeClass("has-success").addClass("has-error");
					$(element).siblings("label").addClass("hide");
				},
				success: function (element) {
					$(element).parent().removeClass("has-error").addClass("has-success");
					$(element).siblings("label").removeClass("hide");
				}
			});
		};


		// Affix Menu
		//-----------------------------------------------
		if ($(".affix-menu").length>0) {
			setTimeout(function () {
				var $sideBar = $('.sidebar')

				$sideBar.affix({
					offset: {
						top: function () {
							var offsetTop = $sideBar.offset().top
							return (this.top = offsetTop - 65)
						},
						bottom: function () {
							var affixBottom = $(".footer").outerHeight(true) + $(".subfooter").outerHeight(true)
							if ($(".footer-top").length>0) {
								affixBottom = affixBottom + $(".footer-top").outerHeight(true)
							}
							return (this.bottom = affixBottom+50)
						}
					}
				})
			}, 100)
		}

		//Scroll Spy
		//-----------------------------------------------
		if($(".scrollspy").length>0) {
			$("body").addClass("scroll-spy");
			if($(".fixed.header").length>0) {
				$('body').scrollspy({
					target: '.scrollspy',
					offset: 85
				});
			} else {
				$('body').scrollspy({
					target: '.scrollspy',
					offset: 20
				});
			}
		}

		//Smooth Scroll
		//-----------------------------------------------
		if ($(".smooth-scroll").length>0) {
			if(($(".header.fixed").length>0) && (Modernizr.mq('only all and (min-width: 768px)'))) {
				$('.smooth-scroll a, a.smooth-scroll').click(function() {
					if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
						var target = $(this.hash);
						target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
						if (target.length) {
							$('html,body').animate({
								scrollTop: target.offset().top-63
							}, 1000);
							return false;
						}
					}
				});
			} else {
				$('.smooth-scroll a, a.smooth-scroll').click(function() {
					if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
						var target = $(this.hash);
						target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
						if (target.length) {
							$('html,body').animate({
								scrollTop: target.offset().top
							}, 1000);
							return false;
						}
					}
				});
			}
		}

		// Offcanvas side navbar
		//-----------------------------------------------
		if ($("#offcanvas").length>0) {
			$('#offcanvas').offcanvas({
				canvas: "body",
				disableScrolling: false,
				toggle: false
			});
		};

		if ($("#offcanvas").length>0) {
			$('#offcanvas [data-toggle=dropdown]').on('click', function(event) {
			// Avoid following the href location when clicking
			event.preventDefault();
			// Avoid having the menu to close when clicking
			event.stopPropagation();
			// close all the siblings
			$(this).parent().siblings().removeClass('open');
			// close all the submenus of siblings
			$(this).parent().siblings().find('[data-toggle=dropdown]').parent().removeClass('open');
			// opening the one you clicked on
			$(this).parent().toggleClass('open');
			});
		};

		// Parallax section
		//-----------------------------------------------
		if (($(".parallax").length>0)  && !Modernizr.touch ){
			$(".parallax").parallax("50%", 0.2);
		};
		if (($(".parallax-2").length>0)  && !Modernizr.touch ){
			$(".parallax-2").parallax("50%", 0.3);
		};
		if (($(".parallax-3").length>0)  && !Modernizr.touch ){
			$(".parallax-3").parallax("50%", 0.4);
		};

		// Notify Plugin
		//-----------------------------------------------
		if ($(".btn-alert").length>0){
			$(".btn-alert").on('click', function(event) {
				$.notify({
					// options
					message: 'Great! you have just created this message :-) you can configure this into the template.js file'
				},{
					// settings
					type: 'info',
					delay: 4000,
					offset : {
						y: 100,
						x: 20
					}
				});
				return false;
			});
		};

		// Remove Button
		//-----------------------------------------------
		$(".btn-remove").click(function() {
			$(this).closest(".remove-data").remove();
		});

		// Shipping Checkbox
		//-----------------------------------------------
		if ($("#shipping-info-check").is(':checked')) {
			$("#shipping-information").hide();
		}
		$("#shipping-info-check").change(function(){
			if ($(this).is(':checked')) {
				$("#shipping-information").slideToggle();
			} else {
				$("#shipping-information").slideToggle();
			}
		});

		// Full Width Image Overlay
		//-----------------------------------------------
		if ($(".full-image-overlay").length>0) {
			overlayHeight = $(".full-image-overlay").outerHeight();
			$(".full-image-overlay").css("marginTop",-overlayHeight/2);
		};

		//This will prevent the event from bubbling up and close the dropdown when you type/click on text boxes (Header Top).
		//-----------------------------------------------
		$('.header-top .dropdown-menu input').click(function(e) {
			e.stopPropagation();
		});

	}); // End document ready

})(this.jQuery);

if (jQuery(".btn-print").length>0) {
	function print_window() {
		var mywindow = window;
		mywindow.document.close();
		mywindow.focus();
		mywindow.print();
		mywindow.close();
	}
}