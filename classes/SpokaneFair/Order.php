<?php

namespace SpokaneFair;

class Order {

	const TABLE_NAME = 'spokane_fair_orders';

	private $id;
	private $photographer_id;
	private $amount;
	private $entries;
	private $created_at;
	private $paid_at;

	public function create()
	{
		global $wpdb;

		$this->setCreatedAt( time() );

		$wpdb->insert(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'photographer_id' => $this->photographer_id,
				'amount' => $this->amount,
				'entries' => $this->entries,
				'created_at' => $this->getCreatedAt( 'Y-m-d H:i:s' ),
				'paid_at' => ( $this->paid_at === NULL ) ? NULL : $this->getPaidAt( 'Y-m-d H:i:s' )
			),
			array(
				'%d',
				'%d',
				'%f',
				'%s',
				'%s'
			)
		);

		$this->setId( $wpdb->insert_id );
	}

	public function read()
	{

	}
	
	public function loadFromRow( \stdClass $row )
	{
		$this
			->setId( $row->id )
			->setPhotographerId( $row->photographer_id )
			->setAmount( $row->amount )
			->setEntries( $row->entries )
			->setCreatedAt( $row->created_at )
			->setPaidAt( $row->paid_at );
	}

	public function update()
	{
		global $wpdb;

		if ( $this->id !== NULL )
		{
			$wpdb->update(
				$wpdb->prefix . self::TABLE_NAME,
				array(
					'photographer_id' => $this->photographer_id,
					'amount' => $this->amount,
					'entries' => $this->entries,
					'created_at' => $this->getCreatedAt( 'Y-m-d H:i:s' ),
					'paid_at' => ( $this->paid_at === NULL ) ? NULL : $this->getPaidAt( 'Y-m-d H:i:s' )
				),
				array(
					'id' => $this->id
				),
				array(
					'%d',
					'%d',
					'%f',
					'%s',
					'%s'
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
	 * @return Order
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
	 * @return Order
	 */
	public function setPaidAt( $paid_at )
	{
		$this->paid_at = ( $paid_at === NULL || is_numeric( $paid_at ) ) ? $paid_at : strtotime( $paid_at );

		return $this;
	}

	public function getOrderNumber()
	{
		return str_pad( $this->getPhotographerId(), 4, '0', STR_PAD_LEFT ) . '_' . str_pad( $this->getId(), 4, '0', STR_PAD_LEFT );
	}

	/**
	 * @param int $photographer_id
	 *
	 * @return Order[]
	 */
	public static function getPhotographerOrders( $photographer_id )
	{
		global $wpdb;
		$photographer_id = ( is_numeric( $photographer_id ) ) ? intval( $photographer_id ) : 0;
		$orders = array();
		
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
			$order = new Order;
			$order->loadFromRow( $row );
			$orders[ $order->getId() ] = $order;
		}

		return $orders;
	}

	/**
	 * @return Order[]
	 */
	public static function getAllOrders()
	{
		global $wpdb;
		$orders = array();

		$sql = "
			SELECT
				*
			FROM
				" . $wpdb->prefix . self::TABLE_NAME;

		$rows = $wpdb->get_results( $sql );
		foreach( $rows as $row )
		{
			$order = new Order;
			$order->loadFromRow( $row );
			$orders[ $order->getId() ] = $order;
		}

		return $orders;
	}
}