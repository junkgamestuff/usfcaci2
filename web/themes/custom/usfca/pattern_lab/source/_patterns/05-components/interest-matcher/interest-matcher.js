(function ($) {
  'use strict';

  var $container = $(this);
  var $tabList = $('.interest-matcher-tabs .active');
  var breakpoint = window.matchMedia('(min-width: 768px)');

  var initInterestMatcher = function (context) {
    $('.tab-content .link').on('click', function (e) {
      e.preventDefault();
      $('.tab-content .first-stage').hide();
      $('.tab-content .second-stage').show();
    });

    var checkBreakpoint = function () {
      $tabList.find('select').on('change', function () {
        var selectValue = $(this).find(':selected').text();

        $('.cc--interest-matcher .tab-content h3 span').text(selectValue);
        $('.cc--interest-matcher .tab-content .second-stage h3').text(selectValue);

        $('.tab-content .first-stage').show();
        $('.tab-content .second-stage').hide();

        $('.cc--interest-matcher .tab-content').show();
      });
    };

    breakpoint.addEventListener('change', checkBreakpoint());
    checkBreakpoint.apply(this);
  };

  window.DP.behaviors.interestMatcher = {
    attach: function (context) {
      $(context).find('.cc--interest-matcher').once('interestMatcher').each(function () {
        initInterestMatcher.apply(this);
      });
    }
  };

}(jQuery));
