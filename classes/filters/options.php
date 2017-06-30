<?php
/**
 * Filter_Options
 *
 * @package     EDD\Ajax_Filters\Filter_Options
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Filter_Options' ) ) {

    class EDD_Ajax_Filters_Filter_Options extends EDD_Ajax_Filters_Filter_Base {

        /**
         * @var $filter string Filter name
         */
        public $filter = 'options';

        /**
         * @var $option_pattern string Filter single option pattern
         */
        public $option_pattern = '';

        public function args() {
            return array(
                'type'  => 'select',                // string | select, block, list
                'field'  => '',                     // string |
                'multiple' => 'no',                 // string | yes, no
                'inline' => 'no',                   // string | yes, no
                'hide_inputs' => 'no',              // string | yes, no
                'options' => '',                    // mixed  | json encoded array, array
            );
        }

        public function classes() {
            // Only apply hide input if type not is select
            if( $this->args['hide_inputs'] == 'yes' && $this->args['type'] != 'select' ) {
                $this->classes[] = 'edd-ajax-filter-hide-inputs';
            }
        }

        public function attributes() {
            if( $this->args['type'] == 'select' ) {
                if( $this->args['multiple'] == 'yes' ) {
                    $this->attributes['multiple'] = 'multiple';
                }
            }
        }

        public function field_pattern() {
            // Field and option pattern based on type
            if( $this->args['type'] == 'select' ) {
                $this->field_pattern = '<select id="{id}" class="{class}" {attr}>{options}</select>';
                $this->option_pattern = '<option value="{option_value}">{option_text}</option>';
            } else if( $this->args['type'] == 'block' ) {
                $this->field_pattern = '<div id="{id}" class="{class}" {attr}>{options}</div>';
                $this->option_pattern = '<div><input type="' . ( ( $this->args['multiple'] == 'yes' ) ? 'checkbox' : 'radio' ) . '" id="{id}-option-{option_index}" name="{id}" value="{option_value}"> <label for="{id}-option-{option_index}">{option_text}</label></div>';
            } else {
                $this->field_pattern = '<ul id="{id}" class="{class}" {attr}>{options}</ul>';
                $this->option_pattern = '<li><input type="' . ( ( $this->args['multiple'] == 'yes' ) ? 'checkbox' : 'radio' ) . '" id="{id}-option-{option_index}" name="{id}" value="{option_value}"> <label for="{id}-option-{option_index}">{option_text}</label></li>';
            }

            // Turn json options in array
            if( ! is_array( $this->args['options'] ) ) {
                $this->args['options'] = json_decode( str_replace( '\'', '"', $this->args['options'] ), true );
            }

            // Generate a string of options
            $options = '';
            $option_index = 0;

            if( is_array( $this->args['options'] ) && $this->args['options'] ) {
                foreach( $this->args['options'] as $value => $text ) {
                    $option_tags = array(
                        '{option_index}',
                        '{option_value}',
                        '{option_text}',
                    );

                    $replacement = array(
                        $option_index,
                        $value,
                        $text,
                    );

                    $options .= str_replace( $option_tags, $replacement, $this->option_pattern );

                    $option_index++;
                }

                // Replace options tag by options string
                $this->field_pattern = str_replace( '{options}', $options, $this->field_pattern );
            } else {
                // No options, so no output
                $this->field_pattern = '';
            }
        }

    }

}