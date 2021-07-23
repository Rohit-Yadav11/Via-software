// Jquery document ready passing in JQuery so not to conflict with other
// libs that use $


 
(function($) {
   "use strict";
   
   $( window ).on( 'elementor/frontend/init', function() {
       
        var wezedobeforeafterelementor = function (){
 

          new BeerSlider( document.getElementById( "wezido-before-after" ), {start: 50} );
   
   };
   
   
   //BeforeAfter
        elementorFrontend.hooks.addAction( 'frontend/element_ready/wezido-before-after.default', function($scope, $){
            wezedobeforeafterelementor();
        } );
   });

})(jQuery);