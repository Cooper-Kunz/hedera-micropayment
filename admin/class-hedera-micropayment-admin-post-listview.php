<?php

class HederaMicropaymentAdminPostListview {

  private $plugin_name;
  private $version;
  private $option_name = 'hedera_micropayment';

  public function __construct($plugin_name, $version) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name . '-admin-posts', plugin_dir_url( __FILE__ ) . 'js/hedera-micropayment-admin-posts.js', array( 'jquery' ), $this->version, false );
  }

  public function manage_posts($columns) {
    $columns['micropayment'] = __('μPay Enabled', $this->plugin_name);
    return $columns;
  }

  public function manage_custom_column($column, $post_id) {
    // get this post's meta information from db
    $meta = get_post_meta($post_id);
    // checkbox value
    $name = $this->option_name . '_checkbox_value';
    $checkbox_value = ( isset( $meta[$name][0] ) &&  '1' === $meta[$name][0] ) ? 1 : 0;    
    if ($column == 'micropayment'){
      echo '<input type="checkbox" name="'. $name .'['. $post_id .']" value=1' . checked($checkbox_value, 1, false) . '>';
    }
  }

  public function register_bulk_micropay($bulk_actions) {
    $bulk_actions['update_micropay'] = __('Update μPay for selected posts', $this->plugin_name);
    return $bulk_actions;
  }

  public function handle_bulk_micropay($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'update_micropay') {
      return $redirect_to;
    }
    $name = $this->option_name . '_checkbox_value';
    $checkbox_values = $_REQUEST[$name];
    foreach ($post_ids as $post_id) {
      // perform action for each post
      $value = $checkbox_values[$post_id];
      if (empty($value)) {
        $value = 0;
      }
      update_post_meta(
        $post_id,
        $name,
        $value
      );
    }
    return add_query_arg('bulk_update_micropay_posts', count($post_ids), $redirect_to);
  }

}