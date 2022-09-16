(function ($) {
  'use strict';

  /**
  * Main functionality should be in an 'init' function
  */
  var ambientVideoInit = function () {
    var container = this;

    var $videoPlayBtn = $('.video-controls .video-play-button', container);
    var $videoPauseBtn = $('.video-controls .video-pause-button', container);
    var $videoUnmuteBtn = $('.video-controls .video-unmute-button', container);
    var mySwiper = $('.swiper-container');
    var ambientVideo = $('.f--ambient-video video');

    $videoPauseBtn.on('click', pauseAmbientVideo);
    $videoPlayBtn.on('click', playAmbientVideo);
    $videoUnmuteBtn.on('click', unmuteVideo);


    function unmuteVideo() {
      var sliderVideo = $(this).parents('.image-video').find(ambientVideo)[0];
      sliderVideo.muted = !sliderVideo.muted;
      $(this).toggleClass('unmuted');
    }

    function pauseAmbientVideo() {
      $(this).addClass('hidden');
      $(this).next('.video-button').addClass('active').focus();

      if (mySwiper.length) {
        var $sliderVideo = $(this).parents('.image-video').find(ambientVideo)[0];
        $sliderVideo.pause();
      }
      else {
        ambientVideo[0].pause();
      }
    }

    function playAmbientVideo() {
      $(this).removeClass('active');
      $(this).prev('.video-button').removeClass('hidden').focus();

      if (mySwiper.length) {
        var $sliderVideo = $(this).parents('.image-video').find(ambientVideo)[0];
        $sliderVideo.play();
      }
      else {
        ambientVideo[0].play();
      }
    }
  };

  /**
  * This is boilerplate: add a behavior to window.DP.behaviors
  */
  window.DP.behaviors.ambientVideo = {
    attach: function (context) {
      $(context).find('.cc--hero-home-ambient, .cc--home-page-hero, .cc--home-page-hero-slider, .cc--landing-page-hero, .cc--full-width-image-with-text').once('ambientVideo').each(function () {
        ambientVideoInit.apply(this);
      });
    }
  };
})(jQuery);
