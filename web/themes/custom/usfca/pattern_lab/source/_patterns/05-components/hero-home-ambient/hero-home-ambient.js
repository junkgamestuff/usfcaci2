(function ($) {
  'use strict';

  var self;

  /**
   * Main functionality should be in an 'init' function
   */
  var heroAmbientInit = function () {
    var self = $(this);
    var breakpoint = window.matchMedia('(min-width: 768px)');
    var $slidesCount = $('.cc--hero-home-ambient-slide', self).length;
    var $videos = $('.cc--hero-home-ambient-slide', self).find('video');

    if ($slidesCount < 1) {
      return false;
    }

    var checkBreakpoint = function () {

      $videos = $('.cc--hero-home-ambient-slide', self).find('video');

      if (breakpoint.matches) {
        $videos.each(function (i, el) {
          $(el).attr('autoplay', '');
          el.play();
        });
      }
      else {
        $videos.each(function (i, el) {
          $(el).removeAttr('autoplay');
          el.pause();
        });
      }
    };

    breakpoint.addEventListener('change', checkBreakpoint());

    /* eslint no-undef: "off" */
    /* eslint no-unused-vars: "off" */
    var mySwiper = new Swiper('.cc--hero-home-ambient .swiper-container', {
      slidesPerView: 1,
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      watchOverflow: true,
      a11y: true,
      loop: $slidesCount > 1,
      simulateTouch: false,
      navigation: {
        nextEl: $('.swiper-button-next', self)[0],
        prevEl: $('.swiper-button-prev', self)[0]
      },
      on: {
        imagesReady: function () {
          $('.swiper-slide:not(.swiper-slide-visible)', self).attr('aria-hidden', true);
          $('.swiper-slide:not(.swiper-slide-visible)', self).attr('tabindex', '-1');
          $('.swiper-slide:not(.swiper-slide-visible)', self).find('a, button').attr({
            'aria-hidden': true,
            'tabindex': -1
          });
          $('.swiper-slide-visible', self).attr('aria-hidden', false);
          $('.swiper-slide-visible', self).attr('tabindex', '0');
        },
        slideChangeTransitionEnd: function () {
          $('.swiper-slide:not(.swiper-slide-visible)', self).attr('aria-hidden', true);
          $('.swiper-slide:not(.swiper-slide-visible)', self).attr('tabindex', '-1');
          $('.swiper-slide:not(.swiper-slide-visible)', self).find('a, button').attr({
            'aria-hidden': true,
            'tabindex': -1
          });
          $('.swiper-slide-visible', self).attr('aria-hidden', false);
          $('.swiper-slide-visible', self).attr('tabindex', '0');
          $('.swiper-slide-visible', self).find('a, button').attr({
            'aria-hidden': false,
            'tabindex': 0
          });
        }
      }
    });

    checkBreakpoint.apply(this);
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.homepageHero = {
    attach: function (context) {
      $(context).find('.cc--hero-home-ambient').once('heroAmbient').each(function () {
        heroAmbientInit.apply(this);
      });
    }
  };

}(jQuery));
