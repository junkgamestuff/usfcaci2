(function ($) {
  'use strict';

  /**
  * Main functionality should be in an 'init' function
  **/
  var initUsfcaAcalogProgramContent = function initUsfcaAcalogProgramContent() {
    var $container = $(this);
    var container_id = $container.attr('id');
    var container_data = $container.data('json');

    const observer = new MutationObserver(function(mutations_list) {
      mutations_list.forEach(function(mutation) {
        mutation.addedNodes.forEach(function(added_node) {
          if (typeof added_node.classList !== 'undefined') {
            if (added_node.classList.contains('acalog-program-name')) {
              console.log('element ".acalog-program-name" added');
            }
            if (added_node.classList.contains('acalog-program-description')) {
              console.log('element ".acalog-program-description" added');
            }
            if (added_node.classList.contains('acalog-program-cores')) {
              if (container_data.acalog_display_headings.length > 0) {
                $('> .acalog-program-core', added_node).each(function () {
                  var core = this;
                  const $core_heading = $('> h2:first-of-type', core);

                  if ($core_heading.length > 0) {
                    const core_heading_text = $core_heading.text();

                    container_data.acalog_display_headings.forEach(function (allowed) {
                      if (core_heading_text.includes(allowed)) {
                        core.classList.add('enabled');
                      }
                    });
                  }
                });
              }

              added_node.classList.add('enabled');
            }
          }
        });
      });
    });

    observer.observe(document.querySelector("#" + container_id), { subtree: true, childList: true });

    $('#' + container_id + ' .acalog').acalogWidgetize({
      gateway: 'https://catalog.usfca.edu'
    });
  };

  /**
  * This is boilerplate: add a behavior to window.DP.behaviors
  **/
  window.DP.behaviors.usfcaAcalogProgramContent = {
    attach: function (context) {
      $(context).find('.cc--rich-text.acalog-program-content').once('usfcaAcalogProgramContent').each(function () {
        initUsfcaAcalogProgramContent.apply(this);
      });
    }
  };

}(jQuery));
