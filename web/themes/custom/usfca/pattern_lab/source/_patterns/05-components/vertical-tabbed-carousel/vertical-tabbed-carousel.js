(function ($) {
  'use strict';

  /**
   * Main functionality should be in an 'init' function
   */
  var sideTabCarousel = function () {
    var self = $(this);
    var $swiper = $('.swiper-container', self)[0];
    var $paginationEl = $('.swiper-pagination', self)[0];
    var $slideTitle = $('.tab-title', self);

    self.swiper = new Swiper($swiper, {
      speed: 0,
      a11y: {
        enabled: false
      },
      pagination: {
        el: $paginationEl,
        clickable: true,
        bulletClass: 'swiper-pagination-customs',
        bulletActiveClass: 'swiper-pagination-customs-active',
        renderBullet: function (index, className) {
          // side navigation
          var slideTitle = $slideTitle.eq(index).html();

          return (
            '<li class="' + className + '"><button class="side-nav-link">' +
            slideTitle +
            '</button></li>'
          );
        }
      },
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      on: {
        imagesReady: function () {
          $('.swiper-slide:not(.swiper-slide-active)', self).attr(
            'aria-hidden',
            true
          );
          $('.swiper-slide-active', self).attr('aria-hidden', false);
          $('.swiper-slide:not(.swiper-slide-active)', self)
            .find('a, button')
            .attr({
              'aria-hidden': true,
              'tabindex': -1
            });
          $('.swiper-slide-active', self).find('a, button').attr({
            'aria-hidden': false,
            'tabindex': 0
          });

          $('select', self).on('change', function(e) {
            var index = this.options[e.target.selectedIndex].value;
            self.swiper.slideTo(index);
          });

          self.addClass('ready');

        },
        slideChangeTransitionEnd: function () {
          $(
            '.swiper-slide-active:not(.swiper-slide-prev, .swiper-slide-next)',
            self
          ).attr('aria-hidden', false);
          $('.swiper-slide:not(.swiper-slide-active)', self).attr(
            'aria-hidden',
            true
          );
          $('.swiper-slide:not(.swiper-slide-active)', self)
            .find('a, button')
            .attr({
              'aria-hidden': true,
              'tabindex': -1
            });
          $('.swiper-slide-active', self).find('a, button').attr({
            'aria-hidden': false,
            'tabindex': 0
          });
          self.find('.slides-images .f--image').css('opacity', 0);
          self.find('.slides-images .f--image').eq(this.activeIndex).css('opacity', 1);
        }
      }
    });
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.sideTabCarousel = {
    attach: function (context) {
      $(context).find('.cc--vertical-tabbed-carousel').once('sideTabCarousel').each(function () {
        sideTabCarousel.apply(this);
      });
    }
  };
})(jQuery);
