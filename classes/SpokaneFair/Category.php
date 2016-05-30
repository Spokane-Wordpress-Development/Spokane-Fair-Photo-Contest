<?php

namespace SpokaneFair;

class Category {

	private $id;
	private $code;
	private $title;

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


}