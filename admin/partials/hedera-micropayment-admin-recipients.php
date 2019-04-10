<!-- check for initial recipient list and to check if recipient list contains an empty array -->
<?php if (!is_array($recipients) || count($recipients) ===0) { ?>

<div class="input_fields_wrap"> <span>Account ID ie. 0.0.1234 | Amount X in tinybars</span>
<br>
  <div>
    <input type="text"  name="hedera_micropayment_recipient[0][account]">
    <input type="text"  name="hedera_micropayment_recipient[0][amount]">
    <a class="add_field_button button-secondary">+</a>
  </div>
</div>

<?php } else { ?>
<div class="input_fields_wrap"> <span>Account ID ie. 0.0.1234 | Amount X in tinybars</span>
<br>
<?php for ($x = 0; $x < count($recipients); $x++) { ?>
  <div>
  <input type="text"  name="hedera_micropayment_recipient[<?php echo $x; ?>][account]" value="<?php echo $recipients[$x]['account']; ?>">
  <input type="text"  name="hedera_micropayment_recipient[<?php echo $x; ?>][amount]" value="<?php echo $recipients[$x]['amount']; ?>">
  <?php if ($x === count($recipients)-1) { 
     ?>
    <a href="#" class="add_field_button button-secondary">+</a>
  <?php } else { ?>
    <a href="#" class="remove_field">-</a>
  <?php } ?>
  </div>
<?php } ?>
</div><!-- input_fields_wrap -->

<?php } ?>