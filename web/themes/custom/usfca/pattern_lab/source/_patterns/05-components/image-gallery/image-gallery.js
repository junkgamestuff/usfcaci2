(function ($) {
  'use strict';

  var self;
  var breakpoint = window.matchMedia('(min-width: 1024px)');

  /**
   * Main functionality should be in an 'init' function
   */
  var initImageGallery = function () {
    self = $(this);
    var $slideCount = self.find('.swiper-container.gallery-top .swiper-slide').length;
    var $swiper = self.find('.swiper-container.gallery-top');

    self.imagesLoaded(function () {
      if ($slideCount > 1) {
        var textBottom = new Swiper('.gallery-text-bottom', {
          a11y: true,
          spaceBetween: 0,
          slidesPerView: 'auto',
          loop: true,
          watchSlidesVisibility: true,
          watchSlidesProgress: true,
          on: {
            init: function () {
              $('.swiper-slide:not(.swiper-slide-visible)').attr('aria-hidden', true);
              $('.swiper-slide:not(.swiper-slide-visible)').attr('tabindex', '-1');
              $('.swiper-slide:not(.swiper-slide-visible)').find('a, button').attr({
                'aria-hidden': true,
                'tabindex': -1
              });
              $('.swiper-slide-visible').attr('aria-hidden', false);
              $('.swiper-slide-visible').attr('tabindex', '0');
            },
            slideChangeTransitionEnd: function () {
              $('.swiper-slide:not(.swiper-slide-visible)').attr('aria-hidden', true);
              $('.swiper-slide:not(.swiper-slide-visible)').attr('tabindex', '-1');
              $('.swiper-slide:not(.swiper-slide-visible)').find('a, button').attr({
                'aria-hidden': true,
                'tabindex': -1
              });
              $('.swiper-slide-visible').attr('aria-hidden', false);
              $('.swiper-slide-visible').attr('tabindex', '0');
              $('.swiper-slide-visible').find('a, button').attr({
                'aria-hidden': false,
                'tabindex': 0
              });
            }
          }
        });

        var galleryTop = new Swiper('.gallery-top', {
          a11y: true,
          spaceBetween: 10,
          slidesPerView: 'auto',
          centeredSlides: true,
          watchSlidesVisibility: true,
          watchSlidesProgress: true,
          preloadImages: true,
          updateOnImagesReady: true,
          loop: true,
          navigation: {
            nextEl: $('.swiper-button-next', self)[0],
            prevEl: $('.swiper-button-prev', self)[0]
          },
          breakpoints: {
            768: {
              spaceBetween: 33
            },
            1024: {
              spaceBetween: 50
            }
          },
          on: {
            imagesReady: function () {
              $('.swiper-slide:not(.swiper-slide-visible)').attr('aria-hidden', true);
              $('.swiper-slide:not(.swiper-slide-visible)').attr('tabindex', '-1');
              $('.swiper-slide:not(.swiper-slide-visible)').find('a, button').attr({
                'aria-hidden': true,
                'tabindex': -1
              });
              $('.swiper-slide-visible').attr('aria-hidden', false);
              $('.swiper-slide-visible').attr('tabindex', '0');
            },
            slideChangeTransitionEnd: function () {
              $('.swiper-slide:not(.swiper-slide-visible)').attr('aria-hidden', true);
              $('.swiper-slide:not(.swiper-slide-visible)').attr('tabindex', '-1');
              $('.swiper-slide:not(.swiper-slide-visible)').find('a, button').attr({
                'aria-hidden': true,
                'tabindex': -1
              });
              $('.swiper-slide-visible').attr('aria-hidden', false);
              $('.swiper-slide-visible').attr('tabindex', '0');
              $('.swiper-slide-visible').find('a, button').attr({
                'aria-hidden': false,
                'tabindex': 0
              });
            }
          }
        });

        // carousel controller
        galleryTop.controller.control = textBottom;
        textBottom.controller.control = galleryTop;

        var checkBreakpoint = function () {
          if (breakpoint.matches) {
            // true - Desktop - Disable swiping
            $swiper.addClass('swiper-no-swiping');
          }
          else {
            // false - Mobile/Tablet - Lets swipe
            $swiper.removeClass('swiper-no-swiping');
          }
        };

        checkBreakpoint.apply(this);
        breakpoint.addEventListener('change', checkBreakpoint.bind(this));

        // Update swipers when tabs are clicked and they contain multiple sliders
        $(window).on('hashchange', function () {
          galleryTop.update();
          textBottom.update();
        });
      }
      else {
        $('.swiper-button-wrapper', self).addClass('hidden');
      }
    });
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.imageGallery = {
    attach: function (context) {
      $(context).find('.cc--image-gallery').once('imageGallery').each(function () {
        initImageGallery.apply(this);
      });
    }
  };

}(jQuery));
