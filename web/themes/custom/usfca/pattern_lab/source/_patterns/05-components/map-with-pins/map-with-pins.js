(function ($) {
  'use strict';
  var self;

  var initMapWithPins = function () {
    self = this;
    var markers = [];
    var pin;
    var lowestLng = 0;
    var locations = self.querySelectorAll('.location');
    var locationsWrapper = self.querySelector('.locations-wrapper');
    var zoomLevel = locationsWrapper.dataset.zoom ? locationsWrapper.dataset.zoom : null;
    var mapCenter = locationsWrapper.dataset.center ? locationsWrapper.dataset.center : null;
    var plWhiteList = [
      'usfca-site-pattern-lab.herokuapp.com',
      'localhost'
    ];
    var host = window.location.hostname;
    var repsonsive = window.matchMedia('(min-width: 768px)');


    var iconUrl = plWhiteList.includes(host) ? '../../images/pin.png' : '/themes/custom/usfca/pattern_lab/source/images/pin.png';
    var iconActiveUrl = plWhiteList.includes(host) ? '../../images/pin-active.png' : '/themes/custom/usfca/pattern_lab/source/images/pin-active.png';

    var pinIcon = {
      url: iconUrl,
      scaledSize: {
        width: 28,
        height: 42,
      },
      size: {
        width: 28,
        height: 42,
      }
    };
    var pinIconActive = {
      url: iconActiveUrl,
      scaledSize: {
        width: 28,
        height: 42,
      },
      size: {
        width: 28,
        height: 42,
      }
    };

    var mapSettings = {
      disableDefaultUI: true,
    };

    // Setting zoom and center if fields are filled
    if (zoomLevel && mapCenter && mapCenter.split(',').length == 2) {
      mapSettings.zoom = parseFloat(zoomLevel);
      mapSettings.center = {
        lat: parseFloat(mapCenter.split(',')[0]),
        lng: parseFloat(mapCenter.split(',')[1])
      }
    }

    // Initialize
    var map = new google.maps.Map(document.getElementById("map"), mapSettings);

    // Creating empty bounds
    var markerBounds = new google.maps.LatLngBounds();

    for (var i = 0; i < locations.length; i++) {
      var modal = locations[i];
      var lat = parseFloat(modal.dataset.lat);
      var lng = parseFloat(modal.dataset.lng);
      var title = modal.dataset.title;
      var markerId = '.id-' + i;

      // Creating lowest lng to make map shift later
      lowestLng = lng < lowestLng ? lng : lowestLng;

      // Creating a pin
      var pin = new google.maps.LatLng(lat, lng);

      // Creating markers
      var marker = new google.maps.Marker({
        position: pin,
        map: map,
        icon: i == 0 && repsonsive.matches ? pinIconActive : pinIcon,
        zIndex: i == 0 && repsonsive.matches ? 100 : 1,
        title: title,
        markerId: markerId,
      });

      // Fillig array with all markers
      markers.push(marker);

      // Creating marker click event
      marker.addListener('click', function() {
        // Toggle content
        if (self.querySelector('.location:not(.hidden)')) {
          self.querySelector('.location:not(.hidden)').classList.add('hidden');
        }
        self.querySelector(this.markerId).classList.remove('hidden');
        self.querySelector(this.markerId).classList.remove('hidden-mob');

        // Resetting map pin icons to default
        for (var j = 0; j < markers.length; j++) {
          var m = markers[j];
          m.setIcon(pinIcon);
          m.setZIndex(1);
        }

        // Setting active map pin icon
        this.setIcon(pinIconActive);
        this.setZIndex(100);
      });

      // Mobile close button
      modal.querySelector('.location-close').addEventListener('click', function (e) {
        e.currentTarget.closest('.location').classList.add('hidden-mob');
      });

      // Calculating map shift on last loop
      if (locations.length == i + 1) {
        lowestLng -= 10;
        markerBounds.extend(new google.maps.LatLng(lat, lowestLng));
      }

      // Extending bound
      markerBounds.extend(pin);
    }

    // Fitting map to bounds if zoom and center was not set
    if (!mapSettings.zoom) {
      map.fitBounds(markerBounds);
    }
  };

  window.DP.behaviors.mapWithPins = {
    attach: function (context) {
      $(context).find('.cc--map-with-pins').once('mapWithPins').each(function () {
        initMapWithPins.apply(this);
      });
    }
  };

}(jQuery));
