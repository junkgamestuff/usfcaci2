(function ($) {
  'use strict';

  /**
  * Main functionality should be in an 'init' function
  **/
  var initNotificationBanner = function () {
    var container = $(this);

    function headerHeight() {
      $('#l--main-header').height($('.cc--header').height());
    }

    if (container.length) {
      headerHeight();

      $(window).on('resize', function () {
        headerHeight();
      });
    }
  };

  /**
  * This is boilerplate: add a behavior to window.DP.behaviors
  **/
  window.DP.behaviors.notificationBanner = {
    attach: function (context) {
      $(context).find('.cc--notification-banner').once('notificationBanner').each(function () {
        initNotificationBanner.apply(this);
      });
    }
  };

}(jQuery));
