(function ($) {
  'use strict';

  var self;
  /**
   * Main functionality should be in an 'init' function
   */
  var initRibbon = function () {
    self = $(this);

    var breakpoint = window.matchMedia('(min-width: 1024px)');
    var $slideCount = self.find('.ribbon-container .swiper-slide').length;

    var ribbonSwiper = new Swiper('.ribbon-container', {
      a11y: true,
      loop: true,
      slidesPerView: 'auto',
      navigation: {
        nextEl: $('.swiper-button-next', self)[0],
        prevEl: $('.swiper-button-prev', self)[0]
      },
      threshold: 25,
      on: {
        init: function () {
          $('.swiper-slide:not(.swiper-slide-visible)').attr('aria-hidden', 'true');
          $('.swiper-slide:not(.swiper-slide-visible)').attr('tabindex', '-1');
          $('.swiper-slide:not(.swiper-slide-visible)').find('a, button').attr({
            'aria-hidden': 'true',
            'tabindex': '-1'
          });
          $('.swiper-slide-visible').attr('aria-hidden', 'false');
          $('.swiper-slide-visible').attr('tabindex', '0');
        },
        slideChangeTransitionEnd: function () {
          $('.swiper-slide:not(.swiper-slide-visible)').attr('aria-hidden', 'true');
          $('.swiper-slide:not(.swiper-slide-visible)').attr('tabindex', '-1');
          $('.swiper-slide:not(.swiper-slide-visible)').find('a, button').attr({
            'aria-hidden': 'true',
            'tabindex': '-1'
          });
          $('.swiper-slide-visible').attr('aria-hidden', 'false');
          $('.swiper-slide-visible').attr('tabindex', '0');
          $('.swiper-slide-visible').find('a, button').attr({
            'aria-hidden': 'false',
            'tabindex': '0'
          });
        }
      }
    });

    var checkBreakpoint = function (ribbonSwiper) {
      if (breakpoint.matches) {
        if (ribbonSwiper && $slideCount <= 2) {
          ribbonSwiper.destroy();
          $('.swiper-controls', self).addClass('hidden');
        }
      }
    };

    checkBreakpoint(ribbonSwiper);

    var timeout;
    $(window).on('resize', function () {
      clearTimeout(timeout);
      timeout = setTimeout(checkBreakpoint(ribbonSwiper), 250);
    });
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.ribbonCarousel = {
    attach: function (context) {
      $(context).find('.cc--ribbon-carousel').once('ribbonCarousel').each(function () {
        initRibbon.apply(this);
      });
    }
  };

}(jQuery));
