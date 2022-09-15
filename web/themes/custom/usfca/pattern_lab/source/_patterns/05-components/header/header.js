(function ($) {
  'use strict';

  /**
   * Main functionality should be in an 'init' function
   */
  var initStickyHeader = function () {
    var self = this;

    var scrollUp = 'scroll-up';
    var scrollDown = 'scroll-down';
    var lastScroll = 0;
    var desktop = window.matchMedia('(min-width: 1024px)');
    var responsive = window.matchMedia('(max-width: 1023px)');
    var $jumpLinks = $('.cc--landing-sub-page-jump-links');

    if (!$jumpLinks.length) {
      window.addEventListener('scroll', function (e) {

        var currentScroll = window.pageYOffset;
        if (currentScroll === 0) {
          self.classList.remove(scrollUp);
          return;
        }

        if (desktop.matches && currentScroll <= 150 || responsive.matches && currentScroll <= 60) {
          self.classList.remove(scrollDown);
          self.classList.remove(scrollUp);
        }
        else if (currentScroll > lastScroll && !self.classList.contains(scrollDown)) {
          // down
          self.classList.remove(scrollUp);
          self.classList.add(scrollDown);
        }
        else if (currentScroll < lastScroll && self.classList.contains(scrollDown)) {
          // up
          self.classList.remove(scrollDown);
          self.classList.add(scrollUp);
        }
        lastScroll = currentScroll;
      });
    }
    else {
      $('body').addClass('jump-links');
    }
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.stickyHeader = {
    attach: function (context) {
      $(context).find('.cc--header').once('stickyHeader').each(function () {
        initStickyHeader.apply(this);
      });
    }
  };

}(jQuery));

