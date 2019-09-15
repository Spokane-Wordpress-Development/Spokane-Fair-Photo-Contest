<?php

ini_set( 'auto_detect_line_endings', TRUE );

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

/** @var \SpokaneFair\Photographer[] $categories */
$photographers = array();

foreach ( $entries as $entry )
{
    if ( ! array_key_exists( $entry->getPhotographerId(), $photographers ) )
    {
        $photographers[ $entry->getPhotographerId() ] = $entry->getPhotographer()->getFullName();
    }
}

asort( $photographers );

?>

<div class="wrap">

	<?php if ( count( $entries ) == 0 ) { ?>

		<h1>
			Submissions
		</h1>

		<div class="alert alert-danger">
			There are no submissions yet.
		</div>

    <?php } elseif( isset( $_GET['import'] ) ) { ?>

        <?php if ( $_GET['import'] == 'true' ) { ?>

            <h1>Import Scores</h1>
            <p>
                <a href="admin.php?page=<?php echo $_GET['page']; ?>" class="btn btn-default">
                    Back
                </a>
            </p>

            <p>
                Upload or browse to an import file below.
                Must be a CSV file and contain the following columns:
            </p>
            <ul style="list-style: disc; padding-left: 25px;">
                <li><strong>Entry</strong> (Image name or Category + Code, ex: ARCH-1234)</li>
                <li><strong>Composition</strong> (0 - 5)</li>
                <li><strong>Impact</strong> (0 - 5)</li>
                <li><strong>Technical</strong> (0 - 5)</li>
                <li><strong>Finalist</strong> (Y or N)</li>
            </ul>

            <p>
                <input type="hidden" name="sf_score_import_file" id="sf-score-import-file">
                <input id="sf-score-import-button" class="button-primary" value="Choose File" type="button">
            </p>

        <?php } else { ?>

            <?php

            $file_is_good = FALSE;
            $file_error = '';
            $import_entries = array();
            $columns = array();

            $file = get_attached_file( $_GET['import'] );

            if ( $file !== FALSE && strlen( $file ) > 0 )
            {
                if ( strtoupper( substr( $file, -3 ) ) == 'CSV' )
                {
                    $handle = fopen( $file, 'r' );

                    if ( $handle !== FALSE )
                    {
                        $columns = fgetcsv( $handle );

                        if ( $columns !== NULL )
                        {
                            foreach ( $columns as $index => $column )
                            {
                                $columns[ $index ] = strtolower( $column );
                            }

                            $required_columns = array( 'entry', 'composition', 'impact', 'technical', 'finalist' );
                            $missing_columns = [];

                            foreach ( $required_columns as $required_column )
                            {
                                if ( ! in_array( $required_column, $columns ) )
                                {
                                    $missing_columns[] = $required_column;
                                }
                            }

                            if ( count( $missing_columns ) == 0 )
                            {
                                while ( ( $line = fgetcsv( $handle ) ) !== FALSE )
                                {
                                    $import_entries[] = $line;
                                }

                                if ( count( $import_entries ) > 0 )
                                {
                                    $file_is_good = TRUE;
                                }
                                else
                                {
                                    $file_error = 'Import file contains no data.';
                                }
                            }
                            else
                            {
                                $file_error = 'Import file is missing the following column' . ( ( count( $missing_columns ) > 1 ) ? 's' : '' ) . ': ' . implode( ', ', $missing_columns );
                            }
                        }
                        else
                        {
                            $file_error = 'The import file appears to be corrupt or invalid.';
                        }
                    }
                    else
                    {
                        $file_error = 'The import file could not be opened.';
                    }
                }
                else
                {
                    $file_error = 'The import file must be a CSV file.';
                }
            }
            else
            {
                $file_error = 'The file you are attempting to use does not exist.';
            }

            ?>

            <?php if ( ! $file_is_good ) { ?>

                <h1>Import Error</h1>
                <p><?php echo $file_error; ?></p>
                <p>
                    <a href="admin.php?page=<?php echo $_GET['page']; ?>" class="btn btn-default">
                        Back
                    </a>
                </p>

            <?php } else { ?>

                <h1>Import Results</h1>
                <p>
                    <a href="admin.php?page=<?php echo $_GET['page']; ?>" class="btn btn-default">
                        Back
                    </a>
                </p>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Entry</th>
                            <th>Found</th>
                            <th>Composition</th>
                            <th>Impact</th>
                            <th>Technical</th>
                            <th>Total</th>
                            <th>Finalist</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $import_entries as $import_entry ) { ?>

                            <?php

                            $data = array(
                                'entry' => '',
                                'composition' => 0,
                                'impact' => 0,
                                'technical' => 0,
                                'finalist' => 'N',
                                'comments' => ''
                            );

                            foreach ( $import_entry as $key => $val )
                            {
                                switch ( $columns[ $key ] )
                                {
                                    case 'entry':
                                        $data['entry'] = $val;
                                        break;
                                    case 'composition':
                                        $data['composition'] = $val;
                                        break;
                                    case 'impact':
                                        $data['impact'] = $val;
                                        break;
                                    case 'technical':
                                        $data['technical'] = $val;
                                        break;
                                    case 'finalist':
                                        $data['finalist'] = $val;
                                        break;
                                    case 'comments':
                                        $data['comments'] = $val;
                                        break;
                                }
                            }

                            $entry = new \SpokaneFair\Entry;
                            $parts = explode( '_', $data['entry'] );

                            if ( count( $parts ) > 1 )
                            {
                                $code = preg_replace( '/[^0-9,.]/', '', $parts[0] );
                                $entries = \SpokaneFair\Entry::getEntryByCode( $code );
                                if ( count( $entries ) == 1 )
                                {
                                    $entry = array_values( $entries )[0];
                                }
                            }

                            ?>

                            <tr>
                                <td><?php echo $data['entry']; ?></td>
                                <?php if ( $entry->getId() !== NULL ) { ?>

                                    <td>
                                        <strong style="color:darkgreen;">
                                            YES
                                        </strong>
                                    </td>
                                    <td nowrap="">
                                        <?php echo $entry->getCompositionScore(); ?>
                                        =&gt;
                                        <?php echo $data['composition']; ?>
                                    </td>
                                    <td nowrap="">
                                        <?php echo $entry->getImpactScore(); ?>
                                        =&gt;
                                        <?php echo $data['impact']; ?>
                                    </td>
                                    <td nowrap="">
                                        <?php echo $entry->getTechnicalScore(); ?>
                                        =&gt;
                                        <?php echo $data['technical']; ?>
                                    </td>
                                    <td nowrap="">
                                        <?php echo $entry->getTotalScore(); ?>
                                        =&gt;
                                        <?php echo intval( $data['composition'] + $data['impact'] + $data['technical'] ); ?>
                                    </td>
                                    <td nowrap="">
                                        <?php echo ( $entry->isFinalist() ) ? 'Y' : 'N'; ?>
                                        =&gt;
                                        <?php echo ( strtolower( $data['finalist'] ) == 'y' ) ? 'Y' : 'N'; ?>
                                    </td>

                                    <?php

                                    $entry
                                        ->setCompositionScore( $data['composition'] )
                                        ->setImpactScore( $data['impact'] )
                                        ->setTechnicalScore( $data['technical'] )
                                        ->setIsFinalist( ( strtolower( $data['finalist'] ) == 'y' ) )
                                        ->update();

                                    ?>

                                <?php } else { ?>
                                    <td>
                                        <strong style="color:red;">
                                            NO
                                        </strong>
                                    </td>
                                    <td colspan="5"></td>
                                <?php } ?>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>

            <?php } ?>

        <?php } ?>

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
		    if ( isset( $_GET['finalists'] ) )
            {
                if ( $_GET['finalists'] == 'yes' && ! $entry->isFinalist() )
                {
                    continue;
                }
                elseif ( $_GET['finalists'] == 'no' && $entry->isFinalist() )
                {
                    continue;
                }
            }

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
                                <?php for ( $x = 0; $x <= 5; $x ++ ) { ?>
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
                                <?php for ( $x = 0; $x <= 5; $x ++ ) { ?>
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
                                <?php for ( $x = 0; $x <= 5; $x ++ ) { ?>
                                    <option value="<?php echo $x; ?>"<?php if ( $entry->getTechnicalScore() == $x ) { ?> selected <?php } ?>>
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
			Submissions
		</h1>

		<form class="well form-inline" method="get">
			<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
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
            <select name="photographer_id" id="photographer_id" class="select-mono">
                <option value="">
                    View All Photographers
                </option>
                <?php foreach ( $photographers as $photographer_id => $photographer_name ) { ?>
                    <option value="<?php echo $photographer_id; ?>"<?php if ( isset( $_GET['photographer_id'] ) && $_GET['photographer_id'] == $photographer_id ) { ?> selected <?php } ?>>
                        <?php echo $photographer_name; ?>
                    </option>
                <?php } ?>
            </select>
			<button class="btn btn-default">Filter</button>
			<?php if ( isset( $_GET['category_code'] )  ) { ?>
				<a href="admin.php?page=<?php echo $_GET['page']; ?>" class="btn btn-default">View All</a>
			<?php } else { ?>
                <p>
                    <a href="admin.php?page=<?php echo $_GET['page']; ?>&export=true" class="btn btn-warning">
                        Export All Photos
                    </a>
                    <a href="admin.php?page=<?php echo $_GET['page']; ?>&export=names" class="btn btn-warning">
                        Export w/ Names
                    </a>
                    <a href="admin.php?page=<?php echo $_GET['page']; ?>&export=names&finalists=yes" class="btn btn-warning">
                        Export Finalists
                    </a>
                    <a href="admin.php?page=<?php echo $_GET['page']; ?>&export=names&finalists=no" class="btn btn-warning">
                        Export non-Finalists
                    </a>
                    <a href="admin.php?page=<?php echo $_GET['page']; ?>&import=true" class="btn btn-warning">
                        Import Scores
                    </a>
                </p>
			<?php } ?>
		</form>

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<td><a href="admin.php?page=spokane_fair_submissions&sort=e.id&dir=<?php echo ( $sort == 'e.id' && $dir == 'DESC' ) ? 'ASC' : 'DESC'; ?>">ID</a></td>
					<td>Photo</td>
                    <td>Dimensions</td>
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
                <?php

                $show = TRUE;
                if ( isset( $_GET['category_code'] ) && strlen( $_GET['category_code'] ) > 0 && $_GET['category_code'] != $entry->getCategory()->getCode() )
                {
                    $show = FALSE;
                }
                elseif ( isset( $_GET['photographer_id'] ) && strlen( $_GET['photographer_id'] ) > 0 && $_GET['photographer_id'] != $entry->getPhotographerId() )
                {
                    $show = FALSE;
                }

                ?>
				<?php if ( $show ) { ?>
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
                        <td>
                            <?php if ($entry->hasDimensions()) { ?>
                                <?php echo $entry->getWidth(); ?>
                                X
                                <?php echo $entry->getHeight(); ?>
                            <?php } ?>
                        </td>
						<td><?php echo $entry->getCode(); ?></td>
						<td><?php echo $entry->getTitle(); ?></td>
						<td><?php echo $entry->getCategory()->getTitle(); ?></td>
						<td><?php echo $entry->getCreatedAt( 'n/j/Y g:i a' ); ?></td>
						<td>
							<a href="admin.php?page=spokane_fair_photographers&action=view&id=<?php echo $entry->getPhotographerId(); ?>">
								<?php echo $entry->getPhotographer()->getFullNameOrEmail(); ?>
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