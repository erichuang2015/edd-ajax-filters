<?php
/**
 * Shortcodes
 *
 * @package     EDD\Ajax_Filters\Shortcodes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Shortcodes' ) ) {

    class EDD_Ajax_Filters_Shortcodes {

        /**
         * @var $id int Last served id
         */
        private $id;

        public function __construct() {
            $this->id = 0;

            // [edd_ajax_filter]
            add_shortcode( 'edd_ajax_filter', array( $this, 'filter' ) );
        }

        /**
         * Available filters:
         *
         * [edd_ajax_filter filter="input" type="text" field="post_title"]
         * [edd_ajax_filter filter="input" type="textarea" field="post_content,post_excerpt"]
         *  input:
         *      post_title, post_content, post_excerpt
         *
         * [edd_ajax_filter filter="options" type="select" options="{'opt1':'Option 1', 'opt2':'Option 2'}"]
         * [edd_ajax_filter filter="options" type="block" options="{'opt1':'Option 1', 'opt2':'Option 2'}" inline="yes"]
         * [edd_ajax_filter filter="options" type="list" options="{'opt1':'Option 1', 'opt2':'Option 2'}" multiple="yes"]
         *  options:
         *
         * [edd_ajax_filter filter="taxonomy" type="select" field="download_category"]
         *  taxonomy:
         *      download_category, download_tag
         *
         * [edd_ajax_filter filter="order" field="post_date" direction="DESC"]
         *  order:
         *      post_title, post_date, _edd_download_sales, edd_price
         *
         * [edd_ajax_filter filter="button" type="submit" text="Apply filters"]
         * [edd_ajax_filter filter="button" type="order" field="post_date" text="Sort by"]
         *  button:
         *      submit, order
         *
         * @param $atts
         * @param null $content
         * @return null|string
         */
        public function filter( $atts, $content = null ) {
            $atts = ( is_array( $atts ) ) ? $atts : array();

            $filter = ( isset( $atts['filter'] ) ) ? $atts['filter'] : '';

            if( isset( edd_ajax_filters()->registered_filters[$filter] ) ) {
                // Next id
                $this->id++;

                $atts['id'] = $this->id;

                $filter_class = edd_ajax_filters()->registered_filters[$filter];

                $filter_object = new $filter_class( $atts );

                ob_start();
                $filter_object->render();
                $content .= ob_get_clean();
            }

            return $content;
        }
    }

}