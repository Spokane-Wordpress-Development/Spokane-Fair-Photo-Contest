<?php

global $user_id;

$state = get_user_meta( $user_id, 'state', TRUE );
$phone = get_user_meta( $user_id, 'phone', TRUE );

$args = array(
	'post_type' => 'wbb_neighborhood',
	'post_status' => 'publish'
);

$query = new WP_Query( $args );

?>

<h3>Spokane Fair Extra User Data</h3>

<table class="form-table">

	<tr>
		<th><label for="state">State</label></th>
		<td><input name="state" id="state" value="<?php echo esc_html( $state ); ?>"></td>
	</tr>
	<tr>
		<th><label for="phone">Phone</label></th>
		<td><input name="phone" id="phone" value="<?php echo esc_html( $phone ); ?>"></td>
	</tr>

</table>