(function ($) {
  'use strict';

  window.DP = window.DP || {};

  window.DP.behaviors = window.DP.behaviors || {};

  $(window).on('load', function () {
    $.each(window.DP.behaviors, function (key, behavior) {
      if (typeof behavior.attach !== 'undefined') {
        behavior.attach(window.document);
      }
    });
  });

}(jQuery));
