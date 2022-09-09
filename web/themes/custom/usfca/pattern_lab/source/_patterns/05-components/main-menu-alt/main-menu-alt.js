(function ($) {
  'use strict';

  var TRANSITION_TIMING = 250;
  var $mainNav = $('.nav-container');

  var $hamburger = $('.header-buttons .mobile-menu-trigger');
  var label = 'Open Menu';
  var labelClose = 'Close Menu';

  var toggleMobileMenu = function () {
    $mainNav.fadeToggle(TRANSITION_TIMING);
  };

  var initMainMenu = function () {
    var $menuItems = $('.cc--main-menu-alt ul.m--menu > li.menu-item--expanded');

    $menuItems.on('click', function () {
      $menuItems.removeClass('ally-focus-within');
    });

    // hamburger click/tap
    $hamburger.on('click', function (e) {
      e.preventDefault();
      $(this).toggleClass('is-active');
      $('body').toggleClass('main-menu-open');
      toggleMobileMenu();

      if ($(this).hasClass('is-active')) {
        $hamburger.attr('aria-label', labelClose);
        $('#l--main-header').siblings().each(function (i, el) {
          el.inert = true;
        });
      }
      else {
        $hamburger.attr('aria-label', label);
        $('#l--main-header').siblings().each(function (i, el) {
          el.inert = false;
        });
      }

      // on click hamburger - hide alert banner if visible
      if ($('.cc--alert-banner').is(':visible')) {
        $('.cc--alert-banner').hide();
      }
    });
  };

  window.DP.behaviors.mainMenu = {
    attach: function (context) {
      $(context).find('.cc--main-menu-alt').once('mainMenu').each(function () {
        initMainMenu.apply();
      });
    }
  };

})(jQuery);
