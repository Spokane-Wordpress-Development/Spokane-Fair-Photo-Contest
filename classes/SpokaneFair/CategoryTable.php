<?php

namespace SpokaneFair;

if ( ! class_exists( 'WP_List_Table' ) ) 
{
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CategoryTable extends \WP_List_Table {

	/**
	 * CategoryTable constructor.
	 */
	public function __construct()
	{
		parent::__construct( array(
			'singular' => 'Category',
			'plural' => 'Categories',
			'ajax' => TRUE
		) );
	}

	/**
	 * @return array
	 */
	public function get_columns()
	{
		$return = array(
			'code' => 'Code',
			'title' => 'Title',
			'is_visible' => 'Visible',
			'entry_count' => 'Entries',
			'edit' => 'Edit'
		);

		return $return;
	}

	/**
	 * @return array
	 */
	public function get_sortable_columns()
	{
		$return =  array(
			'code' => array( 'code', TRUE ),
			'title' => array( 'title', TRUE ),
			'is_visible' => array( 'is_visible', TRUE ),
			'entry_count' => array( 'entry_count', TRUE )
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
			case 'code':
			case 'title':
				return $item->$column_name;
			case 'entry_count':
				if ( $item->$column_name == 0 )
				{
					return '';
				}
				else
				{
					return '<a href="?page=spokane_fair_submissions&category_code='. $item->code .'">' . $item->$column_name . '</a>';
				}
			case 'is_visible':
				return ( $item->is_visible == 1 ) ? 'Yes' : 'No';
			case 'edit':
				return '<a href="?page=' . $_REQUEST['page'] . '&action=edit&id=' . $item->id . '" class="button-primary">Edit</a>';
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
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
				COUNT(e.id) AS entry_count,
				c.*
			FROM
				" . $wpdb->prefix . Category::TABLE_NAME . " c
				LEFT JOIN " . $wpdb->prefix . Entry::TABLE_NAME . " e
					ON c.id = e.category_id
			WHERE
			    e.photographer_id IS NULL
			    OR
			    (
			        e.photographer_id IS NOT NULL 
			        AND e.photographer_id IN ( SELECT DISTINCT photographer_id FROM " . $wpdb->prefix . Order::TABLE_NAME . " )
			    )
			GROUP BY
				c.id";
		if ( isset( $_GET[ 'orderby' ] ) )
		{
			foreach ( $sortable as $s )
			{
				if ( $s[ 0 ] == $_GET[ 'orderby' ] )
				{
					$sql .= "
						ORDER BY c." . $_GET[ 'orderby' ] . " " . ( ( isset( $_GET['order']) && strtolower( $_GET['order'] == 'desc' ) ) ? "DESC" : "ASC" );
					break;
				}
			}
		}
		else
		{
			$sql .= "
				ORDER BY c.id DESC";
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