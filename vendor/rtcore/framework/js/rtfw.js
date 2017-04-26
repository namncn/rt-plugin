(function($) {
  'use strict';

  // Datepicker
  $('[data-datepicker]').each(function() {
    var $el = $(this);
    var format = $el.data('format') ? $el.data('format') : 'yy-mm-dd';

    $el.datepicker({
      dateFormat: format
    });

  });

})(jQuery);
