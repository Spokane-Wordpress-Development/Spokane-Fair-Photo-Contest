<?php

namespace SpokaneFair;

if ( ! class_exists( 'WP_List_Table' ) ) 
{
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class PhotographerTable extends \WP_List_Table {

	/**
	 * PhotographerTable constructor.
	 */
	public function __construct()
	{
		parent::__construct( array(
			'singular' => 'Photographer',
			'plural' => 'Photographers',
			'ajax' => TRUE
		) );
	}

	/**
	 * @return array
	 */
	public function get_columns()
	{
		$return = array(
			'ID' => 'ID',
			'name' => 'Name',
			'state' => 'State',
			'orders' => 'Entries Ordered',
			'paid' => 'Entries Paid For',
			'entries' => 'Submissions',
			'view' => 'View'
		);

		return $return;
	}

	/**
	 * @return array
	 */
	public function get_sortable_columns()
	{
		$return =  array(
			'ID' => array( 'u.ID', TRUE ),
			'name' => array( 'ln.last_name', TRUE ),
			'state' => array( 's.state', TRUE ),
			'orders' => array( 'orders', TRUE ),
			'paid' => array( 'paid', TRUE ),
			'entries' => array( 'entries', TRUE )
		);

		return $return;
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name )
	{
		switch( $column_name ) {
			case 'name':
				return $item->first_name . ' ' . $item->last_name . '<br><a href="mailto:' . $item->email . '">' . $item->email . '</a><br>' . $item->phone;
			case 'view':
				return '<a href="?page=' . $_REQUEST['page'] . '&action=view&id=' . $item->ID . '" class="button-primary">View</a>';
			default:
				return $item->$column_name;
		}
	}

	/**
	 *
	 */
	public function prepare_items()
	{
		global $wpdb;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$sql = "
			SELECT
				u.ID,
				u.user_email AS email,
				fn.first_name,
				ln.last_name,
				s.state,
				p.phone,
				SUM(o.entries) AS orders,
				SUM(IF(o.paid_at IS NOT NULL,o.entries,0)) AS paid,
				COUNT(DISTINCT e.id) AS entries
			FROM
				" . $wpdb->prefix . "users u
				JOIN " . $wpdb->prefix . Order::TABLE_NAME . " o
					ON u.ID = o.photographer_id
				LEFT JOIN " . $wpdb->prefix . Entry::TABLE_NAME . " e
					ON u.ID = e.photographer_id
				LEFT JOIN
				(
					SELECT
						user_id,
						meta_value AS first_name
					FROM
						" . $wpdb->prefix . "usermeta
					WHERE
						meta_key = 'first_name'
				) fn ON u.ID = fn.user_id
				LEFT JOIN
				(
					SELECT
						user_id,
						meta_value AS last_name
					FROM
						" . $wpdb->prefix . "usermeta
					WHERE
						meta_key = 'last_name'
				) ln ON u.ID = ln.user_id
				LEFT JOIN
				(
					SELECT
						user_id,
						meta_value AS phone
					FROM
						" . $wpdb->prefix . "usermeta
					WHERE
						meta_key = 'phone'
				) p ON u.ID = p.user_id
				LEFT JOIN
				(
					SELECT
						user_id,
						meta_value AS state
					FROM
						" . $wpdb->prefix . "usermeta
					WHERE
						meta_key = 'state'
				) s ON u.ID = s.user_id
			GROUP BY
				u.ID";
		if ( isset( $_GET[ 'orderby' ] ) )
		{
			foreach ( $sortable as $s )
			{
				if ( $s[ 0 ] == $_GET[ 'orderby' ] )
				{
					$sql .= "
						ORDER BY " . $_GET[ 'orderby' ] . " " . ( ( isset( $_GET['order']) && strtolower( $_GET['order'] == 'desc' ) ) ? "DESC" : "ASC" );
					break;
				}
			}
		}
		else
		{
			$sql .= "
				ORDER BY
					ln.last_name ASC,
					fn.first_name ASC";
		}

		$total_items = $wpdb->query($sql);

		$max_per_page = 50;
		$paged = ( isset( $_GET[ 'paged' ] ) && is_numeric( $_GET['paged'] ) ) ? abs( round( $_GET[ 'paged' ])) : 1;
		$total_pages = ceil( $total_items / $max_per_page );

		if ( $paged > $total_pages )
		{
			$paged = $total_pages;
		}

		$offset = ( $paged - 1 ) * $max_per_page;
		$offset = ( $offset < 0 ) ? 0 : $offset; //MySQL freaks out about LIMIT -10, 10 type stuff.

		$sql .= "
			LIMIT " . $offset . ", " . $max_per_page;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $max_per_page
		) );

		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items = $wpdb->get_results( $sql );
	}
}