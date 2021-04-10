<?php
/**
 * Exit if accessed directly
 *
 * @package Wallet_System_For_Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( ! class_exists( 'Wallet_Orders_List' ) ) {
    /**
     * Create wallet order list
     */
    class Wallet_Orders_List extends WP_List_Table {

        /** Class constructor */
        public function __construct() {

            parent::__construct( [
                'singular' => __( 'Wallet Recharge Order', 'wallet_payment_gateway' ), //singular name of the listed records
                'plural'   => __( 'Wallet Recharge Orders', 'wallet_payment_gateway' ), //plural name of the listed records
                'ajax'     => false //should this table support ajax?

            ] );


        }


        /**
         * Render the bulk edit checkbox
         *
         * @param array $item
         *
         * @return string
         */
        public function column_cb( $item ) {
            return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
            );
        }

        /**
         * Define the columns that are going to be used in the table
         * @return array $columns, the array of columns to use with the table
         */
        public function get_columns() {
            $columns = array(
                'cb'      => '<input type="checkbox" />',
                'ID'          => __( 'Order', 'wallet_payment_gateway' ),
                'status'      => __( 'Status', 'wallet_payment_gateway' ),
                'order_total' => __( 'Total', 'wallet_payment_gateway' ),
                'date'        => __( 'Date', 'wallet_payment_gateway' )
            );
            return $columns;
        }

        /**
         * Decide which columns to activate the sorting functionality on
         * @return array $sortable, the array of columns that can be sorted by the user
         */
        public function get_sortable_columns() {
            $sortable = array(
                'ID'   => array( 'ID', true ),
                'date' => array( 'date', false ),
                'order_total' => array( 'order_total', false ),
            );
            return $sortable;
        }

        /**
         * Add all, status list link above table
         *
         * @return array
         */
        public function get_views() {
            global $wpdb;
            $views = array();
            $current = ( ! empty($_REQUEST['post_status']) ? sanitize_text_field( $_REQUEST['post_status'] ) : 'all');

            $table_name = $wpdb->prefix . 'posts';
            $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `post_type` = 'wallet_shop_order' AND ( NOT post_status = 'auto-draft' && NOT post_status = 'trash' )");
            
            //All link
            $class = ($current == 'all' ? ' class="current"' :'');
            $all_url = remove_query_arg( 'post_status' );
            $all_url = remove_query_arg( array( 'paged', 'orderby', 'order' ), $all_url );
            $views['all'] = "<a href='{$all_url }' {$class} >All<span class='count'>($rowcount)</span></a>";
         
            
            $order_statuses = wc_get_order_statuses();
            foreach ( $order_statuses as $key => $order_status ) {
                $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `post_type` = 'wallet_shop_order' AND post_status = '$key'");
                if ( $rowcount > 0 ) {
                    $url = add_query_arg( 'post_status', $key );
                    $url = remove_query_arg( array( 'paged', 'orderby', 'order' ), $url );
                    $class = ($current == $key ? ' class="current"' :'');
                    $views[$key] = "<a href='{$url}' {$class} >$order_status<span class='count'>($rowcount)</span></a>"; 
                }
                
            }

            // Trash link
           
            $rowcount1 = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `post_type` = 'wallet_shop_order' AND post_status = 'trash'");
            if ( $rowcount > 0 ) {
                $class = ($current == 'trash' ? ' class="current"' :'');
                $all_url1 = add_query_arg( 'post_status', 'trash' );
                $all_url1 = remove_query_arg( array( 'paged', 'orderby', 'order' ), $all_url1 );
                $views['trash'] = "<a href='{$all_url1}' {$class} >Trash<span class='count'>($rowcount1)</span></a>";
            }
            

            return $views;
        }
        
        /**
         * Extract custom order type data from database
         *
         * @return void
         */
        private function table_data() {      
            global $wpdb;

            $post_status = isset($_REQUEST['post_status'] ) ? sanitize_text_field( $_REQUEST['post_status'] ) : '';

            $table_name = $wpdb->prefix . 'posts';

            $data = array();

            if(isset($_GET['s'])) {
            
            $search=$_GET['s'];

            $search = trim($search);

            $orders = $wpdb->get_results("SELECT * FROM $table_name WHERE post_type = 'wallet_shop_order' AND user_id LIKE '%$search%' and column_name_four='value'");
            
            }

            else{
                if ( isset(  $post_status ) && ! empty( $post_status ) ) {
                    $orders = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `post_type` = 'wallet_shop_order' AND `post_status` =  '$post_status'");
                } else {
                    $orders = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `post_type` = 'wallet_shop_order' AND ( NOT `post_status` = 'auto-draft' && NOT `post_status` = 'trash' )");
                }
            }

            if ( ! empty( $orders ) && is_array( $orders ) ) {
				foreach ( $orders as $order ) {
                    $order_data = wc_get_order( $order->ID );
					$data[] = array(
						'ID' => $order->ID,
                        'status' => $order_data->get_status(),
                        'order_total' => $order_data->get_total(),
                        'date' => $order->post_date,
					);
				}
			}

            return $data;

        }

        /**
         * Returns an associative array containing the bulk action
         *
         * @return array
         */
        public function get_bulk_actions() {
            $actions = [
            'bulk-trash' => 'Move to Trash'
            ];
        
            return $actions;
        }

        /**
         * Undocumented function
         *
         * @return void
         */
        public function process_bulk_action() {

            //Detect when a bulk action is being triggered...
            if ( 'bulk-trash' === $this->current_action() ) {
          
              // In our file that handles the request, verify the nonce.
              $nonce = esc_attr( $_REQUEST['_wpnonce'] );
          
              if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
                die( 'Go get a life script kiddies' );
              }
              else {
                self::delete_customer( absint( $_GET['customer'] ) );
          
                wp_redirect( esc_url( add_query_arg() ) );
                exit;
              }
          
            }
          
            // If the delete bulk action is triggered
            if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
                 || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
            ) {
          
              $delete_ids = esc_sql( $_POST['bulk-delete'] );
          
              // loop over the array of record IDs and delete them
              foreach ( $delete_ids as $id ) {
                self::delete_customer( $id );
          
              }
          
              wp_redirect( esc_url( add_query_arg() ) );
              exit;
            }
        }

        /**
         * Show order in custom wp list table
         *
         * @return void
         */
        public function prepare_items()  {

            global $wpdb;  

            //Retrieve $post_status for use in query to get items.
            
            $columns = $this->get_columns();

            $sortable = $this->get_sortable_columns();

            $hidden=$this->get_hidden_columns();

            $this->process_bulk_action();

            $data = $this->table_data();
            
            $totalitems = count($data);
			

			$perpage  = 10;

            $this->_column_headers = array($columns,$hidden,$sortable); 



            function usort_reorder($a,$b){

                $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'ID'; //If no sort, default to title

                $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc

                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order

                return ($order==='asc') ? $result : -$result; //Send final sort direction to usort

            }

            usort($data, 'usort_reorder');

            $totalpages = ceil($totalitems/$perpage); 

            $currentPage = $this->get_pagenum();
            
            $data = array_slice($data,(($currentPage-1)*$perpage),$perpage);

            $this->set_pagination_args( array(

                "total_items" => $totalitems,

                "total_pages" => $totalpages,

                "per_page" => $perpage,
            ) );
                
            $this->items = $data;

        }

        /**
         * Show data in default columns
         *
         * @param array $item
         * @param string $column_name
         * @return void
         */
        public function column_default( $item, $column_name ) {

            switch( $column_name ) {
                
                case 'ID':
                    $order = wc_get_order( $item[$column_name] );
                    $first_name = $order->get_billing_first_name();
                    $last_name  = $order->get_billing_last_name();
                    if ( ! empty( $first_name ) || ! empty( $last_name ) ) {
                        $billing_name  = $order->get_billing_first_name(). ' ' .$order->get_billing_last_name();
                    } else {
                        $user_id  = $order->get_customer_id();
                        $customer = new WC_Customer( $user_id );
                        $billing_name = $customer->get_username();
                    }
                    if ( isset( $_REQUEST['post_status'] ) && 'trash' == $_REQUEST['post_status'] ) {
                        return '<strong>#' .$item[$column_name].' ' .$billing_name.'</strong>';
                    } else {

                        return '<a href="' . admin_url ( 'post.php?post=' .$item[$column_name] . ' &action=edit' ) . '" class="order-view"><strong>#' .$item[$column_name].' ' .$billing_name.'</strong></a>';
                    }
                    break;
                case 'status':
                    return '<mark class="wallet-status order-status status-'.$item[$column_name].'"><span>'.$item[$column_name].'</span></mark>';
                    break;    
                case 'order_total': 
                    return wc_price( $item[$column_name] );
                    break;
                case 'date':
                    $date = date_create( $item[$column_name] );
                    return date_format( $date, "M d, Y" );
            }

        }

        /** Text displayed when no order data is available */
        public function no_items() {
            _e( 'No order found.', 'sp' );
        }

        public function get_hidden_columns()  {
            // Setup Hidden columns and return them
            return array();
        }

    }        

}
