<?php
/**
 * Query
 *
 * @package     EDD\Ajax_Filters\Query
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Ajax_Filters_Query' ) ) {

    class EDD_Ajax_Filters_Query {

        /**
         * @var $filters array Filters
         */
        private $filters;

        /**
         * @var $select string SQL SELECT value
         */
        private $select;

        /**
         * @var $from string SQL FROM value
         */
        private $from;

        /**
         * @var $wheres array SQL WHERE clauses from filters
         */
        private $wheres;

        /**
         * @var $where string SQL WHERE value
         */
        private $where;

        /**
         * @var $groupBy string SQL GROUP BY value
         */
        private $groupBy;

        /**
         * @var $order string SQL ORDER BY value
         */
        private $order;

        /**
         * @var $limit string SQL LIMIT value
         */
        private $limit;

        /**
         * @var $offset string SQL OFFSET value
         */
        private $offset;

        /**
         * @var $query string SQL sentence
         */
        private $query;

        /**
         * @var $count_query string COUNT() SQL sentence
         */
        private $count_query;

        /**
         * @var $results array ARRAY_A of results
         */
        private $results;

        /**
         * @var $found_results int Found results count
         */
        private $found_results;

        /**
         * @var $executed bool Var to check if query has been already executed
         */
        private $executed;

        public function __construct( $filters = array(), $args = array() ) {
            global $wpdb;

            // Default search values
            $this->select = $wpdb->posts . '.ID ';                                  // SELECT DISTINCT *
            $this->from = $wpdb->posts . ' ';                                       // FROM posts
            $this->wheres = array(                                                   // WHERE clauses
                'OR' => array(),                                                        // OR clauses
                'AND' => array(),                                                       // AND clauses
            );
            $this->groupBy = "$wpdb->posts.ID ";                                    // GROUP BY ID
            $this->order = '';                                                      // ORDER BY
            $this->limit = isset( $args['limit'] ) ? $args['limit'] : 9;            // LIMIT
            $this->offset = isset( $args['offset'] ) ? $args['offset'] : 0;         // OFFSET

            // Initialize vars
            $this->filters = $filters;
            $this->query = '';
            $this->count_query = '';
            $this->results = array();
            $this->found_results = 0;
            $this->executed = false;
        }

        public function get_query() {
            if( ! $this->executed ) {
                $this->execute();
            }

            return $this->query;
        }

        public function get_results() {
            if( ! $this->executed ) {
                $this->execute();
            }

            return $this->results;
        }

        public function get_found_results() {
            if( ! $this->executed ) {
                $this->execute();
            }

            return $this->found_results;
        }

        private function execute() {
            global $wpdb;

            foreach( $this->filters as $filter ) {
                if( $filter['filter'] == 'order' ) {
                    // Order fields
                    $direction = ( strtoupper( $filter['direction'] ) === 'DESC' ) ? 'DESC' : 'ASC';

                    $fields = explode( ',', $filter['field'] );

                    foreach( $fields as $field ) {
                        $this->filter_order( $field, $direction );
                    }
                } else {
                    // Filter fields
                    $fields = explode( ',', $filter['field'] );

                    foreach( $fields as $field ) {
                        if( empty( $filter['value'] ) ) {
                            continue;
                        }

                        $operator = ( strtoupper( $filter['operator'] ) === 'OR' ) ? 'OR' : 'AND';

                        switch( $field ) {
                            case 'post_title':
                            case 'post_content':
                            case 'post_excerpt':
                                $this->filter_search( $filter['value'], $operator, $field );
                                break;
                            case 'download_category':
                            case 'download_tag':
                                $this->filter_term( $filter['value'], $operator, $field );
                                break;
                            default:
                                do_action( 'edd_ajax_filters_filter_custom_field', $this, $filter, $field );
                                break;
                        }
                    }
                }
            }

            // Build the where clause
            $this->where = "1=1 AND $wpdb->posts.post_type = 'download' " .                 // WHERE post_type = download
                "AND $wpdb->posts.post_status = 'publish' " .                               // AND post_status = publish
                ( ! empty( $this->wheres['OR'] ) ?
                    "AND ( 1=1 OR " . implode( ' OR ', $this->wheres['OR'] ) . ") " : "" ) .         // AND ( OR clauses )
                ( ! empty( $this->wheres['AND'] ) ?
                    "AND ( 1=1 AND " . implode( ' AND ', $this->wheres['AND'] ) . ")" : "" );        // AND ( AND clauses )

            // Order check
            if( empty( $this->order ) ) {
                $this->order = "$wpdb->posts.post_date DESC";
            } else {
                // Remove last comma
                $this->order = rtrim( $this->order, ', ' );
            }

            $this->query = "
                SELECT DISTINCT $this->select
                FROM $this->from
                WHERE $this->where
                GROUP BY $this->groupBy
                ORDER BY $this->order
                LIMIT $this->offset, $this->limit
            ";

            /**
             * Hook filter edd_ajax_filters_query allows override the final search query to execute
             *
             * @var $query string
             * @var $edd_ajax_filters_query EDD_Ajax_Filters_Query
             */
            $this->query = apply_filters( 'edd_ajax_filters_query', $this->query, $this );

            $this->count_query = "
                SELECT COUNT($this->select)
                FROM $this->from
                WHERE $this->where
                GROUP BY $this->groupBy
            ";

            /**
             * Hook filter edd_ajax_filters_count_query allows override the final search count query to execute
             *
             * @var $count_query string
             * @var $edd_ajax_filters_query EDD_Ajax_Filters_Query
             */
            $this->count_query = apply_filters( 'edd_ajax_filters_count_query', $this->count_query, $this );

            // Returned results are an array of post ids
            $this->results = $wpdb->get_results( $this->query, ARRAY_A );
            $this->found_results = $wpdb->query( $this->count_query );

            $this->executed = true;
        }

        // Apply the search in post title, excerpt and/or content
        private function filter_search( $search, $operator = 'OR', $field ) {
            if( empty( $field ) ) {
                return;
            }

            global $wpdb;

            $this->wheres[$operator][] = "($wpdb->posts.$field LIKE '%$search%')";
        }

        private function filter_term( $search, $operator = 'OR', $taxonomy = 'download_category' ) {
            global $wpdb;

            // LEFT JOIN to term_relationships table
            if( strpos( $this->from, "LEFT JOIN $wpdb->term_relationships ON" ) === false ) {
                $this->from .= "LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ";
            }

            // Add children to filtering if taxonomy is hierarchical
            if( $taxonomy === 'download_category' ) {
                $term_ids = explode( ',', $search );
                $ids = array();

                foreach( $term_ids as $term_id ) {
                    if( $term_id !== '0' ) {
                        $ids[] = $term_id;

                        $children = get_term_children( $term_id, $taxonomy );

                        if( $children ) {
                            $ids = array_merge( $ids, $children );
                        }
                    }
                }

                $search = implode( ',', $ids );
            }

            if( strpos( $search, ',' ) !== false ) {
                // Filter by multiple terms ids
                $this->wheres[$operator][] =  "( $wpdb->term_relationships.term_taxonomy_id IN ($search) )";
            } else {
                // $search === 0 means all, so no filter here
                if( $search !== '0' ) {
                    // Filter by single term id
                    $this->wheres[$operator][] = "( $wpdb->term_relationships.term_taxonomy_id = $search )";
                }
            }
        }

        private function filter_order( $field, $direction = 'DESC' ) {
            if( empty( $field ) ) {
                return;
            }

            global $wpdb;

            switch($field) {
                case 'edd_price':
                case '_edd_download_sales':
                    // LEFT JOIN to postmeta table
                    if( strpos( $this->from, "LEFT JOIN $wpdb->postmeta AS meta_$field ON" ) === false ) {
                        $this->from .= "LEFT JOIN $wpdb->postmeta AS meta_$field ON ( $wpdb->posts.ID = meta_$field.post_id ) ";
                    }

                    $this->order .= "meta_$field.meta_value $direction, ";
                    break;
                default:
                    $this->order .= "$wpdb->posts.$field $direction, ";
                    break;
            }
        }

    }

}