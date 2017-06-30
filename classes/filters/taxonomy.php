<?php
/**
 * Filter_Taxonomy
 *
 * @package     EDD\Ajax_Filters\Filter_Taxonomy
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Filter_Taxonomy' ) ) {

    class EDD_Ajax_Filters_Filter_Taxonomy extends EDD_Ajax_Filters_Filter_Options {

        /**
         * @var $filter string Filter name
         */
        public $filter = 'taxonomy';

        public function args() {
            // Parent args
            $args = parent::args();

            // Default filter args
            return array_merge( array(
                'field'  => 'download_category',    // string | download_category, download_tag
                // Taxonomy options
                'show_option_all' => 'no',          // string | yes, no
                'hierarchical' => 'no',             // string | yes, no
                'order_by' => 'name',               // string | name, id, slug, count, term_group
                'order' => 'asc',                   // string | asc, desc
                'hide_children' => 'no',            // string | yes, no
                'hide_empty' => 'no',               // string | yes, no
                'show_count' => 'no',               // string | yes, no
            ), $args );
        }

        public function classes() {
            parent::classes();
        }

        public function attributes() {
            parent::attributes();
        }

        public function field_pattern() {
            $this->args['options'] = array();

            if( ! empty( $this->args['field'] ) ) {
                $taxonomy_options = edd_ajax_filters()->functions->get_taxonomy_options( $this->args['field'], array(
                    'show_option_all'  => ( $this->args['show_option_all'] == 'yes' ) ? __( 'All', 'edd-ajax-filters' ) : '',
                    'hierarchical'     => ( $this->args['hierarchical'] == 'yes' ) ? 1 : 0,
                    'orderby'          => $this->args['order_by'],
                    'order'            => $this->args['order'],
                    'depth'            => ( $this->args['hide_children'] == 'yes' ) ? 1 : 0,
                    'hide_empty'       => ( $this->args['hide_empty'] == 'yes' ) ? 1 : 0,
                    'show_count'       => ( $this->args['show_count'] == 'yes' ) ? 1 : 0,
                ) );

                $this->args['options'] = $taxonomy_options;
            }

            parent::field_pattern();
        }

    }

}