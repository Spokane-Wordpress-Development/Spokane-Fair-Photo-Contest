<?php

/** @var \SpokaneFair\Controller $this */

$category_code = ( isset( $_GET['category_code'] ) && strlen( $_GET['category_code'] ) > 0 ) ? $_GET['category_code'] : NULL;

$entries = \SpokaneFair\Entry::getAllEntries();

/** @var \SpokaneFair\Category[] $categories */
$categories = array();

foreach ( $entries as $entry )
{
	if ( ! array_key_exists( $entry->getCategoryId(), $categories ) )
	{
		$categories[ $entry->getCategory()->getCode() ] = $entry->getCategory();
	}
}

ksort( $categories );

?>

<div class="wrap">

	<?php if ( count( $entries ) == 0 ) { ?>

		<h1>
			Spokane Interstate Fair Photo Submissions
		</h1>

		<div class="alert alert-danger">
			There are no submissions yet.
		</div>

	<?php } elseif ( isset( $_GET['export'] ) ) { ?>

		<h1>Submission Export Results</h1>
		<p>
			<a href="admin.php?page=<?php echo $_GET['page']; ?>" class="btn btn-default">
				Back
			</a>
		</p>

		<?php

		$upload_dir = wp_upload_dir();
		$basedir = $upload_dir['basedir'];
		$baseurl = $upload_dir['baseurl'];

		$categories = array();

		if ( ! file_exists( $basedir . '/spokane_fair' ) )
		{
			mkdir( $basedir . '/spokane_fair' );
		}

		if ( ! file_exists( $basedir . '/spokane_fair/' . date('Y') ) )
		{
			mkdir( $basedir . '/spokane_fair/' . date('Y') );
		}

		foreach ( $entries as $entry )
		{
			$code = $entry->getCategory()->getCode();
			$folder = $basedir . '/spokane_fair/' . date('Y') . '/' . $code;

			if ( ! array_key_exists( $code, $categories ) )
			{
				$categories[ $code ] = array();

				if ( ! file_exists( $folder ) )
				{
					mkdir( $folder );
				}
				else
				{
					foreach ( scandir( $folder ) as $item)
					{
						if ( $item != '.' && $item != '..')
						{
							unlink( $folder . '/' . $item );
						}
					}
				}
			}

			$full = wp_get_attachment_image_src( $entry->getPhotoPostId(), 'full' );

			$width = $full[1];
			$height = $full[2];

			if ( $width >= $height )
			{
				$src = wp_get_attachment_image_src( $entry->getPhotoPostId(), \SpokaneFair\Controller::IMG_FULL_LANDSCAPE );
			}
			else
			{
				$src = wp_get_attachment_image_src( $entry->getPhotoPostId(), \SpokaneFair\Controller::IMG_FULL_PORTRAIT );
			}

			$src = str_replace( $baseurl, $basedir, $src[0] );
			$name = $entry->getCode( TRUE );
			copy( $src, $folder . '/' . $name );
			$categories[ $code ][] = $name;
		}

		ksort( $categories );

		$folder = $basedir . '/spokane_fair/' . date('Y');
		foreach ( scandir( $folder ) as $item )
		{
			if ( $item != '.' && $item != '..' )
			{
				if ( is_dir( $folder . '/' . $item ) && ! array_key_exists( $item, $categories ) )
				{
					foreach ( scandir( $folder . '/' . $item ) as $file )
					{
						if ( $file != '.' && $file != '..' )
						{
							unlink( $folder . '/' . $item . '/' . $file );
						}
					}

					rmdir( $folder . '/' . $item );
				}
			}
		}

		?>

		<div class="alert alert-info">Export Complete!</div>

		<pre><?php

			echo 'uploads' . "\r\n";
			echo '|-- spokane_fair' . "\r\n";
			foreach ( $categories as $code => $images )
			{
				echo '    |-- ' . $code . "\r\n";
				foreach ( $images as $image )
				{
					echo '        |-- <a href="' . $baseurl . '/spokane_fair/' . date('Y') . '/' . $code . '/' . $image . '" target="_blank">' . $image . "</a>\r\n";
				}
			}

		?></pre>

	<?php } else { ?>

		<h1>
			Spokane Interstate Fair Photo Submissions
		</h1>

		<form class="well form-inline" method="get">
			<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
			<label for="category_code">Filter By Category</label>
			<select name="category_code" id="category_code" class="select-mono">
				<option value="">
					View All Categories
				</option>
				<?php foreach ( $categories as $code => $category ) { ?>
					<option value="<?php echo $code; ?>"<?php if ( isset( $_GET['category_code'] ) && $_GET['category_code'] == $code ) { ?> selected <?php } ?>>
						<?php echo $category->getCode(); ?>
						-
						<?php echo $category->getTitle(); ?>
					</option>
				<?php } ?>
			</select>
			<button class="btn btn-default">Filter</button>
			<?php if ( isset( $_GET['category_code'] )  ) { ?>
				<a href="admin.php?page=<?php echo $_GET['page']; ?>" class="btn btn-default">View All Categories</a>
			<?php } else { ?>
				<a href="admin.php?page=<?php echo $_GET['page']; ?>&export=true" class="btn btn-warning">
					Export All Photos to Uploads Folder
				</a>
			<?php } ?>
		</form>

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<td>Photo</td>
					<td>Code</td>
					<td>Title</td>
					<td>Category</td>
					<td>Photographer</td>
				</tr>
			</thead>
			<?php foreach ( $entries as $entry ) { ?>
				<?php if ( ! isset( $_GET['category_code'] ) || ( isset( $_GET['category_code'] ) && $_GET['category_code'] == $entry->getCategory()->getCode() ) ) { ?>
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
							<a href="admin.php?page=spokane_fair_photographers&action=view&id=<?php echo $entry->getPhotographerId(); ?>">
								<?php echo $entry->getPhotographer()->getFullName(); ?>
							</a>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>

	<?php }?>

</div>