<?php

define('NAME_ANON_ID', 'anon_id');
define('NAME_NONCE', 'nonce');
define('NAME_ANON_USERS', '_anon_users');
define('NAME_AUTHORIZATION', 'authorization');

class HederaMicropaymentPublic {
  private $plugin_name;
  private $version;
  private $option_name = "hedera_micropayment";
  private $anon_id_name = "anon_id";

  public function __construct($plugin_name, $version) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  public function enqueue_styles() {

  }

  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name . '-fingerprint2', plugin_dir_url( __FILE__ ) . 'js/fingerprint2.min.js', null, $this->version, false );
    wp_enqueue_script( $this->plugin_name . '-public',plugin_dir_url( __FILE__ ) . 'js/hedera-micropayment.js', array( 'jquery' ), $this->version, false );
    $anon_id = $_GET[NAME_ANON_ID];
    $anon_id_nonce = $this->anon_id_nonce( $anon_id );
    $this->associate_wp_user_id_with_anon_id( $anon_id );
    $extension_id = get_option($this->option_name . '_extension_id');
    wp_localize_script( $this->plugin_name . '-public', 'ajax_var', array(
      'url' => home_url(),
      NAME_ANON_ID => $anon_id_nonce[NAME_ANON_ID],
      NAME_NONCE => $anon_id_nonce[NAME_NONCE],
      'extension_id' => $extension_id
    ));
  }

  private function anon_id_nonce( $anon_id ) {
    $nonce = wp_create_nonce($anon_id);
    global $wpdb;
    $table_name = $wpdb->prefix . $this->option_name . NAME_ANON_USERS;
    $data = array('id' => $anon_id);
    $where = array(NAME_NONCE => $nonce);
    $wpdb->update($table_name, $data, $where);
    return array(
      NAME_ANON_ID => $anon_id,
      NAME_NONCE => $nonce
    );
  }

  private function associate_wp_user_id_with_anon_id( $anon_id ) {
    if(is_user_logged_in()) {
      $current_user = wp_get_current_user();
      $wp_user_id = $current_user->ID;
      global $wpdb;
      $table_name = $wpdb->prefix . $this->option_name . NAME_ANON_USERS;
      $data = array('fk_wp_user_id' => $wp_user_id);
      $where = array('id' => $anon_id);
      $wpdb->update($table_name, $data, $where);
    }
  }
  
  private function retrieve_recipients($post_id = 0) {
    $name = $this->option_name . '_recipient';
    if ($post_id > 0) {
      // use the recipient list specific to this $post_id
      $meta = get_post_meta($post_id);
      $recipients = $this->remove_empty_elements(unserialize($meta[$name][0]));
      // but if no recipient list was specified for this $post_id, we will use our global recipient list
      if (empty($recipients)) {
        $recipients = get_option($name, false);
      }
    } else {
      // use the global recipient list
      $recipients = get_option($name, false);
    }

    // render $recipients array into a json string
    $recipientList = '';
    if ($recipients !== false) {
      for ($i = 0; $i <= count($recipients); $i++) {
        if ($recipients[$i] !== NULL) { 
          $item = '{ "to": "' . $recipients[$i]['account'] . '", "tinybars": "' . $recipients[$i]['amount'] . '" }, ';
          $recipientList = $recipientList . $item;
        }
      }
    }
    $recipientList = substr($recipientList, 0, -2);
    return '[' . $recipientList . ']';
  }

  private function remove_empty_elements($value) {
    // $value is not an array, return null
    if (!is_array($value)) {
      return null;
    }
    $value = array_filter($value, function($v) {
      if ($v['account'] !== '' && $v['amount'] !== '') {
        return $v;
      }
    });
    // re-index from 0, then return
    return array_values($value);
  }

  private function assemble_hedera_micropayment_tag($anon_id, $override, $post_id) {
    $submission_node = $this->get_random_node();
    $payment_server = get_option($this->option_name . '_payment_server');
    $extension_id = get_option($this->option_name . '_extension_id');
    $memo = $anon_id . ',' . $post_id;
    $time = (new DateTime())->getTimestamp();
    if ($override) {
      $recipientList = $this->retrieve_recipients($post_id);
    } else {
      $recipientList = $this->retrieve_recipients();
    }

    // note that we do not use camelCase for data attributes because all browsers automatically
    // switch data attributes to lowercase.
    return "<hedera-micropayment
    data-submissionnode='" . $submission_node . "',
    data-paymentserver='" . $payment_server . "',
    data-recipientlist='" . $recipientList . "',
    data-contentid='" . $post_id . "',
    data-memo='" . $memo . "',
    data-extensionid='" . $extension_id . "',
    data-time='" . $time . "',
    ></hedera-micropayment>";
  }

  /**
   * Check if free content quota has been exceeded.
   * @return bool - true if exceeded, false if anon_id still has free content available
   */
  private function exceeded_free_content_quota($anon_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'hedera_micropayment_anon_users';
    $result = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $anon_id");
    if ($result) {
      $free_content = (int)$result->free_content;
      if ($free_content > 0) {
        // minus and then save back into the row with a reduced number
        $free_content--;
        $wpdb->update($table_name, array('free_content' => $free_content), array('id' => $anon_id));
        return false;
      }
    }
    return true;
  }

  /**
   * Check if micropay feature is enabled for a given $post_id
   */
  private function is_micropay_enabled_for_post($post_id) {
    $meta = get_post_meta($post_id);
    $name = $this->option_name . '_checkbox_value';
    return $meta[$name][0];
  }

  public function micropayment_tag( $content ) {
    global $wpdb;
    global $wp;

    $anon_id = $_GET[NAME_ANON_ID];
    if (!empty($anon_id) && is_singular('post')) {
      $post_id = get_the_ID();
      $enabled = $this->is_micropay_enabled_for_post($post_id);
      if (!$enabled) {
        return $content;
      }
      $exceeded = $this->exceeded_free_content_quota($anon_id);
      // μPay is enabled (true) and anon_id user has exceeded free content quota (true)
      if ($exceeded) {
        $override = $enabled;
        $hederaMicropayment = $this->assemble_hedera_micropayment_tag($anon_id, $override, $post_id);
        // if the last payment failed, we will not serve the content
        $table_name = $wpdb->prefix . 'hedera_micropayment_records';
        // retrieve the last transaction record for this $anon_id
        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE fk_anon_users = $anon_id ORDER BY id DESC LIMIT 1");
        if ($result) {
          $last = $result[0];
          $node_precheckcode = (int)$last->node_precheckcode;
          if ($node_precheckcode !== 0) {
            $content = 'sorry, you need to make payment';
          }
        }
        return $hederaMicropayment.$content;
      }
    }

     // if user is on home page, make sure data-type is maximum, no crypto transfer, but an alert to user
     // that publisher consumes micropayment
    $current_url = home_url(add_query_arg(array($_GET), $wp->request));
    if (is_home($current_url)) {
      var_dump("homeOrFrontPage", is_home($current_url));
      $submission_node = $this->get_random_node();
      $payment_server = get_option($this->option_name . '_payment_server');
      $extension_id = get_option($this->option_name . '_extension_id');
      $memo = $anon_id . ',' . $post_id;
      $time = (new DateTime())->getTimestamp();
      $recipientList = $this->retrieve_recipients();
  
      // note that we do not use camelCase for data attributes because all browsers automatically
      // switch data attributes to lowercase.
      return "<hedera-micropayment
      data-submissionnode='" . $submission_node . "',
      data-paymentserver='" . $payment_server . "',
      data-recipientlist='" . $recipientList . "',
      data-type=maximum
      data-memo='" . $memo . "'
      data-extensionid='" . $extension_id . "',
      data-time='" . $time . "',
      ></hedera-micropayment>";    
    }
    return $content;
  }

  public function register_query_vars( $vars ) {
    $vars[] = $this->anon_id;
    return $vars;
  }

  //   /**
  //  * Are we currently on the front page?
  //  *
  //  * @param WP_Query $query Query instance.
  //  * @return bool
  //  */
  // public function is_showing_page_on_front( $query ) {
  //   var_dump(" 11111    ", $query->is_home());
  //   var_dump(" 22222    ", 'page' === get_option( 'show_on_front' ));
  //   // return $query->is_home() && 'page' === get_option( 'show_on_front' );
  //   return $query->is_home();
  // }
  
  public function pre_get_posts( $query ) {
    // check if the user is requesting an admin page 
    // or current query is not the main query
    if ( is_admin() || ! $query->is_main_query() ){
      return;
    }
    $anon_id = $_GET[NAME_ANON_ID];

    if (empty($anon_id)) {
      global $wp;
      $current_url = home_url(add_query_arg(array($_GET), $wp->request));
      if (is_home($current_url)) {
        $new_anon_id = $this->create_anon_user();
        $new_url = home_url(add_query_arg(array( $this->anon_id_name => $new_anon_id ), $wp->request));
        wp_redirect($new_url);
        exit();
      }
    } else {
      // do nothing at the moment
    }
  }

  public function append_query_string($url, $post) {
    $anon_id = $_GET[NAME_ANON_ID];
    if ( $post->post_type == 'post' ) {
      $url = add_query_arg( $this->anon_id_name, $anon_id, $url );
    }
    return $url;
  }

  private function create_anon_user() {
    global $wpdb;
    $free = get_option( $this->option_name . '_free');
    $table_name = $wpdb->prefix . 'hedera_micropayment_anon_users';
    $wpdb->insert($table_name, array('free_content' => (int)$free));
    return $wpdb->insert_id;
  }

  private function get_random_node() {
    $env = getenv('WP_ENV');
    if ($env === false) {
      $env = 'staging';
    }
    $path_to_json = plugin_dir_path( dirname( __FILE__ ) ) . 'config/addressbook.json';
    $address_book = file_get_contents($path_to_json);

    $address_book_json = json_decode($address_book, true);
    $nodes = $address_book_json[$env];

    // output the random node selected from the random index
    $rand_node= $nodes[array_rand($nodes)];
    // get the key string (node accountID) from the selected node
    foreach($rand_node as $key => $value) {
        return $key;
    }
  }

}