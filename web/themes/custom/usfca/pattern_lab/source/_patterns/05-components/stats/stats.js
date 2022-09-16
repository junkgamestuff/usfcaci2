(function ($) {
  'use strict';

  var self;


  /**
   * Main functionality should be in an 'init' function
   */
  var initStats = function () {
    self = $(this);

    var $slidesCount = $('.swiper-slide', self).length;

    if ($slidesCount < 4) {
      return false;
    }


    var mySwiper = new Swiper($('.stats-container', self)[0], {
      a11y: true,
      loop: true,
      slideVisibleClass: 'swiper-slide-visible',
      watchSlidesProgress: true,
      breakpoints: {
        // when window width is >= 320px
        320: {
          slidesPerView: 1,
          slidesPerGroup: 1,
          centeredSlides: true,
        },
        // when window width is >= 768px
        768: {
          slidesPerView: 2,
          slidesPerGroup: 1,
          centeredSlides: true
        },
        // when window width is >= 1024px
        1024: {
          slidesPerView: 'auto',
          slidesPerGroupAuto: false,
          slidesPerGroup: 1,
          initialSlide: 0,
          centeredSlides: true,
          loopedSlides: 6
        }
      },
      simulateTouch: false,
      navigation: {
        nextEl: $('.swiper-button-next', self)[0],
        prevEl: $('.swiper-button-prev', self)[0]
      },
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

  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.stats = {
    attach: function (context) {
      $(context).find('.cc--stats').once('stats').each(function () {
        initStats.apply(this);
      });
    }
  };

}(jQuery));
