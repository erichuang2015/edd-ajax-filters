<?php
/**
 * Widget
 *
 * @package     EDD\Ajax_Filters\Order_Widget
 * @since       1.0.0
 *
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Order_Widget' ) ) {

    class EDD_Ajax_Filters_Order_Widget extends uFramework_Widget {

        public function __construct() {
            $this->widget_slug = 'edd_ajax_filters_order_widget';

            $default_filter_fields = edd_ajax_filters()->widgets->get_default_filter_fields();

            unset( $default_filter_fields['operator'] );

            $this->fields = array_merge( $default_filter_fields, array(
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
                    'name'   => __( 'Field:', 'edd-ajax-filters' ),
                    'id_key' => 'field',
                    'id'     => 'field',
                    'type'   => 'select',
                    'options' => array(
                        'post_title' => 'Title',
                        'post_date' => 'Date',
                        'edd_price' => 'Price',
                        '_edd_download_sales' => 'Sales',
                        'comment_count' => 'Comments',
                    )
                ),
                array(
                    'name'   => __( 'Direction:', 'edd-ajax-filters' ),
                    'id_key' => 'direction',
                    'id'     => 'direction',
                    'type'   => 'select',
                    'options' => array(
                        'asc' => 'ASC',
                        'desc' => 'DESC',
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
                'field'           => '',
                'direction'       => '',
                'text'            => '',
            ) );

            parent::__construct( __( 'EDD Ajax Filters: Order', 'edd-ajax-filters' ), __( 'Ajax filter to sort', 'edd-ajax-filters' ) );
        }

        public function get_widget( $args, $instance ) {
            echo edd_ajax_filters()->functions->do_shortcode( array_merge( edd_ajax_filters()->widgets->get_instance_values( $instance ), array(
                'filter' => 'order',
                'action' => 'order',
                'type' => $instance['type'],
                'field' => $instance['field'],
                'direction' => $instance['direction'],
                'text' => $instance['text'],
            ) ) );
        }
    }

}