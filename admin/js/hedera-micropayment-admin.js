// by default, wordpress bundles with jQuery.
// write all our js logic for our admin page within this function.
(function ($) {
  'use strict';
  $(document).ready(function () {
    console.log('Set up our dynamic input field logic here');
    // variables
    var max_fields = 9;
    var x = ajax_var.num_of_pairs;

    console.log("x here", x)
    // add button
    var add_button = $('<a href="#" class="add_field_button button-secondary">+</a>');
    var remove_button = $('<a href="#" class="remove_field">-</a>');

    // create div
    let div = document.createElement('div')
    let inputAcc = document.createElement('input')
    let inputAmt = document.createElement('input')
    inputAcc.className = 'appended_div'
    inputAcc.style
    inputAcc.type = 'text'
    inputAcc.name = 'hedera_micropayment_recipient[0][account]'
    inputAmt.className = 'appended_div'
    inputAmt.type = 'text'
    inputAmt.min = '1'
    inputAmt.name = 'hedera_micropayment_recipient[0][amount]'
    div.appendChild(inputAcc)
    div.appendChild(inputAmt)

    // prepare div element that always get added when add button is clicked
    var new_div_default = $(div)
    new_div_default.append(add_button);

    // add handler
    var addHandler = function (e) {
      console.log('add button has been clicked ' + x)
      e.preventDefault();
      if (x < max_fields) {
        x++;
        let new_div = new_div_default.clone();
        $(new_div.children()[0]).attr('name', 'hedera_micropayment_recipient[' + x + '][account]')
        $(new_div.children()[1]).attr('name', 'hedera_micropayment_recipient[' + x + '][amount]')
        new_div.insertAfter($(e.target).parent());
      }

      // replace current add button with remove button
      $(e.target).replaceWith(remove_button.clone());
    }
    // remove handler
    var removeHandler = function (e) {
      console.log('remove button has been clicked', x);
      let parentEl = e.target.parentElement.parentElement
      let dynChildLength = parentEl.children.length

      if (x == 8 && dynChildLength == 10 && e.target.className == 'remove_field') {
        $(e.target).replaceWith(add_button.clone());
      }

      e.preventDefault();
      $(e.target).parent('div').remove();
      x--;
    }
    
    $(document).on('click', 'a.add_field_button', addHandler);
    $(document).on('click', 'a.remove_field', removeHandler);
  });

})(jQuery);
