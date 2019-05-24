<?php

class HederaMicropayment {

  protected $loader;
  protected $plugin_name;
  protected $version;

  public function __construct() {
    $this->plugin_name = 'hedera-micropayment';
    $this->version = '1.0.0';

    $this->load_dependencies();
    $this->set_locale();
    $this->create_endpoint();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }

  private function load_dependencies() {
    // specific Hedera class and utility functions for Hedera (re-usable for other php projects, beyond this plugin development)
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'hedera/hedera.php';
    // standard approach to loading in boilerplate php code
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hedera-micropayment-loader.php';
    // importing code specific to our plugin project
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'crypto/class-hedera-micropayment-crypto.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'api/class-hedera-micropayment-api.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hedera-micropayment-admin-link.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hedera-micropayment-admin-post-listview.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hedera-micropayment-admin-post.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hedera-micropayment-admin.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-hedera-micropayment-public.php';
    $this->loader = new HederaMicropaymentLoader();
  }

  private function set_locale() {
    // TODO
  }

  private function create_endpoint() {
    // handle REST API functionalities for our plugin
    $plugin_endpoint = new HederaMicropaymentAPI($this->get_plugin_name(), $this->get_version());
    $this->loader->add_action('rest_api_init', $plugin_endpoint, 'HederaMicropayment_api_routes');
  }

  private function define_admin_hooks() {
    define('ADMIN_ENQUEUE_SCRIPTS', 'admin_enqueue_scripts');
    define('ENQUEUE_SCRIPTS', 'enqueue_scripts');
    // plugin admin settings link
    $plugin_admin_link = new HederaMicropaymentAdminLink($this->get_plugin_name(), $this->get_version());
    $this->loader->add_filter('plugin_action_links' , $plugin_admin_link, 'register_settings_link', 10, 2);
    // plugin admin
    $plugin_admin = new HederaMicropaymentAdmin($this->get_plugin_name(), $this->get_version());
    $this->loader->add_action(ADMIN_ENQUEUE_SCRIPTS, $plugin_admin, 'enqueue_styles');
    $this->loader->add_action(ADMIN_ENQUEUE_SCRIPTS, $plugin_admin, ENQUEUE_SCRIPTS);
    $this->loader->add_action('admin_menu', $plugin_admin, 'add_options_page');
    $this->loader->add_action('admin_init', $plugin_admin, 'register_setting');
    // administering specific post
    $plugin_admin_post = new HederaMicropaymentAdminPost($this->get_plugin_name(), $this->get_version());
    $this->loader->add_action('add_meta_boxes', $plugin_admin_post, 'add_custom_box');
    $this->loader->add_action('save_post', $plugin_admin_post, 'save_post_data');
    // administering post listview
    $plugin_admin_post_listview = new HederaMicropaymentAdminPostListview($this->get_plugin_name(), $this->get_version());
    $this->loader->add_action(ADMIN_ENQUEUE_SCRIPTS, $plugin_admin_post_listview, ENQUEUE_SCRIPTS);
    $this->loader->add_filter('manage_posts_columns', $plugin_admin_post_listview, 'manage_posts');
    $this->loader->add_action('manage_posts_custom_column', $plugin_admin_post_listview, 'manage_custom_column', 10, 2);
    $this->loader->add_filter('bulk_actions-edit-post', $plugin_admin_post_listview, 'register_bulk_micropay');
    $this->loader->add_filter('handle_bulk_actions-edit-post', $plugin_admin_post_listview, 'handle_bulk_micropay', 10, 3);
  }

  private function define_public_hooks() {
    $plugin_public = new HederaMicropaymentPublic( $this->get_plugin_name(), $this->get_version());
    // load our hedera-micropayment js
    $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    // noscript rule
    $this->loader->add_action('wp_head', $plugin_public, 'assemble_noscript_tag');
    // hedera-micropayment tag
    $this->loader->add_filter('the_content', $plugin_public, 'micropayment_tag');
    // register and track anon_id
    $this->loader->add_filter('query_vars', $plugin_public, 'register_query_vars');
    $this->loader->add_action('pre_get_posts', $plugin_public, 'pre_get_posts');
    $this->loader->add_filter('post_link', $plugin_public, 'append_query_string', 10, 3);
  }

  public function run() {
    $this->loader->run();
  }

  public function get_plugin_name() {
    return $this->plugin_name;
  }

  public function get_loader() {
    return $this->loader;
  }

  public function get_version() {
    return $this->version;
  }
}