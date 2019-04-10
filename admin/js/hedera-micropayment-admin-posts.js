(function( $ ) {
  'use strict';
  $(document).ready(function() {

    $('input[name^=hedera_micropayment_checkbox_value]').click(function() {
      var val = $(this).is(":checked")
      $(this).closest('tr').find('th').find('input[name^=post]').prop('checked', val)
    })

  })
})( jQuery );