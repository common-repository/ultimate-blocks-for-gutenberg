<?php

namespace JLTMB\Inc\Classes;

if (!class_exists('StyleGenerator')) {
    class StyleGenerator
    {

        public function __construct()
        {
            add_action('wp_enqueue_scripts', [ $this, 'enqueue_frontend_styles'] );
            add_action('save_post', [ $this, 'get_post_content'], 10, 3 );
        }

        /**
         * Enqueue frontend css for post/page if exists
         */
        public function enqueue_frontend_styles()
        {
            global $post;

            if (!empty($post) && !empty($post->ID)) {
                $upload_dir = wp_upload_dir();

                // Page/Post Style Enqueue
                if (file_exists($upload_dir['basedir'] . '/master_blocks/master-blocks-' . abs($post->ID) . '.min.css')) {
                    $file_path = $upload_dir['baseurl'] . '/master_blocks/master-blocks-' . $post->ID . '.min.css';
                    wp_enqueue_style( 'master-block-style-' . $post->ID, $file_path, [], md5_file($file_path) );
                }
            }
        }

        /**
         * Get post content when page is saved
         */
        public function get_post_content($post_id, $post, $update)
        {
            // Need to get options for selected Post Types
            $post_type          = get_post_type($post_id);
            $allowed_post_types = [
                'page',
                'post',
            ];

            // If This page is draft, do nothing
            if (isset($post->post_status) && 'auto-draft' == $post->post_status) {
                return;
            }

            // Autosave, do nothing
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // If it's a post revision, do nothing
            if (false !== wp_is_post_revision($post_id)) {
                return;
            }

            $parsed_content = parse_blocks($post->post_content);

            if (is_array($parsed_content) && !empty($parsed_content)) {
                $mb_blocks       = [];
                $parsed_response = self::parsed_blocks_styles($parsed_content, $mb_blocks);
                $style           = self::blocks_to_style_array($parsed_response);
                // Write CSS file for this page
                self::write_block_styles($style, $post);
            }
        }

        /**
         * Function for parsing blocks
         */
        public static function parsed_blocks_styles($block, &$mb_blocks)
        {
            if (count($block) > 0) {
                foreach ($block as $item) {
                    $attributes = $item['attrs'];

                    $blockId = '';
                    if (isset($attributes['blockId']) && !empty($attributes['blockId'])) {
                        $blockId = $attributes['blockId'];
                    }
                    $blockMetaStyle = '';
                    if (isset($attributes['blockMetaStyle']) && !empty($attributes['blockMetaStyle'])) {
                        $blockMetaStyle = $attributes['blockMetaStyle'];
                    }

                    if (isset($item['innerBlocks']) && count($item['innerBlocks']) > 0) {
                        self::parsed_blocks_styles($item['innerBlocks'], $mb_blocks);
                        if (isset($attributes['blockMetaStyle']) && !empty($attributes['blockMetaStyle'])) {
                            $mb_blocks[$blockId] = [
                                'blockMetaStyle' => $blockMetaStyle,
                            ];
                        }
                    } elseif (isset($attributes['blockMetaStyle']) && !empty($attributes['blockMetaStyle'])) {
                        $mb_blocks[$blockId] = [
                            'blockMetaStyle' => $blockMetaStyle,
                        ];
                    }
                }
            }

            return $mb_blocks;
        }

        /**
         * Function, Blocks array to Style Array
         */
        public static function blocks_to_style_array($blocks)
        {
            $style_array = [];
            if (is_array($blocks) && count($blocks) > 0) {
                foreach ($blocks as $blockId => $block) {
                    $style_array[$blockId] = [
                        'desktop' => '',
                        'tablet'  => '',
                        'mobile'  => '',
                    ];

                    if (is_array($block) && count($block) > 0) {
                        foreach ($block as $value) {
                            if (is_array($value) && count($value) > 0) {
                                if (isset($value['desktop'])) {
                                    $style_array[$blockId]['desktop'] .= $value['desktop'];
                                }
                                if (isset($value['tablet'])) {
                                    $style_array[$blockId]['tablet'] .= $value['tablet'];
                                }
                                if (isset($value['mobile'])) {
                                    $style_array[$blockId]['mobile'] .= $value['mobile'];
                                }
                            }
                        }
                    }
                }
            }
            return $style_array;
        }

        /**
         * write css in upload directory
         */
        private static function write_block_styles($block_styles, $post)
        {

            // Write CSS for Page/Posts
            if (!empty($css = self::build_css($block_styles))) {
                $upload_dir = wp_upload_dir()['basedir'] . '/master_blocks/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir);
                }
                file_put_contents($upload_dir . 'master-blocks-' . abs($post->ID) . '.min.css', $css);
            }
        }

        /**
         * Enqueue frontend css for post if exists
         */
        public static function build_css($style_object)
        {
            $block_styles = $style_object;

            $css = '';
            foreach ($block_styles as $block_style_key => $block_style) {
                if (!empty($block_css = (array) $block_style)) {
                    $css .= sprintf(
                        '/* %1$s Starts */',
                        $block_style_key
                    );
                    foreach ($block_css as $media => $style) {
                        switch ($media) {
                            case 'desktop':
                                $css .= preg_replace('/\s+/', ' ', $style);
                                break;
                            case 'tablet':
                                $css .= ' @media(max-width: 1024px){';
                                $css .= preg_replace('/\s+/', ' ', $style);
                                $css .= '}';
                                break;
                            case 'mobile':
                                $css .= ' @media(max-width: 767px){';
                                $css .= preg_replace('/\s+/', ' ', $style);
                                $css .= '}';
                                break;
                        }
                    }
                    $css .= sprintf(
                        '/* =%1$s= Ends */',
                        $block_style_key
                    );
                }
            }
            return trim($css);
        }
    }
}