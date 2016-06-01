<?php

namespace SpokaneFair;

class Entry {
	
	const TABLE_NAME = 'spokane_fair_entries';

	private $id;
	private $photographer_id;
	private $category_id;
	private $photo_post_id;
	private $title;
	private $created_at;
	private $updated_at;
	
	/** @var Photographer $photographer */
	private $photographer;
	
	/** @var Category $category */
	private $category;

	/**
	 * Entry constructor.
	 *
	 * @param null $id
	 */
	public function __construct( $id=NULL )
	{
		$this
			->setId( $id )
			->read();
	}

	public function create()
	{
		global $wpdb;

		if ( $this->photographer_id !== NULL && $this->category_id !== NULL && $this->photo_post_id !== NULL )
		{
			$this
				->setCreatedAt( time() )
				->setUpdatedAt( time() );

			$wpdb->insert(
				$wpdb->prefix . self::TABLE_NAME,
				array(
					'photographer_id' => $this->photographer_id,
					'category_id' => $this->category_id,
					'photo_post_id' => $this->photo_post_id,
					'title' => $this->title,
					'created_at' => $this->getCreatedAt( 'Y-m-d H:i:s' ),
					'updated_at' => $this->getUpdatedAt( 'Y-m-d H:i:s' )
				),
				array(
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%s'
				)
			);

			$this->setId( $wpdb->insert_id );
		}
	}

	public function read()
	{

	}

	/**
	 * @param \stdClass $row
	 */
	public function loadFromRow( \stdClass $row )
	{
		$this
			->setId( $row->id )
			->setPhotographerId( $row->photographer_id )
			->setCategoryId( $row->category_id )
			->setPhotoPostId( $row->photo_post_id )
			->setTitle( $row->title )
			->setCreatedAt( $row->created_at )
			->setUpdatedAt( $row->updated_at );
	}

	public function update()
	{

	}

	public function delete()
	{

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
	 * @return Entry
	 */
	public function setId( $id )
	{
		$this->id = ( is_numeric( $id ) ) ? intval( $id ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPhotographerId()
	{
		return $this->photographer_id;
	}

	/**
	 * @param mixed $photographer_id
	 *
	 * @return Entry
	 */
	public function setPhotographerId( $photographer_id )
	{
		$this->photographer_id = ( is_numeric( $photographer_id ) ) ? intval( $photographer_id ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getCategoryId()
	{
		return $this->category_id;
	}

	/**
	 * @param mixed $category_id
	 *
	 * @return Entry
	 */
	public function setCategoryId( $category_id )
	{
		$this->category_id = ( is_numeric( $category_id ) ) ? intval( $category_id ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPhotoPostId()
	{
		return $this->photo_post_id;
	}

	/**
	 * @param mixed $photo_post_id
	 *
	 * @return Entry
	 */
	public function setPhotoPostId( $photo_post_id )
	{
		$this->photo_post_id = ( is_numeric( $photo_post_id ) ) ? intval( $photo_post_id ) : NULL;

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
	 * @return Entry
	 */
	public function setTitle( $title )
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @param null $format
	 *
	 * @return bool|string
	 */
	public function getCreatedAt( $format=NULL )
	{
		if ( $format === NULL )
		{
			return $this->created_at;
		}

		return ( $this->created_at === NULL ) ? '' : date( $format, $this->created_at );
	}

	/**
	 * @param mixed $created_at
	 *
	 * @return Entry
	 */
	public function setCreatedAt( $created_at )
	{
		$this->created_at = ( $created_at === NULL || is_numeric( $created_at ) ) ? $created_at : strtotime( $created_at );

		return $this;
	}

	/**
	 * @param null $format
	 *
	 * @return bool|string
	 */
	public function getUpdatedAt( $format=NULL )
	{
		if ( $format === NULL )
		{
			return $this->updated_at;
		}

		return ( $this->updated_at === NULL ) ? '' : date( $format, $this->updated_at );
	}

	/**
	 * @param mixed $updated_at
	 *
	 * @return Entry
	 */
	public function setUpdatedAt( $updated_at )
	{
		$this->updated_at = ( $updated_at === NULL || is_numeric( $updated_at ) ) ? $updated_at : strtotime( $updated_at );

		return $this;
	}

	/**
	 * @return Photographer
	 */
	public function getPhotographer()
	{
		return $this->photographer;
	}

	/**
	 * @param Photographer $photographer
	 *
	 * @return Entry
	 */
	public function setPhotographer( $photographer )
	{
		$this->photographer = $photographer;

		return $this;
	}

	/**
	 * @return Category
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @param Category $category
	 *
	 * @return Entry
	 */
	public function setCategory( $category )
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * @param int $photographer_id
	 *
	 * @return Entry[]
	 */
	public static function getPhotographerEntries( $photographer_id )
	{
		global $wpdb;
		$photographer_id = ( is_numeric( $photographer_id ) ) ? intval( $photographer_id ) : 0;
		$entries = array();

		$sql = $wpdb->prepare("
			SELECT
				*
			FROM
				" . $wpdb->prefix . self::TABLE_NAME . "
			WHERE
				photographer_id = %d",
			$photographer_id
		);

		$rows = $wpdb->get_results( $sql );
		foreach( $rows as $row )
		{
			$entry = new Entry;
			$entry->loadFromRow( $row );
			$entries[ $entry->getId() ] = $entry;
		}

		return $entries;
	}
}