<?php
/**
 * Options
 *
 * @package     EDD\Ajax_Filters\Options
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Options' ) ) {

    class EDD_Ajax_Filters_Options extends uFramework_Options {

        public function __construct() {
            $this->options_key = 'edd-ajax-filters';

            add_filter( 'tsunoa_' . $this->options_key . '_settings', array( $this, 'register_settings_url' ) );

            parent::__construct();
        }

        public function register_settings_url( $url ) {
            return 'admin.php?page=' . $this->options_key;
        }

        public function reset_form() {
            // Restores default options
            edd_ajax_filters_activation();
        }

        /**
         * Add the options metabox to the array of metaboxes
         * @since  0.1.0
         */
        public function register_form() {
            // Options page configuration
            $args = array(
                'key'      => $this->options_key,
                'title'    => __( 'EDD Ajax Filters', 'edd-ajax-filters' ),
                'topmenu'  => 'tsunoa',
                'cols'     => 2,
                'boxes'    => $this->boxes(),
                'tabs'     => $this->tabs(),
                'menuargs' => array(
                    'menu_title' => __( 'EDD Ajax Filters', 'edd-ajax-filters' ),
                ),
                'savetxt'  => __( 'Save settings' ),
                'resettxt'  => __( 'Reset settings' ),
                'admincss' => '.' . $this->options_key . ' #side-sortables{padding-top: 0 !important;}' .
                    '.' . $this->options_key . '.cmo-options-page .columns-2 #postbox-container-1{margin-top: 0 !important;}' .
                    '.' . $this->options_key . '.cmo-options-page .nav-tab-wrapper{display: none;}'
            );

            // Create the options page
            new Cmb2_Metatabs_Options( $args );
        }

        /**
         * Setup form in settings page
         *
         * @return array
         */
        public function boxes() {
            // Holds all CMB2 box objects
            $boxes = array();

            // Default options to all boxes
            $show_on = array(
                'key'   => 'options-page',
                'value' => array( $this->options_key ),
            );

            // General options box
            $cmb = new_cmb2_box( array(
                'id'      => $this->options_key . '-animations',
                'title'   => __( 'General options', 'edd-ajax-filters' ),
                'show_on' => $show_on,
            ) );

            $cmb->add_field( array(
                'name' => __( 'Entrance animation', 'edd-ajax-filters' ),
                'desc' => '',
                'id'   => 'in_animation',
                'type' => 'animation',
                'preview' => true,
                'groups' => array( 'entrances' ),
            ) );

            $cmb->add_field( array(
                'name' => __( 'Delay', 'edd-ajax-filters' ),
                'desc' => __( 'In milliseconds (1 second = 1000 milliseconds)', 'edd-ajax-filters' ),
                'id'   => 'in_animation_delay',
                'type' => 'text_small',
                'attributes' => array(
                    'type' => 'number',
                    'pattern' => '\d*',
                ),
                'default' => 100,
            ) );

            $cmb->add_field( array(
                'name' => __( 'Exit animation', 'edd-ajax-filters' ),
                'desc' => '',
                'id'   => 'out_animation',
                'type' => 'animation',
                'preview' => true,
                'groups' => array( 'exits' ),
            ) );

            $cmb->add_field( array(
                'name' => __( 'Delay', 'edd-ajax-filters' ),
                'desc' => __( 'In milliseconds (1 second = 1000 milliseconds)', 'edd-ajax-filters' ),
                'id'   => 'out_animation_delay',
                'type' => 'text_small',
                'attributes' => array(
                    'type' => 'number',
                    'pattern' => '\d*',
                ),
                'default' => 100,
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            // Submit box
            $cmb = new_cmb2_box( array(
                'id'      => $this->options_key . '-submit',
                'title'   => __( 'Save changes', 'edd-ajax-search' ),
                'show_on' => $show_on,
                'context' => 'side',
            ) );

            $cmb->add_field( array(
                'name' => '',
                'desc' => '',
                'id'   => 'submit_box',
                'type' => 'title',
                'render_row_cb' => array( $this, 'submit_box' )
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            // Shortcode box
            $cmb = new_cmb2_box( array(
                'id'      => $this->options_key . '-shortcode',
                'title'   => __( 'Shortcode generator', 'edd-ajax-filters' ),
                'show_on' => $show_on,
                'context' => 'side',
            ) );

            $cmb->add_field( array(
                'name' => '',
                'desc' => __( 'From this options page you can configure default parameters for EDD Ajax Filters. Also using form bellow you can generate a shortcode to place it in any page.', 'edd-ajax-filters' ),
                'id'   => 'shortcode_generator',
                'type' => 'title',
                'after' => array( $this, 'shortcode_generator' ),
            ) );

            $cmb->object_type( 'options-page' );

            $boxes[] = $cmb;

            return $boxes;
        }

        public function tabs() {
            $tabs = array();

            $tabs[] = array(
                'id'    => 'general',
                'title' => 'General',
                'desc'  => '',
                'boxes' => array(
                    $this->options_key . '-animations',
                ),
            );

            return $tabs;
        }

        /**
         * Submit box
         *
         * @param array      $field_args
         * @param CMB2_Field $field
         */
        public function submit_box( $field_args, $field ) {
            ?>
            <p>
                <a href="<?php echo tsunoa_product_docs_url( $this->options_key ); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-media-text"></i> <?php _e( 'Documentation' ); ?></a>
                <a href="<?php echo tsunoa_product_url( $this->options_key ); ?>" target="_blank" class="uframework-icon-link"><i class="dashicons dashicons-cart"></i> <?php _e( 'Get support and pro features', 'edd-ajax-search' ); ?></a>
            </p>
            <div class="cmb2-actions">
                <input type="submit" name="reset-cmb" value="<?php _e( 'Reset settings' ); ?>" class="button">
                <input type="submit" name="submit-cmb" value="<?php _e( 'Save settings' ); ?>" class="button-primary">
            </div>
            <?php
        }

        /**
         * Shortcode generator
         *
         * @param array      $field_args
         * @param CMB2_Field $field
         */
        public function shortcode_generator( $field_args, $field ) {
            ?>
            <div id="edd-ajax-filters-shortcode-form" class="uframework-shortcode-generator">
                <p>
                    <textarea type="text" id="edd-ajax-filters-shortcode-input" data-shortcode="edd_ajax_filter" readonly="readonly">[edd_ajax_filter]</textarea>
                </p>

                <p>
                    <label for="shortcode_form_filter">Filter:</label>
                    <select id="shortcode_form_filter" data-shortcode-attr="filter">
                        <?php foreach( edd_ajax_filters()->registered_filters as $filter => $filter_class ) : ?>
                            <option value="<?php echo $filter; ?>"><?php echo ucfirst( str_replace('-', ' ', $filter ) ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="shortcode_form_show_label">Show label:</label>
                    <input id="shortcode_form_show_label" data-shortcode-attr="show_label" type="checkbox" />
                </p>

                <p>
                    <label for="shortcode_form_label">Label:</label>
                    <input id="shortcode_form_label" data-shortcode-attr="label" data-clear-if-empty="true" type="text" />
                </p>

                <p data-hidden-for="order">
                    <label for="shortcode_form_operator">Operator:</label>
                    <select id="shortcode_form_operator" data-shortcode-attr="operator" data-clear-if-hidden="true">
                        <option value="OR">OR</option>
                        <option value="AND">AND</option>
                    </select>
                </p>

                <p>
                    <label for="shortcode_form_type">Type:</label>
                    <select id="shortcode_form_type" data-shortcode-attr="type">
                        <option value="button">Button</option>
                        <option value="block">Block</option>
                    </select>
                </p>

                <p data-hidden-for="button">
                    <label for="shortcode_form_field_text">Field:</label>
                    <input id="shortcode_form_field_text" data-shortcode-attr="field" data-clear-if-empty="true" type="text" />
                </p>

                <!-- Button specific -->
                <p data-visible-for="button">
                    <label for="shortcode_form_action">Action:</label>
                    <select id="shortcode_form_action" data-shortcode-attr="action" data-clear-if-hidden="true" type="text">
                        <option value="submit">Submit</option>
                        <option value="order">Order</option>
                    </select>
                </p>

                <!-- Order specific -->
                <p data-visible-for="order">
                    <label for="shortcode_form_direction">Direction:</label>
                    <select id="shortcode_form_direction" data-shortcode-attr="direction" data-clear-if-hidden="true">
                        <option value="ASC">ASC</option>
                        <option value="DESC">DESC</option>
                    </select>
                </p>

                <p data-visible-for="button,order">
                    <label for="shortcode_form_text">Text:</label>
                    <input id="shortcode_form_text" data-shortcode-attr="text" data-clear-if-empty="true" type="text" />
                </p>

                <!-- Taxonomy specific -->
                <p data-visible-for="taxonomy">
                    <label for="shortcode_form_show_option_all">Show option all:</label>
                    <input id="shortcode_form_show_option_all" data-shortcode-attr="show_option_all" data-clear-if-hidden="true" type="checkbox" />
                </p>

                <p data-visible-for="taxonomy">
                    <label for="shortcode_form_hierarchical">Hierarchical:</label>
                    <input id="shortcode_form_hierarchical" data-shortcode-attr="hierarchical" data-clear-if-hidden="true" type="checkbox" />
                </p>

                <p data-visible-for="taxonomy">
                    <label for="shortcode_form_order_by">Order by:</label>
                    <select id="shortcode_form_order_by" data-shortcode-attr="order_by" data-clear-if-hidden="true">
                        <option value="name">Name</option>
                        <option value="id">Term ID</option>
                        <option value="slug">Slug</option>
                        <option value="count">Count</option>
                        <option value="term_group">Term Group</option>
                    </select>
                </p>

                <p data-visible-for="taxonomy">
                    <label for="shortcode_form_order">Order:</label>
                    <select id="shortcode_form_order" data-shortcode-attr="order" data-clear-if-hidden="true">
                        <option value="ASC">ASC</option>
                        <option value="DESC">DESC</option>
                    </select>
                </p>

                <p data-visible-for="taxonomy">
                    <label for="shortcode_form_hide_children">Hide children:</label>
                    <input id="shortcode_form_hide_children" data-shortcode-attr="hide_children" data-clear-if-hidden="true" type="checkbox" />
                </p>

                <p data-visible-for="taxonomy">
                    <label for="shortcode_form_hide_empty">Hide empty:</label>
                    <input id="shortcode_form_hide_empty" data-shortcode-attr="hide_empty" data-clear-if-hidden="true" type="checkbox" />
                </p>

                <p data-visible-for="taxonomy">
                    <label for="shortcode_form_show_count">Show count:</label>
                    <input id="shortcode_form_show_count" data-shortcode-attr="show_count" data-clear-if-hidden="true" type="checkbox" />
                </p>

                <!-- Options field for Options filter -->
                <div data-visible-for="options">
                    <label for="shortcode_form_options">Options:</label>
                    <div id="shortcode_form_options" data-shortcode-attr="options_options">
                        <div class="option-group">
                            <div>
                                <label>
                                    Value:
                                    <input type="text" name="value" />
                                </label>
                            </div>
                            <div>
                                <label>
                                    Text:
                                    <input type="text" name="text" />
                                </label>
                            </div>
                            <div>
                                <button type="button" class="button remove-group" disabled="disabled"><i class="dashicons-before dashicons-no-alt"></i></button>
                            </div>
                        </div>
                        <button type="button" class="button add-group">Add option</button>
                    </div>
                </div>

                <!-- Options specific -->
                <p data-visible-for="options,taxonomy">
                    <label for="shortcode_form_multiple">Multiple:</label>
                    <input id="shortcode_form_multiple" data-shortcode-attr="multiple" data-clear-if-hidden="true" type="checkbox" />
                </p>

                <p data-visible-for="options,taxonomy">
                    <label for="shortcode_form_inline">Inline:</label>
                    <input id="shortcode_form_inline" data-shortcode-attr="inline" data-clear-if-hidden="true" type="checkbox" />
                </p>

                <p data-visible-for="options,taxonomy">
                    <label for="shortcode_form_hide_inputs">Hide Inputs:</label>
                    <input id="shortcode_form_hide_inputs" data-shortcode-attr="hide_inputs" data-clear-if-hidden="true" type="checkbox" />
                </p>
            </div>
            <?php
        }

    }

}