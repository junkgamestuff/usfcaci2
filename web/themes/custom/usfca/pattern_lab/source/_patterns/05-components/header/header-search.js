(function ($) {
  'use strict';

  var $searchIcon;
  var $searchFormContainer = $('.cc--header .cc--search-form');
  var $searchInput = $searchFormContainer.find('input');

  /**
  * Main functionality should be in an 'init' function
  */
  var initSearchForm = function () {
    $searchIcon = $(this);

    $searchFormContainer.attr({
      'aria-hidden': 'true',
      'aria-labelledby': 'dialog-title'
    });

    $searchIcon.on('click', function (e) {
      e.preventDefault();

      if (!$(this).hasClass('is-active')) {
        openSearch();
        $(this).addClass('is-active');
      }
      else {
        closeSearch();
        $(this).removeClass('is-active');
      }
    });

    $(document).click(function(event) {
      if ($($searchFormContainer).hasClass('is-open')) {
        var $target = $(event.target);
        if(!$target.closest($searchIcon).length && !$target.closest('.cc--header .cc--search-form .input-wrapper').length) {
          closeSearch();
          $searchIcon.removeClass('is-active');
        }
      }
    });

    $searchFormContainer.on('keydown', function (ev) {
      // KEY_TAB
      if (ev.keyCode === 9) {
        // ev.preventDefault();
        var inputs = $searchFormContainer.find('input, button').filter(':visible').not(':disabled');

        // redirect last tab to first input
        if (!ev.shiftKey) {
          if (inputs[inputs.length - 1] === ev.target) {
            ev.preventDefault();
            inputs.first().focus();
          }
        }
        // redirect first shift+tab to last input
        else {
          if (inputs[0] === ev.target) {
            ev.preventDefault();
            inputs.last().focus();
          }
        }
      }

      // KEY_ESC
      if (ev.keyCode === 27) {
        ev.preventDefault();
        closeSearch();
        $searchIcon.removeClass('is-active');
      }
    });
  };

  var openSearch = function () {
    $searchFormContainer.addClass('is-open').attr('aria-hidden', 'false').fadeIn(100);
    $searchInput.first().focus();
  };

  var closeSearch = function () {
    $searchFormContainer.removeClass('is-open').attr('aria-hidden', 'true').fadeOut(100);
  };

  /**
   * This is boilerplate: add a behavior to window.DP.behaviors
   */
  window.DP.behaviors.searchForm = {
    attach: function (context) {
      $(context).find('.cc--header .search-trigger-button').once('searchForm').each(function () {
        initSearchForm.apply(this);
      });
    }
  };

}(jQuery));
