<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function hedera_micropayment_remove_db() {
  global $wpdb;
  
  $sql = "SET FOREIGN_KEY_CHECKS=0;";
  $wpdb->query($sql);
  $t0 = $wpdb->prefix . 'hedera_micropayment_records';
  $sql = "DROP TABLE $t0;";
  $wpdb->query($sql);
  $t1 = $wpdb->prefix . 'hedera_micropayment_anon_users';
  $sql = "DROP TABLE $t1;";
  $wpdb->query($sql);
  $sql = "SET FOREIGN_KEY_CHECKS=1;";
  $wpdb->query($sql);
  
  // remove the current db version key from options table
  delete_option('hedera_micropayment_db_version');
}

hedera_micropayment_remove_db();