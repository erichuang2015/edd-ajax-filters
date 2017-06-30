<?php
/**
 * Widgets
 *
 * @package     EDD\Ajax_Filters\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Widgets' ) ) {

    class EDD_Ajax_Filters_Widgets {

        public function __construct() {
            $this->includes();

            add_action( 'widgets_init', array( $this, 'widgets_init' ) );
        }

        private function includes() {
            require_once EDD_AJAX_FILTERS_DIR . 'widgets/edd-ajax-filters-button-widget.php';
            require_once EDD_AJAX_FILTERS_DIR . 'widgets/edd-ajax-filters-input-widget.php';
            require_once EDD_AJAX_FILTERS_DIR . 'widgets/edd-ajax-filters-options-widget.php';
            require_once EDD_AJAX_FILTERS_DIR . 'widgets/edd-ajax-filters-order-widget.php';
            require_once EDD_AJAX_FILTERS_DIR . 'widgets/edd-ajax-filters-taxonomy-widget.php';
        }

        public function widgets_init() {
            register_widget( 'EDD_Ajax_Filters_Button_Widget' );
            register_widget( 'EDD_Ajax_Filters_Input_Widget' );
            register_widget( 'EDD_Ajax_Filters_Options_Widget' );
            register_widget( 'EDD_Ajax_Filters_Order_Widget' );
            register_widget( 'EDD_Ajax_Filters_Taxonomy_Widget' );
        }

        public function get_default_filter_fields() {
            return array(
                'title' => array(
                    'name'   => __( 'Title:' ),
                    'id_key' => 'title',
                    'id'     => 'title',
                    'type'   => 'text',
                ),
                'show_label' => array(
                    'name'   => __( 'Show label:', 'edd-ajax-filters' ),
                    'id_key' => 'show_label',
                    'id'     => 'show_label',
                    'type'   => 'checkbox',
                ),
                'label' => array(
                    'name'   => __( 'Label:', 'edd-ajax-filters' ),
                    'id_key' => 'label',
                    'id'     => 'label',
                    'type'   => 'text',
                ),
                'operator' => array(
                    'name'   => __( 'Operator:', 'edd-ajax-filters' ),
                    'id_key' => 'operator',
                    'id'     => 'operator',
                    'type'   => 'select',
                    'options' => array(
                        'OR' => 'OR',
                        'AND' => 'AND',
                    )
                ),
            );
        }

        public function get_default_filter_fields_values() {
            return array(
                'title'         => '',
                'show_label'    => '',
                'label'         => '',
                'operator'      => 'OR',
            );
        }

        public function get_instance_values( $instance ) {
            return array(
                'show_label'    => $instance['show_label'] == 'on' ? 'yes' : 'no',
                'label'         => $instance['label'],
                'operator'      => $instance['operator'],
            );
        }

    }

}