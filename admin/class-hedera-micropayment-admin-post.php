<?php

/**
 * implementation for administering specific posts
 */

 class HederaMicropaymentAdminPost {
  private $plugin_name;
  private $version;
  private $option_name = 'hedera_micropayment';

  public function __construct($plugin_name, $version) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * Adding Custom Meta Boxes for Post-specific configuration
   */
  public function add_custom_box() {
    add_meta_box(
      $this->option_name . '_post',
      __( 'Hedera Micropayment Config', $this->plugin_name ),
      array( $this, $this->option_name . '_post_cb' ),
      'post'
    );
  }

  public function hedera_micropayment_post_cb( $post ) {
    // get this post's meta information from db
    $meta = get_post_meta($post->ID);

    // checkbox value
    $name = $this->option_name . '_checkbox_value';
    $checkbox_value = ( isset( $meta[$name][0] ) &&  '1' === $meta[$name][0] ) ? 1 : 0;

    // if recipients value is already stored in $meta, use it, otherwise initialise an empty array
    define('RECIPIENTS', 'hedera_micropayment_recipient');
    if ($meta[RECIPIENTS] === null) {
      $meta[RECIPIENTS] = array();
    }
    $recipients = unserialize($meta[RECIPIENTS][0]);
    ?>
    <input type="checkbox" name="<?php echo $name ?>" value="1" <?php checked($checkbox_value, 1); ?>>Enable<br />
    <?php
    include_once 'partials/hedera-micropayment-admin-recipients.php';
  }

  public function save_post_data( $post_id ) {
    $name = $this->option_name . '_checkbox_value';
    $checkbox_value = ( isset( $_POST[$name] ) && '1' === $_POST[$name] ) ? 1 : 0;
    update_post_meta(
      $post_id,
      $name,
      $checkbox_value
    );
    $name2 = $this->option_name . '_recipient';
    $recipients_value = $_POST[$name2];
    // TODO: we will need to clean $recipients_value like what we did in the admin page too
    update_post_meta(
      $post_id,
      $name2,
      $recipients_value
    );
  }

 }