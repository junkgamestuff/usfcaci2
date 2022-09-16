(function ($) {
  'use strict';

  /**
  * Main functionality should be in an 'init' function
  **/
  var initRequestForInfoForm = function () {
    var container = this;
    var $showBtn = $('.btn-show .button', container);
    var $hideBtn = $('.btn-hide .button--alt', container);
    var $form = $('.form-embed-wrapper', container);
    var $titleWrapper = $('.f--section-title', container);
    var $title = $('.f--section-title h2', container);

    function titleHeight() {
      if ($title.length) {
        $titleWrapper.height($title.height() / 2);
      }
    }

    titleHeight();
    $(window).on('resize', function () {
      titleHeight();
    });

    $showBtn.click(function (e) {
      e.preventDefault();
      $(this).blur().hide();
      $hideBtn.show();
      $form.slideToggle();
    });

    $hideBtn.click(function (e) {
      e.preventDefault();
      container.scrollIntoView({block: 'start', behavior: 'auto'});
      $(this).hide();
      $showBtn.show();
      $form.slideToggle();
      $(window).scrollTop($(window).scrollTop() - $('.cc--header').height());
    });
  };

  /**
  * This is boilerplate: add a behavior to window.DP.behaviors
  **/
  window.DP.behaviors.requestForInfoForm = {
    attach: function (context) {
      $(context).find('.cc--request-for-information').once('requestForInfoForm').each(function () {
        initRequestForInfoForm.apply(this);
      });
    }
  };

}(jQuery));
