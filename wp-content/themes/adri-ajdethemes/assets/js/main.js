(function($){
    'use strict';
    
    navBurger();
    navDropdown();
    navSticky();
    showSearchModal();
    backToTop();
    initWooReviewFormThemeStyle();
    formStyleHelperClasses();
    teamCardHelperClasses();
    opNavScroll();
    postNav();
    widgetSliderRecentPosts();
    initParallax();
    btnInt();



    //
    //  Navigation Dropdown
    // -----------------------------------------

    function navDropdown() {
        if ($('li.menu-item').hasClass('menu-item-has-children')) {

            $('.menu-item-has-children > a').on('click tap touch', function(e) {
                e.preventDefault();

                $(this).parent().toggleClass('open');
            });
    
        }
    }



    //
    //  Navigation Burger
    // -----------------------------------------

    function navBurger() {
        if ($('.brand span').hasClass('nav-burger')) {

            $('.nav-burger').on('click tap touch', function() {
                $(this).toggleClass('open');
                $('nav.nav-main').toggleClass('show-mobile-nav');
            });
    
        }
    }



    //
    //  Navigation Sticky
    // -----------------------------------------

    function navSticky() {
        if ($('header.site-header-nav').hasClass('nav-sticky')) {
            // caches a jQuery object containing the header element, 
            // so it won't query DOM on every scroll.
            var navbar = $('header.site-header-nav'),
                body = $('body');

            $(window).scroll(function() {
                if ($(window).scrollTop() >= 120) {
                    navbar.addClass('stand-by');
                } else {
                    navbar.removeClass('stand-by');
                }

                if ($(window).scrollTop() >= 550) {
                    navbar.addClass('active');
                    
                    if ( ! navbar.hasClass('nav-trans') ) {
                        body.addClass('has-sticky-nav');
                    }
                } else {
                    navbar.removeClass('active');

                    if ( ! navbar.hasClass('nav-trans') ) {
                        body.removeClass('has-sticky-nav');
                    }
                }
            });
    
        }
    }    



    //
    //  Search Modal
    // -----------------------------------------

    function showSearchModal() {
        if ($('button').hasClass('nav-search') && $('aside').hasClass('search-modal')) {

            // show
            $('.nav-search').on('click tap touch', function() {
                $('aside.search-modal').addClass('show').css(
                    {
                        'display':'flex',
                        'width':'100%',
                        'height':'100%',
                    }
                );
            });

            // close
            $('button.dark-bg-click-close, button.x-close').on('click tap touch', function() {
                $('aside.search-modal').removeClass('show');
            });

        }
    }



    //
    //  Back to Top Scroll
    // -----------------------------------------

    function backToTop() {

        var btn = $('#ftrBackToTop');

        if (btn) {

            btn.on('click tap touch', function(e) {
                e.preventDefault();
    
                $('html, body').animate({
                    scrollTop: 0
                }, 1150);
            });

        }
    }



    //
    //  Adds helper classes on the form inputs
    // -----------------------------------------

    function formStyleHelperClasses() {
        $('.form-style').attr('placeholder', '');

        $('.form-style').focus(function() {

            $(this).closest('.form-group').addClass('is-focused');
    
        }).blur(function() {
    
            $(this).closest('.form-group').removeClass('is-focused');
    
            if ( $(this).val() ) {

                $(this).closest('.form-group').addClass('is-not-empty');

            } else {

                $(this).closest('.form-group').removeClass('is-not-empty');

            }
            
        });
    }



    //
    //  Team Card helper class
    // -----------------------------------------

    function teamCardHelperClasses() {
        $('.team-card .btn-bio').on('click tap touch', function(e) {
            e.preventDefault();
            $(this).parentsUntil($('.elementor-widget-container'), '.team-card').toggleClass('open');
        })
    }



    //
    //  Scroll Spy - One Page Nav
    // -----------------------------------------

    function opNavScroll() {

        if ($('.op-section')[0]) {

            var section = $(".op-section");
            var sections = {};
            var i = 0;

            Array.prototype.forEach.call(section, function(e) {
            sections[e.id] = e.offsetTop;
            });

            window.onscroll = function() {
                var scrollPosition = document.documentElement.scrollTop || document.body.scrollTop;

                for (i in sections) {
                    if (sections[i] <= scrollPosition) {
                        $('li.menu-item').removeClass('current_page_item');
                        $('a[href*=' + i + ']').parent().addClass('current_page_item ');
                    }
                }
            }

        }
    }



    //
    //  Single Post Nav.
    // -----------------------------------------

    function postNav() {

        // Tiny jQuery extension that 
        // enables to use "delay" with css
        $.fn.extend({
            qcss: function (css) {
                return $(this).queue(function (next) {
                    $(this).css(css);
                    next();
                });
            },
        });

        var postNav = $('.post-nav');

        if (postNav[0]) {
            
            $(window).scroll(function() {
                if ($(window).scrollTop() >= 590 && ( ( $(document).height() - $(window).height() - $(window).scrollTop() ) >= 600 ) ) {
                    postNav.addClass('active').removeClass('hidden');
                } else {
                    postNav.removeClass('active').addClass('hidden');
                }
            });

            $('.post-nav-control').on('mouseenter', function() {
                $(this).parent().parent().css('z-index', 20);
            });
            
            $('.post-nav-thumb').on('mouseleave', function() {
                $(this).parent().parent().delay('400').qcss({ zIndex: 0});
            });
            
        }
    }



    //
    //   Widget - Slider Recent Posts
    // ---------------------------------

    function widgetSliderRecentPosts() {

        var WSRP = $('.slider-recent-posts');

        if (WSRP[0]) {

            var widgetSliderRP = new Swiper('.slider-recent-posts', {
                loop: true,
              
                navigation: {
                  nextEl: '.swiper-button-next',
                  prevEl: '.swiper-button-prev',
                }
            });
        }
    }



    //
    //  Parallax
    // -----------------------

    function initParallax() {
        if ( $('img').hasClass('rellax') ) {
            var rellax = new Rellax('.rellax', {
                center: true
            });

            if (window.innerWidth < 992) {
                rellax.destroy();
            }
        }
    }


    
    //
    //  WooCommerce Review Form
    // ---------------------------

    function initWooReviewFormThemeStyle() {
        var reviews = $('#review_form');

        if ( reviews[0] ) {
            $('#review_form input[type="text"], #review_form input[type="email"], #review_form textarea').addClass('form-style');
            $('#review_form .comment-form-comment, #review_form .comment-form-author, #review_form .comment-form-email').wrapInner('<div class="form-group"></div>');

            $('#review_form #commentform').wrapInner('<div class="row"></div>');
            $('#review_form .comment-form-author, #review_form .comment-form-email').wrap('<div class="col-lg-6"></div>');
            $('#review_form .comment-form-comment, #review_form .comment-form-cookies-consent, #review_form .form-submit, #review_form .comment-form-rating, #review_form .comment-notes').wrap('<div class="col-lg-12"></div>');
        }
    }


    //
    //  Button Interactive
    // -----------------------

    function btnInt() {
        if ($('a, button, rs-layer').hasClass('btn-int')) {
            $('a.btn-int, button.btn-int, rs-layer.btn-int').wrapInner('<span></span>');
        }

        if ($('li').hasClass('btn-int') && !$('li').hasClass('btn-has-dark-bg')) {
            $('li.btn-int')
                .attr('class', 'btn btn-int btn-int-icon')
                .children()
                .attr('class', '');
            $('li.btn-int > a').wrapInner('<span></span>');
        } else {
            $('li.btn-int')
                .attr('class', 'btn btn-int btn-int-icon btn-has-dark-bg')
                .children()
                .attr('class', '');
            $('li.btn-int > a').wrapInner('<span></span>');
        }
    }



})(jQuery);

