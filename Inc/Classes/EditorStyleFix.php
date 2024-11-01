<?php

namespace JLTMB\Inc\Classes;

if (!class_exists('EditorStyleFix')) {
    class EditorStyleFix
    {

        private static $instance;
        /* Construction Function */
        public function __construct()
        {
            add_action('enqueue_block_editor_assets', [$this, 'jltmb_fix_gutenberg_style']);
        }

        public function jltmb_fix_gutenberg_style()
        {
            $jltmb_editor_fix_css  = '';
            $jltmb_editor_fix_css .= '.postbox {
                    border: 1px solid #dfdfdf !important;
                    margin: 10px 0;
                }

                .postbox-container {
                    width: 100%;
                    background: #f1f1f1;
                }

                .postbox-header {
                    border-bottom: 0;
                }

                .components-panel__body {
                    border: 1px solid #dfdfdf;
                    border-top: none!important;
                    margin: 10px 0;
                    background: #fff;
                }

                .components-panel {
                    background: #f1f1f1;
                }

                .edit-post-meta-boxes-area .postbox .handle-order-higher,
                .edit-post-meta-boxes-area .postbox .handle-order-lower {
                    width: 22px;
                    height: 22px;
                }

                .hndle.ui-sortable-handle {
                    border-bottom: 0 !important;
                }

                .components-button[aria-expanded=true] {
                    border-bottom: 1px solid var(--wp-admin-theme-color) !important;
                    border-radius: 0 !important;
                }

                .components-panel__body {
                    margin: 10px -1px;
                }

                .components-panel__body.is-opened {
                    border: 1px solid var(--wp-admin-theme-color) !important;
                    margin: 10px 0;
                }';

            $jltmb_editor_fix_css = preg_replace('#/\*.*?\*/#s', '', $jltmb_editor_fix_css);
            $jltmb_editor_fix_css = preg_replace('/\s*([{}|:;,])\s+/', '$1', $jltmb_editor_fix_css);
            $jltmb_editor_fix_css = preg_replace('/\s\s+(.*)/', '$1', $jltmb_editor_fix_css);
            wp_add_inline_style('jltmb-editor', wp_strip_all_tags($jltmb_editor_fix_css));
        }
    }
}