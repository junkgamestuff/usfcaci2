(function ($) {
  'use strict';

  var $container;
  var $tables;

  /**
   * Main functionality should be in an 'init' function
   */
  var initResponsiveTables = function () {
    $tables = $('table', $container);

    if ($tables.length > 0) {
      $tables.once().each(function () {
        $(this).wrap('<div class="resp-table"></div>');
      });
    }
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.responsiveTables = {
    attach: function (context) {
      $(context).find('.cc--rich-text').once('responsiveTables').each(function () {
        initResponsiveTables.apply(this);
      });
    }
  };

}(jQuery));
