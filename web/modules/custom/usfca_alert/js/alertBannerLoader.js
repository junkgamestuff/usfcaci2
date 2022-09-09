(function ($, Drupal) {

  'use strict';

  var storageName = 'alert-banner-closed';
  var fetchAlert = function () {
    return $.ajax('/alerts', {
      dataType: 'json'
    });
  };

  var readStorage = function () {
    var result = false;
    var stored = localStorage.getItem(storageName);
    if (stored) {
      result = JSON.parse(stored);
    }
    return result;
  };

  var writeStorage = function (data) {
    localStorage.setItem(storageName, JSON.stringify(data));
  };

  /* eslint-disable no-extend-native */
  Date.prototype.addHours = function (h) {
    this.setTime(this.getTime() + (h * 60 * 60 * 1000));
    return this;
  };
  /* eslint-enable no-extend-native */

  $(document).ready(function () {
    var container = $('.alert-banner-block-container');
    container.once('alert-banner-init').each(function () {
      var $wrapper = $(this);
      var closeState = readStorage();
      fetchAlert()
        .done(function (data, textStatus, jqXHR) {
          if (data.length) {
            var lastChange = new Date(data[0].changed[0].value);
            if ((data[0].uuid[0].value !== closeState.uuid)
              || (lastChange > new Date(closeState.lastChange))
              || (new Date() > new Date(closeState.expires))) {
              $wrapper.html(data[0].rendered);
              var closeButton = $('.close-btn', container);
              /* eslint max-nested-callbacks: ["warn", 4] */
              closeButton.on('click', function (e) {
                e.preventDefault();

                writeStorage({
                  uuid: data[0].uuid[0].value,
                  lastChange: lastChange.toISOString(),
                  expires: (new Date()).addHours(24).toISOString()
                });
              });
              Drupal.attachBehaviors($wrapper.get(0));
            }
          }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
          console.error(errorThrown);
        });
    });
  });
})(jQuery, Drupal);
