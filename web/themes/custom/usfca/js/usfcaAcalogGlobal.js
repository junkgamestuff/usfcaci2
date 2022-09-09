(function ($) {
  'use strict';

  /**
  * Main functionality should be in an 'init' function
  **/
  var initUsfcaAcalogGlobal = function initUsfcaAcalogGlobal() {
    var $container = $(this);

    $('.acalog', $container).acalogWidgetize({
      gateway: 'https://catalog.usfca.edu'
    });
  };

  /**
  * This is boilerplate: add a behavior to window.DP.behaviors
  **/
  window.DP.behaviors.usfcaAcalogGlobal = {
    attach: function (context) {
      // Note: Acalog Program Content has its own implementation.
      $(context).find('.cc--component-container:not(.acalog-program-content)').once('usfcaAcalog').each(function () {
        initUsfcaAcalogGlobal.apply(this);
      });
    }
  };

}(jQuery));
