<?php
/**
 * Widget
 *
 * @package     EDD\Ajax_Filters\Taxonomy_Widget
 * @since       1.0.0
 *
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Taxonomy_Widget' ) ) {

    class EDD_Ajax_Filters_Taxonomy_Widget extends uFramework_Widget {

        public function __construct() {
            $this->widget_slug = 'edd_ajax_filters_taxonomy_widget';

            $this->fields = array_merge( edd_ajax_filters()->widgets->get_default_filter_fields(), array(
                array(
                    'name'   => __( 'Type:', 'edd-ajax-filters' ),
                    'id_key' => 'type',
                    'id'     => 'type',
                    'type'   => 'select',
                    'options' => array(
                        'select' => 'Select',
                        'block' => 'Block',
                        'list' => 'List',
                    )
                ),
                array(
                    'name'   => __( 'Field:', 'edd-ajax-filters' ),
                    'id_key' => 'field',
                    'id'     => 'field',
                    'type'   => 'select',
                    'options' => array(
                        'download_category' => 'Download Categories',
                        'download_tag' => 'Download Tags',
                    )
                ),
                array(
                    'name'   => __( 'Show option all:', 'edd-ajax-filters' ),
                    'id_key' => 'show_option_all',
                    'id'     => 'show_option_all',
                    'type'   => 'checkbox',
                ),
                array(
                    'name'   => __( 'Hierarchical:', 'edd-ajax-filters' ),
                    'id_key' => 'hierarchical',
                    'id'     => 'hierarchical',
                    'type'   => 'checkbox',
                ),
                array(
                    'name'   => __( 'Order by:', 'edd-ajax-filters' ),
                    'id_key' => 'order_by',
                    'id'     => 'order_by',
                    'type'   => 'select',
                    'options' => array(
                        'name'          => __( 'Name', 'edd-ajax-filters' ),
                        'id'            => __( 'Term ID', 'edd-ajax-filters' ),
                        'slug'          => __( 'Slug', 'edd-ajax-filters' ),
                        'count'         => __( 'Count', 'edd-ajax-filters' ),
                        'term_group'    => __( 'Term Group', 'edd-ajax-filters' ),
                    )
                ),
                array(
                    'name'   => __( 'Order:', 'edd-ajax-filters' ),
                    'id_key' => 'order',
                    'id'     => 'order',
                    'type'   => 'select',
                    'options' => array(
                        'asc' => 'ASC',
                        'desc' => 'DESC',
                    ),
                    'default' => 'asc'
                ),
                array(
                    'name'   => __( 'Hide children:', 'edd-ajax-filters' ),
                    'id_key' => 'hide_children',
                    'id'     => 'hide_children',
                    'type'   => 'checkbox',
                ),
                array(
                    'name'   => __( 'Hide empty:', 'edd-ajax-filters' ),
                    'id_key' => 'hide_empty',
                    'id'     => 'hide_empty',
                    'type'   => 'checkbox',
                ),
                array(
                    'name'   => __( 'Show count:', 'edd-ajax-filters' ),
                    'id_key' => 'show_count',
                    'id'     => 'show_count',
                    'type'   => 'checkbox',
                ),
                array(
                    'name'   => __( 'Multiple:', 'edd-ajax-filters' ),
                    'id_key' => 'multiple',
                    'id'     => 'multiple',
                    'type'   => 'checkbox',
                ),
                array(
                    'name'   => __( 'Inline:', 'edd-ajax-filters' ),
                    'id_key' => 'inline',
                    'id'     => 'inline',
                    'type'   => 'checkbox',
                ),
                array(
                    'name'   => __( 'Hide inputs:', 'edd-ajax-filters' ),
                    'id_key' => 'hide_inputs',
                    'id'     => 'hide_inputs',
                    'type'   => 'checkbox',
                ),
            ) );

            $this->defaults = array_merge( edd_ajax_filters()->widgets->get_default_filter_fields_values(), array(
                'type'            => '',
                'field'           => '',
                'show_option_all' => '',
                'hierarchical'    => '',
                'order_by'        => '',
                'order'           => '',
                'hide_children'   => '',
                'hide_empty'      => '',
                'show_count'            => '',
                'multiple'            => '',
                'inline'            => '',
                'hide_inputs'            => '',
            ) );

            parent::__construct( __( 'EDD Ajax Filters: Taxonomy', 'edd-ajax-filters' ), __( 'Ajax filter to filter by a group of options preloaded from a taxonomy', 'edd-ajax-filters' ) );
        }

        public function get_widget( $args, $instance ) {
            echo edd_ajax_filters()->functions->do_shortcode( array_merge( edd_ajax_filters()->widgets->get_instance_values( $instance ), array(
                'filter' => 'taxonomy',
                'type' => $instance['type'],
                'field' => $instance['field'],
                'show_option_all' => $instance['show_option_all'] == 'on' ? 'yes' : 'no',
                'hierarchical' => $instance['hierarchical'] == 'on' ? 'yes' : 'no',
                'order_by' => $instance['order_by'],
                'order' => $instance['order'],
                'hide_children' => $instance['hide_children'] == 'on' ? 'yes' : 'no',
                'hide_empty' => $instance['hide_empty'] == 'on' ? 'yes' : 'no',
                'show_count' => $instance['show_count'] == 'on' ? 'yes' : 'no',
                'multiple' => $instance['multiple'] == 'on' ? 'yes' : 'no',
                'inline' => $instance['inline'] == 'on' ? 'yes' : 'no',
                'hide_inputs' => $instance['hide_inputs'] == 'on' ? 'yes' : 'no',
            ) ) );
        }
    }

}