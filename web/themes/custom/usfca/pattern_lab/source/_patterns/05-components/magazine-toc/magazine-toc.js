(function ($) {
  'use strict';

  /**
  * Main functionality should be in an 'init' function
  **/
  var initMagazineToc = function () {

    var container = $(this);
    var $openBtn = $('.button', container);
    var $tocWrapper = $('.toc-wrapper', container);
    var $tocContainer = $('.toc-container', container);
    var tocHeight;

    var DEFAULT_TEXT = $openBtn.text();
    var DEFAULT_ARIA_LABEL = $openBtn.attr('aria-label');
    var CLOSE_TEXT = 'Close';
    var CLOSE_ARIA_LABEL = 'Close the table of contents';

    $openBtn.on('click', function (e) {
      e.preventDefault();
      var $this = $(this);
      tocHeight = $tocContainer.outerHeight(true);

      if (!$this.hasClass('is-open')) {
        openToc();
      }
      else {
        closeToc();
      }
    });

    var openToc = function () {
      $openBtn.addClass('is-open');
      $openBtn.text(CLOSE_TEXT);
      $openBtn.attr('aria-label', CLOSE_ARIA_LABEL);

      $tocWrapper.attr('aria-hidden', 'false');
      $tocWrapper.attr('aria-expanded', 'true');
      $tocWrapper.find('a').attr('tabindex', '0');

      $tocWrapper.animate({
        height: tocHeight
      }, function () {
        $tocContainer.find('li:first a').focus();
      });

      $(window).on('resize.toc', function () {
        if ($openBtn.hasClass('is-open')) {
          closeToc();
        }
      });
    };

    var closeToc = function () {
      $openBtn.removeClass('is-open');

      $openBtn.text(DEFAULT_TEXT);
      $openBtn.attr('aria-label', DEFAULT_ARIA_LABEL);
      $openBtn.text(DEFAULT_TEXT);
      $tocWrapper.attr('aria-hidden', 'true');
      $tocWrapper.attr('aria-expanded', 'false');
      $tocWrapper.find('a').attr('tabindex', '-1');
      $tocWrapper.animate({
        height: 0
      });

      $(window).unbind('resize.toc');
    };
  };

  /**
  * This is boilerplate: add a behavior to window.DP.behaviors
  **/
  window.DP.behaviors.magazineToc = {
    attach: function (context) {
      $(context).find('.cc--magazine-toc').once('magazineToc').each(function () {
        initMagazineToc.apply(this);
      });
    }
  };

}(jQuery));
