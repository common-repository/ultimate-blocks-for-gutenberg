<?php
/**
 * Plugin Name: Master Blocks - Ultimate Gutenberg Blocks for Marketers
 * Plugin URI:  https://jeweltheme.com/master-blocks
 * Description: Gutenberg Blocks Collection Plugin
 * Version:     1.4.1.3
 * Author:      Jewel Theme
 * Author URI:  https://jeweltheme.com/master-blocks
 * Text Domain: ultimate-blocks-for-gutenberg
 * Domain Path: languages/
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package ultimate-blocks-for-gutenberg
 */

/*
 * don't call the file directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	wp_die( esc_html__( 'You can\'t access this page', 'ultimate-blocks-for-gutenberg' ) );
}

$jltmb_plugin_data = get_file_data(
	__FILE__,
	array(
		'Version'     => 'Version',
		'Plugin Name' => 'Plugin Name',
		'Author'      => 'Author',
		'Description' => 'Description',
		'Plugin URI'  => 'Plugin URI',
	),
	false
);

// Define Constants.
if ( ! defined( 'JLTMB' ) ) {
	define( 'JLTMB', $jltmb_plugin_data['Plugin Name'] );
}

if ( ! defined( 'JLTMB_VER' ) ) {
	define( 'JLTMB_VER', $jltmb_plugin_data['Version'] );
}

if ( ! defined( 'JLTMB_AUTHOR' ) ) {
	define( 'JLTMB_AUTHOR', $jltmb_plugin_data['Author'] );
}

if ( ! defined( 'JLTMB_DESC' ) ) {
	define( 'JLTMB_DESC', $jltmb_plugin_data['Author'] );
}

if ( ! defined( 'JLTMB_URI' ) ) {
	define( 'JLTMB_URI', $jltmb_plugin_data['Plugin URI'] );
}

if ( ! defined( 'JLTMB_DIR' ) ) {
	define( 'JLTMB_DIR', __DIR__ );
}

if ( ! defined( 'JLTMB_FILE' ) ) {
	define( 'JLTMB_FILE', __FILE__ );
}

if ( ! defined( 'JLTMB_SLUG' ) ) {
	define( 'JLTMB_SLUG', dirname( plugin_basename( __FILE__ ) ) );
}

if ( ! defined( 'JLTMB_BASE' ) ) {
	define( 'JLTMB_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'JLTMB_PATH' ) ) {
	define( 'JLTMB_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'JLTMB_URL' ) ) {
	define( 'JLTMB_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
}

if ( ! defined( 'JLTMB_INC' ) ) {
	define( 'JLTMB_INC', JLTMB_PATH . '/Inc/' );
}

if ( ! defined( 'JLTMB_LIBS' ) ) {
	define( 'JLTMB_LIBS', JLTMB_PATH . 'Libs' );
}

if ( ! defined( 'JLTMB_ASSETS' ) ) {
	define( 'JLTMB_ASSETS', JLTMB_URL . 'assets/' );
}

if ( ! defined( 'JLTMB_IMAGES' ) ) {
	define( 'JLTMB_IMAGES', JLTMB_URL . 'images/' );
}


if ( ! class_exists( '\\JLTMB\\Master_Blocks' ) ) {
	// Autoload Files.
	include_once JLTMB_DIR . '/vendor/autoload.php';
	// Instantiate Master_Blocks Class.
	include_once JLTMB_DIR . '/class-ultimate-blocks-for-gutenberg.php';
}

// Activation and Deactivation hooks.
if ( class_exists( '\\JLTMB\\Master_Blocks' ) ) {
	register_activation_hook( JLTMB_FILE, array( '\\JLTMB\\Master_Blocks', 'jltmb_activation_hook' ) );
	// register_deactivation_hook( JLTMB_FILE, array( '\\JLTMB\\Master_Blocks', 'jltmb_deactivation_hook' ) );
}