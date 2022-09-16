(function ($) {
  'use strict';

  var $container = $(this);
  var responsive = window.matchMedia('(max-width: 767px)');
  var $menuContainer = $('.nav-links');

  var initHeroNav = function (context) {

    $menuContainer.on('click', function () {
      if (responsive.matches) {
        $(this).toggleClass('open');
      }
    });
  };

  window.DP.behaviors.heroNav = {
    attach: function (context) {
      $(context).find('.cc--hero-tabbed-navigation').once('heroNav').each(function () {
        initHeroNav.apply(this);
      });
    }
  };

}(jQuery));
