<?php
/**
 * Scripts
 *
 * @package     EDD\Ajax_Filters\Scripts
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Scripts' ) ) {

    class EDD_Ajax_Filters_Scripts {

        public function __construct() {
            // Register scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );

            // Enqueue frontend scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 100 );

            // Enqueue admin scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 100 );
        }

        /**
         * Register scripts
         *
         * @since       1.0.0
         * @return      void
         */
        public function register_scripts() {
            // Use minified libraries if SCRIPT_DEBUG is turned off
            $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

            // Stylesheets
            wp_register_style( 'edd-ajax-filters-animate-css', EDD_AJAX_FILTERS_URL . 'assets/css/animate' . $suffix . '.css', array( ), EDD_AJAX_FILTERS_VER, 'all' );
            wp_register_style( 'edd-ajax-filters-css', EDD_AJAX_FILTERS_URL . 'assets/css/edd-ajax-filters' . $suffix . '.css', array( ), EDD_AJAX_FILTERS_VER, 'all' );
            wp_register_style( 'edd-ajax-filters-force-css', EDD_AJAX_FILTERS_URL . 'assets/css/edd-ajax-filters-force' . $suffix . '.css', array( ), EDD_AJAX_FILTERS_VER, 'all' );
            wp_register_style( 'edd-ajax-filters-admin-css', EDD_AJAX_FILTERS_URL . 'assets/css/edd-ajax-filters-admin' . $suffix . '.css', array( ), EDD_AJAX_FILTERS_VER, 'all' );

            // Scripts
            wp_register_script( 'edd-ajax-filters-js', EDD_AJAX_FILTERS_URL . 'assets/js/edd-ajax-filters' . $suffix . '.js', array( 'jquery', 'jquery-ui-slider' ), EDD_AJAX_FILTERS_VER, true );
            wp_register_script( 'edd-ajax-filters-admin-js', EDD_AJAX_FILTERS_URL . 'assets/js/edd-ajax-filters-admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), EDD_AJAX_FILTERS_VER, true );
        }

        /**
         * Enqueue frontend scripts
         *
         * @since       1.0.0
         * @return      void
         */
        public function enqueue_scripts( $hook ) {
            // Localize scripts
            $script_parameters = array(
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'nonce'	                => wp_create_nonce( 'edd_ajax_filters_nonce' ),
                // Animation parameters
                'in_animation'          => edd_ajax_filters()->options->get( 'in_animation', '' ),
                'in_animation_delay'    => edd_ajax_filters()->options->get( 'in_animation_delay', 100 ),
                'out_animation'         => edd_ajax_filters()->options->get( 'out_animation', '' ),
                'out_animation_delay'   => edd_ajax_filters()->options->get( 'out_animation_delay', 100 ),
            );

            wp_localize_script( 'edd-ajax-filters-js', 'edd_ajax_filters', $script_parameters );

            // Stylesheets
            wp_enqueue_style('edd-ajax-filters-animate-css');
            wp_enqueue_style('edd-ajax-filters-css');

            // Scripts
            wp_enqueue_script( 'edd-ajax-filters-js' );
        }

        /**
         * Enqueue admin scripts
         *
         * @since       1.0.0
         * @return      void
         */
        public function admin_enqueue_scripts( $hook ) {
            //Stylesheets
            wp_enqueue_style( 'edd-ajax-filters-admin-css' );

            //Scripts
            wp_enqueue_script( 'edd-ajax-filters-admin-js' );
        }

    }

}// End if class_exists check