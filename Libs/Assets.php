<?php
namespace JLTMB\Libs;

use JLTMB\Libs\Helper;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Assets' ) ) {

	/**
	 * Assets Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 * @version     1.4.1
	 */
	class Assets {

		/**
		 * Constructor method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
            add_action( 'admin_footer', [$this, 'jltmb_importer'] );
			add_action( 'enqueue_block_editor_assets', [$this, 'jltmb_enqueue_block_editor_assets'] );
			add_action( 'enqueue_block_assets', array( $this, 'jltmb_enqueue_scripts' ), 100 );
			add_action( 'admin_enqueue_scripts', [ $this, 'jltmb_admin_scripts' ] );
		}

		/**
		 * Get environment mode
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_mode() {
			return defined( 'WP_DEBUG' ) && WP_DEBUG ? 'development' : 'production';
		}


		/**
		 * Enqueue block editor only JavaScript and CSS.
		 */
		public function jltmb_enqueue_block_editor_assets()
		{

			// Importer CSS
			wp_enqueue_style('jltmb-importer', JLTMB_ASSETS . 'admin/css/jltmb-importer.css');


			// Blocks Init JS
			$asset_file = include_once JLTMB_PATH . 'assets/index.asset.php';
			wp_register_script( 'master-blocks-blocks', JLTMB_ASSETS . 'index.js', $asset_file['dependencies'], $asset_file['version'], true );
			$deactivated_blocks = $this->jltmb_get_deactivated_blocks_list();

			$data = [
				'deactivated_blocks'        => $deactivated_blocks,
				'rest_nonce'                => wp_create_nonce('wp_rest'),
				'site_rest_url'             => get_rest_url(null, 'master_blocks/v1/'),
				'default_global_settings'   => $this->get_default_global_settings(),
				'global_settings'           => $this->get_global_settings()
			];

			wp_enqueue_script('master-blocks-blocks');
			wp_localize_script('master-blocks-blocks', 'master_blocks', $data);


		}


		public function get_global_settings()
		{
			return array_merge($this->get_default_global_settings(), $this->get_saved_global_settings());
		}


		public function get_default_global_settings()
		{
			return [
				'active_preset' => 'preset_default',
				'presets' => [
					[
						'id' => 'preset_default',
						'label' => __('Preset #1'),
						'colors' => [
							Helper::build_color('primary', '#3A86FF', __('Primary')),
							Helper::build_color('secondary', '#EB5E81', __('Secondary')),
							Helper::build_color('text', '#F79009', __('Text')),
							Helper::build_color('accent', '#475467', __('Accent')),
						],
						'typographies' => [
							Helper::build_typography('heading_1', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '60px'], 'textLineHeight' => ['desktop' => '80px']], __('Heading 1'), 'h1'),
							Helper::build_typography('heading_2', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '48px'], 'textLineHeight' => ['desktop' => '60px']], __('Heading 2'), 'h2'),
							Helper::build_typography('heading_3', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '36px'], 'textLineHeight' => ['desktop' => '48px']], __('Heading 3'), 'h3'),
							Helper::build_typography('heading_4', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '32px'], 'textLineHeight' => ['desktop' => '36px']], __('Heading 4'), 'h4'),
							Helper::build_typography('heading_5', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '18px'], 'textLineHeight' => ['desktop' => '28px']], __('Heading 5'), 'h5'),
							Helper::build_typography('heading_6', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '16px'], 'textLineHeight' => ['desktop' => '24px']], __('Heading 6'), 'h6'),
							Helper::build_typography('paragraph', ['textFont' =>  ['weight' => '400'], 'textSize' =>  ['desktop' => '16px'], 'textLineHeight' => ['desktop' => '24px']], __('Paragraph'), 'p'),
							Helper::build_typography('button', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '16px'], 'textLineHeight' => ['desktop' => '20px']], __('Button'), 'button'),
						]
					],
					[
						'id' => 'preset_2',
						'label' => __('Preset #2'),
						'colors' => [
							Helper::build_color('primary', '#7A5AF8', __('Primary')),
							Helper::build_color('secondary', '#3A86FF', __('Secondary')),
							Helper::build_color('text', '#FBA658', __('Text')),
							Helper::build_color('accent', '#475467', __('Accent')),
						],
						'typographies' => [
							Helper::build_typography('heading_1', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '60px'], 'textLineHeight' => ['desktop' => '80px']], __('Heading 1'), 'h1'),
							Helper::build_typography('heading_2', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '48px'], 'textLineHeight' => ['desktop' => '60px']], __('Heading 2'), 'h2'),
							Helper::build_typography('heading_3', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '36px'], 'textLineHeight' => ['desktop' => '48px']], __('Heading 3'), 'h3'),
							Helper::build_typography('heading_4', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '32px'], 'textLineHeight' => ['desktop' => '36px']], __('Heading 4'), 'h4'),
							Helper::build_typography('heading_5', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '18px'], 'textLineHeight' => ['desktop' => '28px']], __('Heading 5'), 'h5'),
							Helper::build_typography('heading_6', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '16px'], 'textLineHeight' => ['desktop' => '24px']], __('Heading 6'), 'h6'),
							Helper::build_typography('paragraph', ['textFont' =>  ['weight' => '400'], 'textSize' =>  ['desktop' => '16px'], 'textLineHeight' => ['desktop' => '24px']], __('Paragraph'), 'p'),
							Helper::build_typography('button', ['textFont' =>  ['weight' => '700'], 'textSize' =>  ['desktop' => '16px'], 'textLineHeight' => ['desktop' => '20px']], __('Button'), 'button'),
						]
					]
				]
			];
		}

		public function get_saved_global_settings()
		{
			return (array) get_option('master_blocks_global_settings', []);
		}

		/**
		 * Enqueue frontend and editor JavaScript and CSS assets.
		 */
		public function jltmb_enqueue_frontend_and_editor_assets()
		{
			// all blocks style css
			wp_enqueue_style('jltmb-style', JLTMB_URL . 'assets/public/css/jltmb-style.css');
		}

		/**
		 * Admin Scripts
		 *
		 * @return void
		 */
		public function jltmb_admin_scripts(){
			// CSS Files .
			wp_enqueue_style('master-blocks-admin', JLTMB_ASSETS . 'admin/css/master-blocks-admin.css', array('dashicons'), JLTMB_VER, 'all');

			// Script
			wp_enqueue_script( 'master-blocks-admin', JLTMB_ASSETS . 'admin/js/master-blocks-admin.js', array( 'jquery' ), JLTMB_VER, true );
			wp_localize_script(
				'master-blocks-admin',
				'JLTMBCORE',
				array(
					'admin_ajax'        => admin_url( 'admin-ajax.php' ),
					'recommended_nonce' => wp_create_nonce( 'jltmb_recommended_nonce' ),
				)
			);

		}

		/**
		 * Enqueue Scripts
		 *
		 * @method wp_enqueue_scripts()
		 */
		public function jltmb_enqueue_scripts() {

			// Blocks CSS
			wp_enqueue_style('jltmb-editor', JLTMB_ASSETS . 'admin/css/jltmb-editor.css');

			// CSS Files .
			wp_enqueue_style( 'master-blocks-frontend', JLTMB_ASSETS . 'public/css/master-blocks-frontend.css', JLTMB_VER, 'all' );

			// JS Files .
			wp_enqueue_script( 'master-blocks-frontend', JLTMB_ASSETS . 'public/js/master-blocks-frontend.js', array( 'jquery' ), JLTMB_VER, true );
		}


		public function is_activated($val)
		{
			$active_list = $this->jltmb_get_activated_blocks_list();
			if (in_array($val, $active_list)) {
				return true;
			}
			return false;
		}

		public function jltmb_get_activated_blocks_list()
		{
			$blocks_settings  = get_option('jltmb_blocks_settings');
			$activated_blocks = [];
			if (!empty($blocks_settings) && is_array($blocks_settings)) {
				foreach ($blocks_settings as $key => $value) {
					if (0 != $value) {
						$activated_blocks[] = $key;
					}
				}
			}
			return $activated_blocks;
		}

        public function jltmb_importer(){
            echo '<div id="masterBlocksImporter"></div>';
        }

		/**
		 * Get Deactivated blocks list
		 *
		 * @return void
		 */
		public function jltmb_get_deactivated_blocks_list()
		{
			$blocks_settings  = !empty(get_option('jltmb_blocks_settings')) ? get_option('jltmb_blocks_settings')['blocks'] : '';

			$activated_blocks = [];
			if (!empty($blocks_settings) && is_array($blocks_settings)) {
				foreach ($blocks_settings as $key => $value) {
					if (1 != $value) {
						$activated_blocks[] = $key;
					}
				}
			}
			return $activated_blocks;
		}


	}
}