(function ($) {
  'use strict';

  /**
   * Main functionality should be in an 'init' function
   */
  var initStickyCta = function () {
    var self = this;

    function bodyOffset() {
      $('body').css('padding-bottom', self.offsetHeight + 'px');
    }

    if ($(self).length) {
      bodyOffset();

      $(window).on('resize', function () {
        bodyOffset();
      });
    }
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.stickyCta = {
    attach: function (context) {
      $(context).find('.cc--sticky-cta').once('stickyCta').each(function () {
        initStickyCta.apply(this);
      });
    }
  };
})(jQuery);
