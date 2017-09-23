<?php

/** @var \SpokaneFair\Controller $this */

$message = NULL;

if ( isset( $_GET['spokane_fair_photos_delete'] ) && isset( $_GET['nonce'] ) )
{
	$order_count = 0;
	$entry_count = 0;

	if ( $this->validate_nonce( $_GET['nonce'] ) )
	{
		if ( $_GET['spokane_fair_photos_delete'] == 'payments' || $_GET['spokane_fair_photos_delete'] == 'both' )
		{
			$orders = \SpokaneFair\Order::getAllOrders();
			$order_count = count( $orders );

			foreach ( $orders as $order )
			{
				$order->delete();
			}
		}

		if ( $_GET['spokane_fair_photos_delete'] == 'submissions' || $_GET['spokane_fair_photos_delete'] == 'both' )
		{
			$entries = \SpokaneFair\Entry::getAllEntries();
			$entry_count = count( $entries );

			foreach ( $entries as $entry )
			{
				$entry->delete();
			}
		}

		$message = 'Submissions Deleted: ' . $entry_count . '<br>Payments Deleted: ' . $order_count;
	}
}

?>

<div class="wrap">

	<h1>
		Spokane Interstate Fair Photo Submission Settings
	</h1>

	<?php if ( $message !== NULL ) { ?>
		<div class="alert alert-info">
			<?php echo $message; ?>
		</div>
	<?php } ?>

	<form method="post" action="options.php" autocomplete="off">

		<?php

		settings_fields( 'spokane_fair_settings' );
		do_settings_sections( 'spokane_fair_settings' );

		?>

		<table class="form-table">
			<tr>
				<th></th>
				<th>Current Value</th>
				<th>Change To</th>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="spokane_fair_price_per_entry">
						Price Per Entry
					</label>
				</th>
				<td>
					$<?php echo number_format( $this->getPricePerEntry(), 2 ); ?>
				</td>
				<td>
					<input type="text" id="spokane_fair_price_per_entry" name="spokane_fair_price_per_entry" value="$<?php echo number_format( $this->getPricePerEntry(), 2 ); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="spokane_fair_number_free_at">
						Number of Entries to get Free Entries
					</label>
				</th>
				<td>
					<?php echo $this->getNumberFreeAt(); ?>
				</td>
				<td>
					<input type="text" id="spokane_fair_number_free_at" name="spokane_fair_number_free_at" value="<?php echo $this->getNumberFreeAt(); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="spokane_fair_free_qty">
						How Many Free Entries
					</label>
				</th>
				<td>
					<?php echo $this->getFreeQty(); ?>
				</td>
				<td>
					<input type="text" id="spokane_fair_free_qty" name="spokane_fair_free_qty" value="<?php echo $this->getFreeQty(); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="spokane_fair_start_date">
						Entries Open On
					</label>
				</th>
				<td>
					<?php echo ( $this->getStartDate() == '' ) ? 'Open Immediately' : $this->getStartDate( 'l, F j, Y' ); ?>
				</td>
				<td>
					<input type="text" id="spokane_fair_start_date" name="spokane_fair_start_date" value="<?php echo $this->getStartDate(); ?>" placeholder="mm/dd/yyyy">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="spokane_fair_end_date">
						Entries Close On
					</label>
				</th>
				<td>
					<?php echo ( $this->getEndDate() == '' ) ? 'Open Indefinitely' : $this->getEndDate( 'l, F j, Y' ); ?>
				</td>
				<td>
					<input type="text" id="spokane_fair_end_date" name="spokane_fair_end_date" value="<?php echo $this->getEndDate(); ?>" placeholder="mm/dd/yyyy">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="spokane_fair_end_date">
						PayPal Email
					</label>
				</th>
				<td>
					<?php echo ( $this->getPayPalEmail() == '' ) ? 'None' : $this->getPayPalEmail(); ?>
				</td>
				<td>
					<input type="text" id="spokane_fair_paypal_email" name="spokane_fair_paypal_email" value="<?php echo $this->getPayPalEmail(); ?>">
				</td>
			</tr>
            <tr valign="top">
                <th scope="row">
                    <label for="spokane_fair_max_width">
                        Max Pixel Width
                    </label>
                </th>
                <td>
                    <?php echo $this->getMaxWidth(); ?> pixels
                </td>
                <td>
                    <input type="text" id="spokane_fair_max_width" name="spokane_fair_max_width" value="<?php echo $this->getMaxWidth(); ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="spokane_fair_max_height">
                        Max Pixel Height
                    </label>
                </th>
                <td>
                    <?php echo $this->getMaxHeight(); ?> pixels
                </td>
                <td>
                    <input type="text" id="spokane_fair_max_height" name="spokane_fair_max_height" value="<?php echo $this->getMaxHeight(); ?>">
                </td>
            </tr>
		</table>

		<?php submit_button(); ?>

	</form>

	<h1>Shortcode</h1>
	<p>
		<strong>
			Add this shortcode to your page:
		</strong>
	</p>

	[spokane_fair_photos]

	<h1>Delete All Submissions and/or Payments</h1>
	<p>Use the buttons below with extreme caution.</p>
	<?php $nonce = $this->create_nonce(); ?>
	<form method="post" id="delete-all-fair-entries">
		<button class="button button-primary" data-what="submissions" data-which="submissions" data-nonce="<?php echo $nonce; ?>">
			Delete All Submissions
		</button>
		<button class="button button-primary" data-what="payments" data-which="payments" data-nonce="<?php echo $nonce; ?>">
			Delete All Payments
		</button>
		<button class="button button-primary" data-what="submissions and payments" data-which="both" data-nonce="<?php echo $nonce; ?>">
			Delete All Submissions and Payments
		</button>
	</form>

</div>