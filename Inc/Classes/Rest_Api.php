<?php
namespace JLTMB\Inc\Classes;

class Rest_Api
{

    private $namespace = 'jltmb-rest-api/';
    private $version   = 'v1';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'add_endpoints']);
    }

    /**
     * Generate API Namespace
     */
    public function api_namespace()
    {
        return $this->namespace . $this->version;
    }

    /**
     * Register Routes
     */
    public function add_endpoints()
    {
        register_rest_route(
            $this->api_namespace(),
            '/save-jltmb-blocks-items/',
            [
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'save_jltmb_blocks_items'],
                'permission_callback' => '__return_true', // [$this, 'check_permission']
            ]
        );
        register_rest_route(
            $this->api_namespace(),
            '/get-jltmb-blocks-items/',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_jltmb_blocks_items'],
                'permission_callback' => '__return_true', // [$this,'check_permission']
            ]
        );
    }

    public function process_data($item)
    {
        return ($item != 1) ? 0 : 1;
    }

    /**
     * Get admin bar items
     */
    public function save_jltmb_blocks_items($request)
    {
        $blocks      = $request->get_param('blocks');
        $whitelabels = $request->get_param('whitelabels');
        // $blocks = array_map([$this,'process_data'],$blocks);
        update_option(
            'jltmb_blocks_settings',
            [
                'blocks'      => $blocks,
                'whitelabels' => $whitelabels,
            ]
        );
        // return 'success';
        $result['success'] = true;
        wp_send_json(json_encode($result));
    }

    public function get_jltmb_blocks_items($request)
    {
        $opt = get_option('jltmb_blocks_settings', null);
        wp_send_json($opt);
    }

    /**
     * Make sure that user has administrative permission
     */
    public function check_permission()
    {
        return current_user_can('manage_options');
    }

    /**
     * Returns the full rest url of a given endpoint.
     */
    public static function get_rest_url($endpoint)
    {
        $instance = new self();
        return \rest_url($instance->api_namespace() . $endpoint);
    }
}