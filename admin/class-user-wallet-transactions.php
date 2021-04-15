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

if ( ! class_exists( 'Wallet_Transactions_List' ) ) {
    /**
     * Create wallet transaction list
     */
    class Wallet_Transactions_List extends WP_List_Table {

        /** Class constructor */
        public function __construct() {

            parent::__construct( [
                'singular' => __( 'User Wallet Transaction', 'wallet_payment_gateway' ), //singular name of the listed records
                'plural'   => __( 'User Wallet Transactions', 'wallet_payment_gateway' ), //plural name of the listed records
                'ajax'     => false //should this table support ajax?

            ] );


        }

        /**
         * Define the columns that are going to be used in the table
         * @return array $columns, the array of columns to use with the table
         */
        public function get_columns() {
            $columns = array(
                'transaction_id' => __( 'Transaction ID', 'wallet_payment_gateway' ),
                'name'           => __( 'Name', 'wallet_payment_gateway' ),
                'email'          => __( 'Email', 'wallet_payment_gateway' ),
                'amount'         => __( 'Amount', 'wallet_payment_gateway' ),
                'action'         => __( 'Action', 'wallet_payment_gateway' ),
                'method'         => __( 'Method', 'wallet_payment_gateway' ),
                'date'           => __( 'Date', 'wallet_payment_gateway' )
            );
            return $columns;
        }

        /**
         * Decide which columns to activate the sorting functionality on
         * @return array $sortable, the array of columns that can be sorted by the user
         */
        public function get_sortable_columns() {
            $sortable = array(
                'transaction_id' => 'Id',
                'name'           => 'user_id',
                'amount'         => 'amount',
                'date'           => 'date'
            );
            return $sortable;
        }


        private function table_data() {      
            global $wpdb;

            $table_name = $wpdb->prefix . 'mwb_wsfw_wallet_transaction';

            $data=array();

            if(isset($_GET['s']))
            {
            
            $search=$_GET['s'];

            $search = trim($search);

            $transactions = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id LIKE '%$search%' and column_name_four='value'");

            }

            else{
                $transactions = $wpdb->get_results("SELECT * FROM $table_name ");
            }

            if ( ! empty( $transactions ) && is_array($transactions ) ) {
				foreach ( $transactions as $transaction ) {
                    $user = get_user_by( 'id', $transaction->Id );
					$data[] = array(
						'transaction_id' => $transaction->Id,
						'name'           => $user->user_login,
						'email'          => $user->user_email,
						'amount'         => wc_price( $transaction->amount ),
						'action'         => $transaction->transaction_type,
						'method'         => $transaction->payment_method,
                        'date'           => $transaction->date,
					);
				}
			}

            return $data;

        }

        public function prepare_items()  {

            global $wpdb;  

            $columns = $this->get_columns();

            $sortable = $this->get_sortable_columns();

            $hidden=$this->get_hidden_columns();

            $this->process_bulk_action();

            $data = $this->table_data();
            
            $totalitems = count($data);
           
            $user   = get_current_user_id();
			$screen = get_current_screen();
			

			$perpage = 10;

            $this->_column_headers = array($columns,$hidden,$sortable); 



            function usort_reorder($a,$b){

                $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'Id'; //If no sort, default to title

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
                
            $this->items =$data;
        }

        public function column_default( $item, $column_name ) {

            switch( $column_name ) {
                
                case 'transaction_id':

                case 'name':

                case 'email':

                case 'amount':  

                case 'action':  
                    
                case 'method':  

                case 'date':
                    return $item[$column_name];
            }

        }

        public function get_hidden_columns()  {
            // Setup Hidden columns and return them
            return array();
            }
        }

}
