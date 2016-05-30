<?php

namespace SpokaneFair;

class Entry {

	private $id;
	private $photographer_id;
	private $code;
	private $title;
	private $created_at;

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
}