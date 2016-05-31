<?php

namespace SpokaneFair;

class Order {

	private $id;
	private $photographer_id;
	private $amount;
	private $entries;
	private $created_at;
	private $paid_at;

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
	 * @return Order
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
	 * @return Order
	 */
	public function setPhotographerId( $photographer_id )
	{
		$this->photographer_id = ( is_numeric( $photographer_id ) ) ? intval( $photographer_id ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAmount()
	{
		return ( $this->amount === NULL ) ? 0 : $this->amount;
	}

	/**
	 * @param mixed $amount
	 *
	 * @return Order
	 */
	public function setAmount( $amount )
	{
		$this->amount = ( is_numeric( $amount ) ) ? abs( round( $amount, 2 ) ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEntries()
	{
		return ( $this->entries === NULL ) ? 0 : $this->entries;
	}

	/**
	 * @param mixed $entries
	 *
	 * @return Order
	 */
	public function setEntries( $entries )
	{
		$this->entries = ( is_numeric( $entries ) ) ? intval( $entries ) : NULL;

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
	public function getPaidAt( $format=NULL )
	{
		if ( $format === NULL )
		{
			return $this->paid_at;
		}

		return ( $this->paid_at === NULL ) ? '' : date( $format, $this->paid_at );
	}

	/**
	 * @param mixed $paid_at
	 *
	 * @return Entry
	 */
	public function setPaidAt( $paid_at )
	{
		$this->paid_at = ( $paid_at === NULL || is_numeric( $paid_at ) ) ? $paid_at : strtotime( $paid_at );

		return $this;
	}
}