(function ($) {
  'use strict';

  /**
   * Main functionality should be in an 'init' function
   **/
  var initFilterForm = function () {
    var container = this;

    $('.filter-button').on('click', function() {
      $(this).toggleClass('active');
      $(this).parent('.filter-form-header').next('.filter-form-content').slideToggle(200);
    });

    $('h4', container).on('click', function() {
      $(this).parent('.form-item-wrapper').toggleClass('active');
      $(this).next('.fi--form-item').slideToggle(200);
      $(this).next('.form-group').slideToggle(200);
    });
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   **/
  window.DP.behaviors.filterForm = {
    attach: function (context) {
      $(context).find('.cc--filter-form').once('filterForm').each(function () {
        initFilterForm.apply(this);
      });
    }
  };

}(jQuery));
