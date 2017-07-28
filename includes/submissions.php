<?php

/** @var \SpokaneFair\Controller $this */

$category_code = ( isset( $_GET['category_code'] ) && strlen( $_GET['category_code'] ) > 0 ) ? $_GET['category_code'] : NULL;

$sort = isset( $_GET['sort'] ) ? $_GET['sort'] : 'e.id';
$dir = isset( $_GET['dir'] ) ? $_GET['dir'] : 'DESC';

$entries = \SpokaneFair\Entry::getAllEntries( $sort, $dir );

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
			$name = $entry->getCode( TRUE, ( $_GET['export'] == 'names' ) ? TRUE : FALSE );
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

    <?php } elseif ( isset( $_GET['edit'] ) ) { ?>

        <?php $entry = new \SpokaneFair\Entry( $_GET['edit'] ); ?>

        <?php if ( $entry->getId() === NULL ) { ?>

            <h1>Entry Not Found</h1>
            <p>
                <a href="admin.php?page=spokane_fair_submissions">
                    Back to List
                </a>
            </p>

        <?php } else { ?>

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

            <h1>
                '<?php echo $entry->getTitle(); ?>'
                By
                <?php echo $entry->getPhotographer()->getFullName(); ?>
            </h1>
            <p>
                <a href="admin.php?page=spokane_fair_submissions">
                    Back to List
                </a>
            </p>

            <form autocomplete="off">

                <input type="hidden" id="spokane_fair_entry_id" value="<?php echo $entry->getId(); ?>">

                <table class="form-table">
                    <tr>
                        <th>
                            <label for="spokane_fair_is_finalist">Finalist:</label>
                        </th>
                        <td>
                            <select id="spokane_fair_is_finalist">
                                <option value="0"<?php if ( ! $entry->isFinalist() ) { ?> selected<?php } ?>>
                                    No
                                </option>
                                <option value="1"<?php if ( $entry->isFinalist() ) { ?> selected<?php } ?>>
                                    Yes
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="spokane_fair_composition_score">Composition Score:</label>
                        </th>
                        <td>
                            <select id="spokane_fair_composition_score">
                                <?php for ( $x = 0; $x <= 9; $x ++ ) { ?>
                                    <option value="<?php echo $x; ?>"<?php if ( $entry->getCompositionScore() == $x ) { ?> selected <?php } ?>>
                                        <?php echo $x; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="spokane_fair_impact_score">Impact Score:</label>
                        </th>
                        <td>
                            <select id="spokane_fair_impact_score">
                                <?php for ( $x = 0; $x <= 9; $x ++ ) { ?>
                                    <option value="<?php echo $x; ?>"<?php if ( $entry->getImpactScore() == $x ) { ?> selected <?php } ?>>
                                        <?php echo $x; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="spokane_fair_technical_score">Technical Score:</label>
                        </th>
                        <td>
                            <select id="spokane_fair_technical_score">
                                <?php for ( $x = 0; $x <= 9; $x ++ ) { ?>
                                    <option value="<?php echo $x; ?>"<?php if ( $entry->getTechnicalScore() == $x ) { ?> selected <?php } ?>>
                                        <?php echo $x; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="spokane_fair_total_score">Total Score:</label>
                        </th>
                        <td>
                            <select id="spokane_fair_total_score">
                                <?php for ( $x = 0; $x <= 99; $x ++ ) { ?>
                                    <option value="<?php echo $x; ?>"<?php if ( $entry->getTotalScore() == $x ) { ?> selected <?php } ?>>
                                        <?php echo $x; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <a href="#" class="page-title-action" id="spokane-fair-entry-update">
                                Update
                            </a>
                        </td>
                    </tr>
                </table>
            </form>

            <a href="<?php echo $full[0]; ?>" target="_blank">
                <img src="<?php echo $full[0]; ?>" style="max-width: 100%;">
            </a>

        <?php } ?>

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
				<a href="admin.php?page=<?php echo $_GET['page']; ?>&export=names" class="btn btn-warning">
					Export w/ Photographer Names
				</a>
			<?php } ?>
		</form>

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<td><a href="admin.php?page=spokane_fair_submissions&sort=e.id&dir=<?php echo ( $sort == 'e.id' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">ID</a></td>
					<td>Photo</td>
					<td>Code</td>
					<td><a href="admin.php?page=spokane_fair_submissions&sort=e.title&dir=<?php echo ( $sort == 'e.title' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Title</a></td>
					<td><a href="admin.php?page=spokane_fair_submissions&sort=c.title&dir=<?php echo ( $sort == 'c.title' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Category</a></td>
					<td><a href="admin.php?page=spokane_fair_submissions&sort=e.created_at&dir=<?php echo ( $sort == 'e.created_at' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Date</a></td>
					<td><a href="admin.php?page=spokane_fair_submissions&sort=ln.last_name&dir=<?php echo ( $sort == 'ln.last_name' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Photographer</a></td>
                    <td><a href="admin.php?page=spokane_fair_submissions&sort=e.is_finalist&dir=<?php echo ( $sort == 'e.is_finalist' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Finalist</a></td>
                    <td><a href="admin.php?page=spokane_fair_submissions&sort=e.composition_score&dir=<?php echo ( $sort == 'e.composition_score' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Composition</a></td>
                    <td><a href="admin.php?page=spokane_fair_submissions&sort=e.impact_score&dir=<?php echo ( $sort == 'e.impact_score' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Impact</a></td>
                    <td><a href="admin.php?page=spokane_fair_submissions&sort=e.technical_score&dir=<?php echo ( $sort == 'e.technical_score' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Technical</a></td>
                    <td><a href="admin.php?page=spokane_fair_submissions&sort=e.total_score&dir=<?php echo ( $sort == 'e.total_score' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">Total</a></td>
				    <td>Edit</td>
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
						<td><?php echo $entry->getId(); ?></td>
						<td>
							<span class="spokane-fair-image" data-image="<?php echo $full[0]; ?>"><?php echo $thumb; ?></span>
						</td>
						<td><?php echo $entry->getCode(); ?></td>
						<td><?php echo $entry->getTitle(); ?></td>
						<td><?php echo $entry->getCategory()->getTitle(); ?></td>
						<td><?php echo $entry->getCreatedAt( 'n/j/Y g:i a' ); ?></td>
						<td>
							<a href="admin.php?page=spokane_fair_photographers&action=view&id=<?php echo $entry->getPhotographerId(); ?>">
								<?php echo $entry->getPhotographer()->getFullName(); ?>
							</a>
						</td>
                        <td><?php echo ( $entry->isFinalist() ) ? 'YES' : 'NO'; ?></td>
                        <td><?php echo $entry->getCompositionScore(); ?></td>
                        <td><?php echo $entry->getImpactScore(); ?></td>
                        <td><?php echo $entry->getTechnicalScore(); ?></td>
                        <td><?php echo $entry->getTotalScore(); ?></td>
                        <td>
                            <a href="admin.php?page=spokane_fair_submissions&edit=<?php echo $entry->getId(); ?>" class="btn btn-default">
                                Edit
                            </a>
                        </td>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>

	<?php }?>

</div>