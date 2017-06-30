<?php
/**
 * Filter_Button
 *
 * @package     EDD\Ajax_Filters\Filter_Button
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Filter_Button' ) ) {

    class EDD_Ajax_Filters_Filter_Button extends EDD_Ajax_Filters_Filter_Base {

        /**
         * @var $filter string Filter name
         */
        public $filter = 'button';

        public function args() {
            // Default filter args
            return array(
                'type' => 'button',                 // string | button, block
                'action' => 'submit',               // string | submit, order
                'text' => __( 'Submit' ),           // string | What you want
            );
        }

        public function classes() {

        }

        public function attributes() {
            $this->attributes['data-action'] = $this->args['action'];
        }

        public function field_pattern() {
            // Field pattern based on type
            if( $this->args['type'] == 'button' ) {
                $this->field_pattern = '<button type="button" id="{id}" class="{class}" {attr}>' . $this->get( 'text', '' ) . '</button>';
            } else {
                $this->field_pattern = '<div id="{id}" class="{class}" {attr}>' . $this->get( 'text', '' ) . '</div>';
            }
        }

    }

}