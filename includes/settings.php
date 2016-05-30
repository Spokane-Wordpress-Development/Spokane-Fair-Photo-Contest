<div class="wrap">

	<h1>
		Spokane Interstate Fair Photo Submission Settings
	</h1>

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

</div>