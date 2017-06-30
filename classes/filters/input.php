<?php
/**
 * Filter_Input
 *
 * @package     EDD\Ajax_Filters\Filter_Input
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Filter_Input' ) ) {

    class EDD_Ajax_Filters_Filter_Input extends EDD_Ajax_Filters_Filter_Base {

        /**
         * @var $filter string Filter name
         */
        public $filter = 'input';

        public function args() {
            // Default filter args
            return array(
                'type'  => 'text',              // string | text, textarea
                'field'  => 'post_title',       // string (comma separated for multiple) | post_title, post_content, post_excerpt
            );
        }

        public function classes() {

        }

        public function attributes() {

        }

        public function field_pattern() {
            // Field pattern based on type
            if( $this->args['type'] == 'textarea' ) {
                $this->field_pattern = '<textarea id="{id}" name="{id}" class="{class}" {attr}></textarea>';
            } else {
                $this->field_pattern = '<input type="text" id="{id}" name="{id}" class="{class}" {attr}/>';
            }
        }

    }

}