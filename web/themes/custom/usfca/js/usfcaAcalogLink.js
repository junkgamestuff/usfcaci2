(function ($) {
  'use strict';

  /**
  * Main functionality should be in an 'init' function
  **/
  var initUsfcaAcalogLink = function initUsfcaAcalogLink() {
    var $container = $(this);
    var container_id = $container.attr('id');

    $('#' + container_id + ' .acalog').acalogWidgetize({
      gateway: 'https://catalog.usfca.edu'
    });
  };

  /**
  * This is boilerplate: add a behavior to window.DP.behaviors
  **/
  window.DP.behaviors.usfcaAcalogLink = {
    attach: function (context) {
      $(context).find('.cc--rich-text.acalog-link').once('usfcaAcalogLink').each(function () {
        initUsfcaAcalogLink.apply(this);
      });
    }
  };

}(jQuery));
