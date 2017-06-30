<?php
/**
 * Widget
 *
 * @package     EDD\Ajax_Filters\Input_Widget
 * @since       1.0.0
 *
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Input_Widget' ) ) {

    class EDD_Ajax_Filters_Input_Widget extends uFramework_Widget {

        public function __construct() {
            $this->widget_slug = 'edd_ajax_filters_input_widget';

            $this->fields = array_merge( edd_ajax_filters()->widgets->get_default_filter_fields(), array(
                array(
                    'name'   => __( 'Type:', 'edd-ajax-filters' ),
                    'id_key' => 'type',
                    'id'     => 'type',
                    'type'   => 'select',
                    'options' => array(
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'search' => 'Search',
                    )
                ),
                array(
                    'name'   => __( 'Fields to apply the search:', 'edd-ajax-filters' ),
                    'id_key' => 'field',
                    'id'     => 'field',
                    'type'   => 'multicheck',
                    'select_all_button' => false,
                    'options' => array(
                        'post_title' => 'Title',
                        'post_excerpt' => 'Excerpt',
                        'post_content' => 'Content',
                    )
                ),
            ) );

            $this->defaults = array_merge( edd_ajax_filters()->widgets->get_default_filter_fields_values(), array(
                'type'            => '',
                'field'            => '',
            ) );

            parent::__construct( __( 'EDD Ajax Filters: Input', 'edd-ajax-filters' ), __( 'Ajax filter to search by title, excerpt or content', 'edd-ajax-filters' ) );
        }

        public function get_widget( $args, $instance ) {
            echo edd_ajax_filters()->functions->do_shortcode( array_merge( edd_ajax_filters()->widgets->get_instance_values( $instance ), array(
                'filter' => 'input',
                'type' => $instance['type'],
                'field' => is_array( $instance['field'] ) ? implode( ',', $instance['field'] ) : $instance['field'],
            ) ) );
        }
    }

}