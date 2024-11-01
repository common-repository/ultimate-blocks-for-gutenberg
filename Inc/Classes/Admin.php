<?php

namespace JLTMB\Inc\Classes;

use JLTMB\Inc\Api;
use JLTMB\Libs\Featured;
use JLTMB\Inc\Classes\Feedback;
use JLTMB\Inc\Classes\Rest_Api;
use JLTMB\Inc\Classes\Row_Links;
use JLTMB\Inc\Classes\Pro_Upgrade;
use JLTMB\Inc\Classes\Upgrade_Plugin;
use JLTMB\Inc\Classes\EditorStyleFix;
use JLTMB\Inc\Classes\StyleGenerator;
use JLTMB\Inc\Classes\Recommended_Plugins;
use JLTMB\Inc\Classes\AdminSettings;
use JLTMB\Inc\Classes\Notifications\Notifications;


// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
    exit;
}

/*
 * @version 1.4.1
 * @package ultimate-blocks-for-gutenberg
 */
if (!class_exists('Admin')) {
    /**
     * Admin Settings Class
     */
    class Admin
    {

        /**
         * Admin Construct method
         */
        public function __construct()
        {
            $this->includes();

            // This should run earlier .
			// add_action( 'plugins_loaded', [ $this, 'jltmb_maybe_run_upgrades' ], -100 ); .
        }

        /**
         * Includes Admin Classes
         */
        public function includes(){
            new Api();
            new Rest_Api();
            new AdminSettings();
            new EditorStyleFix();
            new StyleGenerator();
            new Recommended_Plugins();
            new Row_Links();
            new Pro_Upgrade();
            new Notifications();
            new Featured();
            new Feedback();
        }


        /**
         * Run Upgrader Class
         *
         * @return void
         */
        public function jltmb_maybe_run_upgrades()
        {
            if (!is_admin() && !current_user_can('manage_options')) {
                return;
            }

            // Run Upgrader .
            $upgrade = new Upgrade_Plugin();

            // Need to work on Upgrade Class .
            if ($upgrade->if_updates_available()) {
                $upgrade->run_updates();
            }
        }


        /**
         * Register Main Menu.
         *
         * @return void
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function settings_menu()
        {
            add_menu_page(
                __('Master Blocks Gutenberg', 'ultimate-blocks-for-gutenberg'),
                __('Master Blocks', 'ultimate-blocks-for-gutenberg'),
                'manage_options',
                'jlt-master-blocks' . '-settings',
                array($this, 'settings_page'),
                'dashicons-admin-generic',
                40
            );

            add_submenu_page(
                'jlt-master-blocks' . '-settings',
                __('Master Blocks Settings', 'ultimate-blocks-for-gutenberg'),
                __('Settings', 'ultimate-blocks-for-gutenberg'),
                'manage_options',
                'jlt-master-blocks' . '-settings',
                array($this, 'settings_page'),
                10
            );
        }

        /**
         * Returns all the settings fields
         *
         * @return void
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function settings_fields()
        {
            $sections = array(
                array(
                    'id'    => 'jltmb_general',
                    'title' => sprintf(
                        __('%s <span> General Settings</span>', 'ultimate-blocks-for-gutenberg'),
                        '<i class="dashicons dashicons-admin-tools" ></i>'
                    ),
                ),

                array(
                    'id'    => 'jltmb_advanced',
                    'title' => sprintf(
                        __('%s <span>Advanced Settings</span>', 'ultimate-blocks-for-gutenberg'),
                        '<i class="dashicons dashicons-admin-generic" ></i>'
                    ),
                ),
                array(
                    'id'    => 'jltmb_custom_css',
                    'title' => sprintf(
                        __('%s <span>Custom CSS</span>', 'ultimate-blocks-for-gutenberg'),
                        '<i class="dashicons dashicons-editor-code" ></i>'
                    ),
                ),
            );

            $settings_fields = array(
                'jltmb_general'    => array(
                    array(
                        'name'              => 'text_val',
                        'label'             => __('Text Input', 'ultimate-blocks-for-gutenberg'),
                        'desc'              => __('Text input description', 'ultimate-blocks-for-gutenberg'),
                        'placeholder'       => __('Text Input placeholder', 'ultimate-blocks-for-gutenberg'),
                        'type'              => 'text',
                        'default'           => 'Title',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    array(
                        'name'              => 'number_input',
                        'label'             => __('Number Input', 'ultimate-blocks-for-gutenberg'),
                        'desc'              => __('Number field with validation callback `floatval`', 'ultimate-blocks-for-gutenberg'),
                        'placeholder'       => __('1.99', 'ultimate-blocks-for-gutenberg'),
                        'min'               => 0,
                        'max'               => 100,
                        'step'              => '0.01',
                        'type'              => 'number',
                        'default'           => 'Title',
                        'sanitize_callback' => 'floatval',
                    ),
                    array(
                        'name'        => 'textarea',
                        'label'       => __('Textarea Input', 'ultimate-blocks-for-gutenberg'),
                        'desc'        => __('Textarea description', 'ultimate-blocks-for-gutenberg'),
                        'placeholder' => __('Textarea placeholder', 'ultimate-blocks-for-gutenberg'),
                        'type'        => 'textarea',
                    ),
                    array(
                        'name' => 'html',
                        'desc' => __('HTML area description. You can use any <strong>bold</strong> or other HTML elements.', 'ultimate-blocks-for-gutenberg'),
                        'type' => 'html',
                    ),
                    array(
                        'name'  => 'checkbox',
                        'label' => __('Checkbox', 'ultimate-blocks-for-gutenberg'),
                        'desc'  => __('Checkbox Label', 'ultimate-blocks-for-gutenberg'),
                        'type'  => 'checkbox',
                    ),
                    array(
                        'name'    => 'radio',
                        'label'   => __('Radio Button', 'ultimate-blocks-for-gutenberg'),
                        'desc'    => __('A radio button', 'ultimate-blocks-for-gutenberg'),
                        'type'    => 'radio',
                        'options' => array(
                            'yes' => 'Yes',
                            'no'  => 'No',
                        ),
                    ),
                    array(
                        'name'    => 'selectbox',
                        'label'   => __('A Dropdown', 'ultimate-blocks-for-gutenberg'),
                        'desc'    => __('Dropdown description', 'ultimate-blocks-for-gutenberg'),
                        'type'    => 'select',
                        'default' => 'no',
                        'options' => array(
                            'yes' => 'Yes',
                            'no'  => 'No',
                        ),
                    ),
                    array(
                        'name'    => 'password',
                        'label'   => __('Password', 'ultimate-blocks-for-gutenberg'),
                        'desc'    => __('Password description', 'ultimate-blocks-for-gutenberg'),
                        'type'    => 'password',
                        'default' => '',
                    ),
                    array(
                        'name'    => 'file',
                        'label'   => __('File', 'ultimate-blocks-for-gutenberg'),
                        'desc'    => __('File description', 'ultimate-blocks-for-gutenberg'),
                        'type'    => 'file',
                        'default' => '',
                        'options' => array(
                            'button_label' => 'Choose Image',
                        ),
                    ),
                ),
                'jltmb_advanced'   => array(
                    array(
                        'name'    => 'color',
                        'label'   => __('Color', 'ultimate-blocks-for-gutenberg'),
                        'desc'    => __('Color description', 'ultimate-blocks-for-gutenberg'),
                        'type'    => 'color',
                        'default' => '',
                    ),
                    array(
                        'name'    => 'password',
                        'label'   => __('Password', 'ultimate-blocks-for-gutenberg'),
                        'desc'    => __('Password description', 'ultimate-blocks-for-gutenberg'),
                        'type'    => 'password',
                        'default' => '',
                    ),
                    array(
                        'name'    => 'wysiwyg',
                        'label'   => __('Advanced Editor', 'ultimate-blocks-for-gutenberg'),
                        'desc'    => __('WP_Editor description', 'ultimate-blocks-for-gutenberg'),
                        'type'    => 'wysiwyg',
                        'default' => '',
                    ),
                    array(
                        'name'    => 'multicheck',
                        'label'   => __('Multile checkbox', 'ultimate-blocks-for-gutenberg'),
                        'desc'    => __('Multi checkbox description', 'ultimate-blocks-for-gutenberg'),
                        'type'    => 'multicheck',
                        'default' => array(
                            'one'  => 'one',
                            'four' => 'four',
                        ),
                        'options' => array(
                            'one'   => 'One',
                            'two'   => 'Two',
                            'three' => 'Three',
                            'four'  => 'Four',
                        ),
                    ),
                ),
                'jltmb_custom_css' => apply_filters(
                    'jltmb_admin_custom_css',
                    array(
                        array(
                            'name'  => 'custom_css',
                            'label' => __('Dark Mode Custom CSS', 'ultimate-blocks-for-gutenberg'),
                            'type'  => 'textarea',
                            'desc'  => 'Add custom css for dark mode only. This CSS will only apply when the dark mode is on. use <b>!important</b> flag on each property.',
                        ),
                    )
                ),
            );

            self::$settings_api = new Settings_API();

            /*
			 * set the settings.
			 */
            self::$settings_api->set_sections($sections);
            self::$settings_api->set_fields($settings_fields);

            /*
			 * initialize settings
			 */
            self::$settings_api->admin_init();
        }

        /**
         * Settings page
         *
         * @return void
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function settings_page()
        {                            ?>

            <div class="wrap jltmb-settings-page">
                <h2 style="display: flex;"><?php esc_html_e('Master Blocks Settings', 'ultimate-blocks-for-gutenberg'); ?>
                    <span id="changelog_badge"></span>
                </h2>
                <?php self::$settings_api->show_settings(); ?>
            </div>
<?php
        }

        /**
         * Get all the pages
         *
         * @return array page names with key value pairs
         */
        public function get_pages()
        {
            $pages         = get_pages();
            $pages_options = array();

            if ($pages) {
                foreach ($pages as $page) {
                    $pages_options[$page->ID] = $page->post_title;
                }
            }

            return $pages_options;
        }
    }
}