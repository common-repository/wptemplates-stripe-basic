<?php
function wpts_show_buttons_page() { 
	global $wpdb;
	global $wpts_table_name; 

	$add_new_url = admin_url( 'admin.php?page=wpts_add_button');
	$this_url = admin_url( 'admin.php?page=wpts_stripe_buttons'); 
	$del = ($_GET['del']) ? $_GET['del'] : false ;

	if ($del){
		check_admin_referer( 'del_'.$del );

		wpts_delete_button();
	} ?>

	<div class="wrap">
		<h1 class="wp-heading-inline">Stripe Buttons</h1>
		<a class="page-title-action" href="<?php echo $add_new_url; ?>">Add New</a>

		<?php
			// Check for the buttons
			$selectSQL = $wpdb->get_results("SELECT * FROM $wpts_table_name", ARRAY_A);
			// Count results
			$row_count = $wpdb->num_rows;
			// List results or show no buttons message.
			if($row_count) { ?>

				<p>Your buttons.</p>
				<table class="wpts_button_table wp-list-table widefat">
					<tr>
						<th>Name</th>
						<th>Type</th>
						<th>Currency</th>
						<th>Amount</th>
						<th>Shortcode</th>
						<th>Success Shortcode</th>
						<th>Fail Shortcode</th>
						<th>EDIT</th>
						<th>DELETE</th>
					</tr>

					<?php 
					foreach ($selectSQL as $key => $value) { 
						if($value['id'] !== $del) { ?>
							<tr>
								<td><?php echo $value['button_name']; ?></td>
								<td><?php echo $value['button_type']; ?></td>
								<td><?php echo strtoupper($value['button_currency']); ?></td>
								<td><?php echo $value['button_amount']; ?></td>
								<td>[wpts id="<?php echo $value['id']; ?>"]</td>
								<td>[wpts_success]</td>
								<td>[wpts_fail]</td>
								<td>
									<?php if($value['button_type'] === 'oneoff') {
										echo '<a class="button" href="'.$add_new_url.'&eid='.$value['id'].'">Edit</a>';
									} else {
										echo 'N/A';
									} ?>
								</td>
								<td>
									<?php 
									$del_url = wp_nonce_url( $this_url.'&del='.$value['id'], 'del_'.$value['id'] );
									echo '<a class="button" href="'.$del_url.'">Delete</a>'; ?>
								</td>
							</tr>
						<?php } ?>
					<?php } ?>

				</table>
			<?php } else { ?>					
				<p>No buttons.</p>
			<?php }
		?>

	</div>
	
<?php }

function wpts_delete_button() {
	global $wpdb;
	global $wpts_table_name;
	$del = ($_GET['del']) ? $_GET['del'] : false ;

	$deleteSQL = "DELETE FROM $wpts_table_name WHERE id='$del'";

	// Delete button
	$sql_query = $wpdb->query($deleteSQL);

	if($sql_query) {
		echo 'Deleted.';
	}
}

?>