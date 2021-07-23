(function($){
    'use strict';

    $(document).ready(function () {

      sliderTestimonials();
      sliderNeue();
      imageSlider();
      portfolioFilters();
      initLightbox();
      initContactTabs();
      thirdsSlider();
      thirdsSliderButtonOpen();
    });



    //
    //  Testimonials Slider
    // -----------------------------------------

    function sliderTestimonials() {
      if ( $('div').hasClass('testimonial-slider-container') ) {

        var sliderTst = new Swiper ('.testimonial-slider-container', {
          loop: true,
          speed: 550,
          direction: 'horizontal',
          pagination: {
            el: '.swiper-pagination',
          },        
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          }
        })

      } 
    }
    
    
    
    //
    //  Neue Slider
    // -----------------------------------------

    function sliderNeue() {
      if ( $('div').hasClass('neue-slider-container') ) {

        var neueSlider = new Swiper ('.neue-slider-container', {
            loop: true,
            speed: 550,
            slidesPerView: 1,
            centeredSlides: false,
            spaceBetween: 0,
            navigation: {
              nextEl: '.ns-button-next',
              prevEl: '.ns-button-prev',
            },
            breakpoints: {
              768: {
                slidesPerView: 2,
                centeredSlides: true,
                spaceBetween: 60,
              }
            }
        })

      }
    }



    //
    //  Image Slider
    // -----------------------------------------

    function imageSlider() {
      if ( $('div').hasClass('image-slider-container') ) {

        var imageSlider = new Swiper ('.image-slider-container', {
            loop: true,
            speed: 550,
            slidesPerView: 1,
            pagination: {
              el: '.swiper-pagination',
            },
            navigation: {
              nextEl: '.swiper-button-next',
              prevEl: '.swiper-button-prev',
            }
        })

      }
    }


    //
    //  Thirds Slider
    // -----------------------------------------

    function thirdsSlider() {
      if ( $('div').hasClass('slider-thirds') ) {

        var sliderThirds = new Swiper ('.slider-thirds', {
            loop: true,
            loopedSlides: 1,
            speed: 550,
            slidesPerView: 1,
            watchSlidesVisibility: true,
            pagination: false,
            navigation: false,
            breakpoints: {
              768: {
                slidesPerView: 2,
              },
              992: {
                slidesPerView: 3,
              }
            }
        })

      }
    }

    function thirdsSliderButtonOpen() {
      if ( $('div').hasClass('slider-thirds') ) {

        $('.s-item-footer button').on('click', function() {
          $(this).parent().addClass('open');
          $(this).closest(':has(.s-item-content)').find('.s-item-content').addClass('open');
        });

        $('button.s-item-btn-close').on('click', function() {
            $(this).closest(':has(.s-item-content)').find('.s-item-content').removeClass('open');
            $(this).closest(':has(.s-item-footer)').find('.s-item-footer').removeClass('open');
        });

      }
    }



    //
    //  Portfolio & Filters
    // -----------------------------------------

    function portfolioFilters() {
      if ( $('div').hasClass('portfolio_grid') ) {

        var $grid = $('.portfolio_grid').imagesLoaded(function() {
          $grid.isotope();
  
          $('.pf-filters').on('click', 'button', function() {
            
            $('.pf-filters button').removeClass('is-checked');
            $(this).addClass('is-checked');
  
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({ filter: filterValue });
          })
        });

      }
    }



    //
    //  Lightbox
    // -----------------------------------------

    function initLightbox() {
      if ( $('.open-lightbox-video')[0] ) {
        $('.open-lightbox-video').magnificPopup({
          disableOn: 700,
          type: 'iframe',
          mainClass: 'mfp-with-zoom',
          zoom: {
            enabled: true
          },
          removalDelay: 160,
          preloader: false,
          fixedContentPos: false
        });
      }

      if ( $('.open-lightbox')[0] ) {
        $('.portfolio_grid').each(function() {
          $(this).magnificPopup({
            delegate: '.open-lightbox',
            type: 'image',
            gallery: {
              enabled: true
            },
            mainClass: 'mfp-with-zoom',
            zoom: {
              enabled: true
            },
            titleSrc: 'title'
          });
        });
      }
    }



    //
    //  Contact Map Tabs
    // -----------------------

    function initContactTabs() {
      if ( $('div').hasClass('cnt-tabs') ) {

        $('.cnt-tabs .tc-header').on('click', function() {
          var tab_id = $(this).attr('data-tab');
  
          $('.cnt-tabs .cnt-map .elementor-custom-embed').removeClass('active');
  
          $('#' + tab_id).addClass('active');
        });

      }
    }

    

})(jQuery);