<?php

namespace SpokaneFair;

class Controller {
	
	const VERSION = '1.1.0';
	const VERSION_JS = '1.1.1';
	const VERSION_CSS = '1.1.0';

	const IMG_THUMB = 'spokane-fair-thumb';
	const IMG_FULL_LANDSCAPE = 'spokane-fair-full';
	const IMG_FULL_PORTRAIT = 'spokane-fair-full-landscape';

	private $errors;
	
	/** @var Photographer $photographer */
	private $photographer;

	/**
	 * @return mixed
	 */
	public function getErrors()
	{
		return ( $this->errors === NULL ) ? array() : $this->errors;
	}

	/**
	 * @param $error
	 *
	 * @return $this
	 */
	public function addError( $error )
	{
		if( $this->errors === NULL )
		{
			$this->errors = array();
		}
		
		$this->errors[] = $error;
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
	 * @return Controller
	 */
	public function setPhotographer( $photographer )
	{
		$this->photographer = $photographer;

		return $this;
	}

	public function activate()
	{
		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		global $wpdb;

		/* create tables */
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) )
		{
			$charset_collate .= " DEFAULT CHARACTER SET " . $wpdb->charset;
		}
		if ( ! empty( $wpdb->collate ) )
		{
			$charset_collate .= " COLLATE " . $wpdb->collate;
		}

		/* categories table */
		$table = $wpdb->prefix . Category::TABLE_NAME;
		if( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) != $table ) {
			$sql = "
				CREATE TABLE `" . $table . "`
				(
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`code` VARCHAR(50) DEFAULT NULL,
					`title` VARCHAR(50) DEFAULT NULL,
					`is_visible` TINYINT(4) DEFAULT NULL,
					PRIMARY KEY (`id`)
				)";
			$sql .= $charset_collate . ";"; // new line to avoid PHP Storm syntax error
			dbDelta( $sql );
		}

		/* entries table */
		$table = $wpdb->prefix . Entry::TABLE_NAME;
		$sql = "CREATE TABLE " . $table . " (
					id INT(11) NOT NULL AUTO_INCREMENT,
					random_code INT(11) DEFAULT NULL,
					photographer_id INT(11) DEFAULT NULL,
					category_id INT(11) DEFAULT NULL,
					photo_post_id INT(11) DEFAULT NULL,
					title VARCHAR(50) DEFAULT NULL,
					created_at DATETIME DEFAULT NULL,
					updated_at DATETIME DEFAULT NULL,
					PRIMARY KEY  (id),
					KEY photographer_id (photographer_id),
					KEY category_id (category_id)
				)";
		$sql .= $charset_collate . ";"; // new line to avoid PHP Storm syntax error
		dbDelta( $sql );

		/* orders table */
		$table = $wpdb->prefix . Order::TABLE_NAME;
		$sql = "CREATE TABLE " . $table . " (
					id INT(11) NOT NULL AUTO_INCREMENT,
					photographer_id INT(11) DEFAULT NULL,
					amount DECIMAL(11,2) DEFAULT NULL,
					entries INT(11) DEFAULT NULL,
					purchased_entries INT(11) DEFAULT NULL,
					free_entries INT(11) DEFAULT NULL,
					created_at DATETIME DEFAULT NULL,
					paid_at DATETIME DEFAULT NULL,
					PRIMARY KEY  (id),
					KEY photographer_id (photographer_id)
				)";
		$sql .= $charset_collate . ";"; // new line to avoid PHP Storm syntax error
		dbDelta( $sql );
	}

	public function init()
	{
		wp_enqueue_media();
		add_thickbox();
		wp_enqueue_style( 'spokane-fair-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), ( WP_DEBUG ) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_script( 'spokane-fair-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/spokane-fair.js', array( 'jquery' ), ( WP_DEBUG ) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'spokane-fair-bootstrap-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/bootstrap.css', array(), ( WP_DEBUG ) ? time() : self::VERSION_CSS );
		wp_enqueue_style( 'spokane-fair-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/spokane-fair.css', array(), ( WP_DEBUG ) ? time() : self::VERSION_CSS );

		add_image_size( self::IMG_THUMB , 200, 200 );
		add_image_size( self::IMG_FULL_LANDSCAPE , 1920, 1080 );
		add_image_size( self::IMG_FULL_PORTRAIT , 720, 1080 );

		if ( $this->photographer === NULL )
		{
			$this->photographer = Photographer::load_from_user();
		}
	}
	
	public function form_capture()
	{
		if ( isset( $_POST['spokane_fair_action'] ) )
		{
			if ( isset( $_POST['spokane_fair_nonce'] ) && wp_verify_nonce( $_POST['spokane_fair_nonce'], 'spokane_fair_' . $_POST['spokane_fair_action'] ) )
			{
				switch ( $_POST['spokane_fair_action'] )
				{
					case 'register':

						if ( empty( $_POST['username'] ) || empty( $_POST['password'] ) || empty( $_POST['email'] ) || empty( $_POST['fname'] ) || empty( $_POST['lname'] ) || empty( $_POST['state'] ) || empty( $_POST['phone'] ) )
						{
							$this->addError( 'Please fill out all required fields' );
						}

						elseif ( 4 > strlen( $_POST['username'] ) )
						{
							$this->addError( 'Username too short. At least 4 characters is required' );
						}

						elseif ( username_exists( $_POST['username'] ) )
						{
							$this->addError( 'Sorry, that username already exists' );
						}

						elseif ( ! validate_username( $_POST['username'] ) )
						{
							$this->addError( 'Sorry, the username you entered is not valid' );
						}

						elseif ( 5 > strlen( $_POST['password'] ) )
						{
							$this->addError( 'Password length must be greater than 5' );
						}

						elseif ( ! is_email( $_POST['email'] ) )
						{
							$this->addError( 'Email is not valid' );
						}

						elseif ( email_exists( $_POST['email'] ) )
						{
							$this->addError( 'That email address is already in use' );
						}

						if ( count( $this->getErrors() ) == 0 )
						{
							$user_data = array(
								'user_login' => $_POST['username'],
								'user_email' => $_POST['email'],
								'user_pass' => $_POST['password'],
								'first_name' => $_POST['fname'],
								'last_name' => $_POST['lname']
							);
							$user_id = wp_insert_user( $user_data );
							update_user_meta( $user_id, 'state', $_POST['state'] );
							update_user_meta( $user_id, 'phone', $_POST['phone'] );

							header( 'Location:' . $this->add_to_querystring( array( 'action' => 'registered' ), TRUE ) );
							exit;
						}

						break;
					
					case 'purchase':

						$entries = ( isset( $_POST['entries'] ) && is_numeric( $_POST['entries'] ) ) ? intval( $_POST['entries'] ) : 0;
						if ( $entries == 0 )
						{
							$this->addError( 'Please enter a valid number of entries' );
						}

						break;

					case 'confirm':

						if ( ! isset( $_POST['make_changes'] ) )
						{
							$new_entries = ( isset( $_POST['entries'] ) && is_numeric( $_POST['entries'] ) ) ? intval( $_POST['entries'] ) : 0;

							if ( $new_entries == 0 )
							{
								$this->addError( 'Please enter a valid number of entries' );
							}
							else
							{
								$previous_entries = $this->getPhotographer()->getPurchasedEntries();
								$total_entries = $new_entries + $previous_entries;

								$total_free = Entry::getFreeEntryCount( $total_entries, $this->getNumberFreeAt(), $this->getFreeQty() );
								$previous_free = $this->getPhotographer()->getFreeEntries();
								$new_free = $total_free - $previous_free;

								$entries = $new_entries + $new_free;

								$price = $new_entries * $this->getPricePerEntry();

								$order = new Order;
								$order
									->setPhotographerId( $this->getPhotographer()->getId() )
									->setAmount( $price )
									->setEntries( $entries )
									->setPurchasedEntries( $new_entries )
									->setFreeEntries( $new_free )
									->setPaidAt( ( $price == 0 ) ? time() : NULL )
									->create();

								header( 'Location:' . $this->add_to_querystring( array( 'action' => 'ordered' ), TRUE ) );
								exit;
							}
						}

						break;

					case 'submit':

						$category_id = $_POST['category_id'];
						$title = trim( $_POST['title'] );

						if ( strlen( $title ) == 0 )
						{
							$this->addError( 'Please enter a title' );
						}
						elseif ( ! isset( $_FILES['file'] ) || empty( $_FILES['file']['tmp_name'] ) )
						{
							$this->addError( 'Please choose a file to upload' );
						}
						elseif ( strtolower( substr(  $_FILES['file']['name'], -4 ) ) != '.jpg' )
						{
							$this->addError( 'Please choose file ending in .jpg' );
						}

						if ( count( $this->errors ) == 0 )
						{
							$image = getimagesize( $_FILES['file']['tmp_name'] );
							if ( $image[0] < 1920 && $image[1] < 1080 )
							{
								$this->addError( 'Photos must be at least 1920 pixels wide or 1080 pixels tall. Yours is ' . $image[0] . ' X ' . $image[1] . ' pixels.' );
							}
						}

						if ( count( $this->errors ) == 0 )
						{
							$file = wp_upload_bits( $_FILES['file']['name'], NULL, @file_get_contents( $_FILES['file']['tmp_name'] ) );

							if ( ! $file['error'] )
							{
								$wp_filetype = wp_check_filetype( $_FILES['file']['name'], NULL );
								$attachment = array(
									'post_mime_type' => $wp_filetype['type'],
									'post_parent' => 0,
									'post_title' => preg_replace( '/\.[^.]+$/', '', $_FILES['file']['name'] ),
									'post_content' => '',
									'post_status' => 'inherit'
								);
								$attachment_id = wp_insert_attachment( $attachment, $file['file'], 0 );
								if ( ! is_wp_error( $attachment_id ) )
								{
									require_once( ABSPATH . 'wp-admin' . '/includes/image.php');
									$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file['file'] );
									wp_update_attachment_metadata( $attachment_id,  $attachment_data );

									$entry = new Entry;
									$entry
										->setPhotographerId( $this->getPhotographer()->getId() )
										->setCategoryId( $category_id )
										->setTitle( $title )
										->setPhotoPostId( $attachment_id )
										->create();

									if ( $entry->getId() !== NULL )
									{
										header( 'Location:' . $this->add_to_querystring( array( 'action' => 'entries', 'new' => 'true' ), TRUE ) );
										exit;
									}
								}
							}

							$this->addError( 'There was a problem uploading your photo. Please try again.' );
						}

						break;

				case 'edit':

					$id = ( is_numeric( $_POST['id'] ) ) ? intval( $_POST['id'] ) : 0;
					$delete = $_POST['delete'];
					$category_id = $_POST['category_id'];
					$title = trim( $_POST['title'] );

					$entry = new Entry( $id );

					if ( $entry->getId() === NULL || $entry->getPhotographerId() != $this->getPhotographer()->getId() )
					{
						$this->addError( 'You do not have access to modify this entry' );
					}

					if ( count( $this->errors ) == 0 )
					{
						if ( $delete == 1 )
						{
							$entry->delete();
							header( 'Location:' . $this->add_to_querystring( array( 'action'  => 'entries', 'deleted' => 'true' ), TRUE ) );
							exit;
						}

						if ( strlen( $title ) == 0 )
						{
							$this->addError( 'Please enter a title' );
						}
						elseif ( ! empty( $_FILES['file']['tmp_name'] ) && strtolower( substr(  $_FILES['file']['name'], -4 ) ) != '.jpg' )
						{
							$this->addError( 'Please choose file ending in .jpg' );
						}

						if ( count( $this->errors ) == 0 && ! empty( $_FILES['file']['tmp_name'] ) )
						{
							$image = getimagesize( $_FILES['file']['tmp_name'] );
							if ( $image[0] < 1920 && $image[1] < 1080 )
							{
								$this->addError( 'Photos must be at least 1920 pixels wide or 1080 pixels tall. Yours is ' . $image[0] . ' X ' . $image[1] . ' pixels.' );
							}
						}

						if ( count( $this->errors ) == 0 && ! empty( $_FILES['file']['tmp_name'] ) )
						{
							$file = wp_upload_bits( $_FILES['file']['name'], NULL, @file_get_contents( $_FILES['file']['tmp_name'] ) );

							if ( ! $file['error'] )
							{
								$wp_filetype = wp_check_filetype( $_FILES['file']['name'], NULL );
								$attachment = array(
									'post_mime_type' => $wp_filetype['type'],
									'post_parent' => 0,
									'post_title' => preg_replace( '/\.[^.]+$/', '', $_FILES['file']['name'] ),
									'post_content' => '',
									'post_status' => 'inherit'
								);
								$attachment_id = wp_insert_attachment( $attachment, $file['file'], 0 );
								if ( ! is_wp_error( $attachment_id ) )
								{
									require_once( ABSPATH . 'wp-admin' . '/includes/image.php');
									$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file['file'] );
									wp_update_attachment_metadata( $attachment_id,  $attachment_data );

									$entry->setPhotoPostId( $attachment_id );
								}
								else
								{
									$this->addError( 'There was a problem uploading your photo. Please try again.' );
								}
							}
							else
							{
								$this->addError( 'There was a problem uploading your photo. Please try again.' );
							}
						}

						if ( count( $this->errors ) == 0 )
						{
							$entry
								->setTitle( $title )
								->setCategoryId( $category_id )
								->update();

							header( 'Location:' . $this->add_to_querystring( array( 'action'  => 'entries', 'updated' => 'true' ), TRUE ) );
							exit;
						}
					}

					break;
				}
			}
			else
			{
				$this->addError( 'It appears you are submitting this form from a different website' );
			}
		}
	}

	public function add_to_querystring( array $args, $remove_old_query_string=FALSE )
	{
		$url = $_SERVER['REQUEST_URI'];
		$parts = explode( '?', $url );
		$url = $parts[0];
		$querystring = array();
		if ( count( $parts ) > 1 )
		{
			$parts = explode( '&', $parts[1] );
			foreach ( $parts as $part )
			{
				if ( ! $remove_old_query_string || substr( $part, 0, 3 ) == 'id=' )
				{
					$querystring[] = $part;
				}
			}
		}

		foreach ( $args as $key => $val )
		{
			$querystring[] = $key . '=' . $val;
		}

		return $url . ( ( count( $querystring ) > 0 ) ? '?' . implode( '&', $querystring ) : '' );
	}
	
	public function short_code()
	{
		ob_start();
		include( dirname( dirname( __DIR__ ) ) . '/includes/shortcode.php');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	public function admin_menus()
	{
		add_menu_page( 'Spokane Fair', 'Spokane Fair', 'manage_options', 'spokane_fair_photos', array( $this, 'print_settings_page' ), 'dashicons-format-gallery' );
		add_submenu_page( 'spokane_fair_photos', 'Settings', 'Settings', 'manage_options', 'spokane_fair_photos' );
		add_submenu_page( 'spokane_fair_photos', 'Categories', 'Categories', 'manage_options', 'spokane_fair_categories', array( $this, 'print_categories_page' ) );
		add_submenu_page( 'spokane_fair_photos', 'Photographers', 'Photographers', 'manage_options', 'spokane_fair_photographers', array( $this, 'print_photographers_page' ) );
		add_submenu_page( 'spokane_fair_photos', 'Submissions', 'Submissions', 'manage_options', 'spokane_fair_submissions', array( $this, 'print_submissions_page' ) );
	}
	
	public function register_settings()
	{
		register_setting( 'spokane_fair_settings', 'spokane_fair_price_per_entry' );
		register_setting( 'spokane_fair_settings', 'spokane_fair_number_free_at' );
		register_setting( 'spokane_fair_settings', 'spokane_fair_free_qty' );
		register_setting( 'spokane_fair_settings', 'spokane_fair_start_date' );
		register_setting( 'spokane_fair_settings', 'spokane_fair_end_date' );
		register_setting( 'spokane_fair_settings', 'spokane_fair_paypal_email' );
	}

	public function getPricePerEntry()
	{
		$price_per_entry = get_option( 'spokane_fair_price_per_entry' , 0 );
		$price_per_entry = preg_replace( '/[^0-9\.]/', '', $price_per_entry );
		return ( is_numeric( $price_per_entry ) ) ? abs( round( $price_per_entry, 2 ) ) : 0;
	}

	public function getNumberFreeAt()
	{
		$number_free_at = get_option( 'spokane_fair_number_free_at' , 0 );
		$number_free_at = preg_replace( '/\D/', '', $number_free_at );
		return ( is_numeric( $number_free_at ) ) ? $number_free_at : 0;
	}

	public function getFreeQty()
	{
		$free_qty = get_option( 'spokane_fair_free_qty' , 0 );
		$free_qty = preg_replace( '/\D/', '', $free_qty );
		return ( is_numeric( $free_qty ) ) ? $free_qty : 0;
	}

	public function getStartDate( $format='n/j/Y' )
	{
		$start_date = get_option( 'spokane_fair_start_date' , '' );
		return ( strlen( $start_date ) == 0 ) ? '' : date( $format, strtotime( $start_date ) );
	}

	public function getEndDate( $format='n/j/Y' )
	{
		$end_date = get_option( 'spokane_fair_end_date' , '' );
		return ( strlen( $end_date ) == 0 ) ? '' : date( $format, strtotime( $end_date ) );
	}

	public function getPayPalEmail()
	{
		$email = get_option( 'spokane_fair_paypal_email' , '' );
		return ( is_email( $email ) ) ? $email : '';
	}

	/**
	 * @return bool
	 */
	public function canSubmitEntry()
	{
		$today = strtotime( date( 'Y-m-d' ) );
		$start = ( $this->getStartDate() == '' ) ? $today : strtotime( $this->getStartDate() );
		$end = ( $this->getEndDate() == '' ) ? $today : strtotime( $this->getEndDate() );

		return ( $today >= $start && $today <= $end );
	}
	
	public function admin_scripts()
	{
		wp_enqueue_media();
		add_thickbox();
		wp_enqueue_script( 'spokane-fair-admin', plugin_dir_url( dirname( __DIR__ ) ) . 'js/admin.js', array( 'jquery' ), ( WP_DEBUG ) ? time() : self::VERSION_JS, TRUE );
		wp_localize_script( 'spokane-fair-admin', 'url_variables', $_GET );
	}

	/**
	 * @param array $links
	 *
	 * @return array
	 */
	public function settings_link( $links )
	{
		$link = '<a href="options-general.php?page=spokane_fair_photos">Settings</a>';
		$links[] = $link;
		return $links;
	}

	/**
	 *
	 */
	public function settings_page()
	{
		add_options_page(
			'Settings',
			'Settings',
			'manage_options',
			'spokane_fair_photos',
			array( $this, 'print_settings_page')
		);
	}

	/**
	 *
	 */
	public function print_settings_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/settings.php' );
	}

	/**
	 *
	 */
	public function print_categories_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/categories.php' );
	}

	/**
	 *
	 */
	public function print_photographers_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/photographers.php' );
	}

	/**
	 *
	 */
	public function print_submissions_page()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/submissions.php' );
	}

	public function add_category()
	{
		$category = new Category;
		$category
			->setCode( $_REQUEST['code'] )
			->setTitle( $_REQUEST['title'] )
			->setIsVisible( $_REQUEST['is_visible'] )
			->create();
	}

	public function update_category()
	{
		$category = new Category( $_REQUEST['id'] );
		$category
			->setCode( $_REQUEST['code'] )
			->setTitle( $_REQUEST['title'] )
			->setIsVisible( $_REQUEST['is_visible'] )
			->update();
	}

	public function delete_category()
	{
		$category = new Category( $_REQUEST['id'] );
		$category->delete();
	}

	public function extra_profile_fields()
	{
		include( dirname( dirname( __DIR__ ) ) . '/includes/user_fields.php' );
	}

	public function save_extra_profile_fields()
	{
		if ( !current_user_can( 'edit_user', $_POST['user_id'] ) )
		{
			return FALSE;
		}

		if ( isset( $_POST['state'] ) )
		{
			update_user_meta( $_POST['user_id'], 'state', $_POST['state'] );
		}

		if ( isset( $_POST['phone'] ) )
		{
			update_user_meta( $_POST['user_id'], 'phone', $_POST['phone'] );
		}

		return TRUE;
	}

	public function create_nonce()
	{
		$nonce = uniqid();
		update_option( 'spokane_fair_nonce', $nonce );
		return $nonce;
	}

	public function validate_nonce( $nonce )
	{
		$option = get_option( 'spokane_fair_nonce', uniqid() );
		if ( $option == $nonce )
		{
			$this->create_nonce();
			return TRUE;
		}

		return FALSE;
	}
}