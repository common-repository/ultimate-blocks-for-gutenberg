<?php
namespace JLTMB\Inc;

class Api
{
    private static $store_url;
    private static $templates_url;

    public function __construct()
    {
        self::$store_url = jltmb_get_api_url() . '/wp-json/master_blocks_template/v1/';
        self::$templates_url = jltmb_get_api_url() . '/master-blocks-templates.json';
        add_action('rest_api_init', [$this, 'register_api_hook']);
    }

    public function register_api_hook()
    {

        register_rest_route('master_blocks/v1', '/get_templates/', [[
            'methods'             => 'GET',
            'callback'            => [$this, 'get_templates'],
            'args'                => [],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]]);

        register_rest_route('master_blocks/v1', '/get_template/', [[
            'methods'             => 'POST',
            'callback'            => [$this, 'get_template_info'],
            'args'                => $this->get_template_args(),
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]]);

        register_rest_route('master_blocks/v1', '/save_template/', [[
            'methods'             => 'POST',
            'callback'            => [$this, 'save_template'],
            'args'                => $this->get_template_args(),
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]]);

        register_rest_route('master_blocks/v1', '/save_global_settings/', [[
            'methods'             => 'POST',
            'callback'            => [$this, 'save_global_settings'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]]);
    }

    public function get_template_args()
    {

        return [
            'post_id' => [
                'required' => true,
                'sanitize_callback' => 'absint'
            ],
            'kit_id' => [
                'required' => false,
                'sanitize_callback' => 'absint'
            ]
        ];
    }

    public function get_license_data()
    {
        return base64_encode( json_encode( jltmb_license_client()->get_license_params() ) );
    }

    public function get_template_info($request)
    {

        $params = $request->get_params();

        try {

            if (!empty($params['post_id'])) {

                $post_id = (int) $params['post_id'];
                $withSettings = !empty($params['withSettings']) ? wp_validate_boolean($params['withSettings']) : false;

                $template = $this->get_template($post_id);

                if (!empty($template)) {

                    $url = self::$store_url . 'get_block_data';
                    $url = add_query_arg('post_id', $post_id, $url);

                    if (wp_validate_boolean($template['pro'])) {
                        $url = add_query_arg('license', $this->get_license_data(), $url);
                    }

                    if ($withSettings) {
                        $url = add_query_arg('withSettings', $withSettings, $url);
                    }

                    $t_request = wp_remote_get($url);
                    $response = wp_remote_retrieve_body($t_request);

                    if (is_wp_error($response)) return ['success' => false];

                    return json_decode($response, true);
                }

                return [
                    'success' => false,
                    'message' => __('Something is wrong, Block not found', 'jltegb'),
                ];
            }
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function save_template($request)
    {

        $params = $request->get_params();

        if (!empty($params['post_id'])) {

            $saved_templates = (array) get_option('master_blocks_saved_templates', []);

            $status = null;

            if (in_array($params['post_id'], $saved_templates)) {
                // Remove if found
                $saved_templates = array_diff($saved_templates, [$params['post_id']]);
                $status = false;
            } else {
                // Add if not found
                array_push($saved_templates, $params['post_id']);
                $status = true;
            }

            update_option('master_blocks_saved_templates', $saved_templates);

            return [
                'success' => true,
                'data' => [
                    'saved_status' => $status
                ]
            ];
        }

        return [
            'success' => false,
            'message' => __('Something is wrong, Template ID not found', 'jltegb'),
        ];
    }

    public function get_templates()
    {

        $templates_data = get_transient('master_blocks_templates');

        if (empty($templates_data)) {
            $request = wp_remote_get(self::$templates_url, ['sslverify' => false]);

            if (is_wp_error($request)) {
                return ['success' => false, 'data' => __('Something is wrong, please try agina after some time.', 'jltegb')];
            }

            $templates_data = json_decode(wp_remote_retrieve_body($request), true);
            set_transient('master_blocks_templates', $templates_data, MINUTE_IN_SECONDS);
        }

        $templates_data['pro_status'] = true;

        $saved_templates = (array) get_option('master_blocks_saved_templates', []);

        $templates_data['templates'] = array_map(function ($template) use ($saved_templates) {
            $template['saved'] = in_array($template['id'], $saved_templates);
            return $template;
        }, $templates_data['templates']);

        return ['success' => true, 'data' => $templates_data];
    }

    public function get_template($template_id)
    {
        $templates_data = $this->get_templates();

        if (empty($templates_data['data']) || empty($templates = $templates_data['data']['templates'])) return null;

        $template = wp_list_filter($templates, ['id' => $template_id]);

        if (empty($template)) return null;

        return end($template);
    }

    public function save_global_settings($request)
    {

        $settings = $request->get_params();

        if (!empty($settings)) {
            update_option('master_blocks_global_settings', $settings);
        }

        return [
            'success' => true,
            'data' => $settings
        ];
    }
}