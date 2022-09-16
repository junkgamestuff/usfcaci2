(function ($) {
  'use strict';

  var TRANSITION_TIMING = 250;
  var $mobileNav = $('.mobile-nav-menu-container');
  var subNavOpenLabel = 'Expand ';
  var subNavCloseLabel = 'Close ';

  var initMobileMenu = function () {
    var $hamburger = $('.mobile-nav-topbar-container .mobile-menu-trigger');
    var $navItems = $('.cc--main-menu ul.m--menu > li .arrow-toggle');
    var label = 'Open Menu';
    var labelClose = 'Close Menu';

    // hamburger click/tap
    $hamburger.on('click', function (e) {
      e.preventDefault();
      $(this).toggleClass('is-active');
      $('body').toggleClass('mobile-menu-open');
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

    // trigger on click/tap events for submenu toggles
    $navItems.on('click', function (e) {
      e.preventDefault();
      toggleSubMenu(this);
    });
  };

  var toggleMobileMenu = function () {
    $mobileNav.fadeToggle(TRANSITION_TIMING);
  };

  // submenu toggle
  var toggleSubMenu = function (el) {
    // get the toggle
    var $el = $(el);
    var $toggleParent = $('li.menu-item');
    // check if it's open
    if ($el.hasClass('is-open')) {
      // close if it's opened
      var navItemsOpenLabel = subNavOpenLabel + 'Sub-Navigation';
      $el.closest($toggleParent).removeClass('is-open');
      $el.removeClass('is-open');
      $el.next('.submenu').slideUp(TRANSITION_TIMING);
      $el.closest($toggleParent).find('.submenus-wrapper:first').slideUp(TRANSITION_TIMING);
      $el.parent().attr('aria-expanded', 'false');
      $el.attr('aria-label', navItemsOpenLabel);
    }
    else {
      // if it's not open, open it
      var navItemsCloseLabel = subNavCloseLabel + 'Sub-Navigation';
      $el.closest($toggleParent).addClass('is-open');
      $el.addClass('is-open');
      $el.next('.submenu').slideDown(TRANSITION_TIMING);
      $el.closest($toggleParent).find('.submenus-wrapper:first').slideDown(TRANSITION_TIMING);
      $el.parent().attr('aria-expanded', 'true');
      $el.attr('aria-label', navItemsCloseLabel);
    }
  };

  window.DP.behaviors.mobileMenu = {
    attach: function (context) {
      $(context).find('.mobile-nav').once('mobileMenu').each(function () {
        initMobileMenu.apply(this);
      });
    }
  };

}(jQuery));
