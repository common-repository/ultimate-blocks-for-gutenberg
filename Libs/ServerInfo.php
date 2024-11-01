<?php

namespace JLTMB\Libs;
use JLTMB\Libs\Helper;

// No, Direct access Sir !!!
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ServerInfo')) {
    /**
     * Server Info Class
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class ServerInfo
    {
        public $server_info = [];
        /**
         * Constructor
         */
        public function __construct()
        {
            $this->server_info = $this->server_infos();
        }

        public function server_infos(){
            return [
                'wordpress' => [
                    'home'      => get_option('home'),
                    'siteurl'   => get_option('siteurl'),
                    'wp_version'   => $this->get_wp_version(),
                    'multisite'   => $this->get_multisite(),
                    'wp_memory_limit'   => $this->get_wp_memory_limit(),
                    'wp_path'   => $this->get_wp_path(),
                    'uploads_writable'   => $this->get_uploads_writable(),
                    'wp_debug'   => $this->get_wp_debug(),
                    'lang'   => $this->get_wp_language(),
                ],
                'server'    => [
                    'server'   => $this->get_server(),
                    'phpversion'   => $this->get_phpversion(),
                    'php_memory_limit'   => $this->get_php_memory_limit(),
                    'php_post_max_size'   => $this->get_php_post_max_size(),
                    'php_time_limit'   => $this->get_php_time_limit(),
                    'php_max_input_vars'   => $this->get_php_max_input_vars(),
                    'mysql_version'   => $this->get_mysql_version(),
                    'max_upload_size'   => $this->get_max_upload_size(),
                ],
                'php'   =>[
                    'php_cURL'   => $this->get_php_cURL(),
                    'php_fsockopen'   => $this->get_php_fsockopen(),
                    'php_SoapClient'   => $this->get_php_SoapClient(),
                    'php_Suhosin'   => $this->get_php_Suhosin(),
                ]
            ];
        }

        /**
         * Get PHP Suhosin
         */
        public function get_php_Suhosin(){
            $get_Suhosin = '';
            if (!function_exists('Suhosin')) {
                $get_Suhosin = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . __('Not Installed', 'ultimate-blocks-for-gutenberg') . '</span>'));
            } else {
                $get_Suhosin = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . __('Supported', 'ultimate-blocks-for-gutenberg') . '</span>'));
            }
            return $get_Suhosin;
        }

        /**
         * Get PHP SoapClient
         */
        public function get_php_SoapClient(){
            $get_SoapClient = '';
            if (!function_exists('SoapClient')) {
                $get_SoapClient = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . __('Not Installed', 'ultimate-blocks-for-gutenberg') . '</span>'));
            } else {
                $get_SoapClient = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . __('Supported', 'ultimate-blocks-for-gutenberg') . '</span>'));
            }
            return $get_SoapClient;
        }


        /**
         * Get PHP fsockopen
         */
        public function get_php_fsockopen(){
            $get_fsockopen = '';
            if (!function_exists('fsockopen')) {
                $get_fsockopen = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . __('Not Installed', 'ultimate-blocks-for-gutenberg') . '</span>'));
            } else {
                $get_fsockopen = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . __('Supported', 'ultimate-blocks-for-gutenberg') . '</span>'));
            }
            return $get_fsockopen;
        }


        /**
         * Get PHP cURL info
         */
        public function get_php_cURL(){
            $get_jltmb_curl = '';
            if (!function_exists('curl_init')) {
                $get_jltmb_curl = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . __('Not Installed', 'ultimate-blocks-for-gutenberg') . '</span>'));
            } else {
                $get_jltmb_curl = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . __('Supported', 'ultimate-blocks-for-gutenberg') . '</span>'));
            }

            return $get_jltmb_curl;

        }

        /**
         * Get Max Upload Size
         */
        public function get_max_upload_size(){
            $jltmb_max_upload_size = (int) size_format(wp_max_upload_size());
            $get_jltmb_max_upload_size = '';
            if ($jltmb_max_upload_size < 20) {
                $get_jltmb_max_upload_size = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_invalid(), wp_kses_post('<span>' . $jltmb_max_upload_size . '(Min: 20 Recommended)</span>'));
            } else {
                $get_jltmb_max_upload_size = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . $jltmb_max_upload_size . '</span>'));
            }
            return $get_jltmb_max_upload_size;
        }


        /**
         * Get MySQL Version
         */
        public function get_mysql_version(){
            global $wpdb;
            $jltmb_mysql_version =  (float) $wpdb->db_version();
            $get_mysql_version = '';
            if ($jltmb_mysql_version < 5.3) {
                $get_mysql_version = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_invalid(), wp_kses_post('<span>' . $jltmb_mysql_version . '(Min: 5.3 Recommended)</span>'));
            } else {
                $get_mysql_version = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . $jltmb_mysql_version . '</span>'));
            }
            return $get_mysql_version;
        }

        /**
         * PHP Max Input Vars
         *
         * @return void
         */
        public function get_php_max_input_vars(){
            if (function_exists('ini_get')) {
                $jltmb_max_input_vars = (int) ini_get('max_input_vars');
                $get_jltmb_max_input_vars = '';
                if ($jltmb_max_input_vars < 1000) {
                    $get_jltmb_max_input_vars = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_invalid(), wp_kses_post('<span>' . $jltmb_max_input_vars . ' (Min: 1000 Recommended)</span>'));
                } else {
                    $get_jltmb_max_input_vars = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . $jltmb_max_input_vars . '</span>'));
                }
                return $get_jltmb_max_input_vars;
            }
        }

        /**
         * PHP Post Time Limit
         */
        public function get_php_time_limit(){
            if (function_exists('ini_get')){
                $jltmb_time_limit = (int) ini_get('max_execution_time');
                $get_jltmb_time_limit = '';
                if ($jltmb_time_limit < 120 && $jltmb_time_limit != 0) {
                    $get_jltmb_time_limit = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_invalid(), wp_kses_post(
                        /* translators: %s: Time Limit, 2: Link */
                        sprintf(__('<span> %s - (Min: Recommended 300).</span><a href="%2$s" target="_blank">Increasing WP Time Limit</a>', 'ultimate-blocks-for-gutenberg'), $jltmb_time_limit, 'https://ultimate-blocks-for-gutenberg.com/elementor-editor-not-loading-issue/')
                    ));
                } else {
                    $get_jltmb_time_limit = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . $jltmb_time_limit . '</span>'));
                }
                return $get_jltmb_time_limit;
            }
        }

        /**
         * PHP Post Max Size
         */
        public function get_php_post_max_size(){
            if (function_exists('ini_get')){
                $jltmb_post_max_size = (int) ini_get('post_max_size');
                $get_jltmb_post_max_size = '';
                if ($jltmb_post_max_size < 32) {
                    $get_jltmb_post_max_size = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_invalid(), wp_kses_post('<span>' . $jltmb_post_max_size . ' (Min: 32M Recommended)</span>'));
                } else {
                    $get_jltmb_post_max_size = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . $jltmb_post_max_size . '</span>'));
                }
                return $get_jltmb_post_max_size;
            }
        }

        /**
         * PHP Memory Limit
         */
        public function get_php_memory_limit(){

            if (function_exists('ini_get')){
                $jltmb_php_memory_limit = (int) ini_get('memory_limit');
                $get_jltmb_php_memory_limit = '';
                if ($jltmb_php_memory_limit < 256) {
                    $get_jltmb_php_memory_limit = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_invalid(), wp_kses_post('<span>' . $jltmb_php_memory_limit . ' (Min: 256M Recommended)</span>'));
                } else {
                    $get_jltmb_php_memory_limit = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . $jltmb_php_memory_limit . '</span>'));
                }
                return $get_jltmb_php_memory_limit;
            }
        }

        /**
         * Get PHP Version
         */
        public function get_phpversion(){
            $phpversion = '';
            // Check if phpversion function exists
            if (function_exists('phpversion')) {
                $php_version = esc_html(phpversion());
                if (version_compare($php_version, '5.6.3', '<')) {
                    $phpversion = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_invalid(), wp_kses_post('<span>' . $php_version . '(Min: 5.6.3 Recommended)</span>'));
                } else {
                    $phpversion = sprintf(__('%1$s %2$s', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_valid(), wp_kses_post('<span">' . $php_version . '</span>'));
                }
            }
            return $phpversion;
        }

        /**
         * Get Server Software
         */
        public function get_server(){
            return esc_html($_SERVER['SERVER_SOFTWARE']);
        }

        /**
         * Get WP Language
         */
        public function get_wp_language(){
            return get_locale();
        }

        /**
         * Get WP Debug
         */
        public function get_wp_debug(){
            $wpdebug = '';
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $wpdebug = Helper::jltmb_valid() . 'Enabled';
            } else {
                $wpdebug = Helper::jltmb_invalid() . 'Disabled';
            }
            return $wpdebug;
        }

        /**
         * Get Uploads Directory Writable
         *
         * @return void
         */
        public function get_uploads_writable(){
            $jltmb_uploads            = wp_upload_dir();
            $jltmb_upload_path        = $jltmb_uploads['basedir'];
            $uploads_writable = '';
            if (is_writable($jltmb_upload_path)) {
                $uploads_writable = Helper::jltmb_valid() . 'Writable';
            } else {
                $uploads_writable = Helper::jltmb_invalid() . 'Not Writable';
            }
            return $uploads_writable;
        }


        /**
         * Get WP Path
         */
        public function get_wp_path(){
            return ABSPATH;
        }

        /**
         * Get WP Memory Limit
         */
        public function get_wp_memory_limit(){
            $wp_memory_limit = '';
            $jltmb_memory_limit       = (int) ini_get('memory_limit');
            if ($jltmb_memory_limit < 256) {
                $wp_memory_limit = wp_kses_post(
                    /* translators: %s: Memory Limit, 2: Link */
                    sprintf(__('%1$s <span>%2$s - (Min: 256M Recommended).</span> <a href="%3$s" target="_blank">Increasing WP Memory Limit</a>', 'ultimate-blocks-for-gutenberg'), Helper::jltmb_invalid(), $jltmb_memory_limit, 'https://ultimate-blocks-for-gutenberg.com/elementor-editor-not-loading-issue/')
                );
            } else {
                $wp_memory_limit = Helper::jltmb_valid() . wp_kses_post('<span>' . $jltmb_memory_limit . '</span>');
            }
            return $wp_memory_limit;
        }

        /**
         * Check if multisite
         */
        public function get_multisite(){
            $is_multisite = '';
            if (is_multisite()) {
                $is_multisite = Helper::jltmb_valid() . 'Enabled';
            } else {
                $is_multisite = Helper::jltmb_invalid() . 'Disabled';
            }
            return $is_multisite;
        }



        /**
         * Get WP Version
         *
         * @return void
         */
        public function get_wp_version(){
            global $wp_version;
            $wpversion = '';
            if (version_compare($wp_version, '4.0') >= 0) {
                $wpversion = Helper::jltmb_valid() . '<span>' . get_bloginfo('version') . '</span>';
            } else {
                $wpversion = '<span>' . get_bloginfo('version') . ' (Min: 4.0 Recommended)</span>';
            }
            return $wpversion;
        }


        /**
         * Get All Active Plugins data
         *
         * @return void
         */
        public static function get_active_plugins()
        {

            $active_plugins = (array) get_option('active_plugins', array());

            if (is_multisite()) {
                $network_activated_plugins = array_keys(get_site_option('active_sitewide_plugins', array()));
                $active_plugins            = array_merge($active_plugins, $network_activated_plugins);
            }

            $all_plugins_info = [];
            foreach ($active_plugins as $key => $plugin) {
                $plugin_data    = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                $dirname        = dirname($plugin);
                $version_string = '';
                $network_string = '';

                if (!empty($plugin_data['Name'])) {

                    // link the plugin name to the plugin url if available
                    $plugin_name = esc_html($plugin_data['Name']);

                    if ('Master Blocks – Gutenberg Blocks Plugin' === $plugin_name) {
                        $plugin_name = JLTMB;
                        $author = JLTMB_AUTHOR;
                        if ('Jewel Theme' !== $author) {
                            $plugin_data['Author'] = JLTMB_AUTHOR;
                        }
                    } elseif ('Master Blocks – Gutenberg Blocks Plugin Pro' === $plugin_name) {
                        $plugin_name = JLTMB;
                        $author = JLTMB_AUTHOR;
                        if ('Jewel Theme' !== $author) {
                            $plugin_data['Author'] = JLTMB_AUTHOR;
                        }
                    }

                    // if (!empty($plugin_data['PluginURI'])) {
                    $all_plugins_info[$key]['PluginName'] = esc_html($plugin_name);
                    $all_plugins_info[$key]['PluginURI'] = esc_url($plugin_data['PluginURI']);
                    // }
                    /* translators: %s: Author Name */
                    $all_plugins_info[$key]['AuthorName'] = esc_html($plugin_data['AuthorName']);
                    $all_plugins_info[$key]['AuthorURI'] = esc_html($plugin_data['AuthorURI']);
                    $all_plugins_info[$key]['Version'] = esc_html($plugin_data['Version']);
                }
            }

            return $all_plugins_info;
        }
    }
}