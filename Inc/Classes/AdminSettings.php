<?php

namespace JLTMB\Inc\Classes;
use JLTMB\Inc\Classes\Rest_Api;
use JLTMB\Libs\ServerInfo;
use JLTMB\Libs\Readme_Parser;
use JLTMB\Libs\Rollback;

/*
	* Master Blocks Dashboard Page
	* Jewel Theme < Liton Arefin >
	*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class AdminSettings {


	public $menu_title;
	public $server_info;
	public $readme_text;
	public $premium_check;
	public $version_rollback;

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'jltmb_admin_menu' ], '', 10 );
		add_action( 'network_admin_menu', [ $this, 'jltmb_admin_menu' ], '', 10 );
		add_action( 'admin_enqueue_scripts', [ $this, 'jltmb_admin_enqueue_scripts' ] );
		add_action( 'admin_head', [ $this, 'jltma_admin_head_script' ] );
		add_action( 'admin_body_class', [ $this, 'jltma_admin_body_class' ] );
        add_action( 'all_plugins', [$this, 'jltmb_white_label_update'],100 );

		$this->server_info = new ServerInfo();

		// Parse Readme File
		$radme_file = new Readme_Parser();
		$readme_file_parse = $radme_file->parse_readme(JLTMB_PATH . '/readme.txt');
		$this->readme_text = wp_filter_nohtml_kses($readme_file_parse['sections']['changelog']);

		//Rollback Version
		$rollback = new Rollback();
		foreach ( $rollback->get_rollback_versions() as $version ) {
			$this->version_rollback[] = $version;
		}
	}

	/**
	 * Admin Body Class
	 */
	public function jltma_admin_body_class( $class ) {
		$bodyclass  = '';
		$bodyclass .= ' jltmb-admin ';
		return $class . $bodyclass;
	}


	public function get_menu_title() {
		return ( $this->menu_title ) ? $this->menu_title : $this->get_page_title();
	}

	protected function get_page_title() {
		return __( 'Master Blocks', 'ultimate-blocks-for-gutenberg' );
	}

	// Main Menu
	public function jltmb_admin_menu() {
		$jltma_white_label_setting = get_option('jltmb_blocks_settings');
		$jltma_white_label_setting = !empty($jltma_white_label_setting) ? $jltma_white_label_setting['whitelabels'] : '';
		$image_id                  = ( ! empty( $jltma_white_label_setting['jltmb_wl_plugin_logo']['id'] ) ) ? $jltma_white_label_setting['jltmb_wl_plugin_logo']['id'] : '';

		$jltmb_logo_image = '';
			$jltmb_logo_image = JLTMB_IMAGES . 'menu-icon.svg';
		$page_title  = ( isset( $jltma_white_label_setting['jltmb_wl_plugin_menu_label'] ) && $jltma_white_label_setting['jltmb_wl_plugin_menu_label'] ) ? $jltma_white_label_setting['jltmb_wl_plugin_menu_label'] : JLTMB;
		$menut_label = ( isset( $jltma_white_label_setting['jltmb_wl_plugin_menu_label'] ) && $jltma_white_label_setting['jltmb_wl_plugin_menu_label'] ) ? $jltma_white_label_setting['jltmb_wl_plugin_menu_label'] : __( 'Master Blocks', 'ultimate-blocks-for-gutenberg' );

		add_menu_page(
			$page_title,
			$menut_label,
			'manage_options',
			'jlt-master-blocks',
			[ $this, 'jltmb_admin_settings_page_content' ],
			$jltmb_logo_image,
			57
		);
	}

	public function jltma_admin_head_script() {
		$jltma_white_label_setting = get_option('jltmb_blocks_settings');
		$jltma_white_label_setting = !empty($jltma_white_label_setting) ? $jltma_white_label_setting['whitelabels'] : '';
		$image_id                  = ( ! empty( $jltma_white_label_setting['jltmb_wl_plugin_logo']['id'] ) ) ? $jltma_white_label_setting['jltmb_wl_plugin_logo']['id'] : '';

		$jltmb_logo_image = '';
			$jltmb_logo_image = JLTMB_IMAGES . 'menu-icon.svg';

		if ( $image_id ) { ?>
			<style>
				.svg .wp-badge.welcome__logo {
					background: url('<?php echo $jltmb_logo_image; ?>') left center no-repeat;
				}

				#adminmenu li.wp-has-current-submenu .wp-menu-image img {
					width: 26px;
				}

				.jltmb .header .jltmb_logo .wp-badge {
					width: none;
				}

				#adminmenu .wp-menu-image img {
					width: 20px;
				}
			</style>
			<?php
		}
	}


	public function jltmb_admin_enqueue_scripts() {
		$screen = get_current_screen();


		// Load Scripts only Master Blocks Admin Page
		if ( $screen->id == 'toplevel_page_jlt-master-blocks' || $screen->id == 'toplevel_page_jlt-master-blocks-network' ) {

			// JS
			if (!did_action('wp_enqueue_media')) {
				wp_enqueue_media();
			}

				$this->premium_check = 'nopremium';
            // React Admin Settings
            wp_enqueue_script( 'master-blocks-admin-settings', JLTMB_ASSETS . 'admin/js/master-blocks-admin-settings.js', array( 'react', 'wp-element', 'wp-i18n' ), JLTMB_VER, true );

            // Localize Scripts
            $jltmb_localize_data = array(
				'rest_url'                 => Rest_Api::get_rest_url(''),
				'ajax_url'                 => admin_url('admin-ajax.php'),
				'nonce'                    => wp_create_nonce( 'wp_rest' ),
                'ajax_url'                 => admin_url('admin-ajax.php'),
                'plugin_name'              => JLTMB,
                'image_dir'                => JLTMB_IMAGES,
                'version'                  => JLTMB_VER,
	            'saving_confirm'           => [
					'title'      => __( 'Saved', 'ultimate-blocks-for-gutenberg' ),
					'text' 		 => __( 'Saved Setttings', 'ultimate-blocks-for-gutenberg' ),
					'btn_text' 	 => __( 'Okay', 'ultimate-blocks-for-gutenberg' ),
				],
				'settings'                	=> get_option('jltmb_blocks_settings', null),
				'is_premium'                => $this->premium_check,
				'server_info'				=> $this->server_info,
				'active_plugins'			=> $this->server_info::get_active_plugins(),
				'readme_text'				=> $this->readme_text,
				'rollback_versions'			=> $this->version_rollback,
				'rollback_url'				=> wp_nonce_url( admin_url('admin-post.php?action=master_blocks_rollback&version=VERSION'), 'master_blocks_rollback'), __('Reinstall', JLTMB_VER),
            );
            wp_localize_script('master-blocks-admin-settings', 'JLTMB', $jltmb_localize_data);
		}

		// Localize Script
		if ( is_customize_preview() ) {
			return;
		}
	}

	/**
	 * Admin Options Settings
	 *
	 * @return void
	 */
	public function jltmb_admin_settings_page_content() {
		// React based Options setting
		echo '<div id="jltmb-settings-root" class="jltmb-admin-full-wrapper"></div>';
	}

	/**
	 * White Label Settings
	 */
	public function jltmb_white_label_update($all_plugins){
		$settings = get_option('jltmb_blocks_settings', null);
		$settings = !empty($settings) ? $settings['whitelabels'] : '';

        if (!empty($all_plugins[JLTMB_BASE]) && is_array($all_plugins[JLTMB_BASE])) {
            $all_plugins[JLTMB_BASE]['Name']           = !empty($settings['jltmb_wl_plugin_name']) ? $settings['jltmb_wl_plugin_name'] : $all_plugins[JLTMB_BASE]['Name'];
            $all_plugins[JLTMB_BASE]['PluginURI']      = !empty($settings['jltmb_wl_plugin_url']) ? $settings['jltmb_wl_plugin_url'] : $all_plugins[JLTMB_BASE]['PluginURI'];
            $all_plugins[JLTMB_BASE]['Description']    = !empty($settings['jltmb_wl_plugin_desc']) ? $settings['jltmb_wl_plugin_desc'] : $all_plugins[JLTMB_BASE]['Description'];
            $all_plugins[JLTMB_BASE]['Author']         = !empty($settings['jltmb_wl_plugin_author_name']) ? $settings['jltmb_wl_plugin_author_name'] : $all_plugins[JLTMB_BASE]['Author'];
            $all_plugins[JLTMB_BASE]['AuthorURI']      = !empty($settings['jltmb_wl_plugin_url']) ? $settings['jltmb_wl_plugin_url'] : $all_plugins[JLTMB_BASE]['AuthorURI'];
            $all_plugins[JLTMB_BASE]['Title']          = !empty($settings['jltmb_wl_plugin_name']) ? $settings['jltmb_wl_plugin_name'] : $all_plugins[JLTMB_BASE]['Title'];
            $all_plugins[JLTMB_BASE]['AuthorName']     = !empty($settings['jltmb_wl_plugin_author_name']) ? $settings['jltmb_wl_plugin_author_name'] : $all_plugins[JLTMB_BASE]['AuthorName'];
            return $all_plugins;
        }
	}
}