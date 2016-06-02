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