<?php
/**
 * Widget
 *
 * @package     EDD\Ajax_Filters\Options_Widget
 * @since       1.0.0
 *
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Options_Widget' ) ) {

    class EDD_Ajax_Filters_Options_Widget extends uFramework_Widget {

        public function __construct() {
            $this->widget_slug = 'edd_ajax_filters_options_widget';

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
                    'type'   => 'text',
                ),
                array(
                    'name'   => __( 'Options:', 'edd-ajax-filters' ),
                    'id_key' => 'options',
                    'id'     => 'options',
                    'type'   => 'group',
                    'inline' => true,
                    'options'     => array(
                        'remove_button' => '<i class="dashicons-before dashicons-no-alt"></i>',
                        'add_button' => __( 'Add option', 'edd-ajax-filters' ),
                    ),
                    'fields' => array(
                        array(
                            'name'   => __( 'Value:', 'edd-ajax-filters' ),
                            'id_key' => 'value',
                            'id'     => 'value',
                            'type'   => 'text',
                        ),
                        array(
                            'name'   => __( 'Text:', 'edd-ajax-filters' ),
                            'id_key' => 'text',
                            'id'     => 'text',
                            'type'   => 'text',
                        ),
                    )
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
                'options'         => '',
                'multiple'        => '',
                'inline'          => '',
                'hide_inputs'     => '',
            ) );

            parent::__construct( __( 'EDD Ajax Filters: Options', 'edd-ajax-filters' ), __( 'Ajax filter to filter by a group of options', 'edd-ajax-filters' ) );
        }

        public function get_widget( $args, $instance ) {
            $options = array();

            // Turns CMB2 field group into a group of valid options for edd ajax filters shortcodes
            if( is_array( $instance['options'] ) ) {
                foreach( $instance['options'] as $option ) {
                    $options[$option['value']] = $option['text'];
                }
            }

            echo edd_ajax_filters()->functions->do_shortcode( array_merge( edd_ajax_filters()->widgets->get_instance_values( $instance ), array(
                'filter' => 'options',
                'type' => $instance['type'],
                'field' => $instance['field'],
                'options' => $options,
                'multiple' => $instance['multiple'] == 'on' ? 'yes' : 'no',
                'inline' => $instance['inline'] == 'on' ? 'yes' : 'no',
                'hide_inputs' => $instance['hide_inputs'] == 'on' ? 'yes' : 'no',
            ) ) );
        }
    }

}