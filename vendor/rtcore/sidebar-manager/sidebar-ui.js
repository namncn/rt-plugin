(function($, RTSidebar) {
  'use strict';

  RTSidebar = window.RTSidebar || {};

  /**
   * Handles sidebar requests.
   */
  var sidebarManager = function(name, action, reload) {
    var request = $.ajax({
      url: ajaxurl,
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'rt_' + action + '_sidebar',
        name: name
      }
    });

    request.done(function( response ) {
      if ( !response || !response.success ) {
        alert( response.error || 'An error occurred while trying to ' + action + ' the sidebar.' );
      }
    });

    request.fail(function( jqXHR, textStatus ) {
      alert( 'Request failed: ' + textStatus );
    });

    if (reload) {
      request.always(function() {
        window.location.reload();
      });
    }

    return request;
  }

  $(function() {
    // New widget area button
    var $button = $(RTSidebar.button);
    $('#wpbody-content > .wrap > :first:header').append($button);

    var temp = wp.template('rt-sidebar-manager');
    $('#rt-sidebar-manager-popup').append(temp);

    $('.sidebar-rt-sidebar').each(function() {
      var id = $(this).find('.widgets-sortables').attr('id');
      var data = RTSidebar.sidebars[id];

      var template = wp.template('rt-sidebar-action');
      $(this).append(template(data));
    });

    $(document).on('click', '.submitdelete', function(e) {
      e.preventDefault();

      if (!confirm('Are you sure you want to do this?')) {
        return;
      }

      var $sidebar = $(this).parents('.sidebar-rt-sidebar');
      $sidebar.addClass('removing');

      $.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
          id: $(this).data('id'),
          action: 'rt_delete_sidebar',
          _rtnonce: RTSidebar.nonce,
        },
      })
      .done(function() {
        $sidebar.remove();
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });

    });

    $(document).on('click', '.show', function(e) {
      e.preventDefault();
      $(this).parent().find('.display').toggle();
    });

  });

})(jQuery, window.RTSidebar);
