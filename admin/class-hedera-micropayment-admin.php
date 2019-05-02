<?php

class HederaMicropaymentAdmin {
  private $plugin_name;
  private $version;
  private $option_name = 'hedera_micropayment';
  
  public function __construct($plugin_name, $version) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  public function enqueue_styles() {
    wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/hedera-micropayment-admin.css', null, $this->version, false );
    wp_enqueue_style( $this->plugin_name . '-admin-post', plugin_dir_url( __FILE__ ) . 'css/hedera-micropayment-admin-post.css', null, $this->version, false );
  } 

  
  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/hedera-micropayment-admin.js', array( 'jquery' ), $this->version, false );

    $recipients = get_option($this->option_name . '_recipient', false);
    $num_of_pairs = 1;
    if (is_array($recipients)) {
      if (count($recipients) != 0) {
        $num_of_pairs = count($recipients);
      }
    }

    wp_localize_script( $this->plugin_name, 'ajax_var', array(
      'num_of_pairs' => $num_of_pairs
    ));
  }

  public function add_options_page() {
    $this->plugin_screen_hook_suffix = add_options_page(
      __('Hedera Micropayment Settings', $this->plugin_name),
      __('Hedera Micropayment', $this->plugin_name),
      'manage_options',
      $this->plugin_name,
      array($this, 'display_options_page')
    );
  }

  /**
   * render the options page for plugin
   */
  public function display_options_page() {
    include_once 'partials/hedera-micropayment-admin-display.php';
  }

  public function register_setting() {
    // add a general section
    add_settings_section(
      $this->option_name . '_general',
      __('General', $this->plugin_name),
      array($this, $this->option_name . '_general_cb'),
      $this->plugin_name
    );

    // number of free content before micropayment is charged
    add_settings_field(
      $this->option_name . '_free',
      __( 'Number of free articles', $this->plugin_name ),
      array( $this, $this->option_name . '_free_cb' ),
      $this->plugin_name,
      $this->option_name . '_general',
      array( 'label_for' => $this->option_name . '_free' )
    );

    /**
     * <hedera-micropayment
     * data-submissionNode='0.0.3',
     * data-paymentServer='mps.thetimesta.mp',
     * data-recipientList='[{ tinybars: 41666, to: '0.0.1001' }]',
     * data-contentID='test1',
     * data-memo='micropayments-test-1',
     * data-type='article',
     * data-extensionID='',
     * data-time='1',
     * ></hedera-micropayment>
     */

    // micropayment submissionNode
    add_settings_field(
      $this->option_name . '_submission_node',
      __( 'Submission Node Account ID', $this->plugin_name ),
      array( $this, $this->option_name . '_submission_node_cb' ),
      $this->plugin_name
      // $this->option_name . '_general',
      // array( 'label_for' => $this->option_name . '_submission_node' )
    );

    // micropayment extensionID
    add_settings_field(
      $this->option_name . '_extension_id',
      __( 'Extension ID', $this->plugin_name ),
      array( $this, $this->option_name . '_extension_id_cb' ),
      $this->plugin_name,
      $this->option_name . '_general',
      array( 'label_for' => $this->option_name . '_extension_id' )
    );

    // micropayment message type ie. maximum/402/article/video
    add_settings_field(
    $this->option_name . '_type',
    __( 'Message type', $this->plugin_name ),
    array( $this, $this->option_name . '_type_cb' ),
    $this->plugin_name,
    $this->option_name . '_general',
    array( 'label_for' => $this->option_name . '_type' )
    );

    // micropayment recipientList
    add_settings_field(
      $this->option_name . '_recipient',
      __( 'Recipient(s)', $this->plugin_name),
      array( $this, $this->option_name . '_recipient_cb'),
      $this->plugin_name,
      $this->option_name . '_general',
      array( 'label_for' => $this->option_name . '_recipient')
    );

    register_setting( $this->plugin_name, $this->option_name . '_free', array( $this, $this->option_name . '_sanitize' ) );
    register_setting( $this->plugin_name, $this->option_name . '_submission_node', array( $this, $this->option_name . '_sanitize' ) );
    register_setting( $this->plugin_name, $this->option_name . '_extension_id', array( $this, $this->option_name . '_sanitize' ) );
    register_setting( $this->plugin_name, $this->option_name . '_type', array( $this, $this->option_name . '_sanitize' ) );
    register_setting( $this->plugin_name, $this->option_name . '_recipient', array( $this, $this->option_name . '_recipient_sanitize' ));
    register_setting( $this->plugin_name, $this->option_name . '_amount', array( $this, $this->option_name . '_amount_sanitize' ));
    register_setting( $this->plugin_name, $this->option_name . '_payment_server', array( $this, $this->option_name . '_sanitize' ));
    register_setting( $this->plugin_name, $this->option_name . '_payment_server_pub', array( $this, $this->option_name . '_sanitize' ));
    register_setting( $this->plugin_name, $this->option_name . '_redirect_non_paying_account', array( $this, $this->option_name . '_sanitize' ));
    register_setting( $this->plugin_name, $this->option_name . '_redirect_no_account', array( $this, $this->option_name . '_sanitize' ));
    register_setting( $this->plugin_name, $this->option_name . '_redirect_homepage', array( $this, $this->option_name . '_sanitize' ));
    register_setting( $this->plugin_name, $this->option_name . '_redirect_no_extension', array( $this, $this->option_name . '_sanitize' ));

    // constants
    define('MICROPAYMENT_SERVER', '_micropayment_server');
    define('MICROPAYMENT_SERVER_URL', '_micropayment_server_url');
    define('MICROPAYMENT_SERVER_PUB', '_micropayment_server_pub');

    // add micropayment server section
    add_settings_section(
      $this->option_name . MICROPAYMENT_SERVER,
      __('Micropayment Server', $this->plugin_name),
      array($this, $this->option_name . '_micropayment_server_cb'),
      $this->plugin_name
    );

    // micropayment payment server URL
    add_settings_field(
      $this->option_name . MICROPAYMENT_SERVER_URL,
      __( 'URL', $this->plugin_name ),
      array( $this, $this->option_name . '_payment_server_cb' ),
      $this->plugin_name,
      $this->option_name . MICROPAYMENT_SERVER,
      array( 'label_for' => $this->option_name . MICROPAYMENT_SERVER_URL )
    );

    // micropayment payment server public key
    add_settings_field(
      $this->option_name . MICROPAYMENT_SERVER_PUB,
      __( 'Public Key', $this->plugin_name ),
      array( $this, $this->option_name . '_payment_server_pub_cb' ),
      $this->plugin_name,
      $this->option_name . MICROPAYMENT_SERVER,
      array( 'label_for' => $this->option_name . MICROPAYMENT_SERVER_URL )
    );

    register_setting( $this->plugin_name, $this->option_name . MICROPAYMENT_SERVER_URL, array( $this, $this->option_name . '_sanitize' ) );
    register_setting( $this->plugin_name, $this->option_name . MICROPAYMENT_SERVER_PUB, array( $this, $this->option_name . '_sanitize' ) );

    // constants
    define('CUSTOMIZE_REDIRECT', '_customize_redirect');

    // add a customize redirect section
    add_settings_section(
      $this->option_name . CUSTOMIZE_REDIRECT,
      __('Customize Redirect URLs', $this->plugin_name),
      array($this, $this->option_name . '_customize_redirect_cb'),
      $this->plugin_name
    );

    // micropayment redirect_non_paying_account url
    add_settings_field(
    $this->option_name . '_redirect_non_paying_account',
    __( 'Redirect non paying account URL', $this->plugin_name ),
    array( $this, $this->option_name . '_redirect_non_paying_account_cb' ),
    $this->plugin_name,
    $this->option_name . CUSTOMIZE_REDIRECT,
    array( 'label_for' => $this->option_name . '_redirect_non_paying_account' )
    );

    // micropayment redirect_no_account url
    add_settings_field(
      $this->option_name . '_redirect_no_account',
      __( 'Redirect no account URL', $this->plugin_name ),
      array( $this, $this->option_name . '_redirect_no_account_cb' ),
      $this->plugin_name,
      $this->option_name . CUSTOMIZE_REDIRECT,
      array( 'label_for' => $this->option_name . '_redirect_no_account' )
    );

    // micropayment redirect_homepage url
    add_settings_field(
      $this->option_name . '_redirect_homepage',
      __( 'Redirect homepage URL', $this->plugin_name ),
      array( $this, $this->option_name . '_redirect_homepage_cb' ),
      $this->plugin_name,
      $this->option_name . CUSTOMIZE_REDIRECT,
      array( 'label_for' => $this->option_name . '_redirect_homepage' )
    );

    // micropayment redirect_no_extension url
    add_settings_field(
      $this->option_name . '_redirect_no_extension',
      __( 'Redirect no extension URL', $this->plugin_name ),
      array( $this, $this->option_name . '_redirect_no_extension_cb' ),
      $this->plugin_name,
      $this->option_name . CUSTOMIZE_REDIRECT,
      array( 'label_for' => $this->option_name . '_redirect_no_extension' )
    );
  }

  public function hedera_micropayment_general_cb() {
    echo '<p>' . __('Please change the settings accordingly.', $this->plugin_name) . '</p>';
  }

  public function hedera_micropayment_free_cb() {
    $free = get_option($this->option_name . '_free');
    echo '<input type="text" name="' . $this->option_name . '_free' . '" id="' . $this->option_name . '_free' . '" value="' . $free .'"> </br><span>No. of articles user reads for free<span/>';
  }

  public function hedera_micropayment_submission_node_cb() {
    $submission_node = get_option($this->option_name . '_submission_node');
    echo '<input type="hidden" name="' . $this->option_name . '_submission_node' . '" id="' . $this->option_name . '_submission_node' . '" value="' . $submission_node .'">';
  }

  public function hedera_micropayment_payment_server_cb() {
    $payment_server = get_option($this->option_name . '_payment_server');
    echo '<input type="text" name="' . $this->option_name . '_payment_server' . '" id="' . $this->option_name . '_payment_server' . '" value="' . $payment_server .'" size="35"> </br><span>Url of payment server ie. https://123.com<span/>';
  }

  public function hedera_micropayment_payment_server_pub_cb() {
    $payment_server_pub = get_option($this->option_name . '_payment_server_pub');
    echo '<input type="text" name="' . $this->option_name . '_payment_server_pub' . '" id="' . $this->option_name . '_payment_server_pub' . '" value="' . $payment_server_pub .'" size="35"> </br><span>Public Key for payment server<span/>';
  }

  public function hedera_micropayment_extension_id_cb() {
    $extension_id = get_option($this->option_name . '_extension_id');
    echo '<input type="text" name="' . $this->option_name . '_extension_id' . '" id="' . $this->option_name . '_extension_id' . '" value="' . $extension_id .'" size="35"> </br><span>A Hedera Chrome extension ID ie. ligpaondaabclfigagcifobaelemiena<span/>';
  }

  public function hedera_micropayment_type_cb() {
    $type = get_option($this->option_name . '_type');
    echo '<input type="text" name="' . $this->option_name . '_type' . '" id="' . $this->option_name . '_type' . '" value="' . $type .'" size="35"> </br><span>Extension message types, ie."article",<span/>';
  }

  public function hedera_micropayment_redirect_non_paying_account_cb() {
    $redirect_non_paying_account = get_option($this->option_name . '_redirect_non_paying_account');
    echo '<input type="text" name="' . $this->option_name . '_redirect_non_paying_account' . '" id="' . $this->option_name . '_redirect_non_paying_account' . '" value="' . $redirect_non_paying_account .'" size="35"> </br><span>Redirect non paying account url path when payment fails to block content ie. "/non-paying-account" <span/>';
  }

  public function hedera_micropayment_redirect_no_account_cb() {
    $redirect_no_account = get_option($this->option_name . '_redirect_no_account');
    echo '<input type="text" name="' . $this->option_name . '_redirect_no_account' . '" id="' . $this->option_name . '_redirect_no_account' . '" value="' . $redirect_no_account .'" size="35"> </br><span>Redirect no account url path when payment fails to block content ie. "/no-account" <span/>';
  }

  public function hedera_micropayment_redirect_homepage_cb() {
    $redirect_homepage = get_option($this->option_name . '_redirect_homepage');
    echo '<input type="text" name="' . $this->option_name . '_redirect_homepage' . '" id="' . $this->option_name . '_redirect_homepage' . '" value="' . $redirect_homepage .'" size="35"> </br><span>Redirect homepage url path when payment fails to block content ie. "/" <span/>';
  }

  public function hedera_micropayment_redirect_no_extension_cb() {
    $redirect_no_extension = get_option($this->option_name . '_redirect_no_extension');
    echo '<input type="text" name="' . $this->option_name . '_redirect_no_extension' . '" id="' . $this->option_name . '_redirect_no_extension' . '" value="' . $redirect_no_extension .'" size="35"> </br><span>Redirect no extension url path when payment fails to block content ie. "/no-extension" <span/>';
  }

  public function hedera_micropayment_recipient_cb() {
    // retrieval code here to pull out stored recipients and then render them in a loop in php with corresponding values
    $recipients = get_option($this->option_name . '_recipient', false);
    include_once 'partials/hedera-micropayment-admin-recipients.php';
  }

  public function hedera_micropayment_sanitize( $value ) {
    return $value;
  }
  
  public function hedera_micropayment_recipient_sanitize( $value ) {
    // ignore any associative array that has empty string for either value
    $value = array_filter($value, function($v) {
      if ($v['account'] !== '' && $v['amount'] !== '') {
        return $v;
      }
    });
    // re-index from 0, then return
    return array_values($value);
  }

  public function hedera_micropayment_micropayment_server_cb() {
    echo '<p>' . __('Specify the configuration details for our micropayment server', $this->plugin_name) . '</p>';
  }

  public function hedera_micropayment_customize_redirect_cb() {
    echo '<p>' . __('Customize redirect URLs', $this->plugin_name) . '</p>';
  }

}