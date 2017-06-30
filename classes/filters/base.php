<?php
/**
 * Filter_Base
 *
 * @package     EDD\Ajax_Filters\Filter_Base
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Filter_Base' ) ) {

    class EDD_Ajax_Filters_Filter_Base {

        /**
         * @var $id     int Filter id
         */
        public $id;

        /**
         * @var $classes array Filter html classes
         */
        public $classes = array();

        /**
         * @var $attributes array Filter html attributes
         */
        public $attributes = array();

        /**
         * @var $filter string Filter name
         */
        public $filter = 'base';

        /**
         * @var $field_pattern string Filter pattern
         */
        public $field_pattern = '';

        /**
         * @var $args   array Filter args
         */
        public $args;

        public function __construct( $args = array() ) {
            $this->args = ( ! is_array( $args ) ) ? array() : $args;

            // Default filter args
            $this->args += shortcode_atts( array(
                'id' => 0,                  // int    | Filter ID
                'label' => '',              // string | Label of filter, whatever you want
                'show_label' => 'no',       // string | yes, no
                'operator' => 'or',         // string | or, and
            ), $this->args, 'edd_ajax_filter' );

            $this->id = $this->get( 'id', 0 );

            $this->args += shortcode_atts( $this->args(), $this->args, 'edd_ajax_filter_' . $this->filter );

            // Translate field abbreviations
            if( isset( $this->args['field'] ) ) {
                $fields = explode( ',', $this->args['field'] );

                foreach( $fields as $key => $field ) {
                    $fields[$key] = $this->args['field'] = $this->translate_field( $field );
                }

                $this->args['field'] = join( ',', $fields );
            }
        }

        public function render() {
            // Filter id
            $this->id = str_replace( '_', '-', "edd-ajax-filter-{$this->filter}-{$this->id}" );

            $this->build_classes();
            $this->build_attributes();
            $this->field_pattern();

            // Turn attributes array into a string
            $field_attributes_string = '';

            foreach( $this->attributes as $attribute => $value ) {
                $field_attributes_string .= sprintf( ' %s="%s"', $attribute, $value );
            }

            // Field pattern template tags
            $template_tags = array(
                '{id}',
                '{class}',
                '{attr}',
            );

            // Field pattern replacements
            $replacement = array(
                $this->id,
                implode( ' ', $this->classes ),
                $field_attributes_string,
            );

            $label = '<label for="' . $this->id . '">' . $this->args['label'] . '</label>';
            $field = str_replace( $template_tags, $replacement, $this->field_pattern ); ?>

            <div class="edd-ajax-filter-row">

                <div class="edd-ajax-filter-label">
                    <?php if( ! empty( $this->args['label'] ) && $this->args['show_label'] == 'yes' ) : ?>
                        <?php echo $label; ?>
                    <?php endif; ?>
                </div>

                <div class="edd-ajax-filter-wrapper">
                    <?php echo $field; ?>
                </div>
            </div>
            <?php
        }

        /**
         * Turns field definition like title or price into post_title and edd_price
         */
        public function translate_field( $field ) {
            $translations = array(
                'title' => 'post_title',
                'content' => 'post_content',
                'excerpt' => 'post_excerpt',
                'author' => 'post_author',
                'date' => 'post_date',
                'price' => 'edd_price',
                'sales' => '_edd_download_sales',
                'comments' => 'comment_count',
                'category' => 'download_category',
                'categories' => 'download_category',
                'tag' => 'download_tag',
                'tags' => 'download_tag',
            );

            if( in_array( $field, array_keys( $translations ) ) ) {
                return $translations[$field];
            }

            return $field;
        }

        private function build_classes() {
            // Default classes
            $this->classes[] = 'edd-ajax-filter';
            $this->classes[] = str_replace( '_', '-', "edd-ajax-filter-{$this->filter}" );

            // Filter classes after filter checks
            $this->classes[] = 'edd-ajax-filter-' . $this->args['type'];

            if( isset( $this->args['inline'] ) && $this->args['inline'] == 'yes' ) {
                $this->classes[] = 'edd-ajax-filter-inline';
            }

            if( isset( $this->args['multiple'] ) && $this->args['multiple'] == 'yes' ) {
                $this->classes[] = 'edd-ajax-filter-multiple';
            }

            $this->classes();
        }

        private function build_attributes() {
            // Default attributes
            $this->attributes['data-operator'] = $this->get('operator', 'or');

            // Filter attributes after filter checks
            $this->attributes['data-type'] = $this->args['type'];

            if( isset( $this->args['field'] ) ) {
                $this->attributes['data-field'] = str_replace(' ', '', $this->args['field']);
            }

            $this->attributes();
        }

        public function get( $key, $default = false ) {
            if( isset( $this->args[$key] ) ) {
                return $this->args[$key];
            }

            return $default;
        }

        // Functions to be overwritten by children
        /**
         * @return array
         */
        public function args() {
            return array(

            );
        }

        /**
         * @return void
         */
        public function classes() {

        }

        /**
         * @return void
         */
        public function attributes() {

        }

        /**
         * @return void
         */
        public function field_pattern() {

        }

    }

}