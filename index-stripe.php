<?php
/**
 * Plugin Name: WP Templates - Stripe - Basic
 * Plugin URI: https://www.wptemplates.store/
 * Description: Stripe - Basic - Add Stripe buttons to your website
 * Version: 1.5.0
 * Author: WP Templates
 * Author URI: https://www.wptemplates.store/plugins/wptemplates-stripe-basic/
 */

// GLOBALS
global $wpdb;
global $wpts_version;
global $wpts_table_name;
global $wpts_error_table_name;
global $wpts_success_message;
global $wpts_err_message;
global $wpts_price_override;
$wpts_version = '1.5.0';
$wpts_table_name = $wpdb->prefix . 'wpts_stripe_buttons';
$wpts_error_table_name = $wpdb->prefix . 'wpts_stripe_errors';
$wpts_price_override = false;

// CREATE TABLE ON ACTIVATION
register_activation_hook( __FILE__, 'wpts_stripe_install' );

function wpts_stripe_install() {
	global $wpdb;
	global $wpts_table_name;
	global $wpts_error_table_name;
	global $wpts_version;
	
	// Set the charset
	$charset_collate = $wpdb->get_charset_collate();

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// SQL
	$sql = "CREATE TABLE $wpts_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		button_custom_id varchar(10) NOT NULL,
		button_name varchar(100) NOT NULL,
		button_text varchar(200) NOT NULL,
		button_css_class varchar(200) NOT NULL,
		button_type varchar(20) NOT NULL,
		button_billing_cycle varchar(10) NOT NULL,
		button_billing_cycle_interval varchar(10) NOT NULL,
		button_product_name varchar(100) NOT NULL,
		button_product_id varchar(100) NOT NULL,
		button_amount varchar(10) NOT NULL,
		button_currency varchar(10) NOT NULL,
		button_description varchar(200) NOT NULL,
		button_success_url varchar(100) NOT NULL,
		button_fail_url varchar(100) NOT NULL,
		button_image_url varchar(100) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	// Execute the First SQL
	dbDelta( $sql );

	$sql = "CREATE TABLE $wpts_error_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		button_name varchar(100) NOT NULL,
		wpts_stripe_error_message TEXT NOT NULL,
		wpts_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  		wpts_datestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id)
	) $charset_collate;";

	// Execute the Secondv SQL	
	dbDelta( $sql );
}

// CHECK VERSION FOR UPDATE
function wpts_check_update() {
    global $wpts_version;
    if ( version_compare( $wpts_version, get_option('wpts_version'), '>' ) ) {
        wpts_stripe_install();

        update_option( "wpts_version", $wpts_version );

    }
}
add_action( 'admin_init', 'wpts_check_update' );


// REMOVE TABLE ON DELETION
register_uninstall_hook( __FILE__, 'wpts_stripe_delete' );

function wpts_stripe_delete() {
	global $wpdb;
	global $wpts_table_name;
	global $wpts_error_table_name;
	
	// When the plugin is de-activated, remove the custom db table
	$deleteSQL = "DROP TABLE IF EXISTS $wpts_table_name;";
	// Delete table
	$wpdb->query($deleteSQL);
	// When the plugin is de-activated, remove the custom db table
	$deleteSQL = " DROP TABLE IF EXISTS $wpts_error_table_name;";
	// Delete table
	$wpdb->query($deleteSQL);
}

// SETTINGS
function wpts_settings() {

	// Define all the options	
	add_option( 'wpts_company_name', '');
	add_option( 'wpts_testmode', 'checked');
	add_option( 'wpts_live_public', '');
	add_option( 'wpts_live_secret', '');
	add_option( 'wpts_test_public', '');
	add_option( 'wpts_test_secret', '');
	add_option( 'wpts_zipcode', 'checked');
	add_option( 'wpts_version', $wpts_version);

	// Register them all
	register_setting( 'wpts_options_group', 'wpts_company_name');
	register_setting( 'wpts_options_group', 'wpts_testmode');
	register_setting( 'wpts_options_group', 'wpts_live_public');
	register_setting( 'wpts_options_group', 'wpts_live_secret');
	register_setting( 'wpts_options_group', 'wpts_test_public');
	register_setting( 'wpts_options_group', 'wpts_test_secret');
	register_setting( 'wpts_options_group', 'wpts_zipcode');
	register_setting( 'wpts_version', 'wpts_version');
}
add_action( 'admin_init', 'wpts_settings' );


// OPTIONS - SHOW IN MENU
function wpts_register_options_page() {
	// Top level
  add_menu_page('Stripe', 'Stripe', 'administrator', 'wpts_stripe', 'wpts_show_option_page');
  // Duplicate the top level menu and rename it
  add_submenu_page('wpts_stripe', 'General Settings', 'General Settings', 'administrator', 'wpts_show_option_page', 'wpts_show_option_page');
  // First submanu
  add_submenu_page('wpts_stripe', 'Add New', 'Add New', 'administrator', 'wpts_add_button', 'wpts_show_add_button_page');
  // Second sub menu
  add_submenu_page('wpts_stripe', 'Stripe Buttons', 'Stripe Buttons', 'administrator', 'wpts_stripe_buttons', 'wpts_show_buttons_page');
  // Third sub menu
  add_submenu_page('wpts_stripe', 'Error Log', 'Error Log', 'administrator', 'wpts_stripe_errors', 'wpts_show_errors_page');
  // Remove the top level dupicate
  remove_submenu_page('wpts_stripe','wpts_stripe');
}
add_action('admin_menu', 'wpts_register_options_page');

// ENQUE THE MEDIA CAPABILITY
add_action ( 'admin_enqueue_scripts', function () {
    if (is_admin ())
        wp_enqueue_media ();
} );

// INCLUDES
include_once( plugin_dir_path( __FILE__ ) . '/options-stripe.php' );
include_once( plugin_dir_path( __FILE__ ) . '/buttons-stripe.php' );
include_once( plugin_dir_path( __FILE__ ) . '/errors-stripe.php' );
include_once( plugin_dir_path( __FILE__ ) . '/add-edit-stripe.php' );
include_once( plugin_dir_path( __FILE__ ) . '/shortcode-stripe.php' );
include_once( plugin_dir_path( __FILE__ ) . '/payment-stripe.php' );

// EXTRA FUNCTION FOR PRICE OVERRIDE.
function wpts_price_override_function() {
	global $wpts_price_override;

	// If there is a price override, use it.
	$wpts_price_override = ($_GET["a"]) ? $_GET["a"] : false;
}
add_action('wp_head', 'wpts_price_override_function');


?>