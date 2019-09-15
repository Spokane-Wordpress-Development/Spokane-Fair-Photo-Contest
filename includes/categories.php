<?php

/** @var \SpokaneFair\Controller $this */

$action = 'list';
if ( isset( $_GET[ 'action' ] ) )
{
	switch( $_GET[ 'action' ] )
	{
		case 'add':
		case 'edit':
        case 'bulk':
			$action = $_GET[ 'action' ];
	}
}

?>

<div class="wrap">

	<?php if ( $action == 'add' ) { ?>

		<h1>
			Add a Category
			<a href="?page=<?php echo $_REQUEST['page']; ?>" class="page-title-action">
				Cancel
			</a>
		</h1>

		<form autocomplete="off">
			<table class="form-table">
				<tr>
					<th>
						<label for="spokane_fair_category_code">Code:</label>
					</th>
					<td>
						<input id="spokane_fair_category_code">
					</td>
				</tr>
				<tr>
					<th>
						<label for="spokane_fair_category_title">Title:</label>
					</th>
					<td>
						<input id="spokane_fair_category_title">
					</td>
				</tr>
				<tr>
					<th>
						<label for="spokane_fair_category_is_visible">Visible:</label>
					</th>
					<td>
						<select id="spokane_fair_category_is_visible">
							<option value="1">
								Yes
							</option>
							<option value="0">
								No
							</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<a href="#" class="page-title-action" id="spokane-fair-category-add">
							Add Category
						</a>
					</td>
				</tr>
			</table>
		</form>

	<?php } elseif ( $action == 'edit' ) { ?>

		<h1>
			Edit Category
			<a href="?page=<?php echo $_REQUEST['page']; ?>" class="page-title-action">
				Cancel
			</a>
		</h1>

		<?php

		$id = ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ) ? intval( $_GET['id'] ) : 0;
		$category = new \SpokaneFair\Category( $id );

		?>

		<?php if ( $category->getId() === NULL ) { ?>

			<div class="alert alert-danger">
				The category you are trying to edit is not currently available.
			</div>

		<?php } else { ?>

			<form autocomplete="off">
				
				<input type="hidden" id="spokane_fair_category_id" value="<?php echo $category->getId(); ?>">
				
				<table class="form-table">
					<tr>
						<th>
							<label for="spokane_fair_category_code">Code:</label>
						</th>
						<td>
							<input id="spokane_fair_category_code" value="<?php echo esc_html( $category->getCode() ); ?>">
						</td>
					</tr>
					<tr>
						<th>
							<label for="spokane_fair_category_title">Title:</label>
						</th>
						<td>
							<input id="spokane_fair_category_title" value="<?php echo esc_html( $category->getTitle() ); ?>">
						</td>
					</tr>
					<tr>
						<th>
							<label for="spokane_fair_category_is_visible">Visible:</label>
						</th>
						<td>
							<select id="spokane_fair_category_is_visible">
								<option value="1">
									Yes
								</option>
								<option value="0"<?php if ( ! $category->isVisible() ) { ?> selected<?php } ?>>
									No
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<a href="#" class="page-title-action" id="spokane-fair-category-update">
								Update
							</a>
							<?php if ( $category->getEntryCount() == 0 ) { ?>
								<a href="#" class="page-title-action" id="spokane-fair-category-delete">
									Delete
								</a>
							<?php } ?>
						</td>
					</tr>
				</table>
			</form>

		<?php } ?>

    <?php } elseif ($action == 'bulk') { ?>

        <h1>
            Bulk Edit Categories
            <a href="?page=<?php echo $_REQUEST['page']; ?>" class="page-title-action">
                Cancel
            </a>
        </h1>

        <form autocomplete="off">
            <table class="form-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Visible</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (\SpokaneFair\Category::getAllCategories() as $category) { ?>
                    <tr>
                        <td><?php echo $category->getId(); ?></td>
                        <td>
                            <input id="code<?php echo $category->getId(); ?>" class="spokane_fair_category_code" data-id="<?php echo $category->getId(); ?>" value="<?php echo esc_html( $category->getCode() ); ?>">
                        </td>
                        <td>
                            <input id="title<?php echo $category->getId(); ?>" class="spokane_fair_category_title" data-id="<?php echo $category->getId(); ?>" value="<?php echo esc_html( $category->getTitle() ); ?>">
                        </td>
                        <td>
                            <select id="visible<?php echo $category->getId(); ?>" class="spokane_fair_category_is_visible" data-id="<?php echo $category->getId(); ?>">
                                <option value="1">
                                    Yes
                                </option>
                                <option value="0"<?php if ( ! $category->isVisible() ) { ?> selected<?php } ?>>
                                    No
                                </option>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <p>
                <a href="#" class="page-title-action" id="spokane-fair-category-bulk">
                    Save Changes
                </a>
            </p>

        </form>

	<?php } else { ?>

		<h1>
			Categories
			<a href="?page=<?php echo $_REQUEST['page']; ?>&action=add" class="page-title-action">
				Add Category
			</a>
            <a href="?page=<?php echo $_REQUEST['page']; ?>&action=bulk" class="page-title-action">
                Bulk Edit
            </a>
		</h1>

		<?php

		$table = new \SpokaneFair\CategoryTable;
		$table->prepare_items();
		$table->display();

		?>

	<?php } ?>

</div>