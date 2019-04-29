<?php
/**
 * @package HederaMicropayment
 * @version 0.9.0
 */
/*
Plugin Name: Hedera Micropayment
Plugin URI: https://hedera.com
Description: Integrate micropayments into your Word Press website. The Hedera Word Press Plugin allows publishers to set prices for their content, denominated in hbar. Users who have a Hedera Browser Extension installed on their Chrome Browser will be able to pay per article in real time.
Version: 0.9.0
Author: Hedera Hashgraph LLC, Calvin Cheng, Serene Lim
Author URI: https://github.com/hashgraph
License: GPL-2.0+
Text Domain: hedera-micropayment
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// autoload various php dependencies installed via composer
require_once("vendor/autoload.php");

// Handle plugin activation and deactivation
function activate_hedera_micropayment() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-hedera-micropayment-activator.php';
  HederaMicropaymentActivator::activate();
}

function deactivate_hedera_micropayment() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-hedera-micropayment-deactivate.php';
  HederaMicropaymentDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hedera_micropayment' );
register_deactivation_hook( __FILE__, 'deactivate_hedera_micropayment' );

/**
 * core plugin class that handles internalization, admin-specific hooks and public-facing site hooks
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hedera-micropayment.php';

function run_hedera_micropayment() {
  $plugin = new HederaMicropayment();
  $plugin->run();
}

run_hedera_micropayment();