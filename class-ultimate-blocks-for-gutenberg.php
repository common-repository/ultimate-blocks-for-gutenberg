<?php
namespace JLTMB;

use JLTMB\Libs\Assets;
use JLTMB\Libs\Helper;
use JLTMB\Inc\Classes\Admin;
use JLTMB\Inc\Classes\Rest_Api;

/**
 * Main Class
 *
 * @ultimate-blocks-for-gutenberg
 * Jewel Theme <support@jeweltheme.com>
 * @version     1.4.1
 */

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Master_Blocks Class
 */
if ( ! class_exists( '\JLTMB\Master_Blocks' ) ) {

	/**
	 * Class: Master_Blocks
	 */
	final class Master_Blocks {

		const VERSION            = JLTMB_VER;
		private static $instance = null;

		/**
		 * what we collect construct method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
			$this->includes();
			add_filter( 'block_categories_all', [$this, 'jltmb_register_block_category'] );
			add_action( 'plugins_loaded', array( $this, 'jltmb_plugins_loaded' ), 999 );
			// Body Class.
			add_filter( 'admin_body_class', array( $this, 'jltmb_body_class' ) );
		}

		/**
		 * plugins_loaded method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function jltmb_plugins_loaded() {
			$this->jltmb_activate();
		}

		/** Adds the Master Blocks block category.
		 *
		 * @param array $categories Existing block categories.
		 *
		 * @return array Updated block categories.
		 */
		public function jltmb_register_block_category($categories)
		{
			return array_merge(
				$categories,
				[
					[
						'slug'  => 'master_blocks',
						'title' => __('Master Blocks', 'ultimate-blocks-for-gutenberg'),
					],
				]
			);
		}

		/**
		 * Version Key
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function plugin_version_key() {
			return Helper::jltmb_slug_cleanup() . '_version';
		}

		/**
		 * Activation Hook
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public static function jltmb_activate() {
			$current_jltmb_version = get_option( self::plugin_version_key(), null );

			if ( get_option( 'jltmb_activation_time' ) === false ) {
				update_option( 'jltmb_activation_time', strtotime( 'now' ) );
			}

			if ( is_null( $current_jltmb_version ) ) {
				update_option( self::plugin_version_key(), self::VERSION );
			}

			$allowed = get_option( Helper::jltmb_slug_cleanup() . '_allow_tracking', 'no' );

			// if it wasn't allowed before, do nothing .
			if ( 'yes' !== $allowed ) {
				return;
			}
			// re-schedule and delete the last sent time so we could force send again .
			$hook_name = Helper::jltmb_slug_cleanup() . '_tracker_send_event';
			if ( ! wp_next_scheduled( $hook_name ) ) {
				wp_schedule_event( time(), 'weekly', $hook_name );
			}
		}


		/**
		 * Add Body Class
		 * @param [type] $classes .
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function jltmb_body_class( $classes ) {
			$classes .= ' ultimate-blocks-for-gutenberg ';
			return $classes;
		}

		/**
		 * Include methods
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function includes() {
			new Assets();
			new Admin();
			new Rest_Api();
		}


		/**
		 * Initialization
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function jltmb_init() {
			$this->jltmb_load_textdomain();
		}


		/**
		 * Text Domain
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function jltmb_load_textdomain() {
			$domain = 'ultimate-blocks-for-gutenberg';
			$locale = apply_filters( 'jltmb_plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, false, dirname( JLTMB_BASE ) . '/languages/' );
		}

		/**
		* Deactivate Pro Plugin if it's not already active
		*
		* @author Jewel Theme <support@jeweltheme.com>
		*/
		public static function jltmb_activation_hook() {
				$plugin = 'ultimate-blocks-for-gutenberg-pro/ultimate-blocks-for-gutenberg.php';
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
			}
		}


		/**
		 * Returns the singleton instance of the class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Master_Blocks ) ) {
				self::$instance = new Master_Blocks();
				self::$instance->jltmb_init();
			}

			return self::$instance;
		}
	}

	// Get Instant of Master_Blocks Class .
	Master_Blocks::get_instance();
}