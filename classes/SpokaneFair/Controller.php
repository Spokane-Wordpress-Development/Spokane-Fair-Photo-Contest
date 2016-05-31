<?php

namespace SpokaneFair;

class Controller {
	
	const VERSION = '1.0.0';
	const VERSION_JS = '1.0.0';
	const VERSION_CSS = '1.0.0';

	public function activate()
	{
		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		global $wpdb;

		/* create tables */
		$charset_collate = '';
		if ( ! empty( $wpdb->charset ) )
		{
			$charset_collate .= "DEFAULT CHARACTER SET " . $wpdb->charset;
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
		if( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) != $table ) {
			$sql = "
				CREATE TABLE `" . $table . "`
				(
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`photographer_id` INT(11) DEFAULT NULL,
					`category_id` INT(11) DEFAULT NULL,
					`code` VARCHAR(50) DEFAULT NULL,
					`title` VARCHAR(50) DEFAULT NULL,
					`created_at` DATETIME DEFAULT NULL,
					PRIMARY KEY (`id`),
					KEY `photographer_id` (`photographer_id`),
					KEY `category_id` (`category_id`)
				)";
			$sql .= $charset_collate . ";"; // new line to avoid PHP Storm syntax error
			dbDelta( $sql );
		}
	}

	public function init()
	{
		add_thickbox();
		wp_enqueue_script( 'spokane-fair-js', plugin_dir_url( dirname( __DIR__ )  ) . 'js/spokane-fair.js', array( 'jquery' ), ( WP_DEBUG ) ? time() : self::VERSION_JS, TRUE );
		wp_enqueue_style( 'spokane-fair-bootstrap-css', plugin_dir_url( dirname( __DIR__ ) ) . 'css/bootstrap.css', array(), ( WP_DEBUG ) ? time() : self::VERSION_CSS );
	}
	
	public function form_capture()
	{
		
	}
	
	public function short_code()
	{
		
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
}