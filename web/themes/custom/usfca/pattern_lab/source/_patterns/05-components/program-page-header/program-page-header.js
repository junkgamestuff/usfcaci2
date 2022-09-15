(function ($) {
  'use strict';


  var initProgramHeader = function (context) {
    var breakpoint = window.matchMedia('(min-width: 1024px)');
    var container = $(this);

    $('.list-toggle', container).on('click', function () {
      $(this).toggleClass('open');
      $('.nav-links', container).slideToggle();
    });

    var checkBreakpoint = function () {
      if (breakpoint.matches) {
        setTimeout(function () {
          var distance = $('.cc--program-page-header .buttons-container').offset().top,
          $window = $(window);

          $window.scroll(function() {
            if ($window.scrollTop() >= distance) {
              $('.cc--program-page-header .buttons-container').addClass('fixed');
            }
            else {
              $('.cc--program-page-header .buttons-container').removeClass('fixed');
            }
          });
        }, 300);
      }
    };

    breakpoint.addEventListener('change', checkBreakpoint());
    checkBreakpoint.apply(this);
  };

  window.DP.behaviors.programHeader = {
    attach: function (context) {
      $(context).find('.cc--program-page-header').once('programHeader').each(function () {
        initProgramHeader.apply(this);
      });
    }
  };

}(jQuery));
