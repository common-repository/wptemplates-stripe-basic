<?php
function wpts_show_errors_page() { 
	global $wpdb;
	global $wpts_error_table_name;?>

	<div class="wrap">
		<h1 class="wp-heading-inline">Error Log</h1>

		<?php
			// Check for the buttons
			$selectSQL = $wpdb->get_results("SELECT * FROM $wpts_error_table_name ORDER BY id DESC", ARRAY_A);
			// Count results
			$row_count = $wpdb->num_rows;
			// List results or show no buttons message.
			if($row_count) { ?>

				<p>List of failed payments.</p>
				<table class="wpts_button_table wp-list-table widefat">
					<tr>
						<th>Button Name</th>
						<th>Error Message</th>
						<th>Date</th>
					</tr>

					<?php 
					foreach ($selectSQL as $key => $value) { ?>

						<tr>
							<td><?php echo $value['button_name']; ?></td>
							<td><?php echo $value['wpts_stripe_error_message']; ?></td>
							<td><?php echo $value['wpts_datestamp']; ?></td>
						</tr>
						
					<?php } ?>

				</table>
			<?php } else { ?>					
				<p>No errors :)</p>
			<?php }
		?>

	</div>
	
<?php }

?>