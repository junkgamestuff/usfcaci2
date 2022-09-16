(function ($) {
  'use strict';

  var $mobileMenu;
  var $arrowToggles;
  var $backButtons;
  var $subMenus;
  var breakpoint = window.matchMedia('(min-width: 1024px)');

  var initMobileMenu = function () {
    var $utilMenu = $('.nav-container .cc--utility-menu-alt');
    $mobileMenu = $('.nav-container .cc--main-menu-alt');
    $arrowToggles = $mobileMenu.find('.arrow-toggle');
    $subMenus = $mobileMenu.find('.submenus-wrapper');
    $backButtons = $mobileMenu.find('.button-back');

    // submenu arrow/back buttons toggle
    $arrowToggles.off('click').on('click', function (e) {
      e.preventDefault();
      var $subMenu = $(this).parents('.menu-item').find('> .submenus-wrapper');
      var $subMenuHeight = $subMenu.height();
      $('.m--main').css('min-height', $subMenuHeight);
      var $parentSubmenu = $(this).parents('.submenus-wrapper');
      $subMenu.attr('aria-hidden', 'false');
      $subMenu.show();
      $subMenu.removeClass('is-hidden').addClass('is-visible');
      $parentSubmenu.removeClass('is-visible').addClass('is-hidden');
      $mobileMenu.addClass('is-hidden');
      $utilMenu.addClass('is-hidden');
    });

    $backButtons.off('click').on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      var $grandParent = $this.closest('.submenus-wrapper').parents('.submenus-wrapper');
      var $parent = $this.closest('.submenus-wrapper');
      var $grandParentHeight = $grandParent.height();
      $parent.attr('aria-hidden', 'true');
      $parent.removeClass('is-visible');

      setTimeout(function () {
        $parent.hide();
      }, 300);

      if ($grandParent.length) {
        $grandParent.removeClass('is-hidden').addClass('is-visible');
        $grandParent.attr('aria-hidden', 'false');
        $('.m--main').css('min-height', $grandParentHeight);
      }
      else {
        $mobileMenu.removeClass('is-hidden');
        $utilMenu.removeClass('is-hidden');
        $('.m--main').removeAttr('style');
      }
    });

    checkBreakpoint();
    breakpoint.addListener(checkBreakpoint);
  };

  var checkBreakpoint = function () {
    if (breakpoint.matches) {
      $mobileMenu.removeAttr('style');
      $('body').removeClass('mobile-menu-is-active');
      hideAllSubmenus();
    }
  };

  var hideAllSubmenus = function () {
    $subMenus.removeClass('is-visible');
    $mobileMenu.removeClass('is-hidden');
    $('.m--main').removeAttr('style');
  };

  window.DP.behaviors.mobileMenu = {
    attach: function (context) {
      $(context).find('.nav-container').once('mobileMenu').each(function () {
        initMobileMenu.apply(this);
      });
    }
  };

}(jQuery));
