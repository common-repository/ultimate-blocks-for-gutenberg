<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @version       1.0.0
 * @package       Master_Blocks
 * @license       Copyright Master_Blocks
 */

if ( ! function_exists( 'jltmb_option' ) ) {
	/**
	 * Get setting database option
	 *
	 * @param string $section default section name jltmb_general .
	 * @param string $key .
	 * @param string $default .
	 *
	 * @return string
	 */
	function jltmb_option( $section = 'jltmb_general', $key = '', $default = '' ) {
		$settings = get_option( $section );

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}

function jltmb_get_options($key, $network_override = true)
{
	if (is_network_admin()) {
		$value = get_site_option($key);
	} elseif (!$network_override && is_multisite()) {
		$value = get_site_option($key);
	} elseif ($network_override && is_multisite()) {
		$value = get_option($key);
		$value = (false === $value || (is_array($value) && in_array('disabled', $value))) ? get_site_option($key) : $value;
	} else {
		$value = get_option($key);
	}

	return $value;
}


function jltmb_check_options($option_name)
{
	if (isset($option_name)) {
		$option_name = $option_name;
	}

	return isset($option_name) ? $option_name : false;
}

// WordPress function 'update_site_option' and 'update_option'
function jltmb_update_options($option_name, $option_value)
{
	if (JLTMB_NETWORK_ACTIVATED == true) {
		// Update network site option
		return update_site_option($option_name, $option_value);
	} else {
		// Update blog option
		return update_option($option_name, $option_value);
	}
}

if ( ! function_exists('jltmb_get_api_url' ) ) {
	/**
	 * Get Templates API URL
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function jltmb_get_api_url() {
		if (defined('JLTMB_BLOCKS_STORE_URL')){
			return JLTMB_BLOCKS_STORE_URL;
		} else{
			return 'https://masterblockstemplates.jeweltheme.com';
		}
	}
}

if ( ! function_exists( 'jltmb_exclude_pages' ) ) {
	/**
	 * Get exclude pages setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function jltmb_exclude_pages() {
		return jltmb_option( 'jltmb_triggers', 'exclude_pages', array() );
	}
}

if ( ! function_exists( 'jltmb_exclude_pages_except' ) ) {
	/**
	 * Get exclude pages except setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function jltmb_exclude_pages_except() {
		return jltmb_option( 'jltmb_triggers', 'exclude_pages_except', array() );
	}
}