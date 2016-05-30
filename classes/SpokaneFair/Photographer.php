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

	/** @var Payment[] $payments */
	private $payments;

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
	 * @return Payment[]
	 */
	public function getPayments()
	{
		return ( $this->payments === NULL ) ? array() : $this->payments;
	}

	/**
	 * @param Payment[] $payments
	 *
	 * @return Photographer
	 */
	public function setPayments( $payments )
	{
		$this->payments = $payments;

		return $this;
	}

	/**
	 * @param Payment $payment
	 *
	 * @return $this
	 */
	public function addPayment( Payment $payment )
	{
		if ( $this->payments === NULL )
		{
			$this->payments = array();
		}

		$this->payments[ $payment->getId() ] = $payment;

		return $this;
	}

	/**
	 * @return int|mixed
	 */
	public function getEntriesPaidForCount()
	{
		$count = 0;

		foreach ( $this->getPayments() as $payment )
		{
			$count += $payment->getEntries();
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