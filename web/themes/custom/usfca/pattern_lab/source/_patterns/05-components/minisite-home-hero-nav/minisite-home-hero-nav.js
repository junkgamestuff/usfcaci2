(function ($) {
  'use strict';

  var initMinisiteMenu = function () {
    var breakpoint = window.matchMedia('(min-width: 1024px)');
    var subNavOpenLabel = 'Expand ';
    var subNavCloseLabel = 'Close ';

    if($('.cc--minisite-home-hero-nav .m--main .menu-item').hasClass('is-open')) {
      $('.cc--minisite-home-hero-nav .m--main .is-open').find('.submenu').slideDown(200);
    }

    // submenu toggle
    var toggleSubMenu = function (el) {
      // get the toggle
      var $el = $(el);
      var $toggleParent = $('li.menu-item');
      // check if it's open
      var navItemsOpenLabel = subNavOpenLabel + 'Sub-Navigation';
      var navItemsCloseLabel = subNavCloseLabel + 'Sub-Navigation';

      if ($el.hasClass('is-open')) {
        // close if it's opened
        $el.closest($toggleParent).removeClass('is-open');
        $el.removeClass('is-open');
        $el.next('.submenu').attr('aria-hidden', 'true');
        $el.next('.submenu').slideUp(200);
        $el.closest($toggleParent).attr('aria-expanded', 'false');
        $el.attr('aria-label', navItemsOpenLabel);
      }
      else {
        // if it's not open, open it
        $el.closest('.m--main').find('li.is-open .submenu').attr('aria-hidden', 'true');
        $el.closest('.m--main').find('li.is-open .submenu').slideUp(200);
        $el.closest('.m--main').find('button.is-open').attr('aria-label', navItemsOpenLabel);
        $el.closest('.m--main').find('li.is-open').attr('aria-expanded', 'false');
        $el.closest('.m--main').find('button.is-open').removeClass('is-open');
        $el.closest('.m--main').find('li.is-open').removeClass('is-open');

        $el.closest($toggleParent).addClass('is-open');
        $el.addClass('is-open');
        $el.next('.submenu').attr('aria-hidden', 'false');
        $el.next('.submenu').slideDown(200);
        $el.closest($toggleParent).attr('aria-expanded', 'true');
        $el.attr('aria-label', navItemsCloseLabel);
      }
    };

    $('.cc--minisite-home-hero-nav .m--main > .menu-item button').on('click', function (e) {
      e.preventDefault();
      toggleSubMenu(this);
    });

    var checkBreakpoint = function () {
      if (breakpoint.matches) {
        setTimeout(function () {
          var distance = $('.cc--minisite-home-hero-nav .buttons-container').offset().top,
          $window = $(window);

          $window.scroll(function() {
            if ($window.scrollTop() >= distance) {
              $('.cc--minisite-home-hero-nav .buttons-container').addClass('fixed');
            }
            else {
              $('.cc--minisite-home-hero-nav .buttons-container').removeClass('fixed');
            }
          });
        }, 300);
      }
    };

    breakpoint.addEventListener('change', checkBreakpoint());
    checkBreakpoint.apply(this);
  };

  window.DP.behaviors.minisiteMenu = {
    attach: function (context) {
      $(context).find('.cc--minisite-home-hero-nav').once('minisiteMenu').each(function () {
        initMinisiteMenu.apply();
      });
    }
  };

})(jQuery);
