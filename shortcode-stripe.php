<?php

// SHORTCODE FOR BUTTON - [wpts]
function wpts_button($atts) {
	global $wpdb;
	global $wpts_table_name;
	global $wpts_price_override;

	ob_start(); 

	// Get details from the shortcode
	$atts = shortcode_atts(array('id' => '0'), $atts, 'wpts'); 

	// Save that to a button variable
	$wpts_button_id = $atts['id'];

	// Check Zip Code
	$zipcode = (get_option('wpts_zipcode')) ? 'true' : 'false';

	// get APIKey
	$api_public_key = (get_option('wpts_testmode') === 'on') ?  get_option('wpts_test_public') : get_option('wpts_live_public');

	// Get all the button details
	$selectSQL = $wpdb->get_results("SELECT * FROM $wpts_table_name WHERE id='$wpts_button_id' LIMIT 1", ARRAY_A);
	// Count results
	$row_count = $wpdb->num_rows;
	// List results or show no buttons message.
	if($row_count) { 
		foreach ($selectSQL as $key => $value) { 

		// Check if the price has been overriden - ONLY ON ONEOFF buttons
		$wpts_price = ($wpts_price_override && $value['button_type'] === 'oneoff') ? $wpts_price_override : $value['button_amount']; ?>

		<!-- THIS IS SUBMITTED AFTER TOKEN VALUES ARE ADDED -->
		<form 
			id="stripeForm<?php echo $value['button_custom_id']; ?>" 
			name="stripeForm<?php echo $value['button_custom_id']; ?>" 
			action="<?php echo get_home_url() . '/' . $value['button_success_url']; ?>" 
			method="POST">
			<input type="hidden" 
				name="token_id_<?php echo $value['button_custom_id']; ?>" 
				id="token_id_<?php echo $value['button_custom_id']; ?>" value="">
			<input type="hidden" 
				name="token_email_<?php echo $value['button_custom_id']; ?>" 
				id="token_email_<?php echo $value['button_custom_id']; ?>" value="">
			<input type="hidden" name="final_amount" id="final_amount" value="<?php echo $wpts_price; ?>">
			<input type="hidden" name="wpts_id" id="wpts_id" value="<?php echo $value['id']; ?>">
		</form>

		<script src="https://checkout.stripe.com/checkout.js"></script>

		<button id="<?php echo $value['button_custom_id']; ?>" class="<?php echo $value['button_css_class']; ?>">
			<?php echo $value['button_text']; ?></button>

		<?php
			if(get_option('wpts_testmode') === 'on'){ ?>
				<style type="text/css">
					.test_mode {
						background-color: #f7e633;
					    color: #363636;
					    border-radius: 5px;
					    font-weight: 700;
					    text-transform: uppercase;
					    padding: 8px;
					}
				</style>
				<p><span class="test_mode">TEST MODE</span></p>
			<?php }
		?>

		<script>
			var handler_<?php echo $value['button_custom_id']; ?> = StripeCheckout.configure({
				key: '<?php echo $api_public_key; ?>',
				image: '<?php echo $value['button_image_url']; ?>',
				locale: 'auto',
				currency: '<?php echo $value['button_currency']; ?>',
				token: function(token) {

					// Disable the button
					document.getElementById('<?php echo $value['button_custom_id']; ?>').disabled = true;

					// Update the stripe 
					document.getElementById('token_id_<?php echo $value['button_custom_id']; ?>').value = token.id;
					document.getElementById('token_email_<?php echo $value['button_custom_id']; ?>').value = token.email;

					document.stripeForm<?php echo $value['button_custom_id']; ?>.submit();
				}
			});

			document.getElementById('<?php echo $value['button_custom_id']; ?>').addEventListener('click', function(e) {
				// Open Checkout with further options:
				handler_<?php echo $value['button_custom_id']; ?>.open({
					name: '<?php echo get_option('wpts_company_name'); ?>',
					description: '<?php echo $value['button_description']; ?>',
					zipCode: <?php echo $zipcode; ?>,
					amount: <?php echo $wpts_price; ?>
				});
				e.preventDefault();
			});

			// Close Checkout on page navigation:
			window.addEventListener('popstate', function() {
				handler_<?php echo $value['button_custom_id']; ?>.close();
			});
		</script>

 	<?php } // foreach end.

	} else {
		echo 'NO BUTTON FOUND :(';
	} // If rowcount end.

 	return ob_get_clean();
}
add_shortcode( 'wpts', 'wpts_button' );

// SHORTCODE FOR SUCCESS PAGE - [wpts_success] - This shows the reciepts to the person. Thats it.
function wpts_success_page() {
  global $wpts_success_message;

  echo "<p>Payment Successful!<p>";
  echo $wpts_success_message;

}
add_shortcode( 'wpts_success', 'wpts_success_page' );

// SHORTCODE FOR FAIL PAGE - [wpts_fail] - This shows the error message. Thats it.
function wpts_fail_page() {
  global $wpts_err_message;

  echo "<p>Payment Failed.</p>";
  echo $wpts_err_message;

}
add_shortcode( 'wpts_fail', 'wpts_fail_page' );


function is_page_a_child($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page->post_parent) {
        return '/';
    } else {
        return '/'.$page->post_parent.'/';
    }
} 


?>