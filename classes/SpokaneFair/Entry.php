<?php

namespace SpokaneFair;

class Entry {
	
	const TABLE_NAME = 'spokane_fair_entries';

	private $id;
	private $photographer_id;
	private $category_id;
	private $code;
	private $title;
	private $created_at;
	
	/** @var Photographer $photographer */
	private $photographer;
	
	/** @var Category $category */
	private $category;

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
	public function getCode()
	{
		return ( $this->code === NULL ) ? '' : $this->code;
	}

	/**
	 * @param mixed $code
	 *
	 * @return Entry
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
}