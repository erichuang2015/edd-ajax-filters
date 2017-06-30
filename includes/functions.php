<?php
/**
 * Functions
 *
 * @package     EDD\Ajax_Filters\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Functions' ) ) {

    class EDD_Ajax_Filters_Functions {

        public function __construct() {
            // Easy Digital Downloads [downloads] shortcode hooks
            add_filter( 'shortcode_atts_downloads', array( $this, 'shortcode_atts_downloads' ), 10, 4 );
            add_filter( 'downloads_shortcode', array( $this, 'downloads_shortcode' ), 10, 2 );
            add_filter( 'edd_download_pagination_args', array( $this, 'edd_download_pagination_args' ), 10, 4 );

            // Ajax requests
            add_action( 'wp_ajax_edd_ajax_filters', array( $this, 'apply_filters' ) );
            add_action( 'wp_ajax_nopriv_edd_ajax_filters', array( $this, 'apply_filters' ) );
        }

        // [downloads] custom attributes
        public function shortcode_atts_downloads( $out, $pairs, $atts, $shortcode ) {
            // Default custom attributes
            $custom_pairs = array(
                'pagination_total' => '',
                'pagination_current' => '',
            );

            foreach ($custom_pairs as $name => $default) {
                if ( array_key_exists( $name, $atts ) )
                    $out[$name] = $atts[$name];
                else
                    $out[$name] = $default;
            }

            return $out;
        }

        // Creates a hidden form with shortcode atts to pass it thought ajax
        public function downloads_shortcode( $display, $atts ) {
            if( ! defined( 'DOING_EDD_AJAX_FILTERS_AJAX' ) ) {
                if (get_query_var('paged'))
                    $paged = get_query_var('paged');
                else if (get_query_var('page'))
                    $paged = get_query_var('page');
                else
                    $paged = 1;

                ob_start(); ?>
                <form id="edd-ajax-filters-shortcode-atts" action="">
                    <?php foreach ($atts as $key => $value) : ?>
                        <?php if (!empty($value)) : ?>
                            <input type="hidden" name="shortcode_atts[<?php echo $key; ?>]" data-att="<?php echo $key; ?>" value="<?php echo $value; ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <input type="hidden" name="paged" value="<?php echo $paged; ?>">
                </form>
                <?php $shortcode_atts_form = ob_get_clean();

                $display = $shortcode_atts_form . $display;
            }

            return $display;
        }

        // Override [downloads] pagination links args
        public function edd_download_pagination_args( $pagination_args, $atts, $downloads, $query ) {
            if( isset( $atts['pagination_total'] ) && ! empty( $atts['pagination_total'] ) ) {
                $pagination_args['total'] = intval( $atts['pagination_total'] );
            }

            if( isset( $atts['pagination_current'] ) && ! empty( $atts['pagination_current'] ) ) {
                $pagination_args['current'] = intval( $atts['pagination_current'] );
            }

            return $pagination_args;
        }

        public function get_taxonomy_options( $taxonomy, $args = array() ) {
            $dropdown_args = shortcode_atts( array(
                'show_option_all'  => '',
                'hierarchical'     => 1,
                'orderby'          => 'name',
                'order'            => 'asc',
                'depth'            => 0,
                'hide_empty'       => 0,
                'show_count'       => false,
                'name'             => 'cat',
                'id'               => '',
                'taxonomy'         => $taxonomy,
                'echo'             => 0,
                'title_li'         => '',
                'class'            => '',
            ), $args );

            $dropdown = wp_dropdown_categories( $dropdown_args );

            $matches = null;
            $options = array();

            if( $args['show_option_all'] ) {
                $options[0] = __( 'All', 'edd-ajax-filters' );
            }

            if(preg_match_all('/value="(.*)">(.*)<\\/option>/', $dropdown, $matches)){

                foreach($matches[1] as $i => $key) {
                    $options[html_entity_decode( $key )] = html_entity_decode( $matches[2][$i] );
                }
            }

            return $options;
        }

        public function do_shortcode( $args ) {
            $shortcode_args = '';

            foreach( $args as $arg => $value ) {
                if( is_array( $value ) ) {
                    $value = str_replace( '"', '\'', json_encode( $value ) );
                    $value = str_replace( '[', '{', $value );
                    $value = str_replace( ']', '}', $value );
                }

                $shortcode_args .= sprintf( ' %s="%s"', $arg, $value);
            }

            return do_shortcode( '[edd_ajax_filter ' . $shortcode_args . ']' );
        }

        public function apply_filters() {
            if ( ! isset( $_REQUEST['nonce'] ) && ! wp_verify_nonce( $_REQUEST['nonce'], 'edd_ajax_filters_nonce' ) ) {
                wp_send_json_error( 'invalid_nonce' );
                wp_die();
            }

            if( ! isset( $_REQUEST['filters'] ) ) {
                wp_send_json_error( 'no_filters' );
                wp_die();
            }

            if( ! is_array( $_REQUEST['filters'] ) ) {
                wp_send_json_error( 'wrong_filters' );
                wp_die();
            }

            // Global to check if current ajax request comes from here
            define( 'DOING_EDD_AJAX_FILTERS_AJAX', true );

            // Shortcode attributes
            $shortcode_atts = $_REQUEST['shortcode_atts'];

            // Setup shortcode atts
            $shortcode_atts['ordery'] = 'post__in'; // Order by ids provided
            $shortcode_atts['number'] = ( ! isset( $shortcode_atts['number'] ) || intval($shortcode_atts['number']) == 0 ) ? 9 : intval($shortcode_atts['number']);

            // Set current page
            $paged = (int) isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1;

            $query = new EDD_Ajax_Filters_Query( $_REQUEST['filters'], array(
                'limit' => $shortcode_atts['number'],
                'offset' => ( $paged - 1 ) * $shortcode_atts['number'],
            ) );

            // Returned results are an array of WP_Post objects
            $results = $query->get_results();
            $found_results = $query->get_found_results();

            // Request response
            $response = array();

            $response['found_results'] = $found_results;

            if( $results && $found_results > 0 ) {
                // Turn results into ids shortcode parameter
                $shortcode_atts['ids'] = implode( ',', array_map( function ( $item ) {
                    return $item['ID'];
                }, $results ) );

                // The content to return is the returned from the shortcode [downloads]
                $response['html'] = do_shortcode( '[downloads ' .
                    implode(' ',
                        array_map( function( $key, $value ) {
                            return $key . '="' . $value . '"';
                        }, array_keys($shortcode_atts), $shortcode_atts )
                    ) .
                    ' pagination_total="' . ceil( $found_results / $shortcode_atts['number'] ) . '"' .
                    ' pagination_current="' . $paged . '"' .
                    ']' );
            } else {
                $response['html'] = sprintf( _x( 'No %s found', 'download post type name', 'easy-digital-downloads' ), edd_get_label_plural() );
            }

            /**
             * Filter to apply filters response
             *
             * @var $response       array                   Response
             * @var $query          EDD_Ajax_Filters_Query  Ajax filters query
             * @var $results        array                   ARRAY_A of downloads
             * @var $found_results  integer                 found results
             * @var $shortcode_atts array                   Shortcode atts
             * @var $paged          integer                 Current page
             */
            $response = apply_filters( 'edd_ajax_filters_apply_filters_response', $response, $query, $results, $found_results, $shortcode_atts, $paged );

            wp_send_json( $response );
            wp_die();
        }

    }

}