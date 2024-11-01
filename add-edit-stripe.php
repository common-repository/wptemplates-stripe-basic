<?php
// ADD NEW / EDIT BUTTON
function wpts_show_add_button_page() { 
	global $wpdb;
	global $wpts_table_name;

	include_once( plugin_dir_path( __FILE__ ) . '/styles.php' ); 
	include_once( plugin_dir_path( __FILE__ ) . '/javascript.php' ); 

	$eid = ($_GET['eid']) ? sanitize_text_field($_GET['eid']) : 'new' ;

	// If there has been some values posted, run wpts_insert_new_button 
	if ($_SERVER["REQUEST_METHOD"] == "POST"){

		check_admin_referer( 'eid_'.$eid );

		wpts_add_update_button();
	} 

	$wpts_page_title = ($eid == 'new') ? "Add New" : "Edit" ;
	// If we're editing, get the current values
	if($eid) {
		// Check for the buttons
		$selectSQL = $wpdb->get_results("SELECT * FROM $wpts_table_name WHERE id='$eid'", ARRAY_A);
		// Count results
		$row_count = $wpdb->num_rows;
		// List results or show no buttons message.
		if($row_count) {
			foreach ($selectSQL as $key => $value) {
				$button_name = ($_SERVER["REQUEST_METHOD"] == "POST") ? sanitize_text_field($_POST["button_name"]) : sanitize_text_field($value['button_name']);
				$button_text = ($_SERVER["REQUEST_METHOD"] == "POST") ? sanitize_text_field($_POST["button_text"]) : sanitize_text_field($value['button_text']);
				$button_css_class = ($_SERVER["REQUEST_METHOD"] == "POST") ? sanitize_text_field($_POST["button_css_class"]) : sanitize_text_field($value['button_css_class']);
				$button_amount = ($_SERVER["REQUEST_METHOD"] == "POST") ? intval($_POST["button_amount"]) : intval($value['button_amount']);
				$button_currency = ($_SERVER["REQUEST_METHOD"] == "POST") ? sanitize_text_field($_POST["button_currency"]) : sanitize_text_field($value['button_currency']);
				$button_description = ($_SERVER["REQUEST_METHOD"] == "POST") ? sanitize_text_field($_POST["button_description"]) : sanitize_text_field($value['button_description']);
				$button_success_url = ($_SERVER["REQUEST_METHOD"] == "POST") ? sanitize_text_field($_POST["button_success_url"]) : sanitize_text_field($value['button_success_url']);
				$button_fail_url = ($_SERVER["REQUEST_METHOD"] == "POST") ? sanitize_text_field($_POST["button_fail_url"]) : sanitize_text_field($value['button_fail_url']);
				$button_image_url = ($_SERVER["REQUEST_METHOD"] == "POST") ? esc_url($_POST["button_image_url"]) : esc_url($value['button_image_url']);
			}
		}
	} ?>
	
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php echo $wpts_page_title; ?> Stripe Button</h1>
		<p>Fill in the details of your new Stripe payment button.</p>
		<form action="" method="post" id="add_edit_form">
			<?php wp_nonce_field( 'eid_'.$eid ); ?>
			<input type="hidden" name="eid" id="eid" value="<?php echo $eid; ?>">
			<table class="wpts_table wp-list-table widefat">
				<tr>
					<th>Name</th>
					<td>
						<?php if($eid !== 'new')  { 
							echo esc_html($button_name); ?>
							<input type="hidden" name="button_name" id="button_name" value="<?php echo esc_html($button_name); ?>">
						<?php } else { ?>
							<input class="validate" type="text" name="button_name" id="button_name" value="" placeholder="Button Name">
						<?php } ?>
						</td>
					<td>*Required</td>
					<td>Only used for reference in the admin section</td>
				</tr>
				<tr>
					<th>Button Text</th>
					<td><input class="validate" type="text" name="button_text" id="button_text" value="<?php echo esc_html($button_text); ?>" placeholder="i.e. Buy Now"></td>
					<td>*Required</td>
					<td>What the button will say. i.e Sign Up, Buy Now</td>
				</tr>
				<tr>
					<th>Type</th>
					<td>
						<?php if($eid == 'new') { ?>
						<select name="button_type" id="button_type">
							<option value="oneoff">One Off Payment</option>
							<option value="subscription">Recurring Subscription</option>
						</select>
						<?php } else {
							echo "One Off Payment";
						} ?>
					</td>
					<td>*Required</td>
					<td></td>
				</tr>
				<?php if($eid == 'new') { ?>
					<tr class="do_not_show">
						<th>Billing Cycle</th>
						<td>
							<select name="button_billing_cycle" id="button_billing_cycle">
								<option value="day">Daily</option>
								<option value="week">Weekly</option>
								<option value="month">Monthly</option>
								<option value="year">Yearly</option>
							</select>
						</td>
						<td>*Required</td>
						<td></td>
					</tr>
					<tr class="do_not_show">
						<th>Billing Cycle (Interval)</th>
						<td>
							<select name="button_billing_cycle_interval" id="button_billing_cycle_interval">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
							</select>
						</td>
						<td>*Required</td>
						<td>1 is default. If set to 3 and Billing Cycle 'Month' then payments would be every 3 months.</td>
					</tr>
					<tr class="do_not_show">
						<th>Product / Subscription Name</th>
						<td><input class="validate" type="text" name="button_product_name" id="button_product_name" value="" placeholder="Must be unique"></td>
						<td>*Required</td>
						<td><span style="color:#A80000">NOTE:</span> This needs to be unique.</td>
					</tr>
				<?php } ?>
				<tr>
					<th>Amount</th>
					<td><input class="validate" type="number" name="button_amount" id="button_amount" value="<?php echo esc_html($button_amount); ?>" placeholder="i.e. 600" step="1" min="0" max="99999999"></td>
					<td>*Required</td>
					<td>In cents. So $6 would be 600. Max of 99999999</td>
				</tr>
				<tr>
					<th>Currency</th>
					<td>
						<select name="button_currency" id="button_currency">
							<option value="usd" <?php if($button_currency == 'usd') { echo 'selected="selected"'; }; ?>>United States (USD)</option>
							<option value="aud" <?php if($button_currency == 'aud') { echo 'selected="selected"'; }; ?>>Australia (AUD)</option>
							<option value="eur" <?php if($button_currency == 'eur') { echo 'selected="selected"'; }; ?>>Euro (EUR)</option>
							<option value="gbp" <?php if($button_currency == 'gbp') { echo 'selected="selected"'; }; ?>>United Kingdom (GBP)</option>							
						</select>
					</td>
					<td>*Required</td>
					<td></td>
				</tr>
				<tr>
					<th>Success page</th>
					<td>
						<select name="button_success_url" id="button_success_url">
							<?php
								$pages = get_pages(); 
							    foreach ($pages as $page) {
							    	$page_link = get_page_link( $page->ID );
							    	$page_slug = trim(str_replace(array('http://','https://'),'',str_replace(home_url(),'',$page_link)), '/');
							    	$selected = ($button_success_url === $page_slug) ? 'selected="selected"': '' ;
							    	if($page_slug) {
							    		echo '<option value="'.$page_slug.'" '.$selected.'>'.$page->post_title.'</option>';
							    	}
							    }
							?>
						</select>
					</td>
					<td>*Required</td>
					<td>The page that the customer is redirected to after a successful payment</td>
				</tr>
				<tr>
					<th>Fail page</th>
					<td>
						<select name="button_fail_url" id="button_fail_url">
							<?php
								$pages = get_pages(); 
							    foreach ($pages as $page) {
							    	$page_link = get_page_link( $page->ID );
							    	$page_slug = trim(str_replace(array('http://','https://'),'',str_replace(home_url(),'',$page_link)), '/');
							    	$selected = ($button_fail_url === $page_slug) ? 'selected="selected"': '' ;
							    	if($page_slug) {
							    		echo '<option value="'.$page_slug.'" '.$selected.'>'.$page->post_title.'</option>';
							    	}
							    }
							?>
						</select>
					</td>
					<td>*Required</td>
					<td>The page that the customer is redirected to after a failed payment</td>
				</tr>
				<tr>
					<th>Description</th>
					<td><input type="text" name="button_description" id="button_description" value="<?php echo esc_html($button_description); ?>" placeholder="Describe the product"></td>
					<td>(Optional)</td>
					<td>The description that appears under the image</td>
				</tr>
				<tr>
					<th>Image</th>
					<td>
						<input type="hidden" class="regular-text process_custom_images" id="button_image_url" name="button_image_url" value="">
    					<button class="set_custom_images button">Add Image</button>
    					<?php
    					if($button_image_url) {
    						echo '<img id="wpts_thumbnail" src="'.esc_url($button_image_url).'" width="28" height="28">';
    					} else {
    						echo '<img class="not_visible" id="wpts_thumbnail" src="" height="28">';
    					}
    					?>
    					
    				</td>
					<td>(Optional)</td>
					<td>Your logo or an image of the product</td>
				</tr>
				<tr>
					<th>CSS Class</th>
					<td><input type="text" name="button_css_class" id="button_css_class" value="<?php echo esc_html($button_css_class); ?>" placeholder="CSS Class"></td>
					<td>(Optional)</td>
					<td>Add in any CSS classes you may want</td>
				</tr>				
			</table>
		</form>
		<p><button data-to-submit="add_edit_form" data-action="validate" type="button" class="button button-primary"><?php echo $wpts_page_title; ?> Button</button></p>

	</div>

<?php }

function wpts_add_update_button() {
	global $wpdb;
	global $wpts_table_name;

	// Check if we are editing a button
	$eid = ($_GET['eid']) ? sanitize_text_field($_GET['eid']) : false;

	// Create a custom ID for the button
	$button_custom_id = 'WPTS' . substr(md5(uniqid(rand(1,6))), 0, 8);

	// If this page has post values sent, save them as a new button
	$button_name = ($_POST["button_name"]) ? sanitize_text_field($_POST["button_name"]) : false;
	$button_text = ($_POST["button_text"]) ? sanitize_text_field($_POST["button_text"]) : false;
	$button_css_class = ($_POST["button_css_class"]) ? sanitize_text_field($_POST["button_css_class"]) : false;
	$button_type = ($_POST["button_type"]) ? sanitize_text_field($_POST["button_type"]) : false;
	$button_billing_cycle = ($_POST["button_billing_cycle"]) ? sanitize_text_field($_POST["button_billing_cycle"]) : false;
	$button_billing_cycle_interval = ($_POST["button_billing_cycle_interval"]) ? sanitize_text_field($_POST["button_billing_cycle_interval"]) : false;
	$button_product_name = ($_POST["button_product_name"]) ? sanitize_text_field($_POST["button_product_name"]) : false;
	$button_product_id = strtolower(str_replace(' ','-',$button_product_name));
	$button_amount = ($_POST["button_amount"]) ? intval($_POST["button_amount"]) : false;
	$button_currency = ($_POST["button_currency"]) ? sanitize_text_field($_POST["button_currency"]) : false;
	$button_description = ($_POST["button_description"]) ? sanitize_text_field($_POST["button_description"]) : false;
	$button_success_url = ($_POST["button_success_url"]) ? sanitize_text_field($_POST["button_success_url"]) : false;
	$button_fail_url = ($_POST["button_fail_url"]) ? sanitize_text_field($_POST["button_fail_url"]) : false;
	$button_image_url = ($_POST["button_image_url"]) ? esc_url($_POST["button_image_url"]) : false;

	// Create the string
	if($button_name && !$eid) {
		$insertSQL = "INSERT INTO $wpts_table_name (button_custom_id, button_name, button_text, button_css_class, button_type, button_billing_cycle, button_billing_cycle_interval, button_product_name, button_product_id, button_amount, button_currency, button_description, button_success_url, button_fail_url, button_image_url) 
		VALUES ('$button_custom_id','$button_name','$button_text','$button_css_class','$button_type','$button_billing_cycle','$button_billing_cycle_interval','$button_product_name','$button_product_id','$button_amount','$button_currency','$button_description','$button_success_url','$button_fail_url','$button_image_url')";

		// Insert the string
		$sql_query = $wpdb->query($insertSQL);

		if($sql_query) {
			echo 'SAVED. All Good :)';
		} else {
			echo 'Oops.. something went wrong when saving your button. Err = '.$wpdb->last_error;
		}
	} else if($eid) {

		$updateSQL = "UPDATE $wpts_table_name 
		SET 
		button_text='$button_text',
		button_css_class='$button_css_class',
		button_amount='$button_amount', 
		button_currency='$button_currency', 
		button_description='$button_description', 
		button_success_url='$button_success_url', 
		button_fail_url='$button_fail_url',
		button_image_url='$button_image_url'
		WHERE id='$eid'";

		// Insert the string
		$sql_query = $wpdb->query($updateSQL);

		if($sql_query) {
			echo 'UPDATED. All Good :)';
		} else {
			echo 'Oops.. something went wrong when updating your button. Err = '.$wpdb->last_error;
		}
	}
	
}

?>