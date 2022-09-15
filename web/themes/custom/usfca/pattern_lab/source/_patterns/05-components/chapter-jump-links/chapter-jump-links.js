(function ($) {
  'use strict';

  var SCROLL_TIMING = 600;
  var ACTIVE_LINK_CLASS = 'is-active';
  var $htmlBody = $('html, body');
  var $container;
  var menuHeight = 0;
  var $window = $(window);
  var $headerHeight = $('#l--main-header').outerHeight();
  var responsive = window.matchMedia('(max-width: 767px)');

  var initMenu = function (context) {
    $container = $(this);
    var $menuContainer = $('.chapter-menu', $container);
    var $links = $menuContainer.find('a');
    var hashParams;

    var selectChapter = function ($links) {
      if (window.location.hash) {
        hashParams = new URLSearchParams(window.location.hash.replace('#', ''));
        var $selectedLink = $links.toArray().reduce(function (acc, link) {
          var $link = $(link);
          var id = $link.attr('href');
          acc = acc || ((id === '#' + hashParams.get('chapter')) ? $link : false);
          return acc;
        }, false);
        if ($selectedLink) {
          $selectedLink.click();
        }
      }
    };

    if ($links.length === 0) {
      return;
    }

    $menuContainer.on('click', function () {
      if (responsive.matches) {
        $(this).toggleClass('open');
        menuHeight = $('.chapter-menu').outerHeight();
      }
      else {
        menuHeight = 0;
      }
    });

    $links.on('click', function (e) {
      e.preventDefault();
      $($links).removeClass(ACTIVE_LINK_CLASS);
      $($links).closest('.chapter-menu-item').removeClass('active');
      var target = $(this);
      target.addClass(ACTIVE_LINK_CLASS);
      target.closest('.chapter-menu-item').addClass('active');
      hashParams = new URLSearchParams(window.location.hash.replace('#', ''));
      var targetHref = target.attr('href');
      var scrollPos = $(targetHref, $('.content-main')).offset().top;
      $htmlBody.stop().animate({scrollTop: scrollPos - menuHeight - 30}, SCROLL_TIMING);
      hashParams.set('chapter', targetHref.replace('#', ''));
      window.location.hash = hashParams.toString();
    });

    selectChapter($links);
    calculateScroll();

    $(window).on('scroll.chapteredMenu', function () {
      calculateScroll();
    });
  };

  function calculateScroll() {
    $('.chapter-menu a.is-active', $container).removeClass('is-active');
    $('.chapter-menu li.active').removeClass('active');

    $('.chaptered-nav-anchor').each(function (i, el) {
      var $el = $(el);
      var $wrapper = $el.closest('.c--chapter');
      var elementTop = $el.offset().top;
      var viewportTop = $window.scrollTop();
      var viewportHeight = $window.height();
      var additionalOffset = 300;

      if ($wrapper.height() > viewportHeight) {
        additionalOffset = $wrapper.height();
      }
      else if ($wrapper.height() >= 125) {
        additionalOffset = 300;
      }

      if ($el.attr('id')) {
        if (elementTop + additionalOffset - viewportTop >= $headerHeight && elementTop - viewportTop <= viewportHeight) {
          var id = $el.attr('id');
          $('.chapter-menu li.active', $container).removeClass('active');
          $('.chapter-menu a.is-active', $container).removeClass('is-active');
          $('.chapter-menu a[href="' + '#' + id + '"]', $container).addClass('is-active');
          $('.chapter-menu a[href="' + '#' + id + '"]').closest('.chapter-menu-item').addClass('active');
          return false;
        }
      }
    });
  }

  window.DP.behaviors.chapteredMenu = {
    attach: function (context) {
      $(context).find('.cc--chapter-jump-links').once('chapteredMenu').each(function () {
        initMenu.apply(this);
      });
    }
  };

}(jQuery));
