(function ($) {
  'use strict';

  var $utilMenu;
  var $arrowToggles;
  var $subMenus;
  var $mobileMenu;
  var breakpoint = window.matchMedia('(min-width: 1024px)');

  var initUtilityMenu = function () {
    $mobileMenu = $('.nav-container .cc--main-menu');
    $utilMenu = $('.nav-container .cc--utility-menu');
    $arrowToggles = $utilMenu.find('.utility-button');
    $subMenus = $utilMenu.find('.submenus-wrapper');

    breakpoint.addEventListener('change', checkBreakpoint);
    checkBreakpoint();

    var submenuToggle = function (el) {
      var $el = $(el);
      var itemParent = $el.closest('.menu-item');

      if (itemParent.hasClass('is-open')) {
        // close if it's opened
        itemParent.removeClass('is-open');
        itemParent.attr('aria-expanded', 'false');
        $el.next('.submenu').slideUp(250);
        $el.next('.submenu').attr('aria-hidden', 'true');
      }
      else {
        // if it's not open, open it
        itemParent.addClass('is-open');
        itemParent.attr('aria-expanded', 'true');
        $el.next('.submenu').slideDown(250);
        $el.next('.submenu').attr('aria-hidden', 'false');

        var scrollPos = $('.nav-container');
        $('.nav-container').animate({
          scrollTop: scrollPos.prop('scrollHeight')
        }, 300);
      }
    };

    $arrowToggles.on('click', function () {
      var $this = $(this);
      submenuToggle($this);
    });
  };

  var hideAllSubmenus = function () {
    $utilMenu.removeAttr('style');
    $subMenus.removeAttr('style');
    $subMenus.removeClass('is-visible');
    $utilMenu.removeClass('is-hidden');
    $mobileMenu.removeClass('is-hidden');
  };

  var checkBreakpoint = function () {
    hideAllSubmenus();
    $arrowToggles.off('click');
  };

  window.DP.behaviors.utilityMenu = {
    attach: function (context) {
      $(context).find('.cc--utility-menu').once('utilityMenu').each(function () {
        initUtilityMenu.apply(this);
      });
    }
  };

}(jQuery));
