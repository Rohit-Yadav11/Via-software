// Jquery document ready passing in JQuery so not to conflict with other
// libs that use $


 
(function($) {
   "use strict";
   
   $( window ).on( 'elementor/frontend/init', function() {
       
        var wezedofliboxelementor = function (){
   class WezidoFlipBox {

      constructor(flipbox) {
         this.$window = $(window)
         this.$flipbox = $(flipbox);
         this.$inner = this.$flipbox.find('.wezido-flip-inner');
         this.$front = this.$flipbox.find('.wezido-flip-front');
         this.$frontImage = this.$flipbox.find('.wezido-flip-front-image');
         this.$frontContent = this.$flipbox.find('.wezido-flip-front-content');
         this.$backContent = this.$flipbox.find('.wezido-flip-back-content');
         this.backContentPadding = parseInt(this.$backContent.css('padding-top')) * 2;
         this.timer;


         // bind event callback menthods so they use class version of this 
         this.mousehoverstart = this.mousehoverstart.bind(this);
         this.mousehoverleave = this.mousehoverleave.bind(this);
         this.touchstart = this.touchstart.bind(this);
         this.resize = this.resize.bind(this);
         this.debounceresize = this.debounceresize.bind(this);

         // setup events
         this.$flipbox.on('mouseenter', this.mousehoverstart);
         this.$flipbox.on('mouseleave', this.mousehoverleave);
         this.$flipbox.on('touchstart', this.touchstart);
         this.$window.on('resize', this.debounceresize);

         this.resize();
      }

      mousehoverstart() {
         this.$inner.addClass('hover');
      }

      mousehoverleave() {
         this.$inner.removeClass('hover');
      }

      touchstart() {
         this.$inner.toggleClass('hover');
      }

      debounceresize() {
         // debounce : don't keep calling resize box method, only call if not resized for 0.25s
         clearTimeout(this.timer);
         this.timer = setTimeout(this.resize, 250);
      }

      resize() {
         // we need to set the height of the flip box based on the max 
         // front and back height
         // outerheight() returns height in pixels with padding and border or margin
         let height = Math.max(
            isNaN(this.$frontContent.height()) ? 0 : this.$frontContent.height(),
            isNaN(this.$backContent.height()) ? 0 : this.$backContent.height()
         ) + this.backContentPadding;
         this.$inner.height(height);
         this.$frontImage.height(height);
      }
   }

   $(function() {
      // loop round all the flip boxes on the page creating an instance
      // for each one adding to an array

      window.WezidoFlipBoxs = [];
      $('.wezido-flip-box').each(function() {
         WezidoFlipBoxs.push(new WezidoFlipBox(this));
      });
   });
   
   };
   
   
   //BeforeAfter
        elementorFrontend.hooks.addAction( 'frontend/element_ready/wezido-flipbox.default', function($scope, $){
            wezedofliboxelementor();
        } );
   });

})(jQuery);