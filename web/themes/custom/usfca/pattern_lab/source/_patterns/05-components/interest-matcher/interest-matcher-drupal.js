(function ($) {
  'use strict';

  var $container = $(this);

  var breakpoint = window.matchMedia('(min-width: 768px)');

  var initInterestMatcher = function (context) {
    var matcher = this;
    var $tabItems = $('.tab-item', matcher);
    var $tabContents = $('.tab-content', matcher);

    $('.interest-matcher-tabs', matcher).find('select').on('change', function () {
      var select = this;
      var $curTab = $(select).parents('.tab-item');
      var $curTabContent = $curTab.next();
      var selectVal = $(select).find(':selected').val();
      var selectText = $(select).find(':selected').text();

      $tabItems.removeClass('active');
      $tabContents.hide();

      if (selectVal != 'all') {

        $curTab.addClass('active');

        $('h3 span', $curTabContent).text(selectText);
        $('.second-stage h3', $curTabContent).text(selectText);

        $('.first-stage', $curTabContent).show();
        $('.second-stage', $curTabContent).hide();

        $curTabContent.show();
      }
    });

    $('.link', $tabContents).on('click', function (e) {
      var link = this;
      var $curTab = $(link).parents('.tab-content').prev();
      var $selectVal = $('select', $curTab).find(':selected').val();
      var $filterForm = $('form.filter-form-content');
      var $drupalInterests = $('select[name="field_interests"]');
      var $drupalCareers = $('select[name="field_career_goals"]');
      var $formSubmit = $('.submit-wrapper .form-submit', $filterForm).first();

      e.preventDefault();

      $('.tab-content .first-stage').hide();
      $('.tab-content .second-stage').show();

      if ($curTab.hasClass('interest')) {
        $drupalInterests.val($selectVal);
        $drupalCareers.val('All');
        $formSubmit.click();
      }
      else if ($curTab.hasClass('want')) {
        $drupalInterests.val('All');
        $drupalCareers.val($selectVal);
        $formSubmit.click();
      }
    });
  };

  window.DP.behaviors.interestMatcher = {
    attach: function (context) {
      $(context).find('.cc--interest-matcher').once('interestMatcher').each(function () {
        initInterestMatcher.apply(this);
      });
    }
  };

}(jQuery));
