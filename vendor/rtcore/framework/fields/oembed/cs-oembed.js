(function($) {
  'use strict';

  /**
   * oEmbed JS.
   */
  $(function() {
    /**
     * Preview oembed
     */
    $(document).on('click', '.js-preview-cs-oembed', function(e) {
      e.preventDefault();

      var $el = $(this).closest('.cs-element');
      var $link = $el.find('input[type="url"]');
      var $preview = $el.find('.cs-oembed-preview');

      // Show progress spinner
      $el.find('.spinner').css('visibility', 'visible');

      // Send AJAX request
      var xhr = $.ajax({
        url: window.ajaxurl,
        type: 'GET',
        dataType: 'json',
        data: {
          action: 'cs_oembed_handler',
          link: $link.val(),
        },
      });

      xhr.done(function(res) {
        if (res.success && res.success) {
          $preview.removeClass('hide');
          $preview.html(res.data);
        } else {
          $preview.addClass('hide');
          alert(res.data);
        }
      });

      xhr.always(function() {
        $el.find('.spinner').css('visibility', 'hidden');
      });

      xhr.fail(function() {
        alert('ERROR');
      });

    });

    /**
     * Remove preview
     */
    $(document).on('click', '.js-remove-cs-oembed', function(e) {
      e.preventDefault();
      var $el = $(this).closest('.cs-element');

      $el.find('input[type="url"]').val('');
      $el.find('.cs-oembed-preview').html('').addClass('hide');
    });
  });

})(jQuery);
