<?php

namespace SpokaneFair;

class Photographer {

	private $id;
	private $username;
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
	 * Photographer constructor.
	 *
	 * @param null $id
	 */
	public function __construct( $id=NULL )
	{
		$this
			->setId( $id )
			->read();
	}

	public function read()
	{
		if ( $this->id !== NULL )
		{
			if ( $user = get_user_by( 'ID', $this->id ) )
			{
				$this
					->setId( $user->ID )
					->setUsername( $user->user_login )
					->setFirstName( $user->user_firstname )
					->setLastName( $user->user_lastname )
					->setEmail( $user->user_email )
					->setState( get_user_meta( $user->ID, 'state', TRUE ) )
					->setPhone( get_user_meta( $user->ID, 'phone', TRUE ) )
					->loadEntries()
					->loadOrders();
			}
			else
			{
				$this->setId( NULL );
			}
		}
	}

	public function update()
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
	public function getUsername()
	{
		return ( $this->username === NULL ) ? '' : $this->username;
	}

	/**
	 * @param mixed $username
	 *
	 * @return Photographer
	 */
	public function setUsername( $username )
	{
		$this->username = $username;

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
	 * @param bool $url_format
	 *
	 * @return mixed|string
	 */
	public function getFullName( $url_format=FALSE )
	{
		$full_name = trim( $this->getFirstName() . ' ' . $this->getLastName() );

		if ( $url_format )
		{
			$full_name = str_replace( ' ', '-', $full_name );

			$patterns = array();
			$replacements = array();

			$patterns[1] = '/[ ]/';
			$patterns[0] = '/[^a-zA-Z0-9-]/';
			$replacements[0] = '-';
			$replacements[1] = '';

			$full_name = preg_replace( $patterns, $replacements, $full_name );
		}

		return trim( $full_name );
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return ( $this->email === NULL ) ? '' : $this->email;
	}

	public function getFullNameOrEmail()
    {
        return ( strlen( $this->getFullName() ) == 0 ) ? $this->getEmail() : $this->getFullName();
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
	public function getEntriesOrderedCount()
	{
		$count = 0;

		foreach ( $this->getOrders() as $order )
		{
			$count += $order->getEntries();
		}
		
		return $count;
	}

	/**
	 * @return int|mixed
	 */
	public function getPaidEntryCount()
	{
		$count = 0;

		foreach ( $this->getOrders() as $order )
		{
			if ( $order->getPaidAt() !== NULL )
			{
				$count += $order->getEntries();
			}
		}

		return $count;
	}

	/**
	 * @return int|mixed
	 */
	public function getUnpaidEntryCount()
	{
		return $this->getEntriesOrderedCount() - $this->getPaidEntryCount();
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
		return $this->getPaidEntries() + $this->getFreeEntries() - $this->getEntriesUsedCount();
	}

	/**
	 * @return int|mixed
	 */
	public function getPurchasedEntries()
	{
		$count = 0;

		foreach ( $this->getOrders() as $order )
		{
			$count += $order->getPurchasedEntries();
		}

		return $count;
	}

	public function getPaidEntries()
	{
		$count = 0;

		foreach ( $this->getOrders() as $order )
		{
			if ( $order->getPaidAt() !== NULL )
			{
				$count += $order->getPurchasedEntries();
			}
		}

		return $count;

	}

	/**
	 * @return int|mixed
	 */
	public function getFreeEntries()
	{
		$count = 0;

		foreach ( $this->getOrders() as $order )
		{
			$count += $order->getFreeEntries();
		}

		return $count;
	}

	/**
	 * @return $this
	 */
	public function loadEntries()
	{
		if ( $this->id !== NULL )
		{
			$this->entries = Entry::getPhotographerEntries( $this->id );
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	public function loadOrders()
	{
		if ( $this->id !== NULL )
		{
			$this->orders = Order::getPhotographerOrders( $this->id );
		}

		return $this;
	}

	/**
	 * @return null|Photographer
	 */
	public static function load_from_user()
	{
		if ( is_user_logged_in() )
		{
			$current_user = wp_get_current_user();

			$photographer = new Photographer;
			$photographer
				->setId( $current_user->ID )
				->setUsername( $current_user->user_login )
				->setFirstName( $current_user->user_firstname )
				->setLastName( $current_user->user_lastname )
				->setEmail( $current_user->user_email )
				->setState( get_user_meta ( $current_user->ID, 'state', TRUE ) )
				->setPhone( get_user_meta ( $current_user->ID, 'phone', TRUE ) )
				->loadEntries()
				->loadOrders();

			return $photographer;
		}

		return NULL;
	}

	/**
	 * @param $id
	 */
	public function deleteOrder( $id )
	{
		foreach( $this->getOrders() as $order )
		{
			if ( $order->getId() == $id )
			{
				$order->delete();
				unset( $this->orders[ $id ] );
			}
		}
	}

	/**
	 * @param $id
	 */
	public function deleteEntry( $id )
	{
		foreach( $this->getEntries() as $entry )
		{
			if ( $entry->getId() == $id )
			{
				$entry->delete();
				unset( $this->entries[ $id ] );
			}
		}
	}
}