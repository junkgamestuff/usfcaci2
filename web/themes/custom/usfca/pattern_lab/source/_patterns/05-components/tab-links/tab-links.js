(function ($) {
  'use strict';

  /**
   * Actual tabs behaviour: switch the tabs content.
   */
  var tabsBehaviour = function () {
    var self = $(this);
    var $tabsContent = $('.tab-content');

    if ($tabsContent.length === 0) {
      return;
    }

    var $tabsContainer = self.find('.tab-headings');
    var $tabs = $('.tab-item', $tabsContainer);

    $tabs.on('click touchstart', function (event) {
      event.preventDefault();
      var id = $(this).find('a').attr('href');
      $tabsContent.attr('aria-hidden', true);
      $tabsContent.removeClass('selected');
      $(id).attr('aria-hidden', false);
      $(id).addClass('selected');
      $tabs.removeClass('selected').attr('aria-selected', false);
      $(this).addClass('selected').attr('aria-selected', true);
    });

    $tabs.on('keydown', function (ev) {
      if (ev.keyCode === 13) {
        $(this).click();
      }
    });
  };

  /**
   * The actual tabs behaviour.
   *
   * Take in account - this is already working in Drupal, so please don't break.
   */
  window.DP.behaviors.filterTabsBehaviour = {
    attach: function (context) {
      $(context).find('.cc--tab-links').once('filterTabsBehaviour').each(function () {
        tabsBehaviour.apply(this);
      });
    }
  };

})(jQuery);
