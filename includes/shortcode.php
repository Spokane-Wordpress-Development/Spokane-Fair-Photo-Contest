<?php

/** @var \SpokaneFair\Controller $this */

$action = ( isset( $_GET['action'] ) ) ? $_GET['action'] : '';

?>

<?php if ( count( $this->getErrors() ) > 0 ) { ?>

	<div class="alert alert-danger">
		<p>
			<strong>
				The following error<?php if ( count( $this->getErrors() ) > 1 ) { ?>s<?php } ?> occured:
			</strong>
		</p>
		<ul style="margin:0">
			<?php foreach ( $this->getErrors() as $error ) { ?>
				<li>
					<?php echo $error; ?>
				</li>
			<?php } ?>
		</ul>
	</div>

<?php } ?>

<?php if ( is_user_logged_in() ) { ?>

	<?php if ( strlen( $action ) > 0 ) { ?>
		<p>
			<a href="<?php echo $this->add_to_querystring( array(), TRUE ); ?>" class="btn btn-default">
				<i class="fa fa-chevron-left"></i>
				Back to Main Page
			</a>
		</p>
	<?php } ?>

	<?php if ( $action == 'purchase' ) { ?>

		<?php if ( $this->canSubmitEntry() ) { ?>

			<?php if ( $this->getPricePerEntry() == 0 ) { ?>

				<p>There is currently no cost to submitting new entries.</p>

			<?php } else { ?>

				<p>
					Entries cost $<?php echo number_format( $this->getPricePerEntry(), 2 ); ?> each.
					<?php if ( $this->getNumberFreeAt() > 0 && $this->getFreeQty() > 0 ) { ?>
						If you purchase <?php echo $this->getNumberFreeAt(); ?> entries, you will get
						<?php echo $this->getFreeQty(); ?> additional free entr<?php if ( $this->getFreeQty() > 1 ) { ?>ies<?php } else { ?>y<?php } ?>.
					<?php } ?>
				</p>

				<form method="post">

					<?php wp_nonce_field( 'spokane_fair_purchase', 'spokane_fair_nonce' ); ?>
					<input type="hidden" name="spokane_fair_action" value="purchase">

					<div class="form-group">
						<label for="entries">How many entries would you like to purchase?</label><br>
						<input class="form-control" name="entries" id="entries" style="width:200px" value="<?php echo ( isset( $_POST['entries'] ) ) ? esc_html( $_POST['entries'] ) : ''; ?>">
					</div>

					<button class="btn btn-default">
						Next Step
						<i class="fa fa-chevron-right"></i>
					</button>

				</form>

			<?php } ?>

		<?php } else { ?>

			<div class="alert alert-danger">
				Entry submission is currently closed.
			</div>

		<?php } ?>
		
	<?php } elseif ( $action == 'ordered' ) { ?>

		<?php if ( $this->getPhotographer()->getEntriesOrderedCount() == 0 ) { ?>

			<div class="alert alert-danger">
				<p>You have not purchased any entries. Click below to purchase one or more entries:</p>
				<p>
					<a class="btn btn-default" href="<?php echo $this->add_to_querystring( array( 'action' => 'purchase' ) ); ?>">
						<i class="fa fa-shopping-cart"></i>
						Purchase Entries
					</a>
				</p>
			</div>

		<?php } else { ?>

			<?php if ( isset( $_GET['paid'] ) ) { ?>

				<?php

				$paid_id = ( is_numeric( $_GET['paid'] ) ) ? intval( $_GET['paid'] ) : 0;
				$orders = $this->getPhotographer()->getOrders();
				if ( isset( $orders[ $paid_id ] ) )
				{
					if ( $this->getPhotographer()->getOrders()[ $paid_id ]->getPaidAt() === NULL )
					{
						$this->getPhotographer()->getOrders()[ $paid_id ]
							->setPaidAt( time() )
							->update();
					}
				}

				?>

				<div class="alert alert-info">
					<p>Thank you for your payment!</p>
					<?php if ( $this->getPhotographer()->getEntriesLeftCount() > 0 ) { ?>
						<p>
							<a class="btn btn-default" href="<?php echo $this->add_to_querystring( array( 'action' => 'submit' ), TRUE ); ?>">
								<i class="fa fa-camera"></i>
								Submit a Photo Entry
							</a>
						</p>
					<?php } ?>
				</div>

			<?php } else { ?>

				<p>
					To pay for your entries, please click the PayPal button next to your order below.
				</p>
				<p>
					If you have paid for your entries, it may take a little while to show that they are paid here.
					You can still submit your entries in the meantime.
				</p>

				<p>
					<a class="btn btn-default" href="<?php echo $this->add_to_querystring( array( 'action' => 'purchase' ), TRUE ); ?>">
						<i class="fa fa-shopping-cart"></i>
						Purchase More Entries
					</a>
					<?php if ( $this->getPhotographer()->getEntriesLeftCount() > 0 ) { ?>
						<a class="btn btn-default" href="<?php echo $this->add_to_querystring( array( 'action' => 'submit' ), TRUE ); ?>">
							<i class="fa fa-camera"></i>
							Submit a Photo Entry
						</a>
					<?php } ?>
				</p>

				<table class="table table-striped table-bordered">
					<thead>
					<tr>
						<th>Date</th>
						<th>Entries Purchased</th>
						<th>Amount Due</th>
						<th>Payment Info</th>
					</tr>
					</thead>
					<?php foreach ( $this->getPhotographer()->getOrders() as $order ) { ?>
						<tr>
							<td><?php echo $order->getCreatedAt( 'n/j/Y' ); ?></td>
							<td><?php echo $order->getEntries(); ?></td>
							<td>$<?php echo number_format( $order->getAmount(), 2 ); ?></td>
							<td>
								<?php if ( $order->getPaidAt() === NULL ) { ?>
									<?php if ( $this->getPayPalEmail() == '' ) { ?>
										Website payments are currently offline. Please check back later.
									<?php } else { ?>
										<form name="_xclick" action="https://www.paypal.com/uk/cgi-bin/webscr" method="post">
											<input type="hidden" name="cmd" value="_xclick">
											<input type="hidden" name="business" value="<?php echo $this->getPayPalEmail(); ?>">
											<input type="hidden" name="currency_code" value="USD">
											<input type="hidden" name="item_name" value="P<?php echo $this->getPhotographer()->getId(); ?>O<?php echo $order->getId(); ?>">
											<input type="hidden" name="amount" value="<?php echo $order->getAmount(); ?>">
											<input type="hidden" name="return" value="<?php echo get_site_url() . $this->add_to_querystring( array( 'action' => 'ordered', 'paid' => $order->getId() ), TRUE ); ?>">
											<input type="hidden" name="cancel_return" value="<?php echo get_site_url() . $this->add_to_querystring( array( 'action' => 'ordered' ), TRUE ); ?>">
											<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/x-click-but06.gif" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
										</form>
									<?php } ?>
								<?php } else { ?>
									Paid on <?php echo $order->getPaidAt( 'n/j/Y' ); ?>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</table>

			<?php } ?>

		<?php } ?>
		
	<?php } elseif ( $action == 'entries' ) { ?>

	<?php } elseif ( $action == 'submit' ) { ?>

		<?php $categories = \SpokaneFair\Category::getAllVisibleCategories(); ?>

		<?php if ( $this->getPhotographer()->getEntriesLeftCount() == 0 ) { ?>

			<div class="alert alert-danger">
				<p>You do not have any more enties left.</p>
				<p>
					<a class="btn btn-default" href="<?php echo $this->add_to_querystring( array( 'action' => 'purchase' ), TRUE ); ?>">
						<i class="fa fa-shopping-cart"></i>
						Purchase More Entries
					</a>
				</p>
			</div>

		<?php } elseif ( count( $categories ) == 0 ) { ?>
			
			<div class="alert alert-danger">
				Photo uploading is currently offline. Please check back later.
			</div>
			
		<?php } else { ?>

			<div class="alert alert-info">
				<strong>Note:</strong>
				Photos must be at least 1920 X 1080 pixels and must be a JPG format.
			</div>
			
			<div class="row">
				<div class="col-md-8">

					<form method="post" enctype="multipart/form-data" id="sf_submit_entry_form">

						<?php wp_nonce_field( 'spokane_fair_submit', 'spokane_fair_nonce' ); ?>
						<input type="hidden" name="spokane_fair_action" value="submit">

						<div class="form-group">
							<label for="sf_category_id">
								Choose a Category
							</label>
							<select class="form-control" name="category_id" id="sf_category_id">
								<?php foreach ( $categories as $category ) { ?>
									<option value="<?php echo $category->getId(); ?>"<?php if ( isset( $_POST['category_id'] ) && $_POST['category_id'] == $category->getId() ) { ?> selected<?php } ?>>
										<?php echo $category->getTitle(); ?>
									</option>
								<?php } ?>
							</select>
						</div>

						<div class="form-group">
							<label for="sf_title">
								Title of Photo
							</label>
							<input class="form-control" name="title" id="sf_title" value="<?php echo ( isset( $_POST['title'] ) ) ? esc_html( $_POST['title'] ) : ''; ?>">
						</div>

						<div class="form-group">
							<label for="sf_file">
								Upload Your Photo
							</label>
							<input type="file" name="file" id="sf_file" class="form-control">
						</div>

						<div class="well">
							<button id="sf_submit_entry_add" class="btn btn-default">
								Submit
							</button>
							<a href="<?php echo $this->add_to_querystring( array(), TRUE ); ?>" class="btn btn-danger">
								Cancel
							</a>
						</div>

					</form>

				</div>
				
			</div>
			
		<?php } ?>

	<?php } else { ?>
	
		<p>Welcome back<?php if ( strlen( $this->getPhotographer()->getFullName() ) > 0 ) { ?>, <?php echo $this->getPhotographer()->getFullName(); ?><?php } ?>!</p>

		<table class="table table-bordered table-striped">
			<tr>
				<th>Entries Purchased</th>
				<td><?php echo $this->getPhotographer()->getEntriesOrderedCount(); ?></td>
				<td>
					<a class="btn btn-default btn-block" href="<?php echo $this->add_to_querystring( array( 'action' => 'purchase' ) ); ?>">
						<i class="fa fa-shopping-cart"></i>
						Purchase <?php if ( $this->getPhotographer()->getEntriesOrderedCount() > 0 ) { ?>More<?php } ?> Entries
					</a>
				</td>
			</tr>
			<?php if ( $this->getPhotographer()->getUnpaidEntryCount() > 0 ) { ?>
				<tr>
					<th>Unpaid Entries</th>
					<td><?php echo $this->getPhotographer()->getUnpaidEntryCount(); ?></td>
					<td>
						<a class="btn btn-default btn-block" href="<?php echo $this->add_to_querystring( array( 'action' => 'ordered' ) ); ?>">
							<i class="fa fa-usd"></i>
							Pay Now
						</a>
					</td>
				</tr>
			<?php } ?>
			<?php if ( $this->getPhotographer()->getPaidEntryCount() > 0 ) { ?>
				<tr>
					<th>Paid Entries</th>
					<td><?php echo $this->getPhotographer()->getPaidEntryCount(); ?></td>
					<td>
						<a class="btn btn-default btn-block" href="<?php echo $this->add_to_querystring( array( 'action' => 'ordered' ) ); ?>">
							<i class="fa fa-usd"></i>
							View Payments
						</a>
					</td>
				</tr>
			<?php } ?>
			<?php if ( $this->getPhotographer()->getEntriesUsedCount() > 0 ) { ?>
				<tr>
					<th>Entries Used</th>
					<td>
						<?php echo $this->getPhotographer()->getEntriesUsedCount(); ?>/<?php echo $this->getPhotographer()->getEntriesOrderedCount(); ?>
					</td>
					<td>
						<a class="btn btn-default btn-block" href="<?php echo $this->add_to_querystring( array( 'action' => 'entries' ) ); ?>">
							<i class="fa fa-eye"></i>
							View Entries
						</a>
					</td>
				</tr>
			<?php } ?>
			<?php if ( $this->getPhotographer()->getEntriesLeftCount() > 0 ) { ?>
				<tr>
					<th>Entries Left</th>
					<td>
						<?php echo $this->getPhotographer()->getEntriesLeftCount(); ?>/<?php echo $this->getPhotographer()->getEntriesOrderedCount(); ?>
					</td>
					<td>
						<?php if ( $this->getPhotographer()->getEntriesLeftCount() > 0 ) { ?>
							<a class="btn btn-default btn-block" href="<?php echo $this->add_to_querystring( array( 'action' => 'submit' ) ); ?>">
								<i class="fa fa-camera"></i>
								Upload a Photo
							</a>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</table>

	<?php } ?>

<?php } else { ?>

	<?php if ( $action == 'register' ) { ?>

		<p>Enter your information below to register for an account:</p>

		<form method="post">

			<?php wp_nonce_field( 'spokane_fair_register', 'spokane_fair_nonce' ); ?>
			<input type="hidden" name="spokane_fair_action" value="register">

			<div>
				<label for="username">Username <strong>*</strong></label><br>
				<input type="text" id="username" name="username" value="<?php echo ( isset( $_POST['username'] ) ) ? esc_html( $_POST['username'] ) : ''; ?>">
			</div>

			<div>
				<label for="password">Password <strong>*</strong></label><br>
				<input type="password" id="password" name="password" value="<?php echo ( isset( $_POST['password'] ) ) ? esc_html( $_POST['password'] ) : ''; ?>">
			</div>

			<div>
				<label for="email">Email <strong>*</strong></label><br>
				<input type="text" id="email" name="email" value="<?php echo ( isset( $_POST['email'] ) ) ? esc_html( $_POST['email'] ) : ''; ?>">
			</div>

			<div>
				<label for="fname">First Name <strong>*</strong></label><br>
				<input type="text" id="fname" name="fname" value="<?php echo ( isset( $_POST['fname'] ) ) ? esc_html( $_POST['fname'] ) : ''; ?>">
			</div>

			<div>
				<label for="lname">Last Name <strong>*</strong></label><br>
				<input type="text" id="lname" name="lname" value="<?php echo ( isset( $_POST['lname'] ) ) ? esc_html( $_POST['lname'] ) : ''; ?>">
			</div>

			<div>
				<label for="state">State <strong>*</strong></label><br>
				<input type="text" id="state" name="state" value="<?php echo ( isset( $_POST['state'] ) ) ? esc_html( $_POST['state'] ) : ''; ?>">
			</div>

			<div>
				<label for="phone">Phone Number <strong>*</strong></label><br>
				<input type="text" id="phone" name="phone" value="<?php echo ( isset( $_POST['phone'] ) ) ? esc_html( $_POST['phone'] ) : ''; ?>">
			</div>

			<br>

			<p>
				<button class="wbb-button wbb-button-default">
					Submit
				</button>
			</p>

		</form>

	<?php } else { ?>

		<?php

		$args = array (
			'echo'           => TRUE,
			'redirect'       => $_SERVER['REQUEST_URI'],
			'form_id'        => 'loginform',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Log In' ),
			'id_username'    => 'user_login',
			'id_password'    => 'user_pass',
			'id_remember'    => 'rememberme',
			'id_submit'      => 'wp-submit',
			'remember'       => TRUE,
			'value_username' => '',
			'value_remember' => FALSE
		);

		?>

		<?php if ( $action == 'registered' ) { ?>

			<div class="alert alert-info">
				Thank you for registering! You can now log in below.
			</div>

		<?php } else { ?>

			<h2>Already Registered?</h2>
			<p>
				If you have a login and password, enter them here.<br>
				Otherwise, <a href="<?php echo $this->add_to_querystring( array( 'action' => 'register' ) ); ?>">click here</a> to register.
			</p>

		<?php } ?>

		<?php wp_login_form( $args ); ?>
		<p>
			<a href="/wp-login.php?action=lostpassword">Lost Password?</a>
		</p>

	<?php } ?>

<?php } ?>