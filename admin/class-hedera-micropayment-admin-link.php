<?php

class HederaMicropaymentAdminLink {
  private $plugin_name;
  private $version;
  
  public function __construct($plugin_name, $version) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  public function register_settings_link( $links, $plugin_filename ) {
    if ($plugin_filename === "hedera-micropayment/hedera-micropayment.php") {
      $mylinks = array('<a href="' . admin_url( 'options-general.php?page=hedera-micropayment' ) . '">' . __('Settings', $this->plugin_name) . '</a>');
      return array_merge($mylinks, $links);
    }
	  return $links;
  }
}