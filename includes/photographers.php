<?php

/** @var \SpokaneFair\Controller $this */

$action = 'list';
if ( isset( $_GET[ 'action' ] ) )
{
	switch( $_GET[ 'action' ] )
	{
		case 'view':
		case 'edit':
			$action = $_GET[ 'action' ];
	}
}

?>

<div class="wrap">

	<?php if ( $action == 'view' ) { ?>

		<?php 
		
		$id = ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) ? intval( $_GET['id'] ) : 0;
		$photographer = new \SpokaneFair\Photographer( $id );
		
		?>

		<h1>
			Photographer Info
		</h1>

		<?php if ( $photographer->getId() === NULL ) { ?>

			<p>
				<a href="admin.php?page=spokane_fair_photographers" class="btn btn-default">
					Back
				</a>
			</p>

			<div class="alert alert-danger">
				The photographer you are trying to view is currently unavailable.
			</div>

		<?php } else { ?>

			<?php

			if ( isset( $_GET['delete_entry'] ) && is_numeric( $_GET['delete_entry'] ) )
			{
				$photographer->deleteEntry( $_GET['delete_entry'] );
			}

			if ( isset( $_GET['delete_order'] ) && is_numeric( $_GET['delete_order'] ) )
			{
				$photographer->deleteOrder( $_GET['delete_order'] );
			}

			$paid = ( isset( $_GET['paid'] ) && is_numeric( $_GET['paid'] ) ) ? intval( $_GET['paid'] ) : 0;
			if ( isset( $photographer->getOrders()[$paid] ) && $photographer->getOrders()[$paid]->getPaidAt() === NULL )
			{
				$photographer->getOrders()[$paid]
					->setPaidAt( time() )
					->update();
			}

			$unpaid = ( isset( $_GET['unpaid'] ) && is_numeric( $_GET['unpaid'] ) ) ? intval( $_GET['unpaid'] ) : 0;
			if ( isset( $photographer->getOrders()[$unpaid] ) && $photographer->getOrders()[$unpaid]->getPaidAt() !== NULL )
			{
				$photographer->getOrders()[$unpaid]
					->setPaidAt( NULL )
					->update();
			}

			?>

			<p>
				<a href="admin.php?page=spokane_fair_photographers" class="btn btn-default">
					Back
				</a>
				<a href="/wp-admin/user-edit.php?user_id=<?php echo $photographer->getId(); ?>&wp_http_referer=<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" class="btn btn-default">
					Edit Photographer
				</a>
			</p>

			<p>
				<strong><?php echo $photographer->getFullName(); ?></strong><br>
				<a href="mailto:<?php echo $photographer->getEmail(); ?>"><?php echo $photographer->getEmail(); ?></a><br>
				<?php echo $photographer->getPhone(); ?><br>
				<?php echo $photographer->getState(); ?>
			</p>

			<h1>Orders</h1>

			<table class="table table-striped table-bordered">
				<thead>
				<tr>
					<th>Order Number</th>
					<th>Date</th>
					<th>Entries Purchased</th>
					<th>Amount Due</th>
					<th>Payment Info</th>
					<th>Delete</th>
				</tr>
				</thead>
				<?php foreach ( $photographer->getOrders() as $order ) { ?>
					<tr>
						<td><?php echo $order->getOrderNumber(); ?></td>
						<td><?php echo $order->getCreatedAt( 'n/j/Y' ); ?></td>
						<td><?php echo $order->getEntries(); ?></td>
						<td>$<?php echo number_format( $order->getAmount(), 2 ); ?></td>
						<td>
							<?php if ( $order->getPaidAt() === NULL ) { ?>
								<a href="admin.php?page=spokane_fair_photographers&action=view&id=<?php echo $photographer->getId(); ?>&paid=<?php echo $order->getId(); ?>" class="btn btn-default">
									Mark as Paid
								</a>
							<?php } else { ?>
								Paid on <?php echo $order->getPaidAt( 'n/j/Y' ); ?>
								(<a href="admin.php?page=spokane_fair_photographers&action=view&id=<?php echo $photographer->getId(); ?>&unpaid=<?php echo $order->getId(); ?>">remove payment</a>)
							<?php } ?>
						</td>
						<td>
							<a href="#" data-photographer="<?php echo $photographer->getId(); ?>" data-id="<?php echo $order->getId(); ?>" class="btn btn-danger sf-admin-delete-order">
								<i class="fa fa-times"></i>
								Delete Order
							</a>
						</td>
					</tr>
				<?php } ?>
			</table>

			<h1>Entries</h1>

			<?php if ( $photographer->getEntriesUsedCount() == 0 ) { ?>

				<p>Photographer hasn't submitted any entries yet</p>

			<?php } else { ?>

				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Photo</th>
							<th>Code</th>
							<th>Title</th>
							<th>Category</th>
							<th>Delete</th>
						</tr>
					</thead>
					<?php foreach ( $photographer->getEntries() as $entry ) { ?>
						<?php

						$thumb = wp_get_attachment_image( $entry->getPhotoPostId(), \SpokaneFair\Controller::IMG_THUMB );
						$full = wp_get_attachment_image_src( $entry->getPhotoPostId(), 'full' );

						$width = $full[1];
						$height = $full[2];

						if ( $width >= $height )
						{
							$full = wp_get_attachment_image_src( $entry->getPhotoPostId(), \SpokaneFair\Controller::IMG_FULL_LANDSCAPE );
						}
						else
						{
							$full = wp_get_attachment_image_src( $entry->getPhotoPostId(), \SpokaneFair\Controller::IMG_FULL_PORTRAIT );
						}

						?>
						<tr>
							<td>
								<span class="spokane-fair-image" data-image="<?php echo $full[0]; ?>"><?php echo $thumb; ?></span>
							</td>
							<td><?php echo $entry->getCode(); ?></td>
							<td><?php echo $entry->getTitle(); ?></td>
							<td><?php echo $entry->getCategory()->getTitle(); ?></td>
							<td>
								<a href="#" data-photographer="<?php echo $photographer->getId(); ?>" data-id="<?php echo $entry->getId(); ?>" class="btn btn-danger sf-admin-delete-photo">
									<i class="fa fa-times"></i>
									Delete Submission
								</a>
							</td>
						</tr>
					<?php } ?>
				</table>

			<?php } ?>

		<?php } ?>

	<?php } elseif ( $action == 'edit' ) { ?>

	<?php } else { ?>

		<h1>
			Spokane Interstate Fair Photographers
		</h1>

		<?php

		$table = new \SpokaneFair\PhotographerTable;
		$table->prepare_items();
		$table->display();

		?>

	<?php } ?>

</div>