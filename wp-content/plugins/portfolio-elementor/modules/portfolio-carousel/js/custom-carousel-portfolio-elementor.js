//Use Strict Mode
(function ($) {
    "use strict";

    //Begin - Window Load
    function elpugCarousel(){


        jQuery('#elementor-preview-iframe').contents().find(".elpt-portfolio-carousel").imagesLoaded( function() {
            jQuery('#elementor-preview-iframe').contents().find(".elpt-portfolio-carousel").owlCarousel({
                nav: false,
                margin: 10,
                responsive : {
                    0 : {
                        items: 1,
                    },
                    // breakpoint from 768 up
                    768 : {
                    items: 2,
                    },
                    980 : {
                        items: 4,
                    }
                }
            });
        });

    }

    jQuery(window).on('load', function(){
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope){
            elpugCarousel();    
        });
    });

    //End - Use Strict mode
})(jQuery);