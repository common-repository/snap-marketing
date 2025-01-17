<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              snap_finance.com
 * @since             1.0.1	
 * @package           Snap_Marketing
 *
 * @wordpress-plugin
 * Plugin Name:       Snap Marketing
 * Plugin URI:        https://developer.snapfinance.com/woocommerce-marketing/
 * Description:       The Snap Marketing plugin enables functionality for credit-challenged shoppers to get preapproved for lease-to-own financing while shopping on your webstore; thereby, giving you greater ability to close more sales.
 * Version:           1.0.9
 * Author:            Snap Finance
 * Author URI:        http://snapfinance.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       snap-marketing
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0
 * Rename this for your plugin and update it as you release new versions.
 */
if ( ! defined( 'Snap_Marketing_VERSION' ) ) {
	define( 'Snap_Marketing_VERSION', '1.0.0' );
}
if ( ! defined( 'Sandbox_API_URL' ) ) {
	define( 'Sandbox_API_URL', 'https://auth-sandbox.snapfinance.com/oauth/token' );
}
if ( ! defined( 'Sandbox_Audience_URL' ) ) {
	define( 'Sandbox_Audience_URL', 'https://api-sandbox.snapfinance.com/platform/v1' );
}
if ( ! defined( 'Training_Audience_URL' ) ) {
	define( 'Training_Audience_URL', 'https://api-release.snapfinance.com/platform/v1' );
}
if ( ! defined( 'Live_API_URL' ) ) {
	define( 'Live_API_URL', 'https://auth.snapfinance.com/oauth/token' );
}
if ( ! defined( 'Live_Audience_URL' ) ) {
	define( 'Live_Audience_URL', 'https://api.snapfinance.com/platform/v1' );
}
if ( ! defined( 'Sandbox_Snap_Marketing_SDK' ) ) {
	define( 'Sandbox_Snap_Marketing_SDK', 'https://js.snapfinance.com/sandbox/v2/snap-sdk.js' );
}
if ( ! defined( 'Live_Snap_Marketing_SDK' ) ) {
	define( 'Live_Snap_Marketing_SDK', 'https://js.snapfinance.com/v2/snap-sdk.js' );
}
if ( ! defined( 'Sandbox_Snap_Marketing_frequency_URL' ) ) {
	define( 'Sandbox_Snap_Marketing_frequency_URL', 'https://api-sandbox.snapfinance.com/platform/v1/calculator/paymentestimate?frequency=' );
}
if ( ! defined( 'Live_Snap_Marketing_frequency_URL' ) ) {
	define( 'Live_Snap_Marketing_frequency_URL', 'https://api.snapfinance.com/platform/v1/calculator/paymentestimate?frequency=' );
}
if ( ! defined( 'Snap_Marketing_Logo_URL' ) ) {
	define( 'Snap_Marketing_Logo_URL', 'https://snap-assets.snapfinance.com/' );
}
/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
$activated = true;
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}
/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
if ( $activated ) {
	add_action( 'plugins_loaded', 'snap_marketing_init_class' );
} else {
	if ( ! function_exists( 'deactivate_plugins' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

function snap_marketing_init_class() {
	add_action( 'init', 'snap_marketing_load_textdomain' );
	if ( is_admin() ) {
		include 'snap-marketing-admin-side.php';
	}
	include 'snap-marketing-front-side.php';
}

function snap_marketing_load_textdomain() {
	if ( get_option( 'default_snap_treatments' ) == '' ) {
		create_default_snap_treatments();
	}
	load_plugin_textdomain( 'snap-marketing', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

function deactivate_snap_marketing() {
	$plugin_data    = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'];
	$to             = 'devsupport@snapfinance.com';
	$subject        = 'Disabled WordPress plugin : ' . site_url();
	$body           = '<p>Following Merchant has disabled plugin</p>
	<p>Merchant URL: ' . site_url() . '</p><p>Plugins Version: ' . $plugin_version . '</p>';
	$headers        = array( 'Content-Type: text/html; charset=UTF-8' );
	wp_mail( $to, $subject, $body, $headers );
}

register_deactivation_hook( __FILE__, 'deactivate_snap_marketing' );

function activate_snap_marketing() {
	$plugin_data    = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'];
	$to             = 'devsupport@snapfinance.com';
	$subject        = 'Activated WordPress plugin : ' . site_url();
	$body           = '<p>Following Merchant has activated plugin</p>
	<p>Merchant URL: ' . site_url() . '</p><p>Plugins Version: ' . $plugin_version . '</p>';
	$headers        = array( 'Content-Type: text/html; charset=UTF-8' );
	wp_mail( $to, $subject, $body, $headers );
}

register_activation_hook( __FILE__, 'activate_snap_marketing' );

function create_default_snap_treatments() {
	update_option( 'default_snap_treatments', true );
	$snap_treatments = array(
		array(
			'title'                => 'Get Approved – As low as Example',
			'Snap_TreatmentType'   => 'AS_LOW_AS',
			'Snap_TreatmentLogo'   => constant( 'Snap_Marketing_Logo_URL' ) . 'en/us/marketing/aslowas/Dark.svg',
			'Snap_TreatmentActive' => 'Enable',
			'Snap_Product_Active'  => true,
		),
		array(
			'title'                => 'Get Approved Example',
			'Snap_TreatmentType'   => 'PRE_APPROVAL',
			'Snap_TreatmentLogo'   => constant( 'Snap_Marketing_Logo_URL' ) . 'en/us/marketing/getapproved/Dark.svg',
			'Snap_TreatmentActive' => 'Enable',
			'Snap_Product_Active'  => false,
		),
	);

	foreach ( $snap_treatments as $snap_treatment ) {
		$snap_treatment_object = get_page_by_title( $snap_treatment['title'], OBJECT, 'snap_treatments' );
		if ( empty( $snap_treatment_object ) ) {
			$treatment_array = array(
				'post_title'  => wp_strip_all_tags( $snap_treatment['title'] ),
				'post_type'   => 'snap_treatments',
				'post_status' => 'publish',
			);
			$treatment_id    = wp_insert_post( $treatment_array );
			if ( $treatment_id ) {
				if ( $snap_treatment['Snap_Product_Active'] ) {
					update_option( 'Snap_Product_Active', $treatment_id );
				}
				unset( $snap_treatment['title'] );
				unset( $snap_treatment['Snap_Product_Active'] );
				foreach ( $snap_treatment as $snap_treatment_key => $snap_treatment_value ) {
					update_post_meta( $treatment_id, $snap_treatment_key, $snap_treatment_value );
				}
			}
		}
	}

}
