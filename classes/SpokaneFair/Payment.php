<?php

namespace SpokaneFair;

class Payment {

	private $id;
	private $amount;
	private $entries;
	private $payment_code;
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
	 * @return Payment
	 */
	public function setId( $id )
	{
		$this->id = ( is_numeric( $id ) ) ? intval( $id ) : NULL;

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
	 * @return Payment
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
	 * @return Payment
	 */
	public function setEntries( $entries )
	{
		$this->entries = ( is_numeric( $entries ) ) ? intval( $entries ) : NULL;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentCode()
	{
		return ( $this->payment_code === NULL ) ? '' : $this->payment_code;
	}

	/**
	 * @param mixed $payment_code
	 *
	 * @return Payment
	 */
	public function setPaymentCode( $payment_code )
	{
		$this->payment_code = $payment_code;

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