<?php
/**
 * Widget
 *
 * @package     EDD\Ajax_Filters\Button_Widget
 * @since       1.0.0
 *
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Button_Widget' ) ) {

    class EDD_Ajax_Filters_Button_Widget extends uFramework_Widget {

        public function __construct() {
            $this->widget_slug = 'edd_ajax_filters_button_widget';

            $this->fields = array_merge( edd_ajax_filters()->widgets->get_default_filter_fields(), array(
                array(
                    'name'   => __( 'Type:', 'edd-ajax-filters' ),
                    'id_key' => 'type',
                    'id'     => 'type',
                    'type'   => 'select',
                    'options' => array(
                        'button' => 'Button',
                        'block' => 'Block',
                    )
                ),
                array(
                    'name'   => __( 'Action:', 'edd-ajax-filters' ),
                    'id_key' => 'action',
                    'id'     => 'action',
                    'type'   => 'select',
                    'options' => array(
                        'submit' => 'Submit',
                        'order' => 'Order',
                    )
                ),
                array(
                    'name'   => __( 'Text:', 'edd-ajax-filters' ),
                    'id_key' => 'text',
                    'id'     => 'text',
                    'type'   => 'text',
                ),
            ) );

            $this->defaults = array_merge( edd_ajax_filters()->widgets->get_default_filter_fields_values(), array(
                'type'            => '',
                'action'          => '',
                'text'            => '',
            ) );

            parent::__construct( __( 'EDD Ajax Filters: Button', 'edd-ajax-filters' ), __( 'Ajax filter to do a submit or order action', 'edd-ajax-filters' ) );
        }

        public function get_widget( $args, $instance ) {
            echo edd_ajax_filters()->functions->do_shortcode( array_merge( edd_ajax_filters()->widgets->get_instance_values( $instance ), array(
                'filter' => 'button',
                'type' => $instance['type'],
                'action' => $instance['action'],
                'text' => $instance['text'],
            ) ) );
        }
    }

}