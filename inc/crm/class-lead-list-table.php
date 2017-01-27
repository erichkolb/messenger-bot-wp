<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

use GigaAI\Storage\Eloquent\Lead as Lead;

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Lead_List_Table extends \WP_List_Table
{
	public $leads = [ ];

	public $per_page = 20;

	public $total = 0;

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items()
	{
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$currentPage = $this->get_pagenum();

		$this->total = Lead::count();
		$data        = Lead::take( $this->per_page )->skip( ( $currentPage - 1 ) * $this->per_page )
		                   ->get()->toArray();

		$this->set_pagination_args( [
			'total_items' => $this->total,
			'per_page'    => $this->per_page
		] );

		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return array
	 */
	public function get_columns()
	{
		return [
			'name'       => __( 'Name', 'giga-messenger-bots' ),
			'email'      => __( 'Email', 'giga-messenger-bots' ),
			'phone'      => __( 'Phone', 'giga-messenger-bots' ),
			'subscribe'  => __( 'Subscribe', 'giga-messenger-bots' ),
            'auto_stop'  => __( 'Status', 'giga-messenger-bots' ),
			'created_at' => __( 'Created At', 'giga-messenger-bots' ),
		];
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return array
	 */
	public function get_hidden_columns()
	{
		return [ ];
	}

	/**
	 * Define the sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns()
	{
		return [
			'name' => [ 'name', false ]
		];
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  array $item Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name )
	{
		if ( $column_name === 'name' ) {
			$name = $item['first_name'] . ' ' . $item['last_name'];

			$redirect_to = add_query_arg( [ 'view' => 'lead', 'lead_id' => $item['user_id'] ] );

			return "<img src='{$item['profile_pic']}' alt='Profile Pic' width='32' height='32' class='avatar avatar-32 photo'> 
                    <strong><a href='{$redirect_to}'>{$name}</a></strong>
            ";
		}
		if ( $column_name === 'subscribe' ) {
			$item[ $column_name ] = $item[ $column_name ] == false ?
                '<span class="light light-error" title="' . __('No', 'giga-messenger-bots') . '"></span>' :
                '<span class="light light-active" title="' . __('Yes', 'giga-messenger-bots') . '"></span>';
		}
		
		if ($column_name === 'auto_stop') {
		    $item[$column_name] = ! empty($item[$column_name]) ?
                '<span class="light light-error" title="' . __('Waiting Answers from Page Administrators', 'giga-messenger-bots') . '"></span>' :
                '<span class="light light-active" title="' . __('Active', 'giga-messenger-bots') . '"></span>';
        }

		return $item[ $column_name ];
	}
}