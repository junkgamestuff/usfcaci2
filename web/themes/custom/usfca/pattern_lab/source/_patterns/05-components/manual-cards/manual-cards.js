(function ($) {
  'use strict';

  /**
  * Main functionality should be in an 'init' function
  */
  var manualCardsInit = function () {

    var container = this;
    var cards = $('.cc--manual-card', container);

    cards.each(function(i, el) {
      $(el).on({
        mouseenter: function () {
          $(this).addClass('hover');
          $('.f--field-components', container).addClass('hovered');
        },
        mouseleave: function () {
          $(this).removeClass('hover');
          $('.f--field-components', container).removeClass('hovered');
        }
      });
    });

  };

  /**
  * This is boilerplate: add a behavior to window.DP.behaviors
  */
  window.DP.behaviors.manualCards = {
    attach: function (context) {
      $(context).find('.cc--manual-cards').once('manualCards').each(function () {
        manualCardsInit.apply(this);
      });
    }
  };
})(jQuery);
