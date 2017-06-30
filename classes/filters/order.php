<?php
/**
 * Filter_Order
 *
 * @package     EDD\Ajax_Filters\Filter_Order
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Filter_Order' ) ) {

    class EDD_Ajax_Filters_Filter_Order extends EDD_Ajax_Filters_Filter_Button {

        /**
         * @var $filter string Filter name
         */
        public $filter = 'order';

        public function args() {
            // Parent args
            $args = parent::args();

            // Action attribute for filter button
            $args['action'] = 'order';

            // Default filter args (overriding parent ones)
            return array_merge( array(
                'field'  => 'post_date',            // string | post_title, post_date, edd_price, _edd_download_sales
                'direction' => 'desc',              // string | initial order direction
                'text'  => '',                      // string | What you want
            ), $args );
        }

        public function classes() {
            parent::classes();
        }

        public function attributes() {
            parent::attributes();

            if( isset( $this->attributes['data-operator'] ) ) {
                unset( $this->attributes['data-operator'] );
            }

            // data-direction attribute
            $this->attributes['data-direction'] = $this->args['direction'];
        }

        public function field_pattern() {
            parent::field_pattern();
        }

    }

}