(function ($) {
  'use strict';

  var $utilMenu;
  var $arrowToggles;
  var $backButtons;
  var $subMenus;
  var $mobileMenu;
  var breakpoint = window.matchMedia('(min-width: 1024px)');

  var initUtilityMenu = function () {
    $mobileMenu = $('.nav-container .cc--main-menu-alt');
    $utilMenu = $('.nav-container .cc--utility-menu-alt');
    $arrowToggles = $utilMenu.find('.utility-button');
    $subMenus = $utilMenu.find('.submenus-wrapper');
    $backButtons = $utilMenu.find('.button-back');

    // submenu arrow/back buttons toggle
    $arrowToggles.on('click', function (e) {
      e.preventDefault();
      var $menuHeight = $mobileMenu.outerHeight() + 6;
      var $subMenu = $(this).next('.submenus-wrapper');
      $subMenu.attr('aria-hidden', 'false');
      $subMenu.show();
      $subMenu.css('top', -$menuHeight);
      $subMenu.removeClass('is-hidden').addClass('is-visible');
      $utilMenu.addClass('is-hidden');
      $mobileMenu.addClass('is-hidden');
    });

    $backButtons.off('click').on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      var $parent = $this.closest('.submenus-wrapper');
      $parent.attr('aria-hidden', 'true');
      $parent.removeClass('is-visible');
      $mobileMenu.removeClass('is-hidden');
      $utilMenu.removeClass('is-hidden');

      setTimeout(function () {
        $parent.hide();
      }, 300);
    });

    checkBreakpoint();
    breakpoint.addListener(checkBreakpoint);
  };

  var checkBreakpoint = function () {
    if (breakpoint.matches) {
      $utilMenu.removeAttr('style');
      $('body').removeClass('mobile-menu-is-active');
      hideAllSubmenus();
    }
  };

  var hideAllSubmenus = function () {
    $subMenus.removeClass('is-visible');
    $utilMenu.removeClass('is-hidden');
    $mobileMenu.removeClass('is-hidden');
  };

  window.DP.behaviors.utilityMenu = {
    attach: function (context) {
      $(context).find('.cc--utility-menu-alt').once('utilityMenu').each(function () {
        initUtilityMenu.apply(this);
      });
    }
  };

}(jQuery));
