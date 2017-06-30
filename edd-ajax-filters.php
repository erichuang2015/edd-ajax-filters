<?php
/**
 * Plugin Name:     EDD Ajax Filters
 * Plugin URI:      https://wordpress.org/plugins/edd-ajax-filters
 * Description:     Live products filtering for Easy Digital Downloads.
 * Version:         1.0.0
 * Author:          Tsunoa
 * Author URI:      https://tsunoa.com
 * Text Domain:     edd-ajax-filters
 *
 * @package         EDD\Ajax_Filters
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters' ) ) {

    /**
     * Main EDD_Ajax_Filters class
     *
     * @since       1.0.0
     */
    class EDD_Ajax_Filters {

        /**
         * @var         EDD_Ajax_Filters $instance The one true EDD_Ajax_Filters
         * @since       1.0.0
         */
        private static $instance;

        /**
         * @var         array EDD Ajax Filters filters
         * @since       1.0.0
         */
        public $registered_filters;

        /**
         * @var         EDD_Ajax_Filters_Functions EDD Ajax Filters functions
         * @since       1.0.0
         */
        public $functions;

        /**
         * @var         EDD_Ajax_Filters_Options EDD Ajax Filters options
         * @since       1.0.0
         */
        public $options;

        /**
         * @var         EDD_Ajax_Filters_Scripts EDD Ajax Filters scripts
         * @since       1.0.0
         */
        public $scripts;

        /**
         * @var         EDD_Ajax_Filters_Shortcodes EDD Ajax Filters shortcodes
         * @since       1.0.0
         */
        public $shortcodes;

        /**
         * @var         EDD_Ajax_Filters_Widgets EDD Ajax Filters widgets
         * @since       1.0.0
         */
        public $widgets;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Ajax_Filters
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Ajax_Filters();
                self::$instance->setup_constants();
                self::$instance->register_filters();
                self::$instance->includes();
                self::$instance->load_textdomain();
            }

            return self::$instance;
        }

        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_AJAX_FILTERS_VER', '1.0.0' );

            // Plugin path
            define( 'EDD_AJAX_FILTERS_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_AJAX_FILTERS_URL', plugin_dir_url( __FILE__ ) );
        }

        /**
         * Register filters
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function register_filters() {
            require_once EDD_AJAX_FILTERS_DIR . 'classes/filters/base.php';
            require_once EDD_AJAX_FILTERS_DIR . 'classes/filters/button.php';
            require_once EDD_AJAX_FILTERS_DIR . 'classes/filters/input.php';
            require_once EDD_AJAX_FILTERS_DIR . 'classes/filters/options.php';
            require_once EDD_AJAX_FILTERS_DIR . 'classes/filters/order.php';
            require_once EDD_AJAX_FILTERS_DIR . 'classes/filters/taxonomy.php';

            $this->registered_filters = array(
                'button' => 'EDD_Ajax_Filters_Filter_Button',
                'input' => 'EDD_Ajax_Filters_Filter_Input',
                'options' => 'EDD_Ajax_Filters_Filter_Options',
                'order' => 'EDD_Ajax_Filters_Filter_Order',
                'taxonomy' => 'EDD_Ajax_Filters_Filter_Taxonomy',
            );
        }

        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            require_once EDD_AJAX_FILTERS_DIR . 'uFramework/uFramework.php';

            // Include classes
            require_once EDD_AJAX_FILTERS_DIR . 'classes/query.php';

            // Include scripts
            require_once EDD_AJAX_FILTERS_DIR . 'includes/functions.php';
            require_once EDD_AJAX_FILTERS_DIR . 'includes/options.php';
            require_once EDD_AJAX_FILTERS_DIR . 'includes/scripts.php';
            require_once EDD_AJAX_FILTERS_DIR . 'includes/shortcodes.php';
            require_once EDD_AJAX_FILTERS_DIR . 'includes/widgets.php';


            $this->functions = new EDD_Ajax_Filters_Functions();
            $this->options = new EDD_Ajax_Filters_Options();
            $this->scripts = new EDD_Ajax_Filters_Scripts();
            $this->shortcodes = new EDD_Ajax_Filters_Shortcodes();
            $this->widgets = new EDD_Ajax_Filters_Widgets();
        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_AJAX_FILTERS_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_ajax_filters_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-ajax-filters' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-ajax-filters', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/edd-ajax-filters/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-ajax-filters/ folder
                load_textdomain( 'edd-ajax-filters', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-ajax-filters/languages/ folder
                load_textdomain( 'edd-ajax-filters', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-ajax-filters', false, $lang_dir );
            }
        }
    }
}


/**
 * The main function responsible for returning the one true EDD_Ajax_Filters instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Ajax_Filters The one true EDD_Ajax_Filters
 */
function edd_ajax_filters() {
    return EDD_Ajax_Filters::instance();
}
add_action( 'plugins_loaded', 'edd_ajax_filters' );


/**
 * EDD_Ajax_Filters activation
 *
 * @since       1.0.0
 * @return      void
 */
function edd_ajax_filters_activation() {
    // Default option => value
    $options = array(

    );

    $opts = array();

    foreach($options as $option => $value) {
        $opts[$option] = $value;
    }

    add_option( 'edd-ajax-filters', $options );
}
register_activation_hook( __FILE__, 'edd_ajax_filters_activation' );