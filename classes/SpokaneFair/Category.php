<?php

namespace SpokaneFair;

class Category {

	const TABLE_NAME = 'spokane_fair_categories';
	
	private $id;
	private $code;
	private $title;
	private $is_visible = FALSE;
	private $entry_count;

	/**
	 * Category constructor.
	 *
	 * @param null $id
	 */
	public function __construct( $id=NULL )
	{
		$this
			->setId( $id )
			->read();
	}

	/**
	 *
	 */
	public function create()
	{
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'code' => $this->code,
				'title' => $this->title,
				'is_visible' => ( $this->isVisible() ) ? 1 : 0
			),
			array(
				'%s',
				'%s',
				'%d'
			)
		);

		$this->setId( $wpdb->insert_id );
	}

	/**
	 *
	 */
	public function read()
	{
		global $wpdb;

		if ( $this->id !== NULL )
		{
			$sql = $wpdb->prepare("
				SELECT
					COUNT(e.id) AS entry_count,
					c.*
				FROM
					" . $wpdb->prefix . self::TABLE_NAME . " c
					LEFT JOIN " . $wpdb->prefix . Entry::TABLE_NAME . " e
						ON c.id = e.category_id
				WHERE
					c.id = %d
				GROUP BY
					c.id",
				$this->id
			);

			if ( $row = $wpdb->get_row( $sql ) )
			{
				$this->loadFromRow( $row );
			}
			else
			{
				$this->setId( NULL );
			}
		}
	}

	/**
	 * @param \stdClass $row
	 */
	public function loadFromRow( \stdClass $row )
	{
		$this
			->setId( $row->id )
			->setCode( $row->code )
			->setTitle( $row->title )
			->setIsVisible( $row->is_visible )
			->setEntryCount( $row->entry_count );
	}

	public function update()
	{
		global $wpdb;

		if ( $this->id !== NULL )
		{
			$wpdb->update(
				$wpdb->prefix . self::TABLE_NAME,
				array(
					'code' => $this->code,
					'title' => $this->title,
					'is_visible' => ( $this->isVisible() ) ? 1 : 0
				),
				array(
					'id' => $this->id
				),
				array(
					'%s',
					'%s',
					'%d'
				),
				array(
					'%d'
				)
			);
		}
	}

	public function delete()
	{
		global $wpdb;

		if ( $this->id !== NULL )
		{
			$wpdb->delete(
				$wpdb->prefix . self::TABLE_NAME,
				array(
					'id' => $this->id
				),
				array(
					'%d'
				)
			);

			$this->setId( NULL );
		}
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 *
	 * @return Category
	 */
	public function setId( $id )
	{
		$this->id = ( is_numeric( $id ) ) ? intval( $id ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCode()
	{
		return ( $this->code === NULL ) ? '' : $this->code;
	}

	/**
	 * @param mixed $code
	 *
	 * @return Category
	 */
	public function setCode( $code )
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return ( $this->title === NULL ) ? '' : $this->title;
	}

	/**
	 * @param mixed $title
	 *
	 * @return Category
	 */
	public function setTitle( $title )
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isVisible()
	{
		return ( $this->is_visible === TRUE );
	}

	/**
	 * @param boolean $is_visible
	 *
	 * @return Category
	 */
	public function setIsVisible( $is_visible )
	{
		$this->is_visible = ( $is_visible === TRUE || $is_visible == 1 );

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEntryCount()
	{
		return ( $this->entry_count === NULL ) ? 0 : $this->entry_count;
	}

	/**
	 * @param mixed $entry_count
	 *
	 * @return Category
	 */
	public function setEntryCount( $entry_count )
	{
		$this->entry_count = ( is_numeric( $entry_count ) ) ? intval( abs( $entry_count ) ) : NULL;

		return $this;
	}

	/**
	 * @return Category[]
	 */
	public static function getAllVisibleCategories()
	{
		global $wpdb;
		$categories = array();

		$sql = "
			SELECT
				COUNT(e.id) AS entry_count,
				c.*
			FROM
				" . $wpdb->prefix . self::TABLE_NAME . " c
				LEFT JOIN " . $wpdb->prefix . Entry::TABLE_NAME . " e
					ON c.id = e.category_id
			WHERE
				c.is_visible = 1
			GROUP BY
				c.id
			ORDER BY
				c.title";

		$rows = $wpdb->get_results( $sql );
		foreach( $rows as $row )
		{
			$category = new Category;
			$category->loadFromRow( $row );
			$categories[ $category->getId() ] = $category;
		}
		
		return $categories;
	}
}