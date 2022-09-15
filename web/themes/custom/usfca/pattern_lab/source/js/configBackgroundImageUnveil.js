/* see https://github.com/aFarkas/lazysizes#js-api---events */

// add simple support for background images:
document.addEventListener('lazybeforeunveil', function (e) {
  'use strict';

  var bg = e.target.getAttribute('data-bg');

  if (bg) {
    e.target.style.backgroundImage = 'url(' + bg + ')';
  }
});
