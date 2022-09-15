(function ($) {
  'use strict';

  var self;

  /**
   * Main functionality should be in an 'init' function
   */
  var homepageHeroSliderInit = function () {
    var self = $(this);
    var breakpoint = window.matchMedia('(min-width: 768px)');
    var $videos = $('.cc--hero-home-item-slide', self).find('video');
    var $videoControls = $('.cc--hero-home-item-slide', self).find('.video-button');

    $videoControls.click(function(e) {
      var btn = e.currentTarget;
      var video = btn.closest('.c--component').querySelector('video');

      if (btn.classList.value.includes('video-pause-button')) {
        video.pause();
        btn.classList.add('hidden');
        btn.nextElementSibling.classList.add('active');
        btn.nextElementSibling.focus();
      } else if (btn.classList.value.includes('video-play-button')) {
        video.play();
        btn.classList.remove('active');
        btn.previousElementSibling.classList.remove('hidden');
        btn.previousElementSibling.focus();
      }
    });

    var checkBreakpoint = function () {

      $videos = $('.cc--hero-home-item-slide', self).find('video');

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

    var $slidesCount = $('.cc--hero-home-item-slide').length;

    /* eslint no-undef: "off" */
    /* eslint no-unused-vars: "off" */
    var mySwiper = new Swiper('.cc--hero-home-slider .swiper-container', {
      slidesPerView: 1,
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      watchOverflow: true,
      a11y: true,
      loop: $slidesCount > 1,
      simulateTouch: false,
      pagination: {
        el: this.querySelector('.swiper-pagination'),
        type: 'bullets',
        clickable: true,
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
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.homepageHeroSlider = {
    attach: function (context) {
      $(context).find('.cc--hero-home-slider').once('homepageHeroSlider').each(function () {
        homepageHeroSliderInit.apply(this);
      });
    }
  };

}(jQuery));
