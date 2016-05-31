<?php

namespace SpokaneFair;

class Photographer {

	private $id;
	private $first_name;
	private $last_name;
	private $email;
	private $phone;
	private $state;

	/** @var Entry[] $entries */
	private $entries;

	/** @var Order[] $orders */
	private $orders;

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
	 * @return Photographer
	 */
	public function setId( $id )
	{
		$this->id = ( is_numeric( $id ) ) ? intval( $id ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFirstName()
	{
		return ( $this->first_name === NULL ) ? '' : $this->first_name;
	}

	/**
	 * @param mixed $first_name
	 *
	 * @return Photographer
	 */
	public function setFirstName( $first_name )
	{
		$this->first_name = $first_name;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLastName()
	{
		return ( $this->last_name === NULL ) ? '' : $this->last_name;
	}

	/**
	 * @param mixed $last_name
	 *
	 * @return Photographer
	 */
	public function setLastName( $last_name )
	{
		$this->last_name = $last_name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return trim( $this->getFirstName() . ' ' . $this->getLastName() );
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return ( $this->email === NULL ) ? '' : $this->email;
	}

	/**
	 * @param mixed $email
	 *
	 * @return Photographer
	 */
	public function setEmail( $email )
	{
		$this->email = ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) ? $email : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPhone()
	{
		return( $this->phone === NULL) ? '' : $this->phone;
	}

	/**
	 * @param mixed $phone
	 *
	 * @return Photographer
	 */
	public function setPhone( $phone )
	{
		$this->phone = $phone;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getState()
	{
		return ( $this->state === NULL ) ? '' : $this->state;
	}

	/**
	 * @param mixed $state
	 *
	 * @return Photographer
	 */
	public function setState( $state )
	{
		$this->state = $state;

		return $this;
	}

	/**
	 * @return Entry[]
	 */
	public function getEntries()
	{
		return ( $this->entries === NULL ) ? array() : $this->entries;
	}

	/**
	 * @param Entry[] $entries
	 *
	 * @return Photographer
	 */
	public function setEntries( $entries )
	{
		$this->entries = $entries;

		return $this;
	}

	/**
	 * @param Entry $entry
	 *
	 * @return $this
	 */
	public function addEntry( Entry $entry )
	{
		if ( $this->entries === NULL )
		{
			$this->entries = array();
		}

		$this->entries[ $entry->getId() ] = $entry;

		return $this;
	}

	/**
	 * @return Order[]
	 */
	public function getOrders()
	{
		return ( $this->orders === NULL ) ? array() : $this->orders;
	}

	/**
	 * @param Order[] $orders
	 *
	 * @return Photographer
	 */
	public function setOrders( $orders )
	{
		$this->orders = $orders;

		return $this;
	}

	/**
	 * @param Order $order
	 *
	 * @return $this
	 */
	public function addOrder( $order )
	{
		if ( $this->orders === NULL )
		{
			$this->orders = array();
		}

		$this->orders[ $order->getId() ] = $order;

		return $this;
	}

	/**
	 * @return int|mixed
	 */
	public function getEntriesPaidForCount()
	{
		$count = 0;

		foreach ( $this->getOrders() as $order )
		{
			$count += $order->getEntries();
		}
		
		return $count;
	}

	/**
	 * @return int
	 */
	public function getEntriesUsedCount()
	{
		return count( $this->getEntries() );
	}

	/**
	 * @return int|mixed
	 */
	public function getEntriesLeftCount()
	{
		return $this->getEntriesPaidForCount() - $this->getEntriesUsedCount();
	}

}