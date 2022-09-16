(function ($) {
  'use strict';

  var TRANSITION_TIMING = 250;
  var $mainNav = $('.nav-container');
  var subNavOpenLabel = 'Expand ';
  var subNavCloseLabel = 'Close ';
  var $arrowToggle = $('.cc--main-menu ul.m--menu > li .arrow-toggle');
  var $hamburger = $('.header-buttons .mobile-menu-trigger');
  var $searchButton = $('.header-buttons .search-trigger-button');
  var label = 'Open Menu';
  var labelClose = 'Close Menu';

  var toggleMobileMenu = function () {
    $mainNav.fadeToggle(TRANSITION_TIMING);
  };

  var initMainMenu = function () {
    // hamburger click/tap
    $hamburger.on('click', function (e) {
      e.preventDefault();
      $(this).toggleClass('is-active');
      $('html').toggleClass('main-menu-open');
      toggleMobileMenu();

      if ($(this).hasClass('is-active')) {
        $hamburger.attr('aria-label', labelClose);
        $('#l--main-header').siblings().each(function (i, el) {
          el.inert = true;
        });
        $searchButton.hide();
      }
      else {
        $hamburger.attr('aria-label', label);
        $('#l--main-header').siblings().each(function (i, el) {
          el.inert = false;
        });
        $searchButton.show();
      }

      // Windows OS scrollbar hack
      setTimeout(function () {
        if ($('.cc--header').hasClass('scroll-down')) {
          $('.cc--header').removeClass('scroll-down');
          $('.cc--header').addClass('scroll-up');
        }
      }, 300);
    });

    // submenu toggle
    var toggleSubMenu = function (el) {
      // get the toggle
      var $el = $(el);
      var $toggleParent = $('li.menu-item');
      var navItemsOpenLabel = subNavOpenLabel + 'Sub-Navigation';
      var navItemsCloseLabel = subNavCloseLabel + 'Sub-Navigation';

      // check if it's open
      if ($el.hasClass('is-open')) {
        // close if it's opened
        $el.closest($toggleParent).removeClass('is-open');
        $el.removeClass('is-open');
        $el.closest($toggleParent).find('.submenu').attr('aria-hidden', 'true');
        $el.closest($toggleParent).find('.submenus-wrapper:first').slideUp(TRANSITION_TIMING);
        $el.parent().attr('aria-expanded', 'false');
        $el.attr('aria-label', navItemsOpenLabel);
      }
      else {
        // if it's not open, open it
        $el.closest('ul').find('li.is-open .link-arrow-wrapper').attr('aria-label', navItemsOpenLabel);
        $el.closest('ul').find('li.is-open .submenu').attr('aria-hidden', 'true');
        $el.closest('ul').find('li.is-open').attr('aria-expanded', 'false');
        $el.closest('ul').find('li.is-open').find('.submenus-wrapper:first').slideUp(TRANSITION_TIMING);
        $el.closest('ul').find('li.is-open .link-arrow-wrapper').removeClass('is-open');
        $el.closest('ul').find('li.is-open').removeClass('is-open');

        $el.closest($toggleParent).addClass('is-open');
        $el.addClass('is-open');
        $el.closest($toggleParent).find('.submenu').attr('aria-hidden', 'false');
        $el.closest($toggleParent).find('.submenus-wrapper:first').slideDown(TRANSITION_TIMING);
        $el.parent().attr('aria-expanded', 'true');
        $el.attr('aria-label', navItemsCloseLabel);
      }
    };

    $arrowToggle.on('click', function (e) {
      e.preventDefault();
      toggleSubMenu(this);
    });

  };

  window.DP.behaviors.mainMenu = {
    attach: function (context) {
      $(context).find('.cc--main-menu').once('mainMenu').each(function () {
        initMainMenu.apply();
      });
    }
  };

})(jQuery);
