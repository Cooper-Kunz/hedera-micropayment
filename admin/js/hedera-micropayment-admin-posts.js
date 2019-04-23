(function( $ ) {
  'use strict';
  $(document).ready(function() {
    $('input[name^=hedera_micropayment_checkbox_value]').click(function() {
      let wpcbEl = $(this).closest('tr').find('th').find('input[name^=post]')
      let wpcbIsChecked = wpcbEl.is(":checked")
      let hcbIsChecked = $(this).is(":checked")
      let hederaCheckboxString = $(this).attr("name").match(/[^[\]]+(?=])/g)
      let hederaCheckboxId = parseInt(hederaCheckboxString[0])
      let wpCheckboxId = parseInt(wpcbEl.attr("value"))
      console.log("wpCheckboxId: ", wpCheckboxId, "hederaCheckboxId: ", hederaCheckboxId )
      if (wpCheckboxId === hederaCheckboxId) {
        console.log(" wpcbIsChecked ", wpcbIsChecked , "hcbIsChecked", hcbIsChecked)
        wpcbEl.prop('checked', hcbIsChecked)
        if (wpcbIsChecked === false && hcbIsChecked === false) {
          wpcbEl.prop('checked', true)
        }
      }
    })
  })
})( jQuery );