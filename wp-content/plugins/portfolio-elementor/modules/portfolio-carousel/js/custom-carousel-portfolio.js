//Use Strict Mode
(function ($) {
    "use strict";

    //Begin - Window Load
    $(window).load(function () {

        $(".elpt-portfolio-carousel").owlCarousel({
            nav: false,
            margin: 10,
            responsive : {
                0 : {
                    items: 1,
                },
                // breakpoint from 768 up
                768 : {
                   items: 3,
                },
                980 : {
                    items: 4,
                }
            }
        });
        

    });
    

    //End - Use Strict mode
})(jQuery);