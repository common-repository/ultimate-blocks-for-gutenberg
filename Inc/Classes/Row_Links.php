<?php

namespace JLTMB\Inc\Classes;

use JLTMB\Libs\RowLinks;

if (!class_exists('Row_Links')) {
    /**
     * Row Links Class
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class Row_Links extends RowLinks
    {

        public $is_active;
        public $is_free;

        /**
         * Construct method
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function __construct()
        {
            parent::__construct();

            $this->is_active = false;
            $this->is_free   = true;
        }


        /**
         * Plugin action links
         *
         * @param [type] $links .
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function plugin_action_links($links)
        {
            $links[] = sprintf(
                '<a href="%1$s">%2$s</a>',
                'https://jeweltheme.com/dashboard/support',
                __('Support', 'ultimate-blocks-for-gutenberg')
            );
            $links[] = sprintf(
                '<a href="%1$s">%2$s</a>',
                'https://jeweltheme.com/docs/master-blocks',
                __('Docs', 'ultimate-blocks-for-gutenberg')
            );
            $links[] = sprintf(
                '<a href="%1$s">%2$s</a>',
                'https://www.youtube.com/playlist?list=PLqpMw0NsHXV8Gxgr-p7Y3z9eqSPAOrFZe',
                __('Video Tutorials', 'ultimate-blocks-for-gutenberg')
            );

            return $links;
        }
    }
}